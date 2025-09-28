# Remote Models

## Login
### URL
`$this->base . "/v1/auth/login",`
### Data
```json lines
{
    "ident":"S10314304O",
    "firstName":"SAMUELE",
    "lastName":"BRUSEGAN",
    "showPwdChangeReminder":false,
    "tokenAP":"...",
    "token":"...",
    "release":"2025-07-05T00:58:01+02:00",
    "expire":"2025-07-05T02:28:01+02:00"
}
```


## Grades
### URL
`$this->base . "/v1/students/" . $this->ident . "/grades",`
### Data
```json lines
{
    "grades": [
        {
            "subjectId": 408761,
            "subjectCode": null,
            "subjectDesc": "EDUCAZIONE CIVICA",
            "evtId": 2274467,
            "evtCode": "GRV0",
            "evtDate": "2024-11-30",
            "decimalValue": 8.5,
            "displayValue": "8½",
            "displaPos": 1,
            "notesForFamily": "Verifica scritta di educazione Civica. tiene conto della presentazione fatta in laboratorio; del risultato della prova scritta del test sulla piattaforma",
            "color": "green",
            "canceled": false,
            "underlined": false,
            "periodPos": 1,
            "periodDesc": "primo trimestre",
            "periodLabel": "primo trimestre",
            "componentPos": 1,
            "componentDesc": "Scritto/Grafico",
            "weightFactor": 1,
            "skillId": 0,
            "gradeMasterId": 0,
            "skillDesc": null,
            "skillCode": null,
            "skillMasterId": 0,
            "skillValueDesc": "",
            "skillValueShortDesc": null,
            "skillValueNote": "",
            "oldskillId": 0,
            "oldskillDesc": "",
            "noAverage": false,
            "teacherName": "********* SERGIO"
        },
        //... altri voti
    ]
}
```

## Subjects
### URL
`$this->base . "/v1/students/" . $this->ident . "/subjects",`
### Data
```json lines
{
    "subjects": [
        {
            "id": 215883,
            "description": "TECNOLOGIE E PROGETTAZIONE DI SISTEMI INFORMATICI E DI TELECOMUNICAZIONI",
            "order": 10,
            "teachers": [
                {
                    "teacherId": "--------",
                    "teacherName": "***** ALVISE"
                },
                {
                    "teacherId": "--------",
                    "teacherName": "**** MARIAROSA"
                }
            ]
        }
        //... altre materie
    ]
}
```

## Status
### URL
`$this->base . "/v1/auth/status"`
### Data
```json lines
{
    "ident": "S10314304O",
    "expire": "2025-07-05T00:16:35+02:00",
    "release": "2025-07-04T22:46:35+02:00",
    "remains": 1790
}
```

## Ticket
### URL
`$this->base . "/v1/auth/ticket"`
### Data
```json lines
{
    "ticket": "**********",
    "len": 10,
    "ulen": 10,
    "md5": "**********  32 chars  **********"
}
```

## Lessons
### URL
`$this->base . "/v1/students/" . $this->ident . "/lessons/today",`
`$this->base . "/v1/students/" . $this->ident . "/lessons/" . $this->date_from,`
`$this->base . "/v1/students/" . $this->ident . "/lessons/" . $this->date_from . "/" . $this->date_to,`
`$this->base . "/v1/students/" . $this->ident . "/lessons/" . $this->date_from . "/" . $this->date_to . "/" . $this->materia,`
### Data
```json lines
[
  {
    "evtId": 25210091,
    "evtDate": "2025-05-08",
    "evtCode": "LSF0",
    "evtHPos": 4,
    "evtDuration": 1,
    "classDesc": "3IB INFORMATICA ART. INFORMATICA",
    "authorName": "****** MARCO",
    "subjectId": 215867,
    "subjectCode": null,
    "subjectDesc": "INFORMATICA",
    "lessonType": "Attività di laboratorio",
    "lessonArg": "Conclusione dell'esercizio sulla gestione prestiti di una biblioteca"
  },
  //... altre lezioni
]
```

