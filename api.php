<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

include_once 'User.php';
include_once 'collegamenti.php';
$c = new Collegamenti();
session_start();

class ClassevivaIntegration {
    private $usr;

    public function __construct() {}

    public function createUser($username, $password): User {
        $usr = new User($username, $password);
        $this -> usr = $usr;
        return $usr;
    }
    public function loadUser(): User|bool {
        //se c'è un utente in sessione
        if (isset($_SESSION['classeviva_auth_token']) && (isset($_SESSION['classeviva_ident']) || isset($_SESSION['classeviva_username'])) ) {
            $uid = $_SESSION['classeviva_username'];
            $pwd = $_SESSION['classeviva_password'];

            //Se la sessione non è expired
            if ( date(DATE_ATOM, time()) < $_SESSION['classeviva_session_expiration_date'] ) {

                $this -> usr          = new User($uid, $pwd);
                $this -> usr -> token = $_SESSION['classeviva_auth_token'];
                $this -> usr -> ident = $_SESSION['classeviva_ident']     ;
                $this -> usr -> uid   = $_SESSION['classeviva_username']  ;
                $this -> usr -> pwd   = $_SESSION['classeviva_password']  ;
                $this -> usr -> expDt = $_SESSION['classeviva_session_expiration_date'];
                $this -> usr -> reqDt = $_SESSION['classeviva_session_request_date'];
                $this -> usr -> is_logged_in = true;
                return $this -> usr;
            } else {
                $this -> usr = $this -> createUser($uid, $pwd);
                $this -> login(); // Passa l'ident al metodo di login
                return $this -> usr;
            }
        } else {
            return false;
        }
    }
    public function buildUser($lr): User|bool {
        //se c'è un utente in sessione
        //print_r($lr);

        $ident      = $lr['ident'];
        $firstName  = $lr['firstName'];
        $lastName   = $lr['lastName'];
        $token      = $lr['token'];
        $tokenAP    = $lr['tokenAP'];
        $expire     = $lr['expire'];
        $release    = $lr['release'];
        $pwd        = $_SESSION['classeviva_password'] ?? null;

        $this -> usr          = new User($ident, $pwd);
        $this -> usr -> token = $token;
        $this -> usr -> ident = $ident;
        $this -> usr -> uid   = $ident;
        $this -> usr -> pwd   = $pwd;
        $this -> usr -> expDt = $expire;
        $this -> usr -> reqDt = $release;
        $this -> usr -> is_logged_in = true;
        return $this -> usr;
    }

    public function login() {
        //Dev'esistere un utente da loggare
        if ($this -> usr == null) return false;

        //Verifico di non essere già loggato
        if (isset($_SESSION['classeviva_auth_token']) && date(DATE_ATOM, time()) < $_SESSION['classeviva_session_expiration_date'] && false ) {
            if($_SESSION['classeviva_ident'] == $this -> usr -> uid || $_SESSION['classeviva_username'] == $this -> usr -> uid) {
                $this -> usr -> token = $_SESSION['classeviva_auth_token'];
                return array("status" => "602", "studentId" => $_SESSION['classeviva_ident'], "username" => $_SESSION['classeviva_username']);
            }
        }

        $resp = $this -> usr -> login();
        //TODO: Verificare che il login sia andato a buon fine

        //Salvo in sessione le informazioni restituitemi
        $_SESSION['classeviva_auth_token']  = $this -> usr -> token;
        $_SESSION['classeviva_ident']       = $this -> usr -> ident;
        $_SESSION['classeviva_username']    = $this -> usr -> uid;
        $_SESSION['classeviva_password']    = $this -> usr -> pwd;

        $_SESSION['classeviva_session_expiration_date'] = $this -> usr -> expDt;
        $_SESSION['classeviva_session_request_date']    = $this -> usr -> reqDt;
        return $resp;
    }
    public function getGrades() {
        //Dev'esistere un utente
        if ($this -> usr == null) return false;
        //Restituisco
        return $this -> usr -> getVoti();
    }
    public function getSubjects() {
        //Dev'esistere un utente
        if ($this -> usr == null) return false;
        //Restituisco
        return $this -> usr -> getSubjects();
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

        $cvApi = new ClassevivaIntegration();
        $user = $cvApi -> createUser($username, $password);
        $result = $cvApi -> login($ident); // Passa l'ident al metodo di login
        echo json_encode($result);
        break;
    case 'POST:fastLogin':
        $llr = json_decode(file_get_contents('php://input'), true);

        $cvApi = new ClassevivaIntegration();
        $user = $cvApi -> buildUser($llr);
        break;
    
    case 'GET:grades':
        $cvApi = new ClassevivaIntegration();
        $user   = $cvApi -> loadUser();
        $grades = $cvApi -> getGrades();
        
        if (isset($grades['error'])) {
            http_response_code(401); // Unauthorized o altro errore
            echo json_encode(["message" => $grades['error']]);
        } else {
            http_response_code(200);
            echo json_encode($grades);
        }
        break;

    case 'GET:subjects':
        $cvApi = new ClassevivaIntegration();
        $user   = $cvApi -> loadUser();
        $resp = $cvApi -> getSubjects();

        if (isset($resp['error'])) {
            http_response_code(401); // Unauthorized o altro errore
            echo json_encode(["message" => $resp['error']]);
        } else {
            http_response_code(200);
            echo json_encode($resp);
        }
        break;
    
    default:
        http_response_code(405); // Not Found
        echo json_encode(["message" => "Endpoint non trovato o metodo non supportato."]);
        break;
}

?>