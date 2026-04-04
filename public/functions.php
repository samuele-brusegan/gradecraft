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
 * Sends an HTTP POST request to the specified URL and processes the response.
 *
 * @return mixed Returns the decoded JSON response as an array if the HTTP status code is 200.
 * Otherwise, returns an associative array containing error details, status code, response message, headers, and body.
 */
function loginRequest(): mixed {
    $ch = curl_init(URL_PATH . "/login");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'usr' => $_SESSION['classeviva_username'],
        'pwd' => $_SESSION['classeviva_password'],
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode == 200) {
        return json_decode($response, true);
    }
    error_log("loginRequest() failed: HTTP $httpCode, body=" . substr($response ?? '', 0, 500));
    return ["error" => "HTTP_CODE_DIFFERS_FROM_200", "status" => $httpCode, "message" => $response, "body" => $response, "stackTrace" => debug_backtrace()];
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
    <div class="z-5 p-4 m-3 col-md-5 col-10" style="background: var(--card-color); position: absolute; top: 2em; right: 10px; border-radius: 1rem; box-shadow: 0 10px 20px #0004" id="cvv_sync_form">
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
                <button type="submit" class="btn btn-primary col-md-2 col-6">Submit</button>
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