## Card
### URL
`$this->base . "/v1/students/" . $this->ident . "/card",`
### Data
```json lines
{
    "ident": "S12345678A",
    "usrType": "S",
    "usrId": 12345678,
    "miurSchoolCode": "VETF04000T",
    "miurDivisionCode": "VETF04000T",
    "firstName": "SAMUELE",
    "lastName": "BRUSEGAN",
    "birthDate": "YYYY-MM-GG",
    "fiscalCode": "**********",
    "schCode": "VEIT0007",
    "schName": "ISTITUTO TECNICO INDUSTRIALE STATALE",
    "schDedication": " C. ZUCCANTE ",
    "schCity": "VENEZIA - MESTRE",
    "schProv": "VE"
}
```

## Bacheca (noticeboard)
### URL
`'bacheca'                : $this->base . "/v1/students/" . $this->ident . "/noticeboard",`
`'bacheca_leggi'          : $this->base . "/v1/students/" . $this->ident . "/noticeboard/read/" . $this->eventCode . "/" . $this->pubId . "/101",`
`'bacheca_allega'         : $this->base . "/v1/students/" . $this->ident . "/noticeboard/attach/{{}}/{{}}/101",` (informazioni attuali incomplete)
`'bacheca_allega_esterno' : "https://web.spaggiari.eu/sif/app/default/bacheca_personale.php?action=file_download&com_id={{}}",` (informazioni attuali incomplete)
### Data
```json lines
{
    "items": [
        {
            "pubId": 28791122,
            "pubDT": "2025-05-19T00:00:00+02:00",
            "readStatus": false,
            "evtCode": "CF_SDG",
            "cntId": 2558648,
            "cntValidFrom": "2025-05-19",
            "cntValidTo": "2025-08-31",
            "cntValidInRange": true,
            "cntStatus": "active",
            "cntTitle": "CIRC-551 Sciopero 23 e 24 maggio 2025 - Comunicazione alle Famiglie.",
            "cntCategory": "Documenti - Segreteria Digitale",
            "cntHasChanged": false,
            "cntHasAttach": true,
            "needJoin": false,
            "needReply": false,
            "needFile": false,
            "needSign": false,
            "dinsert_allegato": "2024-11-07 06:35:57",
            "attachments": [
                {
                  "fileName": "CIRC-551 Sciopero 23 e 24 -05-2025- Famiglie.pdf",
                  "attachNum": 1
                },
                {
                  "fileName": "scheda+informativa+23+e+24+maggio+2025.pdf",
                  "attachNum": 2
                },
                {
                  "fileName": "annotazione_CIRC-551 Sciopero 23 e 24 -05-2025- Famiglie.pdf",
                  "attachNum": 3
                }
            ]
        },
        //... altre circolari
    ]
}
```

## Agenda
### URL
`$this->base . "/v1/students/" . $this->ident . "/agenda/all/"       . $this->date_from  . "/" . $this->date_to,`
`$this->base . "/v1/students/" . $this->ident . "/agenda/"           . $this->event_code. "/". $this->date_from . "/" . $this->date_to,`
### Data
```json lines
{
    "agenda": [
        {
            "evtId": 2507671,
            "evtCode": "AGHW", //Event code per l'url(2)
            "evtDatetimeBegin": "2025-06-03T10:00:00+02:00",
            "evtDatetimeEnd": "2025-06-03T11:00:00+02:00",
            "isFullDay": false,
            "notes": "Quiz game di Storia, a squadre, su quanto spiegato in classe, vedi qui: https://classroom.google.com/c/***/detailsnnoppure sul testo pp. 387-389.",
            "authorName": "******** LORENZO",
            "classDesc": "3IB INFORMATICA ART. INFORMATICA",
            "subjectId": 215881,
            "subjectDesc": "STORIA",
            "homeworkId": null
        },
    ]
}
```

