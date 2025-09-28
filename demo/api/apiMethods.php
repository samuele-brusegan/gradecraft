<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

use api\Collegamenti;

include_once 'collegamenti.php';
$c = new Collegamenti();

$methods = [
    //NOME => [
    //    "name"       => NOME,
    //    "path"       => api endpoint in `api.php`
    //    "method"     => metodo con cui chiamare l'api in `api.php`
    //    "url"        => key dell'array in `collegamenti.php` il cui elemento è l' URL esterno verso classeviva
    //    "cvvArrKey"  => chiave dell'array restituitomi da classeviva essendo che usualmente, viene restituito un array
    //                    nella forma {"cvvArrKey": ... }
    //    "extraInput" => array che contiene i nomi delle informazioni extra richieste per completare il link.
    //                    Devono combaciare con i nomi dei parametri in `collegamenti.php`
    //    "reqMethod"  => metodo con cui chiamare l'api esterna di classeviva
    //];
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
        "reqMethod" => "GET"
    ],
    "assenze"           => [
        "name"  => "assenze",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'assenze',
        "cvvArrKey" => "events",
        "extraInput" => [],
        "reqMethod" => "GET"
    ], // ABA0: ASSENZA,  ABU0: USCITA,  ABR0: RITARDO,  ABR1: RITARDO_BREVE,
    "assenze_da"        => [
        "name"  => "assenze_da",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'assenze_da',
        "cvvArrKey" => "events",
        "extraInput" => [
            "date_from"
        ],
        "reqMethod" => "GET"
    ],
    "assenze_da_a"      => [
        "name"  => "assenze_da_a",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'assenze_da_a',
        "cvvArrKey" => "events",
        "extraInput" => [
            "date_from",
            "date_to"
        ],
        "reqMethod" => "GET"
    ],
    "agenda_da_a"       => [
        "name"  => "agenda_da_a",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'agenda_da_a',
        "cvvArrKey" => "events",
        "extraInput" => [
            "date_from",
            "date_to"
        ],
        "reqMethod" => "GET"
    ],
    "agenda_cod_da_a"   => [
        "name"  => "agenda_cod_da_a",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'agenda_codice_da_a',
        "cvvArrKey" => "events",
        "extraInput" => [
            "date_from",
            "date_to",
            "agenda_code"       //TODO: Da capire cos'è
        ],
        "reqMethod" => "GET"
    ], //Agenda_cod_da_a: Scopri cos'è agenda_code
    "documenti"         => [
        "name"  => "documenti",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'documenti',
        "cvvArrKey" => "",//documents && schoolreports
        "extraInput" => [],
        "reqMethod" => "POST"
    ],
    "controllo_doc"     => [
        "name"  => "controllo_doc",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'controllo_documento',
        "cvvArrKey" => "document",
        "extraInput" => [
            "document_id"
        ],
        "reqMethod" => "POST"
    ],
    /*"leggi_doc"         => [
        "name"  => "leggi_doc",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'leggi_documento',
        "cvvArrKey" => "",
        "extraInput" => [
            "document_id"
        ],
        "reqMethod" => "POST",
        "particularDataParse" => "PDF"
    ]*///LeggiDoc: Inutilmente difficile da gestire senza UI di appoggio. Ritorna direttamente in codice del pdf
    "didattica"         => [
        "name"  => "didattica",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'didattica',
        "cvvArrKey" => "",//didactics
        "extraInput" => [],
        "reqMethod" => "GET"
    ],
    "didattica_elem"    => [
        "name"  => "didattica_elem",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'didattica_elemento',
        "cvvArrKey" => "",
        "extraInput" => [
            "elementId"
        ],
        "reqMethod" => "GET"
    ],
    "calendario"        => [
        "name"  => "calendario",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'calendario',
        "cvvArrKey" => "calendar",
        "extraInput" => [],
        "reqMethod" => "GET"
    ],//SD: School Day, HD: holiday, NW: No Work
    "calendario_da_a"   => [
        "name"  => "calendario_da_a",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'calendario_da_a',
        "cvvArrKey" => "calendar",
        "extraInput" => [
            "date_from",
            "date_to"
        ],
        "reqMethod" => "GET"
    ],
    "libri"             => [
        "name"  => "libri",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'libri',
        "cvvArrKey" => "schoolbooks",
        "extraInput" => [],
        "reqMethod" => "GET"
    ],
    "periodi"           => [
        "name"  => "periodi",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'periodi',
        "cvvArrKey" => "periods",
        "extraInput" => [],
        "reqMethod" => "GET"
    ],
    "note"              => [
        "name"  => "note",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'note',
        "cvvArrKey" => "",
        "extraInput" => [],
        "reqMethod" => "GET"
    ],//NTTE: Note dell'Insegnante, NTCL: Note Classeviva/Annotazioni, NTWN: Richiami, NTST: Disciplinari
    "leggi_nota"        => [
        "name"  => "leggi_nota",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'leggi_nota',
        "cvvArrKey" => "event",
        "extraInput" => [
            "noteType",
            "elementId"
        ],
        "reqMethod" => "POST"
    ],
    "cambia_anno_scolastico" => [
        "name"  => "cambia_anno_scolastico",
        "path"  => "chYear",
        "method" => "POST",
        "cvvArrKey" => "",
        "extraInput" => [
            "year"
        ]
    ]
    /*"avatar"            => [
        "name"  => "avatar",
        "path"  => "generic",
        "method" => "POST",
        "url"   => 'avatar',
        "cvvArrKey" => "",
        "extraInput" => [],
        "reqMethod" => "GET"
    ]*/// Avatar: invalid ident. Forse perché l'avatar è default? Da verificare
];

?>