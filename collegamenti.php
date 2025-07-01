<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

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

    public function __construct() {
        $this->setValues();
    }
    private function setValues(): void {
        $this->base                    = "https://web.spaggiari.eu/rest";
        $this->login                   = $this->base . "/v1/auth/login";
        $this->stato                   = $this->base . "/v1/auth/status";
        $this->biglietto               = $this->base . "/v1/auth/ticket";
        $this->documenti               = $this->base . "/v1/students/{{}}/documents";
        $this->controllo_documento     = $this->base . "/v1/students/{{}}/documents/check/{{}}";
        $this->leggi_documento         = $this->base . "/v1/students/{{}}/documents/read/{{}}";
        $this->assenze                 = $this->base . "/v1/students/{{}}/absences/details";
        $this->assenze_da              = $this->base . "/v1/students/{{}}/absences/details/{{}}";
        $this->assenze_da_a            = $this->base . "/v1/students/{{}}/absences/details/{{}}/{{}}";
        $this->agenda_da_a             = $this->base . "/v1/students/{{}}/agenda/all/{{}}/{{}}";
        $this->agenda_codice_da_a      = $this->base . "/v1/students/{{}}/agenda/{{}}/{{}}/{{}}";
        $this->didattica               = $this->base . "/v1/students/{{}}/didactics";
        $this->didattica_elemento      = $this->base . "/v1/students/{{}}/didactics/item/{{}}";
        $this->bacheca                 = $this->base . "/v1/students/{{}}/noticeboard";
        $this->bacheca_leggi           = $this->base . "/v1/students/{{}}/noticeboard/read/{{}}/{{}}/101";
        $this->bacheca_allega          = $this->base . "/v1/students/{{}}/noticeboard/attach/{{}}/{{}}/101";

        $this->bacheca_allega_esterno  = "https://web.spaggiari.eu/sif/app/default/bacheca_personale.php?action=file_download&com_id={{}}";
        $this->lezioni                 = $this->base . "/v1/students/{{}}/lessons/today";
        $this->lezioni_giorno          = $this->base . "/v1/students/{{}}/lessons/{{}}";
        $this->lezioni_da_a            = $this->base . "/v1/students/{{}}/lessons/{{}}/{{}}";
        $this->lezioni_da_a_materia    = $this->base . "/v1/students/{{}}/lessons/{{}}/{{}}/{{}}";
        $this->calendario              = $this->base . "/v1/students/{{}}/calendar/all";
        $this->calendario_da_a         = $this->base . "/v1/students/{{}}/calendar/{{}}/{{}}";
        $this->libri                   = $this->base . "/v1/students/{{}}/schoolbooks";
        $this->carta                   = $this->base . "/v1/students/{{}}/card";
        $this->voti                    = $this->base . "/v1/students/{{}}/grades";
        $this->periodi                 = $this->base . "/v1/students/{{}}/periods";
        $this->materie                 = $this->base . "/v1/students/{{}}/subjects";
        $this->note                    = $this->base . "/v1/students/{{}}/notes/all";
        $this->leggi_nota              = $this->base . "/v1/students/{{}}/notes/{{}}/read/{{}}";
        $this->panoramica_da_a         = $this->base . "/v1/students/{{}}/overview/all/{{}}/{{}}";
        $this->avatar                  = $this->base . "/v1/users/{{}}/avatar";
    }
}
