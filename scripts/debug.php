#!/usr/bin/env php
<?php
/**
 * Script CLI per debug e test delle API GradeCraft
 *
 * USAGE: php debug.php <command> [args...]
 *
 * Comandi disponibili:
 *   login                     - Effettua login con le credenziali .env
 *   status                    - Mostra stato sessione corrente
 *   clear                     - Cancella tutte le sessioni e dati salvati
 *   subjects                  - Recupera e stampa l'elenco delle materie
 *   grades [subject_id]       - Recupera e stampa i voti (tutti o per materia)
 *   grades-period             - Mostra i voti per periodo
 *   agenda [date_from] [date_to] - Mostra agenda (default: oggi)
 *   absences [date_from] [date_to] - Mostra assenze (default: oggi)
 *   overview [date_from] [date_to] - Mostra panoramica giornaliera
 *   test-all                  - Testa tutti gli endpoint e riporta tabella
 *   api <method_key> [json]   - Chiamata generica a un metodo specifico
 *   shell                     - Avia una shell interattiva per test
 */

require_once __DIR__ . '/bootstrap.php';

use cvv\CvvIntegration;

/**
 * Parses command line arguments
 */
function parseArgs(): array {
    global $argv;
    array_shift($argv); // Remove script name
    return $argv;
}

/**
 * Gets the command from arguments
 */
function getCommand(array $args): ?string {
    return $args[0] ?? null;
}

/**
 * Gets remaining arguments after command
 */
function getCommandArgs(array $args): array {
    array_shift($args);
    return $args;
}

/**
 * Checks if user is logged in, attempts to restore session
 */
function ensureLoggedIn(): bool {
    if (!isset($_SESSION['classeviva_auth_token']) || empty($_SESSION['classeviva_auth_token'])) {
        // Try to restore from CLI session file
        if (loadCliSession()) {
            echo colorize("[INFO] Session restored from storage.\n", 'cyan');
            // Ricarica l'oggetto utente dalla sessione ripristinata
            $GLOBALS['CVV_API']->loadUser();
        } else {
            echo colorize("[ERRORE] Nessuna sessione attiva. Effettua prima 'login'.\n", 'red');
            return false;
        }
    }

    // Check if token is still valid
    if (isset($_SESSION['classeviva_session_expiration_date'])) {
        $current = date(DATE_ATOM, time());
        if ($current > $_SESSION['classeviva_session_expiration_date']) {
            echo colorize("[WARN] Sessione scaduta. Effettua nuovamente il login.\n", 'yellow');
            return false;
        }
    }

    return true;
}

/**
 * Command: login - Authenticate with Classeviva using .env credentials
 */
function cmd_login(array $args): void {
    $username = getenv('CVV_USERNAME') ?: $_ENV['CVV_USERNAME'] ?? null;
    $password = getenv('CVV_PASSWORD') ?: $_ENV['CVV_PASSWORD'] ?? null;
    $year = getenv('CLASSEVIVA_YEAR') ?: $_ENV['CLASSEVIVA_YEAR'] ?? '24';

    if (!$username || !$password) {
        echo colorize("[ERRORE] Credenziali non trovate nel file .env.\n", 'red');
        echo "Configura le variabili CVV_USERNAME e CVV_PASSWORD nel file .env\n";
        return;
    }

    // Set year
    $GLOBALS['CVV_URLS']->setGeneric('year', $year);

    // Create user and login
    $cvvApi = $GLOBALS['CVV_API'];
    $cvvApi->createUser($username, $password);
    echo colorize("[INFO] Tentativo di login...\n", 'cyan');

    $result = $cvvApi->login();

    // Check if login succeeded by inspecting session vars
    if (isset($_SESSION['classeviva_auth_token']) && isset($_SESSION['classeviva_ident'])) {
        echo colorize("[SUCCESS] Login effettuato con successo!\n", 'green');
        echo "  Nome: " . ($_SESSION['classeviva_first_name'] ?? 'N/A') . "\n";
        echo "  Cognome: " . ($_SESSION['classeviva_last_name'] ?? 'N/A') . "\n";
        echo "  Ident: " . ($_SESSION['classeviva_ident'] ?? 'N/A') . "\n";
        echo "  Token: " . substr($_SESSION['classeviva_auth_token'] ?? '', 0, 20) . "...\n";
        echo "  Scadenza: " . ($_SESSION['classeviva_session_expiration_date'] ?? 'N/A') . "\n";

        // Save CLI session
        saveCliSession();
        echo colorize("[INFO] Sessione salvata in storage/cli_session.json\n", 'cyan');
    } else {
        echo colorize("[ERRORE] Login fallito.\n", 'red');
        if (is_array($result)) {
            print_r($result);
        } else {
            echo $result . "\n";
        }
    }
}

