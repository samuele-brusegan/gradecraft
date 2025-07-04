<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

include_once 'collegamenti.php';
$c = new Collegamenti();

$methods = [
    "login"             => [
        "name" => "login",
        "path" => "login",
        "method" => "POST",
        "url" => 'login',
        "cvvArrKey" => "",
        "extraInput" => [],
        "reqMethod" => "POST"
    ],
    "get_grades"        => [
        "name" => "get_grades",
        "path" => "generic",
        "method" => "POST",
        "url" => 'voti',
        "cvvArrKey" => "grades",
        "extraInput" => [],
        "reqMethod" => "GET"
    ],
    "subjects"          => [
        "name" => "subjects",
        "path" => "generic",
        "method" => "POST",
        "url" => 'materie',
        "cvvArrKey" => "subjects",
        "extraInput" => [],
        "reqMethod" => "GET"
    ],
    "status"            => [
        "name" => "status",
        "path" => "generic",
        "method" => "POST",
        "url" => 'stato',
        "cvvArrKey" => "status",
        "extraInput" => [],
        "reqMethod" => "GET"
    ],
    "get_ticket"        => [
        "name" => "get_ticket",//Common name
        "path" => "generic",//My api endpoint
        "method" => "POST",//my api http method
        "url" => 'biglietto',
        "cvvArrKey" => "",
        "extraInput" => [],
        "reqMethod" => "GET"
    ],
    "lezioniToday"      => [
        "name" => "lezioniToday",
        "path" => "generic",
        "method" => "POST",
        "url" => 'lezioni',
        "cvvArrKey" => "lessons",
        "extraInput" => [],
        "reqMethod" => "GET"
    ],
    "lezioniGiorno"     => [
        "name"  => "lezioniGiorno",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'lezioni_giorno',
        "cvvArrKey" => "lessons",
        "extraInput" => [
            "date_from" //Formato: YYYYMMDD
        ],
        "reqMethod" => "GET"
    ],
    "lezioniDaA"        => [
        "name"  => "lezioniDaA",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'lezioni_da_a',
        "cvvArrKey" => "lessons",
        "extraInput" => [
            "date_from",    //Formato: YYYYMMDD
            "date_to"       //Formato: YYYYMMDD
        ],
        "reqMethod" => "GET"
    ],
    "lezioniDaAMateria" => [
        "name"  => "lezioniDaAMateria",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'lezioni_da_a_materia',
        "cvvArrKey" => "lessons",
        "extraInput" => [
            "date_from",    //Formato: YYYYMMDD
            "date_to",      //Formato: YYYYMMDD
            "materia"
        ],
        "reqMethod" => "GET"
    ],
    "carta"             => [
        "name"  => "carta",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'carta',
        "cvvArrKey" => "card",
        "extraInput" => [],
        "reqMethod" => "GET"
    ],
    "bacheca"           => [
        "name"  => "bacheca",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'bacheca',
        "cvvArrKey" => "items",
        "extraInput" => [],
        "reqMethod" => "GET"
    ],
    "bachecaLeggi"      => [
        "name"  => "bachecaLeggi",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'bacheca_leggi',
        "cvvArrKey" => "item",
        "extraInput" => [
            "eventCode",
            "pubId"
        ],
        "reqMethod" => "POST"
    ],
    "overview"          => [
        "name"  => "overview",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'panoramica_da_a',
        "cvvArrKey" => "",
        "extraInput" => [
            "date_from",
            "date_to"
        ],
        "requeryMethod" => "GET"
    ]
]

?>