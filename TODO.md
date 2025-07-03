# Todo
## Backend
- [ ] Finire l'implementazione dell'API (2/18) *Considerando solo il primo livello*
  - [X] Login  - {ident, firstName, lastName, showPwdChangeReminder, tokenAP, token, release, expire} 
  - [X] Status - {release:time, ident:str, expire:time, remains:seconds}
  - [~] Ticket - {ticket:Stringaccia}
  - [ ] Documents
    - [ ] Check
    - [ ] Read
  - [ ] Assenze (0/3) 
  - [ ] Agenda (0/2)
  - [ ] Didattica
    -  [ ] Elemento
  - [ ] Bacheca (noticeboard)
    - [ ] Leggi
    - [ ] Allega
  - [ ] Lezioni
    - [ ] Oggi
    - [ ] Giorno
    - [ ] Da - A
    - [ ] Da - A + Materia
  - [ ] Calendar
  - [ ] Libri (Esiste?!?!)
  - [ ] Card
  - [X] Voti - PerOgni {subjectId, subjectCode, subjectDesc, evtId, evtCode, evtDate, decimalValue, displayValue, displaPos, notesForFamily, color, canceled, underlined, periodPos, periodDesc, periodLabel, componentPos, componentDesc, weightFactor, skillId, gradeMasterId, skillDesc, skillCode, skillMasterId, skillValueDesc, skillValueShortDesc, skillValueNote, oldskillId, oldskillDesc, noAverage,teacherName}
  - [ ] Periodi
  - [X] Materie - PerOgni {id, description, order, teachers -> {teacherId, teacherName}}
  - [ ] Note
    - [ ] Leggi nota
  - [ ] Overview
  - [ ] Avatar

## Frontend
- [ ] Iniziare la grafica