/**
 * Command: status - Show current session status
 */
function cmd_status(array $args): void {
    if (empty($_SESSION['classeviva_auth_token'])) {
        echo colorize("[INFO] Nessuna sessione attiva.\n", 'yellow');
        return;
    }

    echo colorize("[STATUS SESIONE CORRENTE]\n", 'bold');
    echo "  Username: " . ($_SESSION['classeviva_username'] ?? 'N/A') . "\n";
    echo "  Ident: " . ($_SESSION['classeviva_ident'] ?? 'N/A') . "\n";
    echo "  Token: " . substr($_SESSION['classeviva_auth_token'] ?? '', 0, 20) . "...\n";
    echo "  Scadenza: " . ($_SESSION['classeviva_session_expiration_date'] ?? 'N/A') . "\n";
    echo "  Richiesta: " . ($_SESSION['classeviva_session_request_date'] ?? 'N/A') . "\n";

    $exp = $_SESSION['classeviva_session_expiration_date'] ?? null;
    if ($exp) {
        $now = new DateTime();
        $expDate = new DateTime($exp);
        $remaining = $now->diff($expDate);
        if ($now < $expDate) {
            echo colorize("  Valida per: {$remaining->format('%a giorni, %h ore, %i minuti')}\n", 'green');
        } else {
            echo colorize("  [SCADUTA]\n", 'red');
        }
    }
}

/**
 * Command: clear - Clear all sessions and stored data
 */
function cmd_clear(array $args): void {
    $sessionPath = BASE_PATH . '/storage/sessions';
    $cliSessionFile = BASE_PATH . '/storage/cli_session.json';

    // Clear PHP session
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    // Remove session files
    $count = 0;
    if (is_dir($sessionPath)) {
        $files = glob($sessionPath . '/*');
        foreach ($files as $file) {
            if (unlink($file)) $count++;
        }
        rmdir($sessionPath);
    }

    // Remove CLI session file
    if (file_exists($cliSessionFile)) {
        unlink($cliSessionFile);
        $count++;
    }

    echo colorize("[CLEAN] Rimosso {$count} file di sessione.\n", 'green');
}

/**
 * Command: subjects - Fetch and display subjects list
 */
function cmd_subjects(array $args): void {
    if (!ensureLoggedIn()) return;

    echo colorize("[QUERY] Recupero materie...\n", 'cyan');
    $cvvApi = $GLOBALS['CVV_API'];
    $result = $cvvApi->genericQuery('materie');

    if (isset($result['error'])) {
        echo colorize("[ERRORE] " . ($result['message'] ?? 'Unknown error') . "\n", 'red');
        return;
    }

    if (empty($result)) {
        echo colorize("[INFO] Nessuna materia trovata.\n", 'yellow');
        return;
    }

    // Format data for table
    $data = array_map(function($subject) {
        return [
            'id' => $subject['id'] ?? 'N/A',
            'codice' => $subject['subjectCode'] ?? 'N/A',
            'descrizione' => $subject['subjectDesc'] ?? 'N/A',
            'ordine' => $subject['order'] ?? 'N/A',
        ];
    }, $result);

    printTable($data, ['id', 'codice', 'descrizione', 'ordine']);
    echo colorize("[SUCCESS] Recuperate " . count($result) . " materie.\n", 'green');
}

/**
 * Command: grades - Fetch and display grades
 */