## Overview
### URL
`$this->base . "/v1/students/" . $this->ident . "/overview/all/" . $this->date_from . "/" . $this->date_to,`
### Data
```json lines
{
    "virtualClassesAgenda": [
        //???
    ],
    "lessons": [
        //Come in lessons
    ],
    "agenda": [
        //Come in agenda
    ],
    "events": [
        //???
    ],
    "grades": [
        //Come in grades
    ],
    "notes": {
        "NTTE": [],
        "NTCL": [],
        "NTWN": [],
        "NTST": []
    }
}
```

## Assenze
### URL
`$this->base . "/v1/students/" . $this->ident . "/absences/details",`
`$this->base . "/v1/students/" . $this->ident . "/absences/details/" . $this->date_from,`
`$this->base . "/v1/students/" . $this->ident . "/absences/details/" . $this->date_from  . "/" . $this->date_to,`
### Data
```json lines
{
    "events": [
        {
            "evtId": 275196,
            "evtCode": "ABA0",
            "evtDate": "YYYY-MM-GG",
            "evtHPos": null,
            "evtValue": null,
            "isJustified": true,
            "justifReasonCode": "C",
            "justifReasonDesc": "Altri motivi",
            "hoursAbsence": [],
            "webJustifStatus": 1
        },
        //Altre assenze
    ]
}
```

## Documents
### URL
`$this->base . "/v1/students/" . $this->ident . "/documents",`
`$this->base . "/v1/students/" . $this->ident . "/documents/check/"  . $this->document_id,`
`$this->base . "/v1/students/" . $this->ident . "/documents/read/"   . $this->document_id,`
### Data
```json lines
{
    "documents": [
        {
            "hash": "*************** 40 chars ***************",
            "desc": "SOL lettera corsi recuperi 1 periodo_24_25 (Primo Periodo)"
        },
        {
            "hash": "*************** 40 chars ***************",
            "desc": "Pagella (Formato A3)"
        },
        {
            "hash": "*************** 40 chars ***************",
            "desc": "Pagella Religione (Formato Piccolo) Finale"
        },
        {
            "hash": "*************** 40 chars ***************",
            "desc": "SOL lettera corsi recupero_2_periodo_24_25 (Secondo Periodo)"
        }
    ],
    "schoolReports": [
        {
            "desc": "Recuperi",
            "confirmLink": "https://web.spaggiari.eu/sol/app/default/...",
            "viewLink": "https://web.spaggiari.eu/sol/app/default/..."
        },
        {
            "desc": "Pagella (Web) Primo Periodo",
            "confirmLink": "https://web.spaggiari.eu/sol/app/default/...",
            "viewLink": "https://web.spaggiari.eu/sol/app/default/..."
        },
        {
            "desc": "Pagella (Web) Finale",
            "confirmLink": "https://web.spaggiari.eu/sol/app/default/...",
            "viewLink": "https://web.spaggiari.eu/sol/app/default/..."
        }
    ]
}
```

