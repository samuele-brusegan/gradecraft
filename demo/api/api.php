<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

namespace api;

include_once 'User.php';
include_once 'collegamenti.php';
$c = new Collegamenti();
session_start();

class ClassevivaIntegration {
    private $usr;

    public function __construct() {
    }

    public function createUser($username, $password): User {
        $usr = new User($username, $password);
        $this->usr = $usr;
        return $usr;
    }

    public function loadUser(): User|bool {
        //se c'è un utente in sessione
        if (isset($_SESSION['classeviva_auth_token']) && (isset($_SESSION['classeviva_ident']) || isset($_SESSION['classeviva_username']))) {
            $uid = $_SESSION['classeviva_username'];
            $pwd = $_SESSION['classeviva_password'];

            //Se la sessione non è expired
            if (date(DATE_ATOM, time()) < $_SESSION['classeviva_session_expiration_date']) {

                $this->usr = new User($uid, $pwd);
                $this->usr->token = $_SESSION['classeviva_auth_token'];
                $this->usr->ident = $_SESSION['classeviva_ident'];
                $this->usr->uid = $_SESSION['classeviva_username'];
                $this->usr->pwd = $_SESSION['classeviva_password'];
                $this->usr->expDt = $_SESSION['classeviva_session_expiration_date'];
                $this->usr->reqDt = $_SESSION['classeviva_session_request_date'];
                $this->usr->is_logged_in = true;
                return $this->usr;
            } else {
                $this->usr = $this->createUser($uid, $pwd);
                $this->login(); // Passa l'ident al metodo di login
                return $this->usr;
            }
        } else {
            return false;
        }
    }

    public function buildUser($lr): User|bool {
        //se c'è un utente in sessione
        //print_r($lr);

        $ident = $lr['ident'];
        $firstName = $lr['firstName'];
        $lastName = $lr['lastName'];
        $token = $lr['token'];
        $tokenAP = $lr['tokenAP'];
        $expire = $lr['expire'];
        $release = $lr['release'];
        $pwd = $_SESSION['classeviva_password'] ?? null;

        $this->usr = new User($ident, $pwd);
        $this->usr->token = $token;
        $this->usr->ident = $ident;
        $this->usr->uid = $ident;
        $this->usr->pwd = $pwd;
        $this->usr->expDt = $expire;
        $this->usr->reqDt = $release;
        $this->usr->is_logged_in = true;
        return $this->usr;
    }

    public function login() {
        //Dev'esistere un utente da loggare
        if ($this->usr == null) return false;

        //Verifico che il token non sia scaduto
        $isExpired = true;
        if (isset($_SESSION['classeviva_session_expiration_date'])) {
            $dt1 = strtotime(date(DATE_ATOM, time()));
            $dt2 = strtotime($_SESSION['classeviva_session_expiration_date']);
            $isExpired = $dt1 > $dt2;
        }

        //Verifico di non essere già loggato
        if (isset($_SESSION['classeviva_auth_token']) && !$isExpired) {
            if ($_SESSION['classeviva_ident'] == $this->usr->uid || $_SESSION['classeviva_username'] == $this->usr->uid) {
                $this->usr->token = $_SESSION['classeviva_auth_token'];
                return array("status" => "602", "studentId" => $_SESSION['classeviva_ident'], "username" => $_SESSION['classeviva_username'], "expire" => $_SESSION['classeviva_session_expiration_date'], "request" => $_SESSION['classeviva_session_request_date']);
            }
        }

        $resp = $this->usr->login();
        //TODO: Verificare che il login sia andato a buon fine

        //Salvo in sessione le informazioni restituitemi
        $_SESSION['classeviva_auth_token'] = $this->usr->token;
        $_SESSION['classeviva_ident'] = $this->usr->ident;
        $_SESSION['classeviva_username'] = $this->usr->uid;
        $_SESSION['classeviva_password'] = $this->usr->pwd;
        //print_r($this -> usr);
        $_SESSION['classeviva_session_expiration_date'] = $this->usr->expDt;
        $_SESSION['classeviva_session_request_date'] = $this->usr->reqDt;
        return $resp;
    }

    public function getGrades() {
        //Dev'esistere un utente
        if ($this->usr == null) return false;
        //Restituisco
        return $this->usr->getVoti();
    }

    public function getSubjects() {
        //Dev'esistere un utente
        if ($this->usr == null) return false;
        //Restituisco
        return $this->usr->getSubjects();
    }

    public function getStatus() {
        //Dev'esistere un utente
        if ($this->usr == null) return false;
        //Restituisco
        return $this->usr->getStatus();
    }

    public function getTicket() {
        //Dev'esistere un utente
        if ($this->usr == null) return false;
        //Restituisco
        return $this->usr->getTicket();
    }

    public function genericQuery($request, $extraInput = null, $isPost = false): array {
        //Dev'esistere un utente
        global $c;
        if ($this->usr == null) return ["error" => 'NO_USER', "message" => "Prima di chiamare un API (diversa da login) devi loggarti.", "instr" => "Per loggarti, chiamare questo stesso file in POST con path = login, nel body passare username e password."];
        //Restituisco
//        echo $c->collegamenti[$request];
        if (isset($c->collegamenti[$request])) {
            return $this->usr->genericQuery($request, $extraInput, $isPost);
        } else {
            $apis = array_keys($c->collegamenti);
            return ["error" => 'WRONG_API_REQUEST', "message" => "API " . $request . " non trovata.", "apis" => $apis];
        }
    }
}

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

?>