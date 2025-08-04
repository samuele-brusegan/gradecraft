<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */
global $router;

// === Pagine ===
$router->add('/'        , 'Controller', 'index');
$router->add('/grades'  , 'Controller', 'grades');

// === API ===
$router->add('/api'     , 'ApiController', 'classeviva');
$router->add('/api'     , 'ApiController', 'classevivaLogin');