## Calendar
### URL
`$this->base . "/v1/students/" . $this->ident . "/calendar/all",`
`$this->base . "/v1/students/" . $this->ident . "/calendar/". $this->date_from . "/" . $this->date_to,`
### Data
```json lines
{
    "calendar": [
      {dayDate: '2024-07-01', dayOfWeek: 2, dayStatus: 'HD'},
      {dayDate: '2024-07-02', dayOfWeek: 3, dayStatus: 'HD'},
      {dayDate: '2024-07-03', dayOfWeek: 4, dayStatus: 'HD'},
      {dayDate: '2024-07-04', dayOfWeek: 5, dayStatus: 'HD'},
      {dayDate: '2024-07-05', dayOfWeek: 6, dayStatus: 'HD'},
      {dayDate: '2024-07-06', dayOfWeek: 7, dayStatus: 'HD'},
      {dayDate: '2024-07-07', dayOfWeek: 1, dayStatus: 'NW'},//È domenica
      //...
      {dayDate: '2025-01-26', dayOfWeek: 1, dayStatus: 'NW'},
      {dayDate: '2025-01-27', dayOfWeek: 2, dayStatus: 'SD'},
      {dayDate: '2025-01-28', dayOfWeek: 3, dayStatus: 'SD'},
      {dayDate: '2025-01-29', dayOfWeek: 4, dayStatus: 'SD'},
      {dayDate: '2025-01-30', dayOfWeek: 5, dayStatus: 'SD'},
      {dayDate: '2025-01-31', dayOfWeek: 6, dayStatus: 'SD'},
      {dayDate: '2025-02-01', dayOfWeek: 7, dayStatus: 'SD'},
      //... Tutto l'anno fino a:
      {dayDate: '2025-07-25', dayOfWeek: 6, dayStatus: 'HD'},
      {dayDate: '2025-07-26', dayOfWeek: 7, dayStatus: 'HD'},
      {dayDate: '2025-07-27', dayOfWeek: 1, dayStatus: 'NW'},
      {dayDate: '2025-07-28', dayOfWeek: 2, dayStatus: 'HD'},
      {dayDate: '2025-07-29', dayOfWeek: 3, dayStatus: 'HD'},
      {dayDate: '2025-07-30', dayOfWeek: 4, dayStatus: 'HD'},
      {dayDate: '2025-07-31', dayOfWeek: 5, dayStatus: 'HD'}
    ]
}
```

## Libri
### URL
`$this->base . "/v1/students/" . $this->ident . "/schoolbooks",`
### Data
```json lines
{
    "schoolbooks": {
        "courseId": 205,
        "courseDesc": "INFORMATICA",
        "books": [
            {
            "bookId": 156950,
            "isbnCode": "9780194817851",
            "title": "OXF GRAMMAR 360°",
            "subheading": "SB S/C + EBK",
            "volume": "U",
            "author": "AA VV",
            "publisher": "OXFORD UNIVERSITY PRESS",
            "subjectDesc": "INGLESE GRAMMATICA",
            "price": 28,
            "toBuy": false,
            "newAdoption": false,
            "alreadyOwned": true,
            "alreadyInUse": true,
            "recommended": false,
            "recommendedFor": null,
            "coverUrl": null,
            "publisherUnlockCode": ""
            }, 
            //... altri libri
        ]
    }
}
```

## Periodi
### URL
`$this->base . "/v1/students/" . $this->ident . "/periods",`
### Data
```json lines
{
    "periods": [
        {
            "periodCode": "Q1",
            "periodPos": 1,
            "periodDesc": "primo trimestre",
            "periodLabel": "primo trimestre",
            "isFinal": false,
            "dateStart": "2024-09-09",
            "dateEnd": "2024-12-21",
            "miurDivisionCode": null
        },
        {
            "periodCode": "Q3",
            "periodPos": 3,
            "periodDesc": "secondo pentamestre",
            "periodLabel": "secondo pentamestre",
            "isFinal": true,
            "dateStart": "2024-12-22",
            "dateEnd": "2025-06-07",
            "miurDivisionCode": null
        }
    ]
}
```

## Note
### URL
`$this->base . "/v1/students/" . $this->ident . "/notes/all",`
`$this->base . "/v1/students/" . $this->ident . "/notes/".$this->noteType."/read/".$this->elementId,`
### Data
```json lines
{
    "NTTE": [
    {
        "evtId": 12345678,
        "evtText": "Descrizione della nota",
        "evtDate": "YYYY-MM-GG",
        "authorName": "PROFESSORE",
        "readStatus": true
    }
    ],
    "NTCL": [
    {
        "evtId": 12345678,
        "evtText": "Descrizione della nota",
        "evtDate": "YYYY-MM-GG",
        "authorName": "PROFESSORE",
        "readStatus": true
    }
    ],
    "NTWN": [
        //???
    ],
    "NTST": [
        //???
    ]
}
```
