<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

use cvv\Collegamenti;
use cvv\CvvIntegration;
// Definisci il percorso base dell'applicazione

define('BASE_PATH', dirname(__DIR__));
session_start();
//1 => development; 0 => published
const URL_PATH = "https://gradecraft.". ((1) ? "test" : "brusegan.it") ;
const COMMON_HTML_HEAD    = BASE_PATH . '/public/commons/head.php';
const COMMON_HTML_FOOT    = BASE_PATH . '/public/commons/bottom_navigation.php';
const COMMON_HTML_TNAVBAR = BASE_PATH . '/public/commons/top_navigation.php';

const THEME = (0) ? 'light' : 'dark';
const API_DETACH = false;

// Includi i file necessari, a mano o con l'autoloader di Composer
require_once BASE_PATH . '/app/core/Router.php';
require_once BASE_PATH . '/public/functions.php';
require_once BASE_PATH . '/public/commons/session_wall.php';
require_once BASE_PATH . '/app/controllers/Controller.php';

require_once BASE_PATH . '/app/core/cvv/collegamenti.php';
require_once BASE_PATH . '/app/core/cvv/ClassevivaIntegration.php';
require_once BASE_PATH . '/app/controllers/ApiController.php';

checkSessionExpiration();

// Determina l'anno scolastico (da .env se presente, altrimenti default '24')
$year = '24'; // default
if (file_exists(BASE_PATH . '/.env')) {
    $envLines = file(BASE_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            if (trim($key) === 'CLASSEVIVA_YEAR') {
                $year = trim($value);
                break;
            }
        }
    }
}

// Inizializza CVV_URLS global
$GLOBALS['CVV_URLS'] = new Collegamenti();
$GLOBALS['CVV_URLS']->setGeneric('year', $year);

// Inizializza CVV_API global
$GLOBALS['CVV_API'] = new CvvIntegration();

// Carica utente in sessione
define("USR", $GLOBALS['CVV_API']->loadUser());


// Inizializza il router
$router = new Router();

// Definisci le rotte
require BASE_PATH . '/public/routes.php';

// Ottieni l'URL richiesto e fai partire il router
$url = $_SERVER['REQUEST_URI'];
$router->dispatch($url);
?>

<script type="module">
    import { IndexedDBService } from './js/dbApi.js';
    // Inizializza il database all'avvio dell'applicazione
    const dbService = new IndexedDBService('gradecraft', 1, {
        grades: { keyPath: 'ident', autoIncrement: true },
    })
    async function initDB() {
        try {
            await dbService.init('grades');
            console.log('Database IndexedDB pronto!', 'success');
        } catch (error) {
            console.log(`Errore nell'inizializzazione del database: ${error.message}`, 'error');
        }
    }
    initDB();
</script>
