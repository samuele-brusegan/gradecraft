<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

include_once BASE_PATH . '/app/core/cvv/apiMethods.php';

class Controller {
    public function index(): void {
        // 1. Chiede al Model di ottenere i dati
        $response = [];
        try {
            $rq = "subjects";
            $responseA = $this->requestToApi($rq);
            $response['subjects'] = $responseA;
        } catch (Exception $e) {
            $response['error_subjects'] = "API_ERROR_SUBJECTS";
        }
        try {
            $rq = "get_grades";
            $responseA = $this->requestToApi($rq);
            $response['grades'] = $responseA;
        } catch (Exception $e) {
            $response['error_grades'] = "API_ERROR_GRADES";
        }

            // 2. Passa i dati alla View per la visualizzazione
        require_once BASE_PATH . '/app/views/dashboard.php';
    }
    public function grades(): void {
        try {
            $rq = "get_grades";
            $allGrades = $this->requestToApi($rq);
        } catch (Exception $e) {
            $allGrades = ["error" => "API_ERROR"];
        }

        $subjectId = $_GET['subject'] ?? null;
        $error = null;
        $validGrades = [];
        $subjectsList = [];

        if (isset($allGrades['error'])) {
            $error = $allGrades['error'];
        } else {
            // Filtra solo voti con decimalValue numerico
            $validGrades = array_filter($allGrades, function($g) {
                return isset($g['decimalValue']) && is_numeric($g['decimalValue']);
            });

            // Recupera lista materie (per nomi)
            try {
                $subjectsRaw = $this->requestToApi('subjects');
                foreach ($subjectsRaw as $s) {
                    $subjectsList[$s['id']] = $s['description'];
                }
            } catch (Exception $e) {
                // ignore
            }
        }

        // Se c'è subjectId, filtra
        if ($subjectId && !$error) {
            $filteredGrades = array_filter($validGrades, function($g) use ($subjectId) {
                return $g['subjectId'] == $subjectId;
            });
            $validGrades = array_values($filteredGrades);
        }

        // Calcolo medie
        $overallAvg = null;
        $subjectAverages = [];
        $globalPeriods = [];

        if (!$error && count($validGrades) > 0) {
            // Media globale
            $sum = array_sum(array_column($validGrades, 'decimalValue'));
            $count = count($validGrades);
            $overallAvg = $sum / $count;

            // Raggruppa per materia e periodo
            foreach ($validGrades as $g) {
                $sid = $g['subjectId'];
                $pDesc = $g['periodDesc'] ?? 'Senza periodo';
                $pPos = $g['periodPos'] ?? 0;
                $value = floatval($g['decimalValue']);

                // init materia se necessario
                if (!isset($subjectAverages[$sid])) {
                    $subjectAverages[$sid] = [
                        'name' => $subjectsList[$sid] ?? ($g['subjectDesc'] ?? 'Materia'),
                        'total_sum' => 0,
                        'total_count' => 0,
                        'periods' => []
                    ];
                }
                $subjectAverages[$sid]['total_sum'] += $value;
                $subjectAverages[$sid]['total_count']++;

                // init periodo
                if (!isset($subjectAverages[$sid]['periods'][$pDesc])) {
                    $subjectAverages[$sid]['periods'][$pDesc] = [
                        'sum' => 0,
                        'count' => 0,
                        'pos' => $pPos
                    ];
                }
                $subjectAverages[$sid]['periods'][$pDesc]['sum'] += $value;
                $subjectAverages[$sid]['periods'][$pDesc]['count']++;

                // global periodi
                if (!isset($globalPeriods[$pDesc])) {
                    $globalPeriods[$pDesc] = ['sum' => 0, 'count' => 0, 'pos' => $pPos];
                }
                $globalPeriods[$pDesc]['sum'] += $value;
                $globalPeriods[$pDesc]['count']++;
            }

            // Calcola medie per materia
            foreach ($subjectAverages as &$subj) {
                $subj['total_avg'] = $subj['total_count'] > 0 ? $subj['total_sum'] / $subj['total_count'] : 0;
                // Trova ultimo periodo (max pos)
                $lastPeriod = null;
                $maxPos = -1;
                foreach ($subj['periods'] as $pDesc => $pData) {
                    if ($pData['pos'] > $maxPos) {
                        $maxPos = $pData['pos'];
                        $lastPeriod = [
                            'desc' => $pDesc,
                            'avg' => $pData['count'] > 0 ? $pData['sum'] / $pData['count'] : 0
                        ];
                    }
                }
                $subj['last_period'] = $lastPeriod;
                // ordina periodi per pos
                uasort($subj['periods'], fn($a,$b) => ($a['pos'] ?? 0) <=> ($b['pos'] ?? 0));
                foreach ($subj['periods'] as &$p) {
                    $p['avg'] = $p['count'] > 0 ? $p['sum'] / $p['count'] : 0;
                    unset($p['sum'], $p['count'], $p['pos']);
                }
            }
            unset($subj);

            // Global periods
            foreach ($globalPeriods as &$p) {
                $p['avg'] = $p['count'] > 0 ? $p['sum'] / $p['count'] : 0;
                unset($p['sum'], $p['count'], $p['pos']);
            }
            uasort($globalPeriods, fn($a,$b) => ($a['pos'] ?? 0) <=> ($b['pos'] ?? 0));
        }

        // Prepara dati per la view
        if ($subjectId) {
            $subjectName = null;
            if (isset($subjectAverages[$subjectId])) {
                $subjectName = $subjectAverages[$subjectId]['name'];
            } elseif (isset($subjectsList[$subjectId])) {
                $subjectName = $subjectsList[$subjectId];
            } else {
                $subjectName = 'Materia';
            }
            $subject = [
                'id' => $subjectId,
                'description' => $subjectName
            ];
            $grades = $validGrades;
        }

        require_once BASE_PATH . '/app/views/voti/grades.php';
    }

