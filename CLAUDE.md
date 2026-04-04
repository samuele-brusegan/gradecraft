# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**GradeCraft** is a PHP web application that integrates with Classeviva (Italian school management system). It displays student grades, subjects, agenda events, noticeboard items, absences, notes, documents, and calendar data with offline capabilities via IndexedDB.

**Tech Stack:**
- Backend: PHP 8.1+ (no framework)
- Frontend: Vanilla JavaScript ES6, Web Components, Chart.js
- Styling: Bootstrap 5, Tailwind CSS
- Data Storage: IndexedDB (client-side), PHP sessions
- APIs: Classeviva REST API via cURL

## Architecture

### Request Flow

```
public/index.php (front controller)
    ↓
Router (app/core/Router.php) dispatches based on routes.php
    ↓
Controller → ApiController (or Controller for pages)
    ↓
CvvIntegration → User (cvv namespace)
    ↓
Collegamenti (URL builder) → Classeviva API
```

### Key Components

**Core Routing** (`app/core/Router.php`):
- Router con supporto parametri: `$router->add('/path/:param', 'Controller', 'action')`
- I segmenti che iniziano con `:` sono catturati come parametri e passati come argomenti al metodo
- Route fallback a query params ancora supportate (es. `/grades?subject=X`)
- Routes definite in `public/routes.php`
- 404 handling includes common layout

**Controllers** (`app/controllers/`):
- `Controller.php`: Main page controllers (dashboard, grades, gradeSubject, absences, notes, documents, noticeboard, noticeboardRead, noticeboardAttach, calendar, settings, settingsJson, agenda, subjects, account)
- `ApiController.php`: JSON API endpoints + login handler (also handles logout via `action=logout` POST param)
- Controller uses `requestToApi()` helper to call Classeviva APIs

**Classeviva Integration** (`app/core/cvv/`):
- `User.php`: HTTP client using cURL, handles authentication token
- `ClassevivaIntegration.php`: Session management, login logic, query orchestration, CredentialStore autoload fallback
- `Collegamenti.php`: URL builder with dynamic path substitution (`{ident}`, `{date_from}`, etc.)
- `apiMethods.php`: Configuration array mapping logical API names to URLs and parameters

**Credential Security** (`config/`):
- `CryptoHelper.php`: AES-256-CBC encryption/decryption with PBKDF2 key derivation (100k iterations) and wrapped-key pattern (user-derived key encrypted with server master key)
- `CredentialStore.php`: Persistent encrypted credential storage in `data/credentials_*.enc` — supports multi-user, survives server restarts without re-login
- `server_key.php`: Master server key (auto-generated on first run, MUST be in `.gitignore`)

**Configuration**: Environment settings in `public/index.php`:
```
define('BASE_PATH', dirname(__DIR__));
const URL_PATH = "https://gradecraft.test";  // Change for production
const THEME = 'dark';  // or 'light'
const API_DETACH = false;  // Set true to disable actual API calls
const CLASSEVIVA_YEAR: defines academic year suffix, read from .env with default ''
```

**Debug Mode**: Toggle `$_SESSION['show_json_debug']` via `/settings/json` — displays raw JSON API responses in every view card.

**Frontend** (`public/`):
- ES6 module in `js/dbApi.js` (IndexedDB service with `attachments` store for noticeboard)
- Web Components in `components/` (GradeGauge, AverageAreaChart, GradeGraph, SubjectCard)
- Common layout partials in `commons/` (head, bottom navigation)
- `functions.php`: Session expiration helper, login request function, credential autoload from CredentialStore, `cvv_sync()` partial form
- `bottom_navigation.php`: Bottom navbar with links to Home, Assenze, Voti, Oggi, Bacheca, Profilo
- All views must include `<!DOCTYPE html>` before `<html>` tag to avoid Quirks Mode

**Demo/Testing Interface** (`demo/`):
- Standalone API explorer with full access to all endpoints
- Useful for testing API responses and debugging

## Development Commands