function cmd_grades(array $args): void {
    if (!ensureLoggedIn()) return;

    $subjectId = $args[0] ?? null;

    echo colorize("[QUERY] Recupero voti...\n", 'cyan');
    $cvvApi = $GLOBALS['CVV_API'];
    $result = $cvvApi->genericQuery('voti');

    if (isset($result['error'])) {
        echo colorize("[ERRORE] " . ($result['message'] ?? 'Unknown error') . "\n", 'red');
        return;
    }

    if (empty($result)) {
        echo colorize("[INFO] Nessun voto trovato.\n", 'yellow');
        return;
    }

    // Filter by subject if provided
    if ($subjectId) {
        $filtered = array_filter($result, function($grade) use ($subjectId) {
            return $grade['subjectId'] == $subjectId;
        });
        $result = array_values($filtered);
    }

    if (empty($result)) {
        echo colorize("[INFO] Nessun voto trovato per la materia specificata.\n", 'yellow');
        return;
    }

    // Format data
    $data = array_map(function($grade) {
        return [
            'data' => $grade['evtDate'] ?? 'N/A',
            'materia' => $grade['subjectDesc'] ?? 'N/A',
            'tipo' => $grade['evtCode'] ?? 'N/A',
            'valore' => $grade['displayValue'] ?? ($grade['decimalValue'] ?? 'N/A'),
            'periodo' => $grade['periodDesc'] ?? 'N/A',
            'note' => substr($grade['notesForFamily'] ?? '', 0, 30) . (strlen($grade['notesForFamily'] ?? '') > 30 ? '...' : ''),
        ];
    }, $result);

    printTable($data, ['data', 'materia', 'tipo', 'valore', 'periodo', 'note']);
    echo colorize("[SUCCESS] Recuperati " . count($result) . " voti.\n", 'green');
}

/**
 * Command: grades-period - Display grades by period (requires view)
 */
function cmd_grades_period(array $args): void {
    echo colorize("[INFO] La vista grades_period richiede l'implementazione frontend.\n", 'yellow');
    echo "Invia una chiamata API diretta con: api grades4Period\n";
}

/**
 * Command: agenda - Display agenda events
 */
function cmd_agenda(array $args): void {
    if (!ensureLoggedIn()) return;

    $today = new DateTime();
    $date_from = $args[0] ?? $today->format('Ymd');
    $date_to = $args[1] ?? $date_from;

    echo colorize("[QUERY] Recupero agenda dal {$date_from} al {$date_to}...\n", 'cyan');
    $cvvApi = $GLOBALS['CVV_API'];
    $result = $cvvApi->genericQuery('agenda_da_a', [
        'date_from' => $date_from,
        'date_to' => $date_to,
    ]);

    if (isset($result['error'])) {
        echo colorize("[ERRORE] " . ($result['message'] ?? 'Unknown error') . "\n", 'red');
        return;
    }

    if (empty($result)) {
        echo colorize("[INFO] Nessun evento agenda trovato.\n", 'yellow');
        return;
    }

    $events = $result[0]['events'] ?? $result;
    if (empty($events)) {
        echo colorize("[INFO] Nessun evento agenda trovato.\n", 'yellow');
        return;
    }

    // Format data
    $data = [];
    foreach ($events as $event) {
        $data[] = [
            'data' => $event['evtDate'] ?? 'N/A',
            'ora' => $event['evtTime'] ?? 'N/A',
            'tipo' => $event['evtCode'] ?? 'N/A',
            'materia' => $event['subjectDesc'] ?? 'N/A',
            'note' => substr($event['notes'] ?? $event['evtNote'] ?? '', 0, 40) . (strlen($event['notes'] ?? $event['evtNote'] ?? '') > 40 ? '...' : ''),
        ];
    }

    printTable($data, ['data', 'ora', 'tipo', 'materia', 'note']);
    echo colorize("[SUCCESS] Recuperati " . count($data) . " eventi.\n", 'green');
}

/**
 * Command: absences - Display absences
 */
function cmd_absences(array $args): void {
    if (!ensureLoggedIn()) return;

    $today = new DateTime();
    $date_from = $args[0] ?? $today->format('Ymd');
    $date_to = $args[1] ?? $date_from;

    echo colorize("[QUERY] Recupero assenze dal {$date_from} al {$date_to}...\n", 'cyan');
    $cvvApi = $GLOBALS['CVV_API'];
    $result = $cvvApi->genericQuery('assenze_da_a', [
        'date_from' => $date_from,
        'date_to' => $date_to,
    ]);

    if (isset($result['error'])) {
        echo colorize("[ERRORE] " . ($result['message'] ?? 'Unknown error') . "\n", 'red');
        return;
    }

    if (empty($result)) {
        echo colorize("[INFO] Nessuna assenza trovata.\n", 'yellow');
        return;
    }

    $events = $result[0]['events'] ?? $result;
    if (empty($events)) {
        echo colorize("[INFO] Nessuna assenza trovata.\n", 'yellow');
        return;
    }

    // Format data
    $data = [];
    foreach ($events as $event) {
        $data[] = [
            'data' => $event['evtDate'] ?? 'N/A',
            'tipo' => $event['evtCode'] ?? 'N/A',
            'stato' => $event['isVerified'] ?? 'N/A',
            'note' => substr($event['evtNote'] ?? '', 0, 40) . (strlen($event['evtNote'] ?? '') > 40 ? '...' : ''),
        ];
    }

    printTable($data, ['data', 'tipo', 'stato', 'note']);
    echo colorize("[SUCCESS] Recuperate " . count($data) . " assenze.\n", 'green');
}

