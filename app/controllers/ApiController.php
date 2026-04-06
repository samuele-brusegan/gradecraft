<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

include_once BASE_PATH . '/app/core/cvv/apiMethods.php';


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

    /**
     * /debug/api — lista di tutti gli endpoint disponibili
     */
    public function classevivaDebugIndex(): void {
        $groups = [
            'Voti & Materie' => ['get_grades', 'subjects', 'periodi'],
            'Lezioni'        => ['lezioniToday', 'lezioniGiorno', 'lezioniDaA', 'lezioniDaAMateria'],
            'Agenda'         => ['agenda_da_a', 'agenda_cod_da_a'],
            'Calendario'     => ['calendario', 'calendario_da_a'],
            'Assenze'        => ['assenze', 'assenze_da', 'assenze_da_a'],
            'Note'           => ['note', 'leggi_nota'],
            'Bacheca'        => ['bacheca', 'bachecaLeggi', 'bachecaAllegato'],
            'Documenti'      => ['documenti', 'controllo_doc'],
            'Utente'         => ['carta', 'status', 'didattica', 'didattica_elem'],
            'Varie'          => ['libri', 'overview', 'login'],
        ];

        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html lang="it"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
        echo '<title>Debug API</title>';
        echo '<style>
            body{font-family:system-ui,sans-serif;max-width:800px;margin:0 auto;padding:2rem}
            h1{font-size:1.5rem;margin-bottom:1.5rem}
            h3{margin:1.5rem 0 0.5rem;color:#6b7280;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.05em}
            a{display:block;padding:0.5rem 0.75rem;margin-bottom:0.25rem;border-radius:0.4rem;
              text-decoration:none;color:#e5e7eb;font-size:0.9rem;font-family:monospace;background:#1e293b}
            a:hover{background:#334155}
          </style></head><body>';
        echo '<h1>🔧 Debug API — Classeviva Raw Endpoints</h1>';

        global $methods;
        foreach ($groups as $groupName => $apiNames) {
            echo "<h3>$groupName</h3>";
            foreach ($apiNames as $name) {
                if (!isset($methods[$name])) continue;
                $m = $methods[$name];
                $tag = '';
                if (!empty($m['extraInput'])) {
                    $tag = ' <span style="color:#fbbf24;font-size:0.75rem">(' . implode(', ', $m['extraInput']) . ')</span>';
                }
                echo "<a href=\"/debug/api/$name\">$name</a>$tag";
            }
        }

        echo '</body></html>';
    }

    /**
     * /debug/api/:methodName — restituisce il JSON raw dell'API richiesta
     * Usa il proxy /api esistente (classeviva) per autenticazione, re-login, ecc.
     */
    public function classevivaDebug(string $methodName): void {
        global $methods;

        if (!isset($methods[$methodName])) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => "Unknown API method '$methodName'", 'available_methods' => array_keys($methods)], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            return;
        }

        $api = $methods[$methodName];
        $extraInput = $api['extraInput'] ?? [];
        $isPost = ($api['reqMethod'] ?? 'GET') === 'POST';

        // Raccogli parametri da query string / POST, filtrando chiavi non utili
        $params = [];
        foreach ($_GET as $k => $v) {
            if (is_string($k) && !ctype_digit($k)) $params[$k] = $v;
        }
        foreach ($_POST as $k => $v) {
            if (is_string($k) && !ctype_digit($k)) $params[$k] = $v;
        }

        $missing = [];
        foreach ($extraInput as $key) {
            if (!isset($params[$key])) {
                $missing[] = $key;
            }
        }

        // Se mancano parametri, mostra form
        if (!empty($missing)) {
            header('Content-Type: text/html; charset=utf-8');
            echo '<!DOCTYPE html><html lang="it"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
            echo '<title>Debug — ' . htmlspecialchars($methodName) . '</title>';
            echo '<style>
                body{font-family:system-ui,sans-serif;max-width:600px;margin:0 auto;padding:2rem;color:#e5e7eb;background:#0f172a}
                h2{font-family:monospace;margin-bottom:1rem}
                label{display:block;margin-bottom:0.25rem;font-size:0.85rem;color:#94a3b8}
                input{width:100%;padding:0.5rem;margin-bottom:1rem;border-radius:0.4rem;border:1px solid #334155;
                      background:#1e293b;color:#e5e7eb;font-family:monospace;font-size:0.9rem;box-sizing:border-box}
                button{padding:0.5rem 1.5rem;border-radius:0.4rem;border:none;background:#3b82f6;color:#fff;
                       cursor:pointer;font-size:0.9rem}
                button:hover{background:#2563eb}
                a{color:#60a5fa}
            </style></head><body>';
            echo '<h2>' . htmlspecialchars($methodName) . '</h2>';
            echo "<p style='font-size:0.85rem;color:#94a3b8;margin-bottom:1.5rem;'>Parametri richiesti: " . implode(', ', $extraInput) . "</p>";
            echo '<form method="GET" action="/debug/api/' . htmlspecialchars($methodName) . '">';
            foreach ($missing as $key) {
                $default = '';
                if ($key === 'date_from') {
                    $default = date('Ymd', strtotime('-30 days'));
                } elseif ($key === 'date_to') {
                    $default = date('Ymd', strtotime('+30 days'));
                }
                $safeKey = htmlspecialchars($key);
                echo '<label>' . $safeKey . '</label><input name="' . $safeKey . '" value="' . $default . '">';
            }
            echo '<button type="submit">Chiama API</button>';
            echo '</form>';
            echo "<p style='margin-top:1rem;font-size:0.8rem;'><a href=\"/debug/api\">← Torna alla lista</a></p>";
            echo '</body></html>';
            return;
        }

        // Forward al proxy esistente — usa la chiave di collegamento (campi url), non il nome logico
        // cvvArrKey = '' (vuoto) → la risposta NON viene filtrata, ritorno il JSON completo
        $payload = [
            'request'    => $api['url'],
            'extraInput' => $params,
            'isPost'     => $isPost,
            'cvvArrKey'  => '',
        ];

        $resp = $this->classeviva($payload, false, true);
        header('Content-Type: application/json');
        echo json_encode($resp, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
