<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

include_once BASE_PATH . '/app/core/cvv/apiMethods.php';

class Controller {
    public function index() {
        // 1. Chiede al Model di ottenere i dati
        // $prodotti = Prodotto::findAll();

        // 2. Passa i dati alla View per la visualizzazione
        require_once BASE_PATH . '/app/views/home.php';
    }

    public function grades() {

        try {
            $rq = "get_grades";
            $response = $this->requestToApi($rq);
            $grades = $response;
        } catch (Exception $e) {
            echo $e->getMessage();
            $grades = ["error" => "API_ERROR"];
        }

        //2.  catch  Fetch local db (w JS)

        require_once BASE_PATH . '/app/views/grades.php';
    }

    /**
     * Makes an API call using the provided request key.
     *
     * @param string $rq The request key used to retrieve API configuration details.
     * @return array Returns the API response as a string on success or false if the response cannot be retrieved.
     * @throws Exception If the HTTP response code differs from 200, throwing an exception with error details.
     */
    private function requestToApi(string $rq): array {
        global $methods;
        $apiCtrl = new ApiController();
        $method = $methods[$rq];
        $url = $method['url'];
        $cvvArrKey = $method['cvvArrKey'];
        $requestMethod = $method['reqMethod'];

        $data = [
            'request' => $url,
            'extraInput' => [""],
            'cvvArrKey' => $cvvArrKey,
            'isPost' => $requestMethod == "POST",
        ];
        $response = $apiCtrl->classeviva($data, false, true);
        return $response;
    }
}