/**
 * Command: overview - Display daily overview
 */
function cmd_overview(array $args): void {
    if (!ensureLoggedIn()) return;

    $today = new DateTime();
    $date_from = $args[0] ?? $today->format('Ymd');
    $date_to = $args[1] ?? $date_from;

    echo colorize("[QUERY] Recupero panoramica per {$date_from}...\n", 'cyan');
    $cvvApi = $GLOBALS['CVV_API'];
    $result = $cvvApi->genericQuery('overview', [
        'date_from' => $date_from,
        'date_to' => $date_to,
    ]);

    if (isset($result['error'])) {
        echo colorize("[ERRORE] " . ($result['message'] ?? 'Unknown error') . "\n", 'red');
        return;
    }

    if (empty($result)) {
        echo colorize("[INFO] Nessun dato panoramica trovato.\n", 'yellow');
        return;
    }

    echo colorize("[PANORAMICA {$date_from}]\n", 'bold');
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
}

/**
 * Command: test-all - Test all available API methods
 */
function cmd_test_all(array $args): void {
    if (!ensureLoggedIn()) return;

    $methods = require BASE_PATH . '/app/core/cvv/apiMethods.php';
    $cvvApi = $GLOBALS['CVV_API'];

    echo colorize("[TEST] Verifica di tutti gli endpoint API...\n\n", 'cyan');

    $results = [];
    foreach (array_keys($methods) as $methodKey) {
        echo colorize("  Testing: {$methodKey}...", 'yellow');

        $start = microtime(true);
        $result = $cvvApi->genericQuery($methodKey);
        $duration = microtime(true) - $start;

        if (isset($result['error'])) {
            echo colorize(" FAIL (" . number_format($duration, 3) . "s)\n", 'red');
            $results[] = [
                'method' => $methodKey,
                'status' => 'FAIL',
                'error' => $result['message'] ?? 'Unknown',
                'time' => number_format($duration, 3) . 's',
            ];
        } else {
            echo colorize(" OK (" . number_format($duration, 3) . "s)\n", 'green');
            $results[] = [
                'method' => $methodKey,
                'status' => 'OK',
                'time' => number_format($duration, 3) . 's',
            ];
        }
    }

    echo "\n" . colorize("[RIEPILOGO TEST]\n", 'bold');
    $summary = [
        'total' => count($results),
        'ok' => count(array_filter($results, fn($r) => $r['status'] === 'OK')),
        'fail' => count(array_filter($results, fn($r) => $r['status'] === 'FAIL')),
    ];

    echo "  Totale: {$summary['total']}\n";
    echo colorize("  Successi: {$summary['ok']}\n", 'green');
    echo colorize("  Fallimenti: {$summary['fail']}\n", 'red');

    // Show failures
    $failures = array_filter($results, fn($r) => $r['status'] === 'FAIL');
    if ($failures) {
        echo "\n" . colorize("[DETTAGLI ERRORI]\n", 'bold');
        foreach ($failures as $f) {
            echo colorize("  {$f['method']}: {$f['error']}\n", 'red');
        }
    }
}

/**
 * Command: api - Generic API call to any method
 */
function cmd_api(array $args): void {
    if (!ensureLoggedIn()) return;

    if (empty($args)) {
        echo colorize("[ERRORE] Specifica il metodo API.\nEsempio: php debug.php api lezioniToday\n", 'red');
        echo "Metodi disponibili: " . implode(', ', array_keys(require BASE_PATH . '/app/core/cvv/apiMethods.php')) . "\n";
        return;
    }

    $methodKey = $args[0];
    $jsonOutput = in_array('--json', $args) || in_array('-j', $args);

    // Parse additional parameters from args (key=value format)
    $extraInput = [];
    foreach ($args as $arg) {
        if (strpos($arg, '=') !== false && $arg !== '--json' && $arg !== '-j') {
            list($key, $value) = explode('=', $arg, 2);
            $extraInput[$key] = $value;
        }
    }

    $methods = require BASE_PATH . '/app/core/cvv/apiMethods.php';
    if (!isset($methods[$methodKey])) {
        echo colorize("[ERRORE] Metodo '{$methodKey}' non found.\n", 'red');
        echo "Metodi disponibili: " . implode(', ', array_keys($methods)) . "\n";
        return;
    }

    $methodConfig = $methods[$methodKey];
    echo colorize("[API] Chiamata {$methodKey} -> {$methodConfig['url']}\n", 'cyan');

    $cvvApi = $GLOBALS['CVV_API'];
    $result = $cvvApi->genericQuery($methodKey, $extraInput);

    if ($jsonOutput) {
        echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
    } else {
        if (isset($result['error'])) {
            echo colorize("[ERRORE] {$result['message']}\n", 'red');
            print_r($result);
        } else {
            echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
        }
    }
}