    public function grades4Period():void {
        require_once BASE_PATH . '/app/views/voti/grades_forPeriod.php';
    }

    public function settings(): void {
        require_once BASE_PATH . '/app/views/settings.php';
    }
    public function subjects(): void {
        try {
            $rq = "subjects";
            $response = $this->requestToApi($rq);
        } catch (Exception $e) {
                        $response = ["error" => "API_ERROR"];
        }
        require_once BASE_PATH . '/app/views/subjects.php';
    }
    public function agenda(): void {
        $error = null;
        try {
            // Use overview API to get both lessons and agenda events
            $rq = "overview";

            // Determine date to show - support both GET and POST
            $dateParam = $_GET['date'] ?? $_POST['date'] ?? null;
            if ($dateParam) {
                $date_from = $date_to = date("Ymd", strtotime($dateParam));
            } else {
                // Default: only today
                $today = new DateTime();
                $date_from = $date_to = $today->format('Ymd');
            }

            $overview = $this->requestToApi($rq, [
                "date_from" => $date_from,
                "date_to"   => $date_to
            ]);

            // Extract lessons and agenda items
            $lessons = $overview['lessons'] ?? [];
            $agenda = $overview['agenda'] ?? [];

        } catch (Exception $e) {
            $lessons = [];
            $agenda = [];
            $error = "API_ERROR";
        }
        require_once BASE_PATH . '/app/views/agenda.php';
    }
    public function account(): void {
        require_once BASE_PATH . '/app/views/account.php';
    }

    /**
     * Makes an API call using the provided request key.
     *
     * @param string $rq The request key used to retrieve API configuration details.
     * @return array Returns the API response as a string on success or false if the response cannot be retrieved.
     * @throws Exception If the HTTP response code differs from 200, throwing an exception with error details.
     */
    private function requestToApi(string $rq, array $extraInput=[]): array {
        global $methods;
        if (!isset($methods[$rq])) {
            return ['error' => 'INVALID_API_REQUEST', 'message' => "API request '{$rq}' not found"];
        }
        $apiCtrl = new ApiController();
        $method = $methods[$rq];
        $url = $method['url'];
        $cvvArrKey = $method['cvvArrKey'];
        $requestMethod = $method['reqMethod'];

        $data = [
            'request' => $url,
            'extraInput' => $extraInput,
            'cvvArrKey' => $cvvArrKey,
            'isPost' => $requestMethod === "POST",
        ];
        return $apiCtrl->classeviva($data, false, true);
//        return [];
    }
}
