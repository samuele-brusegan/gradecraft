<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

function checkSessionExpiration(): void {
    if (isset($_SESSION['classeviva_session_expiration_date'])) {
        $requestDate = $_SESSION['classeviva_session_request_date'] ?? $_SESSION['classeviva_session_expiration_date'];
        $expirationDate = $_SESSION['classeviva_session_expiration_date'];

        date_default_timezone_set('Europe/Rome');
        $currentDate = date(DATE_ATOM, time());
        $reLoginOffest = 10;

        if ($expirationDate < $currentDate && $requestDate < date(DATE_ATOM, time() - $reLoginOffest)) {
            loginRequest();
        }
    } elseif (!isset($_SESSION['classeviva_auth_token'])) {
        // Sessione non avviata: tentativo di autoload da CredentialStore
        $stored = CredentialStore::loadStored();
        if ($stored) {
            $_SESSION['classeviva_username'] = $stored['username'];
            $_SESSION['classeviva_password'] = $stored['password'];
            $_SESSION['classeviva_session_expiration_date'] = $stored['exp'] ?? '';
            $_SESSION['classeviva_session_request_date'] = $stored['exp'] ?? '';
            $_SESSION['classeviva_auth_token'] = $stored['token'] ?? '';
            loginRequest();
        }
    }
}

/**
 * Sends a login request directly to Classeviva using the in-process API.
 * Always forces a fresh login to Classeviva (bypasses expiration checks).
 * Preserves existing session state if login fails.
 *
 * @return mixed Returns the decoded JSON response on success, or error array on failure.
 */
function loginRequest(): mixed {
    if (!isset($_SESSION['classeviva_username'], $_SESSION['classeviva_password'])) {
        return ["error" => "MISSING_CREDENTIALS", "message" => "Username o password non presenti in sessione"];
    }

    $cvvApi = $GLOBALS['CVV_API'] ?? null;
    if (!$cvvApi) {
        $cvvApi = new \cvv\CvvIntegration();
        $GLOBALS['CVV_API'] = $cvvApi;
    }

    // Crea utente se non esiste
    if (!isset($cvvApi->usr)) {
        $cvvApi->createUser($_SESSION['classeviva_username'], $_SESSION['classeviva_password']);
    }

    // Salva stato corrente per ripristinare in caso di fallimento
    $prevToken = $cvvApi->usr->token ?? null;
    $prevIdent = $cvvApi->usr->ident ?? null;
    $prevLoggedIn = $cvvApi->usr->is_logged_in ?? false;

    $cvvApi->usr->uid = $_SESSION['classeviva_username'];
    $cvvApi->usr->pwd = $_SESSION['classeviva_password'];
    $result = $cvvApi->usr->login();

    if ($cvvApi->usr->is_logged_in) {
        $_SESSION['classeviva_auth_token'] = $cvvApi->usr->token;
        $_SESSION['classeviva_ident'] = $cvvApi->usr->ident;
        $_SESSION['classeviva_session_expiration_date'] = $cvvApi->usr->expDt;
        $_SESSION['classeviva_session_request_date'] = $cvvApi->usr->reqDt;
    } else {
        // Se credenziali sbagliate, rimuovi CredentialStore e distruggi sessione
        if (isset($cvvApi->usr->last_login_response['decoded']) &&
            isset($cvvApi->usr->last_login_response['decoded']['info']) &&
            $cvvApi->usr->last_login_response['decoded']['info'] === 'WrongCredentials:r') {
            error_log("loginRequest() WrongCredentials, deleting CredentialStore data");
            CredentialStore::delete($_SESSION['classeviva_username']);
            session_unset();
            session_destroy();
            header('Location: /');
            exit;
        }

        // Ripristina stato precedente se il login è fallito
        $cvvApi->usr->token = $prevToken;
        $cvvApi->usr->ident = $prevIdent;
        $cvvApi->usr->is_logged_in = $prevLoggedIn;
        $_SESSION['classeviva_auth_token'] = $prevToken;
        $_SESSION['classeviva_ident'] = $prevIdent;
        error_log("loginRequest() failed, restored previous session state");
    }

    if (is_bool($result) && !$result) {
        error_log("loginRequest() failed: Classeviva login returned false");
        return ["error" => "HTTP_CODE_DIFFERS_FROM_200", "status" => 0, "message" => "Login fallito - nessuna risposta", "body" => "", "curl_error" => "Login diretto fallito", "curl_errno" => 0];
    }

    if (is_array($result) && isset($result['error'])) {
        error_log("loginRequest() failed: " . json_encode($result));
        return ["error" => $result['error'], "status" => 0, "message" => $result['message'] ?? '', "body" => json_encode($result), "curl_error" => '', "curl_errno" => 0];
    }

    return $result;
}

/**
 * Displays a sync/login form when the user is not authenticated with Classeviva.
 * This function outputs HTML for a modal overlay with login credentials input.
 */
function cvv_sync(): void {
    // Display login error if present in session, then clear it
    if (isset($_SESSION['login_error'])): ?>
        <div class="alert alert-danger m-3" role="alert" style="position: absolute; top: 2em; right: 10px; z-index: 1001; max-width: 300px;">
            <?= htmlspecialchars($_SESSION['login_error']) ?>
        </div>
        <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>
    <div class="cvv-sync-form z-5 p-4 m-3 col-md-5 col-10" style="" id="cvv_sync_form">
        <button class="btn btn-danger" style="position: absolute; right: 10px; top: 10px; aspect-ratio: 1; border-radius: 50%" id="close_cvv_sync_form">X</button>
        <h3>Vuoi effettuare il Sync con classeviva?</h3>
        <h5>Puoi sempre farlo in un secondo momento</h5>

        <form action="/login" method="post" data-bs-theme="<?=THEME?>">
            <div class="mb-3">
                <label for="inputUsername" class="form-label">Email address</label>
                <input type="text" autocomplete="email" class="form-control" id="inputUsername" aria-describedby="emailHelp" name="usr">
                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
            </div>
            <div class="mb-3">
                <label for="inputPassword" class="form-label">Password</label>
                <input autocomplete="current-password" type="password" class="form-control" id="inputPassword" name="pwd">
            </div>
            <div class="col-12 d-flex justify-content-center align-items-center">
                <button type="submit" class="btn btn-primary col-6">Submit</button>
            </div>
        </form>
        <script>
            document.getElementById('close_cvv_sync_form').addEventListener('click', () => {
                document.getElementById('cvv_sync_form').classList.add('hidden');
            });
        </script>
    </div>
    <?php
}