### Local Development
```bash
# Docker Compose (nginx + PHP-FPM + MySQL)
docker compose up -d
# Access at https://gradecraft.test

# Or PHP built-in server
php -S localhost:8000 -t public
```

### API Testing
```bash
# Via demo/ directory
curl -X POST http://localhost:8000/api -d '{"request":"materie","extraInput":{},"cvvArrKey":"subjects","isPost":false}'

# Via CLI debug script
php scripts/debug.php login
php scripts/debug.php agenda
php scripts/debug.php grades
php scripts/debug.php api bacheca
```

### Debug
- Check PHP-FPM logs: `docker compose logs | grep php_fpm`
- Toggle JSON debug in views: visit `/settings/json`
- CLI session: `storage/cli_session.json` (separate from web sessions)
- Web sessions: PHP default session storage

## Database

No SQL database. Uses:
- PHP sessions for auth tokens and user identity
- IndexedDB (`gradecraft` DB, `grades` + `attachments` object stores) for offline caching
- Encrypted files in `data/*.enc` for persistent credential storage (wrapped-key AES-256-CBC)

## Code Style & Conventions

- PHP: Opening `<?php` tag with copyright comment block
- Namespaces: `cvv\` for integration classes
- Classes: PascalCase, methods camelCase
- Constants: UPPER_SNAKE_CASE
- Functions: snake_case for procedural functions in global scope
- JavaScript: ES6 classes, web components (custom elements), Chart.js
- HTML templates: All views must start with `<!DOCTYPE html>` before `<html lang="it">`

## Important Configuration Points

1. **Year handling**: `CLASSEVIVA_YEAR` constant in `index.php`, read from `.env` file with default `''`. Controls Classeviva URL suffix (`web{$year}.spaggiari.eu`).
2. **Theme switching**: `THEME` constant in `index.php`; also stored in `sessionStorage` for frontend
3. **API endpoints**: Map new endpoints in `app/core/cvv/apiMethods.php` with `cvvArrKey` to extract specific response keys
4. **URL_PATH**: Should point to the deployed domain (no trailing slash)
5. **Noticeboard attachments**: `bachecaAllegato` uses `particularDataParse: "PDF"` — `User::sendRequest` wraps raw response as `["pdfData" => $response]`
6. **Frontend resources**: All CSS/JS/component paths in `head.php` must be absolute (`/css/...`, `/components/...`) to work from nested routes

## Testing Notes

- No formal test suite
- Use `demo/` directory for manual API testing
- Use `scripts/debug.php` for CLI testing
- Session state persists; clear via `debug.php clear` or restart
- `/settings/json` shows raw API responses in view cards

## Known Issues / TODOs

- Noticeboard: PDF viewer iframe needs browser-specific sandbox settings (Firefox blocks blob URLs with `sandbox`)
- Noticeboard attachments: backend `bachecaAllegato` returns raw PDF but CORS/proxy for external downloads may need work
- Agenda multi-day event handling in time string logic
- `index.php` trailing `<script type="module">` can cause 404 from nested routes
- `functions.php` typo: `reLoginOffest` → `reLoginOffset`
- Frontend graphs use hardcoded sample data in some places

## Security Considerations

- **Credential storage**: Encrypted with AES-256-CBC using PBKDF2-derived key + server-wrapped master key (`config/server_key.php`, gitignored)
- Session expiration checked via `checkSessionExpiration()` in `functions.php` with autoload from CredentialStore
- API token stored in PHP session
- CSRF protection not implemented
- XSS: views use `htmlspecialchars()` on all dynamic output
- Logout: `POST /login` with `action=logout` destroys session + deletes stored credentials

## Deployment Checklist

1. Update `URL_PATH` in `public/index.php` to production domain
2. Set `THEME` appropriately
3. Ensure `API_DETACH = false`
4. Configure web server for PHP with proper permissions on session directory
5. Enable HTTPS (required by Classeviva API)
6. Consider moving configuration to environment variables (currently hardcoded)
