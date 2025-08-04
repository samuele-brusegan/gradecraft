<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

// Definisci il percorso base dell'applicazione
define('BASE_PATH', dirname(__DIR__));
const URL_PATH = "gradecraft.test";

// Includi i file necessari, a mano o con l'autoloader di Composer
require_once BASE_PATH . '/app/core/Router.php';
//require_once BASE_PATH . '/app/models/Prodotto.php';
require_once BASE_PATH . '/app/controllers/Controller.php';

require_once BASE_PATH . '/app/core/cvv/ClassevivaIntegration.php';
require_once BASE_PATH . '/app/controllers/ApiController.php';


// Inizializza il router
$router = new Router();

// Definisci le rotte todo: spostare su un file routes.php
require BASE_PATH . '/public/routes.php';

// Ottieni l'URL richiesto e fai partire il router
$url = $_SERVER['REQUEST_URI'];
$router->dispatch($url);
