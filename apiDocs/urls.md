`base____________________= "https://web.spaggiari.eu/rest";`  
`login___________________= base + "/v1/auth/login";`  
`stato___________________= base + "/v1/auth/status";`  
`biglietto_______________= base + "/v1/auth/ticket";`  
`documenti_______________= base + "/v1/students/STUDENT_IDENT_HERE/documents";`  
`controllo_documento_____= base + "/v1/students/STUDENT_IDENT_HERE/documents/check/ELEMENT_ID";`  
`leggi_documento_________= base + "/v1/students/STUDENT_IDENT_HERE/documents/read/ELEMENT_ID";`  
`assenze_________________= base + "/v1/students/STUDENT_IDENT_HERE/absences/details";`  
`assenze_da______________= base + "/v1/students/STUDENT_IDENT_HERE/absences/details/DATE_FROM";`  
`assenze_da_a____________= base + "/v1/students/STUDENT_IDENT_HERE/absences/details/DATE_FROM/DATE_TO";`  
`agenda_da_a_____________= base + "/v1/students/STUDENT_IDENT_HERE/agenda/all/DATE_FROM/DATE_TO";`  
`agenda_codice_da_a______= base + "/v1/students/STUDENT_IDENT_HERE/agenda/DATE_FROM/DATE_TO/MAYBE_A_CODE";`  
`didattica_______________= base + "/v1/students/STUDENT_IDENT_HERE/didactics";`  
`didattica_elemento______= base + "/v1/students/STUDENT_IDENT_HERE/didactics/item/ELEMENT_ID";`  
`bacheca_________________= base + "/v1/students/STUDENT_IDENT_HERE/noticeboard";`  
`bacheca_leggi___________= base + "/v1/students/STUDENT_IDENT_HERE/noticeboard/read/{{}}/{{}}/101";`  
`bacheca_allega__________= base + "/v1/students/STUDENT_IDENT_HERE/noticeboard/attach/{{}}/{{}}/101";`

`bacheca_allega_esterno__= "https://web.spaggiari.eu/sif/app/default/bacheca_personale.php?action=file_download&com_id={{}}";`

`lezioni_________________= base + "/v1/students/STUDENT_IDENT_HERE/lessons/today";`  
`lezioni_giorno__________= base + "/v1/students/STUDENT_IDENT_HERE/lessons/DATE";`  
`lezioni_da_a____________= base + "/v1/students/STUDENT_IDENT_HERE/lessons/DATE_FROM/DATE_TO";`  
`lezioni_da_a_materia____= base + "/v1/students/STUDENT_IDENT_HERE/lessons/DATE_FROM/DATE_TO/SUBJECT";`  
`calendario______________= base + "/v1/students/STUDENT_IDENT_HERE/calendar/all";`  
`calendario_da_a_________= base + "/v1/students/STUDENT_IDENT_HERE/calendar/DATE_FROM/DATE_TO";`  
`libri___________________= base + "/v1/students/STUDENT_IDENT_HERE/schoolbooks";`  
`carta___________________= base + "/v1/students/STUDENT_IDENT_HERE/card";`  
`voti____________________= base + "/v1/students/STUDENT_IDENT_HERE/grades";`  
`periodi_________________= base + "/v1/students/STUDENT_IDENT_HERE/periods";`  
`materie_________________= base + "/v1/students/STUDENT_IDENT_HERE/subjects";`  
`note____________________= base + "/v1/students/STUDENT_IDENT_HERE/notes/all";`  
`leggi_nota______________= base + "/v1/students/STUDENT_IDENT_HERE/notes/{{}}/read/{{}}";`  
`panoramica_da_a_________= base + "/v1/students/STUDENT_IDENT_HERE/overview/all/DATE_FROM/DATE_TO";`  
`avatar__________________= base + "/v1/users/STUDENT_IDENT_HERE/avatar";`

- Student Ident è il "nome utente default (X000000000)"
- Element ID è reperibile tra gli attributi degli oggetti restituiti dalla richiesta più generale
  - Ad esempio se mi serve uno specifico ELEMENT_ID dei documenti posso trovarlo chiedendo a `documenti`