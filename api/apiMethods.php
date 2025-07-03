<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

include_once 'collegamenti.php';
$c = new Collegamenti();

$methods = [
    "login"         => [
        "name" => "login",
        "path" => "login",
        "method" => "POST",
        "url" => 'login'
    ],
    "get_grades"    => [
        "name" => "get_grades",
        "path" => "grades",
        "method" => "GET",
        "url" => 'voti'
    ],
    "subjects"      => [
        "name" => "subjects",
        "path" => "subjects",
        "method" => "GET",
        "url" => 'materie'
    ],
    "status"        => [
        "name" => "status",
        "path" => "status",
        "method" => "GET",
        "url" => 'stato'
    ],
    "get_ticket"    => [
        "name" => "get_ticket",//Common name
        "path" => "ticket",//My api endpoint
        "method" => "GET",//my api http method
        "url" => 'biglietto'
    ],
    "lezioni"       => [
        "name" => "lezioni",
        "path" => "generic",
        "method" => "POST",
        "url" => 'lezioni'
    ]
    //"logout"        => "logout",
    //"get_user"      => "getUser",
    //"get_users"     => "getUsers",
    //"get_courses"   => "getCourses",
    //"get_course"    => "getCourse",
]

?>