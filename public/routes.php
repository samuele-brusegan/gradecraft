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
$router->add('/grades/subject/:id', 'Controller', 'gradeSubject');
$router->add('/settings'     , 'Controller', 'settings');
$router->add('/account'      , 'Controller', 'account');
$router->add('/subjects'     , 'Controller', 'subjects');
$router->add('/agenda'       , 'Controller', 'agenda');
$router->add('/absences'     , 'Controller', 'absences');
$router->add('/notes'        , 'Controller', 'notes');
$router->add('/documents'    , 'Controller', 'documents');
$router->add('/noticeboard'  , 'Controller', 'noticeboard');
$router->add('/noticeboard/read'    , 'Controller', 'noticeboardRead');
$router->add('/noticeboard/attach'  , 'Controller', 'noticeboardAttach');
$router->add('/calendar'     , 'Controller', 'calendar');
$router->add('/settings/json'  , 'Controller', 'settingsJson');