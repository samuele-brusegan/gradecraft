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
const URL_PATH = "gradecraft.test";

// Includi i file necessari, a mano o con l'autoloader di Composer
require_once BASE_PATH . '/app/core/Router.php';
require_once BASE_PATH . '/public/functions.php';
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

<!--<script>
    // Inizializza il database all'avvio dell'applicazione
    try {
        await dbService.open();
        console.log('Database IndexedDB pronto!', 'success');
    } catch (error) {
        console.log(`Errore nell'inizializzazione del database: ${error.message}`, 'error');
    }
</script>-->
