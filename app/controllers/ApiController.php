<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

use cvv\CvvIntegration;

class ApiController {
    public function classeviva(): void {
        $body = json_decode(file_get_contents('php://input'), true);
        //print_r($body['isPost']);
        $cvvApi = new CvvIntegration();
        $user = $cvvApi->loadUser();
        $resp = ["status" => "000", "message" => "API call not started (Commented row?)", "error" => "1"];

        $resp = $cvvApi->genericQuery($body['request'], $body['extraInput'], $body['isPost']);

        if (isset($resp['error'])) {
            http_response_code(401); // Unauthorized o altro errore
            echo json_encode($resp);
        } else {
            http_response_code(200);
            if ($body['cvvArrKey'] != "" && $body['cvvArrKey'] != null && isset($resp[$body['cvvArrKey']])) {
                echo json_encode($resp[$body['cvvArrKey']]);
            } else {
                echo json_encode($resp);
            }
        }
    }
}