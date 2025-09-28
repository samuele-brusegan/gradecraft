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
const URL_PATH = "https://gradecraft.test";
const COMMON_HTML_HEAD = BASE_PATH . '/public/commons/head.php';
const COMMON_HTML_FOOT = BASE_PATH . '/public/commons/bottom_navigation.php';

const THEME = (0) ? 'light' : 'dark';
const API_DETACH = false;

// Includi i file necessari, a mano o con l'autoloader di Composer
require_once BASE_PATH . '/app/core/Router.php';
require_once BASE_PATH . '/public/functions.php';
require_once BASE_PATH . '/public/commons/session_wall.php';
require_once BASE_PATH . '/app/controllers/Controller.php';

require_once BASE_PATH . '/app/core/cvv/collegamenti.php';
const CVV_URLS = new Collegamenti(); CVV_URLS->setGeneric("year", "24");
require_once BASE_PATH . '/app/core/cvv/ClassevivaIntegration.php';
require_once BASE_PATH . '/app/controllers/ApiController.php';

checkSessionExpiration();


// Ricreo l'ambiente di Cvv
const CVV_API  = new CvvIntegration(); //Req. PHP 8.1
define("USR", CVV_API->loadUser());


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
