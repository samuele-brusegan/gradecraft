<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

namespace cvv;
/*
include_once 'User.php';
include_once 'collegamenti.php';
$c = new Collegamenti();
session_start();

// --- Gestione delle richieste API ---
$request_path = $_GET['path'] ?? '';

if (empty($request_path)) {
    http_response_code(404);
    echo json_encode(["message" => "Endpoint API non specificato."]);
    exit();
}

switch ($_SERVER['REQUEST_METHOD'] . ':' . $request_path) {
    case 'POST:login':
        $input = json_decode(file_get_contents('php://input'), true);
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';
        $ident = $input['ident'] ?? null; // Aggiunto per supportare la selezione multi-account

        if (empty($username) || empty($password)) {
            http_response_code(601); // Non inseriti usr || pwd
            echo json_encode(["message" => "Username e password sono richiesti."]);
            exit();
        }

        $cvvApi = new ClassevivaIntegration();
        $user = $cvvApi->createUser($username, $password);
        $result = $cvvApi->login(); // Passa l'ident al metodo di login
        echo json_encode($result);
        break;
    case 'POST:fastLogin':
        $llr = json_decode(file_get_contents('php://input'), true);

        $cvvApi = new ClassevivaIntegration();
        $user = $cvvApi->buildUser($llr);
        break;

    case 'GET:grades':
        $cvvApi = new ClassevivaIntegration();
        $user = $cvvApi->loadUser();
        $grades = $cvvApi->getGrades();

        if (isset($grades['error'])) {
            http_response_code(401); // Unauthorized o altro errore
            echo json_encode(["message" => $grades['error']]);
        } else {
            http_response_code(200);
            echo json_encode($grades["grades"]);
        }
        break;
    case 'GET:subjects':
        $cvvApi = new ClassevivaIntegration();
        $user = $cvvApi->loadUser();
        $resp = $cvvApi->getSubjects();

        if (isset($resp['error'])) {
            http_response_code(401); // Unauthorized o altro errore
            echo json_encode(["message" => $resp['error']]);
        } else {
            http_response_code(200);
            echo json_encode($resp["subjects"]);
        }
        break;
    //status = {release:string, ident:string, expire:string, remains:number}
    case 'GET:status':
        $cvvApi = new ClassevivaIntegration();
        $user = $cvvApi->loadUser();
        $resp = $cvvApi->getStatus();

        if (isset($resp['error'])) {
            http_response_code(401); // Unauthorized o altro errore
            echo json_encode(["message" => $resp['error']]);
        } else {
            http_response_code(200);
            echo json_encode($resp['status']);

        }
        break;
    case 'GET:ticket':
        $cvvApi = new ClassevivaIntegration();
        $user = $cvvApi->loadUser();
        $resp = $cvvApi->getTicket();

        if (isset($resp['error'])) {
            http_response_code(401); // Unauthorized o altro errore
            echo json_encode(["message" => $resp['error']]);
        } else {
            http_response_code(200);
            echo json_encode($resp);

        }
        break;
    case 'POST:generic':
        $body = json_decode(file_get_contents('php://input'), true);
        //print_r($body['isPost']);
        $cvvApi = new ClassevivaIntegration();
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
        break;
    case 'POST:chYear':
        $body = json_decode(file_get_contents('php://input'), true);

        //print_r($body['isPost']);
        $cvvApi = new ClassevivaIntegration();

        $year = $body['extraInput']['year'];

        $resp = ["status" => "0", "message" => "Anno cambiato con successo", "year" => $year];

        $year4Cvv = $year - 2000;

        $c->setGeneric("year", $year4Cvv);

        http_response_code(200);
        if ($body['cvvArrKey'] != "" && $body['cvvArrKey'] != null && isset($resp[$body['cvvArrKey']])) {
            echo json_encode($resp[$body['cvvArrKey']]);
        } else {
            echo json_encode($resp);
        }

        break;
    case 'GET:rmSession':
        $cvvApi = new ClassevivaIntegration();
        //Salvo in sessione le informazioni restituitemi
        unset($_SESSION['classeviva_auth_token']);
        unset($_SESSION['classeviva_ident']);
        unset($_SESSION['classeviva_username']);
        unset($_SESSION['classeviva_password']);
        unset($_SESSION['classeviva_session_expiration_date']);
        unset($_SESSION['classeviva_session_request_date']);
        break;

    default:
        http_response_code(405); // Not Found
        echo json_encode(["message" => "Endpoint non trovato o metodo non supportato."]);
        break;
}
*/
?>