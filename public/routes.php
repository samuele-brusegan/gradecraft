<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */
global $router;

// === API ===
$router->add('/api'     , 'ApiController', 'classeviva');
$router->add('/login'   , 'ApiController', 'login');

// === Pagine ===
$router->add('/'             , 'Controller', 'index');
$router->add('/grades'       , 'Controller', 'grades');
$router->add('/grades/period', 'Controller', 'grades4Period');
//$router->add('/settings'     , 'Controller', 'settings');
$router->add('/account'      , 'Controller', 'account');
//$router->add('/subjects'     , 'Controller', 'subjects');
//$router->add('/agenda'       , 'Controller', 'agenda');