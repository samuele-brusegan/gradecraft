# BUG_TRACKER.md

Tracciamento bug, ipotesi e test per GradeCraft.

---

## Bug 1: Rilevamento verifiche non funziona

**Stato:** In indagine
**Priorità:** Alta

### Descrizione
Il rilevamento automatico delle verifiche nell'agenda (pagina `/agenda`) non funziona. Gli eventi che dovrebbero essere segnati come "VERIFICA" non lo sono, né in modo automatico (matching keyword) né manuale (toggle manuale).

### Analisi
- **File coinvolti:**
  - `public/js/agendaTests.js` — logica JS con keyword matching (`verifica`, `test`, `compito`, `interrogazione`, `prova`, `quiz`, `valutazione`, `esame`)
  - `app/views/agenda.php` — HTML con `toggleTest()` onclick buttons
  - `public/js/dbApi.js` — IndexedDBService con store `agendaAnnotations`

- **Flusso previsto:**
  1. Page load → `applyAllTestStyles()` in `<script type="module">` (agenda.php:253-278)
  2. Legge annotations da IndexedDB
  3. Per ogni evento: controlla se annotation esiste, altrimenti auto-detect via `isLikelyTest()`
  4. Applica/rimuove `.test-event` e badge VERIFICA
  5. Click su toggle → `window.toggleTest(key)` → salva annotation → aggiorna UI

### Ipotesi da verificare
1. **IndexedDB non si inizializza** — lo store `agendaAnnotations` potrebbe non esistere o creare errori
2. **`isLikelyTest()` fallisce** — keyword matching potrebbe non funzionare con il testo effettivo delle API
3. **Toggle non registrato** — `window.toggleTest` potrebbe non essere accessibile
4. **API overview restituisce dati diversi** — i campi `subjectDesc`, `notes`, `lessonArg`, ecc. potrebbero essere nulli o diversi dal previsto

### Prossimi test da fare
- [ ] Aprire la console del browser e verificare errori JS al caricamento `/agenda`
- [ ] Dumpare i valori di `lessons` e `agenda` nella pagina per vedere i campi effettivi
- [ ] Verificare che `dbService.getAllAnnotations()` non fallisca
- [ ] Controllare se i testi degli eventi contengono le keyword attese

---

## Bug 2: Calendario — "esiste solo la tab"

**Stato:** In indagine
**Priorità:** Media

### Descrizione
L'utente segnala che il calendario non è presente, esiste solo la tab. Da verificare se la pagina `/agenda/calendar` mostra effettivamente contenuto o appare vuota.

### Stato attuale del codice
- **Route:** `/agenda/calendar` → `Controller::agendaCalendar()` (definita, riga 457-505 in Controller.php)
- **View:** `app/views/agenda_calendar.php` — completa con calendario a griglia, navigazione mensile, popup dettaglio
- **Route:** `/calendar` → `Controller::calendar()` — calendario scolastico (giorni SD/HD/NW) separato dall'agenda
- **Tabs:** In `agenda.php` e `agenda_calendar.php` ci sono i link per switchare Timeline/Calendario

### Ipotesi da verificare
1. **API overview restituisce vuoto** — se `$allEvents` è vuoto, il calendario mostra solo la griglia senza eventi (sembra "esistere solo la tab")
2. **Errore JS nel rendering** — `renderCalendar()` potrebbe fallire silenziosamente
3. **La pagina `/calendar` (calendario scolastico) è poco utile** — mostra solo i giorni scolastici come lista, non un calendario visivo
4. **IndexedDB initialization** — `new IndexedDBService('gradecraft', 1, { agendaAnnotations: {} })` potrebbe generare errori di versione se il DB ha già versione > 1

### Prossimi test da fare
- [ ] Aprire `/agenda/calendar` nel browser e verificare se la griglia del calendario viene renderizzata
- [ ] Controllare se `allEvents` JSON contiene dati
- [ ] Verificare che non ci siano errori JS nella console
- [ ] Capire se il problema è `/agenda/calendar` (calendario eventi agenda) o `/calendar` (calendario scolastico)

---

## Bug 3: Server key permission denied (deploy)

**Stato:** Risolto (workaround disponibile)
**Priorità:** Media

### Descrizione
`file_put_contents` fallisce su `/var/www/html/gradecraft/config/server_key.php` per permessi negati.

### Fix
Eseguire sul server:
```bash
sudo chown -R www-data:www-data /var/www/html/gradecraft/config/
sudo chmod -R 775 /var/www/html/gradecraft/config/
```

Oppure generare il file manualmente con `scripts/generate_server_key.php`.

### Prossimi test da fare
- [ ] Verificare che i permessi siano corretti dopo il fix

---

## Cronologia modifiche
- 2026-04-06: Creato tracker, aggiunti bug 1-3