/**
 * Command: shell - Interactive test shell
 */
function cmd_shell(array $args): void {
    if (!ensureLoggedIn()) return;

    echo colorize("[SHELL] Modalità interattiva. Digita 'exit' per uscire.\n", 'cyan');
    echo "Esempi:\n";
    echo "  api lezioniToday\n";
    echo "  api lezioniDaA date_from=20250301 date_to=20250301\n";
    echo "  status\n\n";

    $cvvApi = $GLOBALS['CVV_API'];

    while (true) {
        $line = readline("debug> ");
        if ($line === false) break;
        $line = trim($line);
        if ($line === 'exit' || $line === 'quit') {
            break;
        }

        if (empty($line)) continue;

        $parts = preg_split('/\s+/', $line);
        $cmd = array_shift($parts);

        if ($cmd === 'api') {
            $methodKey = $parts[0] ?? null;
            $extraInput = [];
            foreach ($parts as $part) {
                if (strpos($part, '=') !== false) {
                    list($key, $value) = explode('=', $part, 2);
                    $extraInput[$key] = $value;
                }
            }

            if (!$methodKey) {
                echo colorize("[ERRORE] Specifica il metodo API.\n", 'red');
                continue;
            }

            $result = $cvvApi->genericQuery($methodKey, $extraInput);
            echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
        } elseif ($cmd === 'status') {
            cmd_status([]);
        } elseif ($cmd === 'clear') {
            cmd_clear([]);
        } else {
            echo colorize("[ERRORE] Comando shell non recognized. Usa 'api' o 'status'.\n", 'red');
        }

        readline_add_history($line);
    }
}

/**
 * Main dispatcher
 */
function main(): void {
    global $argv;
    $args = parseArgs();
    $command = getCommand($args);
    $commandArgs = getCommandArgs($args);

    // Show help if no command
    if (!$command) {
        echo "GradeCraft Debug CLI\n";
        echo "Usage: php debug.php <command> [args...]\n\n";
        echo "Comandi disponibili:\n";
        echo "  login                     - Effettua login con le credenziali .env\n";
        echo "  status                    - Mostra stato sessione corrente\n";
        echo "  clear                     - Cancella tutte le sessioni\n";
        echo "  subjects                  - Recupera e stampa l'elenco delle materie\n";
        echo "  grades [subject_id]       - Recupera e stampa i voti\n";
        echo "  agenda [date_from] [date_to] - Mostra agenda (YYYYMMDD)\n";
        echo "  absences [date_from] [date_to] - Mostra assenze\n";
        echo "  overview [date_from] [date_to] - Mostra panoramica\n";
        echo "  test-all                  - Testa tutti gli endpoint API\n";
        echo "  api <method_key> [args]   - Chiamata generica a un metodo\n";
        echo "  shell                     - Shell interattiva per test\n\n";
        echo "Esempio: php debug.php api lezioniToday\n";
        exit(1);
    }

    // Map command to function
    $cmdMap = [
        'login' => 'cmd_login',
        'status' => 'cmd_status',
        'clear' => 'cmd_clear',
        'subjects' => 'cmd_subjects',
        'grades' => 'cmd_grades',
        'grades-period' => 'cmd_grades_period',
        'agenda' => 'cmd_agenda',
        'absences' => 'cmd_absences',
        'overview' => 'cmd_overview',
        'test-all' => 'cmd_test_all',
        'test_all' => 'cmd_test_all',
        'api' => 'cmd_api',
        'shell' => 'cmd_shell',
    ];

    if (!isset($cmdMap[$command])) {
        echo colorize("[ERRORE] Comando '{$command}' sconosciuto.\n", 'red');
        echo "Usa 'php debug.php' senza argomenti per la lista dei comandi.\n";
        exit(1);
    }

    call_user_func($cmdMap[$command], $commandArgs);
}

main();
