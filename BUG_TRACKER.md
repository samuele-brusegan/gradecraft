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

## Bug 4: Sessione condivisa tra dispositivi

**Stato:** Risolto (device binding)
**Priorità:** Critica

### Descrizione
Chiunque visiti il sito vedeva le credenziali e i dati di Samuele a causa dell'autoload automatico delle credenziali dal CredentialStore senza verifica del dispositivo.

### Fix
- Aggiunto cookie `gc_device_id` (httpOnly, 365 giorni)
- `CredentialStore::save()` ora salva anche `device_id` insieme alle credenziali
- `CredentialStore::loadStored()` verifica che il `device_id` del file corrisponda al cookie; altrimenti nega l'autoload e mostra il form di login
- Retrocompatibilità: se le credenziali non hanno `device_id`, vengono aggiornate automaticamente al prossimo load con il device ID corrente
- Solo il dispositivo originale (con lo stesso cookie) ottiene l'autologin

### File modificati
- `config/CredentialStore.php`: aggiunto `getOrCreateDeviceId()`, modificato `save()` e `loadStored()`

---

## Bug 5: Permessi server_key.php bloccano il sito

**Stato:** In risoluzione (permessi errati dopo chmod 0600)
**Priorità:** Critica

### Descrizione
Il sito restituisce errore "Permission denied" su `config/server_key.php` perché il file, dopo essere stato creato con `chmod 0600`, è leggibile solo dall'owner (UID 1000) mentre PHP-FPM gira come www-data (UID 33). Inoltre il file potrebbe già esistere con permessi 0600.

### Fix in corso
- Modificare `CryptoHelper::getServerKey()` per usare `chmod($path, 0640)` invece di `0600`
- Assicurarsi che ownership sia `www-data:www-data` o almeno gruppo `www-data`

---

## Bug 6: Agenda timeline non mostra le verifiche

**Stato:** In indagine
**Priorità:** Alta

### Descrizione
Nella pagina `/agenda` (timeline), le verifiche non vengono evidenziate (nessuna barra viola sinistra, nessun badge "VERIFICA"), mentre nella pagina `/agenda/calendar` funzionano (i pallini viola appaiono).

### Ipotesi
- L'applicazione JS `agendaTests.js` potrebbe non caricarsi o fallire a causa di errori JS
- Le variabili `lessons` e `agenda` potrebbero non essere disponibili o vuote
- IndexedDB potrebbe non inizializzare lo store `agendaAnnotations` correttamente
- La funzione `applyAllTestStyles()` potrebbe non essere chiamata o interrotta

### Prossimi test
- [ ] Aprire console browser in `/agenda` e verificare errori
- [ ] Verificare che `lessons` e `agenda` siano definite e contengano dati
- [ ] Testare `isLikelyTest()` manualmente con i testi effettivi
- [ ] Controllare se `dbService.getAllAnnotations()` risolve senza errori

---

## Bug 7: Parola chiave verifica non evidenziata in viola

**Stato:** Da implementare
**Priorità:** Media

### Descrizione
Quando una verifica viene rilevata (automaticamente o manualmente), la parola che ha triggerato il rilevamento (es. "verifica", "compito", "test") deve essere evidenziata in colore viola (test-color) nel testo della card.

### Implementazione necessaria
- In `public/js/agendaTests.js`: al momento dell'applicazione dello stile, individuare la keyword che ha fatto match in `isLikelyTest()` e avvolgerla in `<span class="test-keyword">` con `color: var(--test-color)`
- Il CSS deve definire `.test-keyword { color: var(--test-color); font-weight: 600; }`
- La stessa logica deve valere sia per timeline che per calendario (dove attualmente non c'è highlighting della singola parola)

---

## Bug 8: Gestione date/time errata (offset fuso orario)

**Stato:** In indagine
**Priorità:** Alta

### Descrizione
Il sistema segna come "Oggi" la data dell'08/04 mentre è il 07/04, e mescola eventi di oggi con quelli di domani nella stessa vista. C'è un problema di offset o fuso orario.

### Sospetti
- Le date API potrebbero essere in UTC e non convertite correttamente a Europe/Rome
- Il confronto `$currentDate` in agenda.php potrebbe usare timezone sbagliata
- Il datePicker e le chiamate AJAX potrebbero non sincronizzarsi

### Prossimi test
- [ ] Controllare timezone in PHP: `date_default_timezone_set('Europe/Rome')` è chiamato in `checkSessionExpiration()` ma non globalmente
- [ ] Verificare le date restituite dall'API overview (sono in formato locale o UTC?)
- [ ] Testare navigazione date evedere se mostra giorni corretti

---

## Cronologia modifiche
- 2026-04-06: Creato tracker, aggiunti bug 1-3
- 2026-04-07: Aggiunti bug 4 (sessione condivisa risolto), 5 (permessi server_key), 6 (timeline verifiche mancanti), 7 (keyword highlight), 8 (date offset)
