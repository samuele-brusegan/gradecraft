<?php
// Backend PHP (api.php)
// Questo codice è una base "irrobustita" per interagire con Classeviva.
// Le sezioni con commenti dettagliati indicano dove dovrai inserire
// la logica specifica di interazione con l'API non pubblica di Classeviva
// tramite richieste HTTP (cURL).

include_once 'User.php';
include_once 'collegamenti.php';
$c = new Collegamenti();
session_start();
// Abilita il reporting degli errori per il debug (da disabilitare in produzione)
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// *** INIZIO TEST RAPIDO DI DIAGNOSI: LASCIARE PER ORA, POI RIMUOVERE ***
// Se stai riscontrando "Endpoint non trovato o metodo non supportato.", aggiungi
// queste righe per capire cosa il backend PHP sta effettivamente ricevendo.
// Controlla l'output nella console del browser (Network tab -> Response)
// o nei log degli errori del server.
// error_log("GET parameters: " . print_r($_GET, true));
// error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
// Se vuoi vedere l'output direttamente nella risposta API (temporaneamente):
// var_dump($_GET);
// var_dump($_SERVER['REQUEST_METHOD']);
// *** FINE TEST RAPIDO DI DIAGNOSI ***

// Inizia la sessione PHP per memorizzare dati tra le richieste (es. cookie di Classeviva)
session_start();

// Configurazione CORS
// In produzione, sostituisci '*' con il dominio esatto del tuo frontend (es. 'https://gradecraft.tuo-dominio.com')
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

const CLIENT_USER_AGENT = 'CVVS/std/4.1.7 Android/10';
const CLIENT_DEV_APIKEY = 'Tg1NWEwNGIgIC0K';
const CLIENT_CONTENT_TP = 'application/json';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}*/

class ClassevivaIntegration {
    private $usr;

    public function __construct() {}
    
    public function login($identity = null) {
        if (isset($_SESSION['classeviva_auth_token']) && (isset($_SESSION['classeviva_ident']) || isset($_SESSION['classeviva_username'])) ) {
            if($_SESSION['classeviva_ident'] == $this -> usr -> uid || $_SESSION['classeviva_username'] == $this -> usr -> uid) {
                $this -> usr -> token = $_SESSION['classeviva_auth_token'];
                return array("status" => "602", "studentId" => $_SESSION['classeviva_ident'], "username" => $_SESSION['classeviva_username']);
            }
        }

        if ($this -> usr == null) return false;
        $resp = $this -> usr -> login();
        $_SESSION['classeviva_auth_token'] = $this -> usr -> token;
        $_SESSION['classeviva_ident'] = $this -> usr -> ident;
        $_SESSION['classeviva_username'] = $this -> usr -> uid;
        return $resp;
        /*global $c;
        $loginUrl = $c -> login;
        $username = $this->usr->username;
        $password = $this->usr->password;

        $headerRequired = array(
            'Content-Type: application/json',
            'User-Agent: '      . CLIENT_USER_AGENT,
            'X-Dev-Apikey: '    . CLIENT_DEV_APIKEY,
            'X-Content-Type: '  . CLIENT_CONTENT_TP
        );

//        $data = array(
//            'ident' => null,
//            'pass'  => $password,
//            'uid'   => $username
//        );
        $data = '{{"ident": null, "pass": "'.$password.'", "uid": "'.$username.'"}}';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $loginUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerRequired);
        $response = curl_exec($ch);
        curl_close($ch);

        $decoded = json_decode($response, true);
        $status = $decoded['statusCode'];

        if ($status === 200) {
            $this -> usr -> setRawData($decoded);
            $this -> usr -> inizio = $decoded["release"];
            $this -> usr -> fine = $decoded["expire"];
            $this -> usr -> _token = $decoded["token"];
            return true;
        } elseif ($status === 422) {
            echo "Errore di autenticazione";
            return false;
        } else {
            echo json_encode($decoded);
            return false;
        }*/
    }
    public function getGrades() {
        //print_r($this -> usr);
        if ($this -> usr == null) return false;
        return $this -> usr -> getVoti();
        /*global $c;
        $loginUrl = $c -> login;
        $username = $this->usr->username;
        $password = $this->usr->password;

        $headerRequired = array(
            'Content-Type: application/json',
            'User-Agent: '      . CLIENT_USER_AGENT,
            'X-Dev-Apikey: '    . CLIENT_DEV_APIKEY,
            'X-Content-Type: '  . CLIENT_CONTENT_TP
        );

//        $data = array(
//            'ident' => null,
//            'pass'  => $password,
//            'uid'   => $username
//        );
        $data = '{{"ident": null, "pass": "'.$password.'", "uid": "'.$username.'"}}';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $loginUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerRequired);
        $response = curl_exec($ch);
        curl_close($ch);

        $decoded = json_decode($response, true);
        $status = $decoded['statusCode'];

        if ($status === 200) {
            $this -> usr -> setRawData($decoded);
            $this -> usr -> inizio = $decoded["release"];
            $this -> usr -> fine = $decoded["expire"];
            $this -> usr -> _token = $decoded["token"];
            return true;
        } elseif ($status === 422) {
            echo "Errore di autenticazione";
            return false;
        } else {
            echo json_encode($decoded);
            return false;
        }*/
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