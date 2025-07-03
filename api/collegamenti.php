<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */


//Grazie a Lioydiano per i link: https://github.com/Lioydiano/Classeviva

class Collegamenti {

    public $base;
    public $login;
    public $stato;
    public $biglietto;
    public $documenti;
    public $controllo_documento;
    public $leggi_documento;
    public $assenze;
    public $assenze_da;
    public $assenze_da_a;
    public $agenda_da_a;
    public $agenda_codice_da_a;
    public $didattica;
    public $didattica_elemento;
    public $bacheca;
    public $bacheca_leggi;
    public $bacheca_allega;
    public $bacheca_allega_esterno;
    public $lezioni;
    public $lezioni_giorno;
    public $lezioni_da_a;
    public $lezioni_da_a_materia;
    public $calendario;
    public $calendario_da_a;
    public $libri;
    public $carta;
    public $voti;
    public $periodi;
    public $materie;
    public $note;
    public $leggi_nota;
    public $panoramica_da_a;
    public $avatar;
    public $collegamenti;
    private $ident;
    private $date_from;
    private $date_to;
    private $agenda_code;
    private $materia;

    public function __construct() {
        $this->setValues();
    }
    private function setValues(): void {
        $this->collegamenti = [
            'base'                   => "https://web.spaggiari.eu/rest",
            'login'                  => $this->base . "/v1/auth/login",
            'stato'                  => $this->base . "/v1/auth/status",
            'biglietto'              => $this->base . "/v1/auth/ticket",
            'documenti'              => $this->base . "/v1/students/" . $this->ident . "/documents",
            'controllo_documento'    => $this->base . "/v1/students/" . $this->ident . "/documents/check/{{}}", //FIXME:AAAAA
            'leggi_documento'        => $this->base . "/v1/students/" . $this->ident . "/documents/read/{{}}", //FIXME:AAAAA
            'assenze'                => $this->base . "/v1/students/" . $this->ident . "/absences/details",
            'assenze_da'             => $this->base . "/v1/students/" . $this->ident . "/absences/details/" . $this->date_from,
            'assenze_da_a'           => $this->base . "/v1/students/" . $this->ident . "/absences/details/" . $this->date_from . "/" . $this->date_to,
            'agenda_da_a'            => $this->base . "/v1/students/" . $this->ident . "/agenda/all/" . $this->date_from . "/" . $this->date_to,
            'agenda_codice_da_a'     => $this->base . "/v1/students/" . $this->ident . "/agenda/" . $this->date_from . "/" . $this->date_to . "/" . $this->agenda_code,
            'didattica'              => $this->base . "/v1/students/" . $this->ident . "/didactics",
            'didattica_elemento'     => $this->base . "/v1/students/" . $this->ident . "/didactics/item/{{}}", //FIXME:AAAAA
            'bacheca'                => $this->base . "/v1/students/" . $this->ident . "/noticeboard",
            'bacheca_leggi'          => $this->base . "/v1/students/" . $this->ident . "/noticeboard/read/{{}}/{{}}/101", //FIXME:AAAAA
            'bacheca_allega'         => $this->base . "/v1/students/" . $this->ident . "/noticeboard/attach/{{}}/{{}}/101", //FIXME:AAAAA
            'bacheca_allega_esterno' => "https://web.spaggiari.eu/sif/app/default/bacheca_personale.php?action=file_download&com_id={{}}", //FIXME:AAAAA
            'lezioni'                => $this->base . "/v1/students/" . $this->ident . "/lessons/today",
            'lezioni_giorno'         => $this->base . "/v1/students/" . $this->ident . "/lessons/" . $this->date_from,
            'lezioni_da_a'           => $this->base . "/v1/students/" . $this->ident . "/lessons/" . $this->date_from . "/" . $this->date_to,
            'lezioni_da_a_materia'   => $this->base . "/v1/students/" . $this->ident . "/lessons/" . $this->date_from . "/" . $this->date_to . "/" . $this->materia,
            'calendario'             => $this->base . "/v1/students/" . $this->ident . "/calendar/all",
            'calendario_da_a'        => $this->base . "/v1/students/" . $this->ident . "/calendar/" . $this->date_from . "/" . $this->date_to,
            'libri'                  => $this->base . "/v1/students/" . $this->ident . "/schoolbooks",
            'carta'                  => $this->base . "/v1/students/" . $this->ident . "/card",
            'voti'                   => $this->base . "/v1/students/" . $this->ident . "/grades",
            'periodi'                => $this->base . "/v1/students/" . $this->ident . "/periods",
            'materie'                => $this->base . "/v1/students/" . $this->ident . "/subjects",
            'note'                   => $this->base . "/v1/students/" . $this->ident . "/notes/all",
            'leggi_nota'             => $this->base . "/v1/students/" . $this->ident . "/notes/{{}}/read/{{}}", //FIXME:AAAAA
            'panoramica_da_a'        => $this->base . "/v1/students/" . $this->ident . "/overview/all/" . $this->date_from . "/" . $this->date_to,
            'avatar'                 => $this->base . "/v1/users/"    . $this->ident . "/avatar"
        ];
        $this->base                    = "https://web.spaggiari.eu/rest";
        $this->login                   = $this->base . "/v1/auth/login";
        $this->stato                   = $this->base . "/v1/auth/status";
        $this->biglietto               = $this->base . "/v1/auth/ticket";
        $this->documenti               = $this->base . "/v1/students/".$this->ident."/documents";
        $this->controllo_documento     = $this->base . "/v1/students/".$this->ident."/documents/check/{{}}";//FIXME:AAAAA
        $this->leggi_documento         = $this->base . "/v1/students/".$this->ident."/documents/read/{{}}";//FIXME:AAAAA
        $this->assenze                 = $this->base . "/v1/students/".$this->ident."/absences/details";
        $this->assenze_da              = $this->base . "/v1/students/".$this->ident."/absences/details/".$this->date_from;
        $this->assenze_da_a            = $this->base . "/v1/students/".$this->ident."/absences/details/".$this->date_from."/".$this->date_to;
        $this->agenda_da_a             = $this->base . "/v1/students/".$this->ident."/agenda/all/".$this->date_from."/".$this->date_to;
        $this->agenda_codice_da_a      = $this->base . "/v1/students/".$this->ident."/agenda/".$this->date_from."/".$this->date_to."/".$this->agenda_code;
        $this->didattica               = $this->base . "/v1/students/".$this->ident."/didactics";
        $this->didattica_elemento      = $this->base . "/v1/students/".$this->ident."/didactics/item/{{}}";//FIXME:AAAAA
        $this->bacheca                 = $this->base . "/v1/students/".$this->ident."/noticeboard";
        $this->bacheca_leggi           = $this->base . "/v1/students/".$this->ident."/noticeboard/read/{{}}/{{}}/101";//FIXME:AAAAA
        $this->bacheca_allega          = $this->base . "/v1/students/".$this->ident."/noticeboard/attach/{{}}/{{}}/101";//FIXME:AAAAA

        $this->bacheca_allega_esterno  = "https://web.spaggiari.eu/sif/app/default/bacheca_personale.php?action=file_download&com_id={{}}";//FIXME:AAAAA
        $this->lezioni                 = $this->base . "/v1/students/".$this->ident."/lessons/today";
        $this->lezioni_giorno          = $this->base . "/v1/students/".$this->ident."/lessons/".$this->date_from;
        $this->lezioni_da_a            = $this->base . "/v1/students/".$this->ident."/lessons/".$this->date_from."/".$this->date_to;
        $this->lezioni_da_a_materia    = $this->base . "/v1/students/".$this->ident."/lessons/".$this->date_from."/".$this->date_to."/".$this->materia;
        $this->calendario              = $this->base . "/v1/students/".$this->ident."/calendar/all";
        $this->calendario_da_a         = $this->base . "/v1/students/".$this->ident."/calendar/".$this->date_from."/".$this->date_to;
        $this->libri                   = $this->base . "/v1/students/".$this->ident."/schoolbooks";
        $this->carta                   = $this->base . "/v1/students/".$this->ident."/card";
        $this->voti                    = $this->base . "/v1/students/".$this->ident."/grades";
        $this->periodi                 = $this->base . "/v1/students/".$this->ident."/periods";
        $this->materie                 = $this->base . "/v1/students/".$this->ident."/subjects";
        $this->note                    = $this->base . "/v1/students/".$this->ident."/notes/all";
        $this->leggi_nota              = $this->base . "/v1/students/".$this->ident."/notes/{{}}/read/{{}}";//FIXME:AAAAA
        $this->panoramica_da_a         = $this->base . "/v1/students/".$this->ident."/overview/all/".$this->date_from."/".$this->date_to;
        $this->avatar                  = $this->base . "/v1/users/"   .$this->ident."/avatar";
    }

    public function setMateria($materia): void {
        $this->materia = $materia;
        $this->setValues();
    }
    public function setAgendaCode($agenda_code): void {
        $this->agenda_code = $agenda_code;
        $this->setValues();
    }
    public function setDateTo($date_to): void {
        $this->date_to = $date_to;
        $this->setValues();
    }
    public function setDateFrom($date_from): void {
        $this->date_from = $date_from;
        $this->setValues();
    }
    public function setIdent($ident): void {
        $this->ident = $ident;
        $this->setValues();
    }
}
