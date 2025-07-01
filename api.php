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
    
    public function login($identity = null) {
        //Dev'esistere un utente da loggare
        if ($this -> usr == null) return false;

        //Verifico di non essere già loggato
        if (isset($_SESSION['classeviva_auth_token']) && (isset($_SESSION['classeviva_ident']) || isset($_SESSION['classeviva_username'])) ) {
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
        return $resp;
    }
    public function getGrades() {

        if ($this -> usr == null) return false;
        return $this -> usr -> getVoti();
    }

    public function createUser($username, $password): User {
        $usr = new User($username, $password);
        $this -> usr = $usr;

        // Recupera il token di autenticazione e l'ID studente dalla sessione PHP, se esistono.
        //TODO: verifica che i tocken salvati siano coerenti con lo usr
        /*if (isset($_SESSION['classeviva_auth_token']) && isset($_SESSION['classeviva_student_id'])) {
            $this->authToken = $_SESSION['classeviva_auth_token'];
            $this->studentId = $_SESSION['classeviva_student_id'];
        }*/
        return $usr;
    }
    public function loadUser(): User|bool {
        //se c'è un utente in sessione
        if (isset($_SESSION['classeviva_auth_token']) && (isset($_SESSION['classeviva_ident']) || isset($_SESSION['classeviva_username'])) ) {
            $this -> usr          = new User();
            $this -> usr -> token = $_SESSION['classeviva_auth_token'];
            $this -> usr -> ident = $_SESSION['classeviva_ident']     ;
            $this -> usr -> uid   = $_SESSION['classeviva_username']  ;
            $this -> usr -> is_logged_in = true;
            return $this -> usr;
        } else {
            return false;
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
        
        $cvApi = new ClassevivaIntegration();
        $user = $cvApi -> createUser($username, $password);
        $result = $cvApi -> login($ident); // Passa l'ident al metodo di login
        echo json_encode($result);
        //echo json_encode($result);
        //print_r($result);
    
        /*if ($result['status'] === "success") {
            http_response_code(200);
            echo json_encode(["message" => "Login riuscito.", "studentId" => $result['studentId']]);

        } elseif ($result['status'] === "multi_account") {
            http_response_code(200); // Ritorna 200 ma con le scelte
            echo json_encode($result); // Invia le scelte al frontend

        } else {
            http_response_code(600); // My generic error
            echo json_encode(["message" => $result['message']]);

        }*/
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
//            echo "YESS!";
            echo json_encode($grades);
        }
        break;
    
    /*case 'GET:topics':
        $cvApi = new ClassevivaIntegration();
        $topics = $cvApi->getTopics();
        
        if (isset($topics['error'])) {
            http_response_code(401); // Unauthorized o altro errore
            echo json_encode(["message" => $topics['error']]);
        } else {
            http_response_code(200);
            echo json_encode($topics);
        }
        break;*/
    
    default:
        http_response_code(405); // Not Found
        echo json_encode(["message" => "Endpoint non trovato o metodo non supportato."]);
        break;
}

?>