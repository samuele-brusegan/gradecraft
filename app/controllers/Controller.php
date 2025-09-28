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
        try {
            $rq = "subjects";
            $responseA = $this->requestToApi($rq);
            $response['subjects'] = $responseA;
        } catch (Exception $e) {
            echo $e->getMessage();
            $response = ["error" => "API_ERROR_SUBJECTS"];
        }
        try {
            $rq = "get_grades";
            $responseA = $this->requestToApi($rq);
            $response['grades'] = $responseA;
        } catch (Exception $e) {
            echo $e->getMessage();
            $response = ["error" => "API_ERROR_GRADES"];
        }

            // 2. Passa i dati alla View per la visualizzazione
        require_once BASE_PATH . '/app/views/dashboard.php';
    }
    public function grades(): void {

        /*try {
            $rq = "get_grades";
            $response = $this->requestToApi($rq);
            $grades = $response;
        } catch (Exception $e) {
            echo $e->getMessage();
            $grades = ["error" => "API_ERROR"];
        }*/

        //2.  catch  Fetch local db (w JS)
        
        $grades = [
            [
                "subjectId" =>  408761,
                "subjectCode" =>  null,
                "subjectDesc" =>  "EDUCAZIONE CIVICA",
                "evtId" =>  2274467,
                "evtCode" =>  "GRV0",
                "evtDate" =>  "2024-11-30",
                "decimalValue" =>  8.5,
                "displayValue" =>  "8½",
                "displaPos" =>  1,
                "notesForFamily" =>  "Verifica scritta di educazione Civica. tiene conto della presentazione fatta in laboratorio; del risultato della prova scritta del test sulla piattaforma",
                "color" =>  "green",
                "canceled" =>  false,
                "underlined" =>  false,
                "periodPos" =>  1,
                "periodDesc" =>  "primo trimestre",
                "periodLabel" =>  "primo trimestre",
                "componentPos" =>  1,
                "componentDesc" =>  "Scritto/Grafico",
                "weightFactor" =>  1,
                "skillId" =>  0,
                "gradeMasterId" =>  0,
                "skillDesc" =>  null,
                "skillCode" =>  null,
                "skillMasterId" =>  0,
                "skillValueDesc" =>  "",
                "skillValueShortDesc" =>  null,
                "skillValueNote" =>  "",
                "oldskillId" =>  0,
                "oldskillDesc" =>  "",
                "noAverage" =>  false,
                "teacherName" =>  "********* SERGIO"
            ]
        ];
        
        require_once BASE_PATH . '/app/views/grades.php';
    }
    public function settings(): void {
        require_once BASE_PATH . '/app/views/settings.php';
    }
    public function subjects(): void {
        try {
            $rq = "subjects";
            $response = $this->requestToApi($rq);
        } catch (Exception $e) {
            echo $e->getMessage();
            $response = ["error" => "API_ERROR"];
        }
        require_once BASE_PATH . '/app/views/subjects.php';
    }
    public function agenda(): void {
        try {
            $rq = "agenda_da_a";

            $date_from = "";
            $date_to   = "";
            if (isset($_POST['date_from']) && isset($_POST['date_to'])) {
                $date_from = ["date" => $_POST['date_from']];
                $date_to   = ["date" => $_POST['date_to']];
            } else {
                $date_from  = (array) new DateTime();
                $date_to    = (array) new DateTime("now +1 month");
            }

            $date_from = $date_from['date'];
            $date_to   = $date_to  ['date'];
            $date_from = date("Ymd", strtotime($date_from));
            $date_to  =  date("Ymd", strtotime($date_to));

            $date_from = "20250301";
            $date_to   = "20250401";


            $response = $this->requestToApi($rq, [
                "date_from" => $date_from,
                "date_to"   => $date_to
            ]);

        } catch (Exception $e) {
            echo $e->getMessage();
            $response = ["error" => "API_ERROR"];
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
    private function requestToApi(string $rq, array $extraInput=[""]): array {
        global $methods;
        $apiCtrl = new ApiController();
        $method = $methods[$rq];
        $url = $method['url'];
        $cvvArrKey = $method['cvvArrKey'];
        $requestMethod = $method['reqMethod'];

        $data = [
            'request' => $url,
            'extraInput' => $extraInput,
            'cvvArrKey' => $cvvArrKey,
            'isPost' => $requestMethod == "POST",
        ];
        return $apiCtrl->classeviva($data, false, true);
    }
}