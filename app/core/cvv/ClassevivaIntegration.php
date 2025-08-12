<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

namespace cvv;

include_once 'User.php';
include_once 'collegamenti.php';
//$c = new Collegamenti();
$c = \CVV_URLS;

class CvvIntegration {
    private $usr;

    public function __construct() {}
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
                $this->usr->uid   = $_SESSION['classeviva_username'];
                $this->usr->pwd   = $_SESSION['classeviva_password'];
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
        $this->usr->firstName = $firstName;
        $this->usr->lastName = $lastName;
        $this->usr->token = $token;
        $this->usr->tokenAP = $tokenAP;
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
        $_SESSION['classeviva_first_name'] = $this->usr->firstName;
        $_SESSION['classeviva_last_name'] = $this->usr->lastName;
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

    /**
     * Handles a generic query to interact with a specific API endpoint.
     *
     * Ensures that a user is authenticated before proceeding. If the user is not logged in,
     * an error is returned. If the requested API is invalid or not found, an appropriate error
     * and a list of available APIs are returned. Otherwise, the query is delegated to the user's
     * genericQuery method.
     *
     * @param string $request The API request path being called.
     * @param mixed|null $extraInput Additional input data for the query, if applicable.
     * @param bool $isPost Indicates if the query should be executed as a POST request.
     * @return array The result of the query or an array containing error details.
     */
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