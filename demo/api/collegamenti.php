<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */


//Grazie a Lioydiano per i link: https://github.com/Lioydiano/Classeviva

namespace api;
class Collegamenti {

    public string $base;

    public mixed $collegamenti;
    private string $ident = "";
    private string $date_from = "";
    private string $date_to = "";
    private string $agenda_code = "";
    private string $materia = "";
    private string $pubId = "";
    private string $eventCode = "";
    private string $year = "";
    private $document_id;
    private $elementId;
    private $noteType;

    public function __construct() {
        $this->setValues();
    }

    private function setValues(): void {
        $this->base = "https://web" . $this->year . ".spaggiari.eu/rest";
        $this->collegamenti = [
            'base' => $this->base,
            'login' => $this->base . "/v1/auth/login",
            'stato' => $this->base . "/v1/auth/status",
            'biglietto' => $this->base . "/v1/auth/ticket",
            'documenti' => $this->base . "/v1/students/" . $this->ident . "/documents",
            'controllo_documento' => $this->base . "/v1/students/" . $this->ident . "/documents/check/" . $this->document_id,               /*FIXME:AAAAA*/
            'leggi_documento' => $this->base . "/v1/students/" . $this->ident . "/documents/read/" . $this->document_id,               /*FIXME:AAAAA*/
            'assenze' => $this->base . "/v1/students/" . $this->ident . "/absences/details",
            'assenze_da' => $this->base . "/v1/students/" . $this->ident . "/absences/details/" . $this->date_from,
            'assenze_da_a' => $this->base . "/v1/students/" . $this->ident . "/absences/details/" . $this->date_from . "/" . $this->date_to,
            'agenda_da_a' => $this->base . "/v1/students/" . $this->ident . "/agenda/all/" . $this->date_from . "/" . $this->date_to,
            'agenda_codice_da_a' => $this->base . "/v1/students/" . $this->ident . "/agenda/" . $this->agenda_code . "/" . $this->date_from . "/" . $this->date_to,
            'didattica' => $this->base . "/v1/students/" . $this->ident . "/didactics",
            'didattica_elemento' => $this->base . "/v1/students/" . $this->ident . "/didactics/item/" . $this->elementId,                                  /*FIXME:AAAAA*/
            'bacheca' => $this->base . "/v1/students/" . $this->ident . "/noticeboard",
            'bacheca_leggi' => $this->base . "/v1/students/" . $this->ident . "/noticeboard/read/" . $this->eventCode . "/" . $this->pubId . "/101",
            'bacheca_allega' => $this->base . "/v1/students/" . $this->ident . "/noticeboard/attach/{{}}/{{}}/101",                     /*FIXME:AAAAA*/
            'bacheca_allega_esterno' => "https://web.spaggiari.eu/sif/app/default/bacheca_personale.php?action=file_download&com_id={{}}",      /*FIXME:AAAAA*/
            'lezioni' => $this->base . "/v1/students/" . $this->ident . "/lessons/today",
            'lezioni_giorno' => $this->base . "/v1/students/" . $this->ident . "/lessons/" . $this->date_from,
            'lezioni_da_a' => $this->base . "/v1/students/" . $this->ident . "/lessons/" . $this->date_from . "/" . $this->date_to,
            'lezioni_da_a_materia' => $this->base . "/v1/students/" . $this->ident . "/lessons/" . $this->date_from . "/" . $this->date_to . "/" . $this->materia,
            'calendario' => $this->base . "/v1/students/" . $this->ident . "/calendar/all",
            'calendario_da_a' => $this->base . "/v1/students/" . $this->ident . "/calendar/" . $this->date_from . "/" . $this->date_to,
            'libri' => $this->base . "/v1/students/" . $this->ident . "/schoolbooks",
            'carta' => $this->base . "/v1/students/" . $this->ident . "/card",
            'voti' => $this->base . "/v1/students/" . $this->ident . "/grades",
            'periodi' => $this->base . "/v1/students/" . $this->ident . "/periods",
            'materie' => $this->base . "/v1/students/" . $this->ident . "/subjects",
            'note' => $this->base . "/v1/students/" . $this->ident . "/notes/all",
            'leggi_nota' => $this->base . "/v1/students/" . $this->ident . "/notes/" . $this->noteType . "/read/" . $this->elementId,                                 /*FIXME:AAAAA*/
            'panoramica_da_a' => $this->base . "/v1/students/" . $this->ident . "/overview/all/" . $this->date_from . "/" . $this->date_to,
            'avatar' => $this->base . "/v1/users/" . $this->ident . "/avatar"
        ];
    }

    public function setIdent($ident): void {
        $this->ident = $ident;
        $this->setValues();
    }

    public function setGeneric($propertyName, $value): void {
        $this->$propertyName = $value;
        $this->setValues();
    }
}
