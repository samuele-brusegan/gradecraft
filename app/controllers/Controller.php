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

    // /grades/subject/:id  — dettaglio materia
    public function gradeSubject(string $subjectId): void {
        try {
            $rq = "get_grades";
            $allGrades = $this->requestToApi($rq);
        } catch (Exception $e) {
            $allGrades = ["error" => "API_ERROR"];
        }

        $subjectsList = [];

        // Recupera lista materie (per nomi)
        try {
            $subjectsRaw = $this->requestToApi('subjects');
            foreach ($subjectsRaw as $s) {
                $subjectsList[$s['id']] = $s['description'];
            }
        } catch (Exception $e) {
            // ignore
        }

        // Filtra voti validi e per questa materia
        $validGrades = [];
        if (!isset($allGrades['error'])) {
            $validGrades = array_values(array_filter($allGrades, function($g) use ($subjectId) {
                return isset($g['decimalValue']) && is_numeric($g['decimalValue']) && $g['subjectId'] == $subjectId;
            }));
        }

        // Prepara subject
        $subjectName = $subjectsList[$subjectId] ?? 'Materia';
        $subject = [
            'id' => $subjectId,
            'description' => $subjectName
        ];
        $grades = $validGrades;
        $gradesData = isset($allGrades['error']) ? ['error' => $allGrades['error']] : null;
        if ($gradesData) $grades = $gradesData;

        require_once BASE_PATH . '/app/views/voti/grades_subject.php';
    }

    public function absences(): void {
        $error = null;
        $apiError = null;
        $events = [];
        try {
            $raw = $this->requestToApi('assenze');
            if (isset($raw['error'])) {
                $error = $raw['error'];
                $apiError = ($raw['error'] === 'HTTP_CODE_DIFFERS_FROM_200') ? $raw : null;
            } else {
                $events = is_array($raw) ? $raw : [];
                // Categorize each event
                foreach ($events as &$ev) {
                    $code = $ev['evtCode'] ?? '';
                    $ev['type'] = match ($code) {
                        'ABA0' => 'ASSENZA',
                        'ABR0' => 'RITARDO',
                        'ABR1' => 'RITARDO_BREVE',
                        'ABU0' => 'USCITA',
                        default => 'ALTRO',
                    };
                    // Sum hours for this event
                    $ev['hours'] = isset($ev['hoursAbsence']) && is_array($ev['hoursAbsence']) ? count($ev['hoursAbsence']) : 0;
                }
                unset($ev);
                // Sort by date descending
                usort($events, fn($a, $b) => ($b['evtDate'] ?? '') <=> ($a['evtDate'] ?? ''));
            }
        } catch (Exception $e) {
            $error = 'API_ERROR';
        }
        require_once BASE_PATH . '/app/views/absences.php';
    }

    public function notes(): void {
        $error = null;
        $apiError = null;
        $noteTypes = [
            'NTTE' => 'Annotazione Insegnante',
            'NTCL' => 'Annotazione Generale',
            'NTWN' => 'Richiamo',
            'NTST' => 'Note Disciplinare',
        ];
        $allNotes = [];
        try {
            $raw = $this->requestToApi('note');
            if (isset($raw['error'])) {
                $error = $raw['error'];
                $apiError = ($raw['error'] === 'HTTP_CODE_DIFFERS_FROM_200') ? $raw : null;
            } else {
                foreach ($noteTypes as $typeKey => $typeLabel) {
                    if (!empty($raw[$typeKey]) && is_array($raw[$typeKey])) {
                        foreach ($raw[$typeKey] as $note) {
                            $note['typeKey'] = $typeKey;
                            $note['typeLabel'] = htmlspecialchars($typeLabel);
                            $allNotes[] = $note;
                        }
                    }
                }
                usort($allNotes, fn($a, $b) => ($b['evtDate'] ?? '') <=> ($a['evtDate'] ?? ''));
            }
        } catch (Exception $e) {
            $error = 'API_ERROR';
        }
        require_once BASE_PATH . '/app/views/notes.php';
    }

    public function documents(): void {
        $error = null;
        $apiError = null;
        $documents = [];
        $schoolReports = [];
        try {
            $raw = $this->requestToApi('documenti');
            if (isset($raw['error'])) {
                $error = $raw['error'];
                $apiError = ($raw['error'] === 'HTTP_CODE_DIFFERS_FROM_200') ? $raw : null;
            } else {
                $documents = $raw['documents'] ?? [];
                $schoolReports = $raw['schoolReports'] ?? [];
            }
        } catch (Exception $e) {
            $error = 'API_ERROR';
        }
        require_once BASE_PATH . '/app/views/documents.php';
    }

    public function noticeboard(): void {
        $error = null;
        $apiError = null;
        $items = [];
        try {
            $raw = $this->requestToApi('bacheca');
            if (isset($raw['error'])) {
                $error = $raw['error'];
                $apiError = ($raw['error'] === 'HTTP_CODE_DIFFERS_FROM_200') ? $raw : null;
            } else {
                $items = $raw ?? [];
                usort($items, function($a, $b) {
                    $da = $a['pubDT'] ?? $a['dinsert_allegato'] ?? '1970-01-01';
                    $db = $b['pubDT'] ?? $b['dinsert_allegato'] ?? '1970-01-01';
                    return $db <=> $da;
                });
            }
        } catch (Exception $e) {
            $error = 'API_ERROR';
        }
        require_once BASE_PATH . '/app/views/noticeboard.php';
    }

    public function noticeboardRead(): void {
        $error = null;
        $apiError = null;
        $item = null;
        $evtCode = $_GET['evtCode'] ?? '';
        $pubId   = $_GET['pubId'] ?? '';
        try {
            // Chiama l'API lista per recuperare i metadati della card
            $listResp = $this->requestToApi('bacheca');
            $cardMeta = null;
            if (!isset($listResp['error']) && is_array($listResp)) {
                foreach ($listResp as $i) {
                    if (($i['evtCode'] ?? '') === $evtCode && ($i['pubId'] ?? '') == $pubId) {
                        $cardMeta = $i;
                        break;
                    }
                }
            }

            // Chiama l'API dettaglio per leggere il contenuto
            $raw = $this->requestToApi('bachecaLeggi', [
                'eventCode' => $evtCode,
                'pubId'     => $pubId,
            ]);
            if (isset($raw['error'])) {
                $error = $raw['error'];
                $apiError = ($raw['error'] === 'HTTP_CODE_DIFFERS_FROM_200') ? $raw : null;
            } else {
                // Unisci: i metadati della card hanno priorità per title/attachments,
                // il dettaglio per il testo
                if ($cardMeta) {
                    // Unisci attachments dalla card (spesso il dettaglio non le ha)
                    if (isset($cardMeta['attachments']) && is_array($cardMeta['attachments'])) {
                        $raw['attachments'] = $cardMeta['attachments'];
                    }
                    // Metadati supplementari dalla card
                    foreach (['pubDT', 'cntCategory', 'title', 'cntTitle', 'readStatus'] as $key) {
                        if (!isset($raw[$key]) && isset($cardMeta[$key])) {
                            $raw[$key] = $cardMeta[$key];
                        }
                    }
                }
                // Aggiungi evtCode e pubId al dettaglio per il JS
                $raw['evtCode'] = $evtCode;
                $raw['pubId']   = $pubId;
                $item = $raw;
            }
        } catch (Exception $e) {
            $error = 'API_ERROR';
        }
        require_once BASE_PATH . '/app/views/noticeboard_read.php';
    }

    public function noticeboardAttach(): void {
        $evtCode   = $_GET['evtCode'] ?? '';
        $pubId     = $_GET['pubId'] ?? '';
        $attachNum = $_GET['attachNum'] ?? '';
        if (!$evtCode || !$pubId || !$attachNum) {
            http_response_code(400);
            echo 'Parametri mancanti';
            return;
        }
        try {
            $raw = $this->requestToApi('bachecaAllegato', [
                'eventCode' => $evtCode,
                'pubId'     => $pubId,
                'attachNum' => $attachNum,
            ]);
            if (is_array($raw) && isset($raw['pdfData'])) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="allegato_' . $attachNum . '.pdf"');
                echo $raw['pdfData'];
            } else {
                http_response_code(500);
                echo 'Download fallito: ' . json_encode($raw);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo 'Errore: ' . $e->getMessage();
        }
    }

    public function calendar(): void {
        $error = null;
        $apiError = null;
        $calendar = [];
        try {
            $raw = $this->requestToApi('calendario');
            if (isset($raw['error'])) {
                $error = $raw['error'];
                $apiError = ($raw['error'] === 'HTTP_CODE_DIFFERS_FROM_200') ? $raw : null;
            } else {
                $calendar = $raw ?? [];
            }
        } catch (Exception $e) {
            $error = 'API_ERROR';
        }
        require_once BASE_PATH . '/app/views/calendar.php';
    }

    public function settings(): void {
        // Recupera lista materie per il pannello argomenti
        $subjectsList = [];
        try {
            $subjectsRaw = $this->requestToApi('subjects');
            foreach ($subjectsRaw as $s) {
                if (is_array($s) && isset($s['id'], $s['description'])) {
                    $subjectsList[$s['id']] = $s['description'];
                }
            }
        } catch (Exception $e) {}

        require_once BASE_PATH . '/app/views/settings.php';
    }

    public function settingsJson(): void {
        // Toggle JSON debug mode
        if (!isset($_SESSION)) session_start();
        $_SESSION['show_json_debug'] = !($_SESSION['show_json_debug'] ?? false);
        header('Location: /settings');
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
    public function agendaCalendar(): void {
        $error = null;
        $allEvents = [];

        try {
            // Recupero eventi degli ultimi 2 mesi e prossimi 2 mesi
            $now = new DateTime();
            $from = (clone $now)->modify('-1 month')->format('Ymd');
            $to = (clone $now)->modify('+2 months')->format('Ymd');

            $overview = $this->requestToApi('overview', [
                'date_from' => $from,
                'date_to'   => $to,
            ]);

            $lessons = $overview['lessons'] ?? [];
            $agenda = $overview['agenda'] ?? [];

            // Unisco tutti gli eventi e li raggruppo per data
            $byDate = [];
            foreach ($lessons as $l) {
                $d = $l['evtDate'] ?? '';
                if ($d) {
                    if (!isset($byDate[$d])) $byDate[$d] = ['lessons' => [], 'agenda' => []];
                    $byDate[$d]['lessons'][] = $l;
                }
            }
            foreach ($agenda as $a) {
                // Gli eventi agenda possono avere date diverse (evtDatetimeBegin)
                $d = '';
                if (!empty($a['evtDate'])) {
                    $d = $a['evtDate'];
                } elseif (!empty($a['evtDatetimeBegin'])) {
                    try { $d = (new DateTime($a['evtDatetimeBegin']))->format('Y-m-d'); } catch (Exception $e) {}
                }
                if ($d) {
                    if (!isset($byDate[$d])) $byDate[$d] = ['lessons' => [], 'agenda' => []];
                    $byDate[$d]['agenda'][] = $a;
                }
            }

            $allEvents = $byDate;

        } catch (Exception $e) {
            $error = 'API_ERROR';
        }

        require_once BASE_PATH . '/app/views/agenda_calendar.php';
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
     * Automatically retries once with token refresh if 401 is returned.
     */
    private function requestToApi(string $rq, array $extraInput=[], bool $retry = false): array {
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
        $response = $apiCtrl->classeviva($data, false, true);

        // Se errore auth e non ho ancora ritentato, faccio re-login e riprovo
        if (isset($response['error']) && in_array($response['error'], ['NOT_LOGGED_IN', 'HTTP_CODE_DIFFERS_FROM_200'], true) && !$retry) {
            // Se il re-login ha distrutto la sessione (WRONG_CREDENTIALS), non retryare
            if (!isset($_SESSION['classeviva_ident']) && !isset($_SESSION['classeviva_auth_token'])) {
                return $response;
            }
            $result = loginRequest();
            // Se il re-login è fallito, restituisci l'errore originale
            if (is_array($result) && isset($result['error'])) {
                return $result;
            }
            // Ricarica l'utente CVV con il nuovo token
            if (isset($_SESSION['classeviva_ident'])) {
                $cvvApi = $GLOBALS['CVV_API'];
                $cvvApi->usr->token = $_SESSION['classeviva_auth_token'];
                $cvvApi->usr->ident = $_SESSION['classeviva_ident'];
                $cvvApi->usr->is_logged_in = true;
                $cvvApi->usr->expDt = $_SESSION['classeviva_session_expiration_date'];
            }
            return $this->requestToApi($rq, $extraInput, true);
        }

        return $response;
    }
}
