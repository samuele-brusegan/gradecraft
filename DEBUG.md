# GradeCraft - Guida al Debug e Test

Questa guida descrive gli strumenti di debug e le procedure per testare le funzionalità di GradeCraft.

## Indice

1. [Configurazione](#configurazione)
2. [Script CLI](#script-cli)
3. [Test Web](#test-web)
4. [Risoluzione Problemi](#risoluzione-problemi)
5. [Architettura](#architettura)

## Configurazione

### File `.env`

Prima di utilizzare gli strumenti di debug, configura le tue credenziali Classeviva nel file `.env` (nella root del progetto):

```env
CVV_USERNAME=tuo.email@scuola.it
CVV_PASSWORD=la_tua_password
CLASSEVIVA_YEAR=25
API_DETACH=false
DEBUG=false
```

**Note:**
- Il file `.env` è già in `.gitignore` e non verrà committato
- `CLASSEVIVA_YEAR`: anno scolastico (es: `25` per 2025-2026)
- `API_DETACH`: imposta a `true` per disabilitare le chiamate API reali (测试用)
- `DEBUG`: abilita output di debug aggiuntivo (se implementato)

### Esempio

Copiate `.env.example` in `.env` e compilate i campi:

```bash
cp .env.example .env
# Modifica .env con il tuo editor preferito
```

## Script CLI

Lo script `scripts/debug.php` fornisce una interfaccia a riga di comando per testare tutte le API senza browser.

### Comandi Disponibili

| Comando | Descrizione |
|---------|-------------|
| `login` | Effettua il login con le credenziali `.env` |
| `status` | Mostra lo stato della sessione corrente |
| `clear` | Cancella tutte le sessioni e dati salvati |
| `subjects` | Recupera e stampa l'elenco delle materie |
| `grades [subject_id]` | Recupera e stampa i voti (tutti o per materia specifica) |
| `agenda [from] [to]` | Mostra eventi agenda (default: oggi) |
| `absences [from] [to]` | Mostra assenze (default: oggi) |
| `overview [from] [to]` | Mostra panoramica giornaliera |
| `test-all` | Testa automaticamente tutti gli endpoint API |
| `api <method> [args]` | Chiamata generica a un metodo specifico |
| `shell` | Shell interattiva per test |

### Esempi di Utilizzo

```bash
# Login
php scripts/debug.php login

# Controllo stato sessione
php scripts/debug.php status

# Lista materie
php scripts/debug.php subjects

# Voti di una materia specifica
php scripts/debug.php grades 215883

# Agenda di oggi
php scripts/debug.php agenda

# Agenda di un intervallo
php scripts/debug.php agenda 20250301 20250310

# Test completo di tutti gli endpoint
php scripts/debug.php test-all

# Chiamata diretta a un API endpoint
php scripts/debug.php api lezioniToday

# Shell interattiva
php scripts/debug.php shell
debug> api lezioniDaA date_from=20250301 date_to=20250301
```

### Note sulla CLI

- Le sessioni CLI vengono salvate in `storage/cli_session.json` separate dalle sessioni web
- Per evitare conflitti, le sessioni CLI usano una directory dedicata in `storage/sessions/`
- I token di sessione hanno scadenza; se scaduti, eseguire `clear` e poi `login`

## Test Web

### Avvio Server di Sviluppo

```bash
php -S localhost:8000 -t public
```

Il server sarà accessibile all'indirizzo `http://localhost:8000`

### Route Disponibili

| URL | Controller | Azione | Descrizione |
|-----|------------|--------|-------------|
| `/` | Controller | `index` | Dashboard con materie e grafico voti |
| `/grades` | Controller | `grades` | Lista completa dei voti |
| `/grades/period` | Controller | `grades4Period` | Voti per periodo (grafico) |
| `/subjects` | Controller | `subjects` | Elenco materie con docenti |
| `/agenda` | Controller | `agenda` | Agenda eventi (default: oggi) |
| `/settings` | Controller | `settings` | Pagina impostazioni (debug) |
| `/account` | Controller | `account` | Profilo utente |
| `/api` | ApiController | `classeviva` | Endpoint API JSON |
| `/login` | ApiController | `login` | Login (POST) |
| `/demo/` | Demo App | – | Explorer API interattivo |

### Checklist di Test

- [ ] **Dashboard `/`**: Carica materie e voti, grafico visibile
- [ ] **Voti `/grades`**: Tabella voti con filtri (se implementati)
- [ ] **Voti per periodo `/grades/period`**: Grafico per periodo
- [ ] **Materie `/subjects`**: Lista materie con docenti
- [ ] **Agenda `/agenda`**: Eventi del giorno, navigazione date
- [ ] **Impostazioni `/settings`**: Info sessione e cookies
- [ ] **Account `/account`**: Profilo con avatar e dati
- [ ] **Login**: Form funzionante, reindirizzamento corretto
- [ ] **Logout**: (se implementato) sessione eliminata

### Debug in Browser

- Aprire Console (F12) per vedere errori JavaScript
- Verificare tabella `gradecraft` in IndexedDB per dati offline
- Controllare tab Cookies per token salvati
- Tab Network per chiamate API e risposte

## Risoluzione Problemi

### Errore: `incompatible domain`

**Sintomo:** L'API risponde con `249:CvvRestApi/incompatible domain`.

**Cause possibili:**
- Anno scolastico (`CLASSEVIVA_YEAR`) errato
- Token scaduto o emesso per dominio diverso

**Soluzione:**
1. Verificare che `CLASSEVIVA_YEAR` in `.env` corrisponda all'anno corrente (es: `25` per 2025-2026)
2. Eseguire `php scripts/debug.php clear && php scripts/debug.php login` per rinnovare il token

### Errore: `NOT_LOGGED_IN`

**Sintomo:** L'API restituisce errore di autenticazione.

**Soluzione:**
1. Eseguire `php scripts/debug.php login`
2. Verificare che le credenziali in `.env` siano corrette
3. Controllare che il token non sia scaduto (`status`)

### La dashboard non carica dati

**Verifiche:**
1. Aprire Console browser per errori JS
2. Verificare chiamate API in tab Network
3. Controllare che le sessioni siano attive (`/settings`)
4. Provare CLI: `php scripts/debug.php subjects` e `grades`

### Problemi di sessione

**Sintomo:** Login effettuato ma sessione non persiste.

**Soluzione:**
1. Verificare che `session.save_path` sia scrivibile
2. Controllare cookies nel browser (`users`)
3. Cancellare sessioni: `php scripts/debug.php clear`
4. Ricaricare pagina e rieseguire login

## Architettura

### Modifiche Apportate

**Costanti globali sostituite:**
- `const CVV_URLS` → `$GLOBALS['CVV_URLS']`
- `const CVV_API` → `$GLOBALS['CVV_API']`

Motivo: PHP non permette `const` per oggetti. Usare variabili globali.

**Struttura aggiornata in `public/index.php`:**
```php
$GLOBALS['CVV_URLS'] = new Collegamenti();
$GLOBALS['CVV_URLS']->setGeneric('year', $year);
$GLOBALS['CVV_API'] = new CvvIntegration();
define('USR', $GLOBALS['CVV_API']->loadUser());
```

**Note:** L'anno scolastico viene letto da `.env` (variabile `CLASSEVIVA_YEAR`) con default `'24'`.

## Supporto

Per problemi o domande, consultare:
- `CLAUDE.md` – linee guida progetto
- `demo/TODO.md` – stato implementazione API
- Commenti nel codice sorgente

---

Ultimo aggiornamento: 2026-03-31
