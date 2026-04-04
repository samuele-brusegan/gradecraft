<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */


class ApiController {
    public function classeviva($data="", $isLogin=false, $return=false): array|null {
        $body = ($data !== "") ? $data : json_decode(file_get_contents('php://input'), true);

        $cvvApi = $GLOBALS['CVV_API'];

        if ($isLogin) {
            $resp = $cvvApi->login();
        } else {
            $resp = $cvvApi->genericQuery($body['request'], $body['extraInput'], $body['isPost']);
        }

        $isError = isset($resp['error']);
        http_response_code($isError ? 401 : 200);

        $payload = $resp;
        if (!$isError && !empty($body['cvvArrKey']) && isset($resp[$body['cvvArrKey']])) {
            $payload = $resp[$body['cvvArrKey']];
        }

        if ($return) {
            return $payload;
        }

        echo json_encode($payload);
        return null;
    }

    public function login(): void {
        // Only handle POST requests; redirect GET to home
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        // Handle logout action
        if (($_POST['action'] ?? null) === 'logout') {
            $username = $_SESSION['classeviva_username'] ?? '';
            if ($username) {
                CredentialStore::delete($username);
            }
            session_unset();
            session_destroy();
            header('Location: /');
            exit;
        }

        $usr = $_POST['usr'] ?? '';
        $pwd = $_POST['pwd'] ?? '';

        // Validate credentials
        if (empty($usr) || empty($pwd)) {
            $_SESSION['login_error'] = "Username e password sono richiesti.";
            header('Location: /');
            exit;
        }

        // Attempt login via Classeviva API
        $cvvApi = $GLOBALS['CVV_API'];
        $cvvApi->createUser($usr, $pwd);
        $result = $cvvApi->login();
        error_log("ApiController::login() - CVV login result: " . substr(print_r($result, true), 0, 1000));
        error_log("ApiController::login() - session ident=" . ($_SESSION['classeviva_ident'] ?? 'NOT SET'));
        error_log("ApiController::login() - last_login_response: " . substr(print_r($cvvApi->last_login_response ?? 'null', true), 0, 1000));

        // Determine success: if the integration set the ident in session, login succeeded
        if (isset($_SESSION['classeviva_ident'])) {
            // Save credentials to persistent cookies on successful login
            $this->addLoginInCookies();
            header('Location: /');
            exit;
        } else {
            // Login failed (e.g., invalid credentials)
            $_SESSION['login_error'] = "Login fallito: credenziali non valide o errore di connessione.";
            header('Location: /');
            exit;
        }

        // Redirect to home after successful login
        header('Location: /');
        exit;
    }

    private function addLoginInCookies(): void {
        $current_usr = $_SESSION['classeviva_username'];
        $current_pwd = $_SESSION['classeviva_password'];
        $current_tkn = $_SESSION['classeviva_auth_token'];
        $current_exp = $_SESSION['classeviva_session_expiration_date'];

        // Salva credenziali cifrate con wrapped key pattern
        CredentialStore::save($current_usr, $current_pwd, $current_tkn, $current_exp);
    }
}
