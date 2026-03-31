#!/usr/bin/env php
<?php
/**
 * Bootstrap per ambiente CLI di debug GradeCraft
 * Carica configurazione, classi e inizializza l'integrazione Classeviva
 */

// Imposta timezone
date_default_timezone_set('Europe/Rome');

// Carica variabili d'ambiente dal file .env (nella root o nella directory scripts)
$envPaths = [
    dirname(__DIR__) . '/.env',
    __DIR__ . '/.env',
];

foreach ($envPaths as $envPath) {
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                putenv(trim($key) . '=' . trim($value));
                $_ENV[trim($key)] = trim($value);
            }
        }
        break;
    }
}

// Definisci costanti se non già definite
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
if (!defined('URL_PATH')) {
    // In CLI usiamo localhost per default, sovrascrivibile con env
    define('URL_PATH', getenv('URL_PATH') ?: 'http://localhost:8000');
}
if (!defined('API_DETACH')) {
    define('API_DETACH', filter_var(getenv('API_DETACH') ?: false, FILTER_VALIDATE_BOOLEAN));
}
if (!defined('THEME')) {
    define('THEME', 'dark');
}

// Carica le classi necessarie
require_once BASE_PATH . '/app/core/Router.php';
require_once BASE_PATH . '/app/controllers/Controller.php';
require_once BASE_PATH . '/app/controllers/ApiController.php';
require_once BASE_PATH . '/app/core/cvv/collegamenti.php';
require_once BASE_PATH . '/app/core/cvv/ClassevivaIntegration.php';
require_once BASE_PATH . '/app/core/cvv/User.php';
require_once BASE_PATH . '/public/functions.php';

// Inizializza CVV_URLS global
$GLOBALS['CVV_URLS'] = new \cvv\Collegamenti();

// Imposta l'anno scolastico da env, default '24'
$year = getenv('CLASSEVIVA_YEAR') ?: '24';
$GLOBALS['CVV_URLS']->setGeneric('year', $year);

// Inizializza CVV_API global
$GLOBALS['CVV_API'] = new \cvv\CvvIntegration();

// Configura sessione per CLI (usa storage/sessions)
$sessionPath = BASE_PATH . '/storage/sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0700, true);
}
session_save_path($sessionPath);
session_start();

// Utility per output colorato
if (!function_exists('colorize')) {
    function colorize(string $text, string $color = 'white'): string {
        $colors = [
            'reset' => "\033[0m",
            'red' => "\033[31m",
            'green' => "\033[32m",
            'yellow' => "\033[33m",
            'blue' => "\033[34m",
            'cyan' => "\033[36m",
            'white' => "\033[37m",
            'bold' => "\033[1m",
        ];
        return ($colors[$color] ?? $colors['white']) . $text . $colors['reset'];
    }
}

// Utility per stampare tabelle ASCII semplici
if (!function_exists('printTable')) {
    function printTable(array $data, array $headers = null): void {
        if (empty($data)) {
            echo "Nessun dato disponibile.\n";
            return;
        }

        // Prendi le chiavi dal primo elemento se headers non forniti
        if ($headers === null) {
            $headers = array_keys($data[0]);
        }

        // Calcola larghezza colonne
        $widths = [];
        foreach ($headers as $header) {
            $widths[$header] = strlen($header);
        }
        foreach ($data as $row) {
            foreach ($headers as $header) {
                $value = isset($row[$header]) ? (string)$row[$header] : '';
                $widths[$header] = max($widths[$header], strlen($value));
            }
        }

        // Stampa header
        $line = '+';
        foreach ($headers as $header) {
            $line .= str_repeat('-', $widths[$header] + 2) . '+';
        }
        echo $line . "\n";

        echo '|';
        foreach ($headers as $header) {
            echo ' ' . str_pad($header, $widths[$header]) . ' |';
        }
        echo "\n";

        echo str_replace('+', '|', $line) . "\n";

        // Stampa righe
        foreach ($data as $row) {
            echo '|';
            foreach ($headers as $header) {
                $value = isset($row[$header]) ? (string)$row[$header] : '';
                echo ' ' . str_pad($value, $widths[$header]) . ' |';
            }
            echo "\n";
        }
        echo $line . "\n";
    }
}

// Funzione per salvare la sessione CLI (evita conflitti con web)
$cliSessionFile = BASE_PATH . '/storage/cli_session.json';
if (!function_exists('saveCliSession')) {
    function saveCliSession(): void {
        global $cliSessionFile;
        $data = [
            'classeviva_username' => $_SESSION['classeviva_username'] ?? null,
            'classeviva_password' => $_SESSION['classeviva_password'] ?? null,
            'classeviva_auth_token' => $_SESSION['classeviva_auth_token'] ?? null,
            'classeviva_ident' => $_SESSION['classeviva_ident'] ?? null,
            'classeviva_session_expiration_date' => $_SESSION['classeviva_session_expiration_date'] ?? null,
            'classeviva_session_request_date' => $_SESSION['classeviva_session_request_date'] ?? null,
            'classeviva_first_name' => $_SESSION['classeviva_first_name'] ?? null,
            'classeviva_last_name' => $_SESSION['classeviva_last_name'] ?? null,
        ];
        file_put_contents($cliSessionFile, json_encode($data, JSON_PRETTY_PRINT));
    }
}

// Funzione per caricare la sessione CLI
if (!function_exists('loadCliSession')) {
    function loadCliSession(): bool {
        global $cliSessionFile;
        if (file_exists($cliSessionFile)) {
            $data = json_decode(file_get_contents($cliSessionFile), true);
            if ($data) {
                foreach ($data as $key => $value) {
                    if ($value !== null) {
                        $_SESSION[$key] = $value;
                    }
                }
                return true;
            }
        }
        return false;
    }
}
