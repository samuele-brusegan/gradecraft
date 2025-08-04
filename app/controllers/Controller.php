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

        global $methods;

        //1.   try   Fetch classeviva
        //T.ODO: Decidere come gestire le API
        //--- T.ODO: Modificare le API per restituire un array
        //--- T.ODO: $grades

        $rq = "get_grades";
        $method = $methods[$rq];
        $path = $method['path'];
        $url = $method['url'];
        $httpMethod = $method['method'];
        $extraInputRequests = $method['extraInput'];
        $cvvArrKey = $method['cvvArrKey'];
        $requestMethod = $method['reqMethod'];


        $ch = curl_init(URL_PATH . "/api");
        $body = [
            'request' => $url,
            'extraInput' => [""],
            'cvvArrKey' => $cvvArrKey,
            'isPost' => $requestMethod == "POST",
        ];
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        if ($httpCode != 200) {
            throw new Exception(json_encode(["error" => "HTTP_CODE_DIFFERS_FROM_200", "status" => $httpCode, "message" => $response, "body" => $response]));
        }
        echo $response;
        $grades = json_decode($response, true);

        //2.  catch  Fetch local db (w JS)


        require_once BASE_PATH . '/app/views/grades.php';
    }
}