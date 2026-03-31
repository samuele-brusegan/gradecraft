# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**GradeCraft** is a PHP web application that integrates with Classeviva (Italian school management system). It displays student grades, subjects, agenda events, and other school data with offline capabilities via IndexedDB.

**Tech Stack:**
- Backend: PHP 8.1+ (no framework)
- Frontend: Vanilla JavaScript ES6 modules, Web Components, Chart.js
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
- Simple router: `$router->add($url, $controller, $action)`
- Routes defined in `public/routes.php`
- 404 handling includes common layout

**Controllers** (`app/controllers/`):
- `Controller.php`: Main page controllers (dashboard, grades, account, etc.)
- `ApiController.php`: JSON API endpoints for frontend consumption
- Controller uses `requestToApi()` helper to call Classeviva APIs

**Classeviva Integration** (`app/core/cvv/`):
- `User.php`: HTTP client using cURL, handles authentication token
- `ClassevivaIntegration.php`: Session management, login logic, query orchestration
- `Collegamenti.php`: URL builder pattern with dynamic path substitution (`{ident}`, `{date_from}`, etc.)
- `apiMethods.php`: Configuration array mapping logical API names to URLs and parameters

**Configuration**: All environment-specific settings in `public/index.php`:
```
define('BASE_PATH', dirname(__DIR__));
const URL_PATH = "https://gradecraft.test";  // Change for production
const THEME = 'dark';  // or 'light'
const API_DETACH = false;  // Set true to disable actual API calls
```

**Frontend** (`public/`):
- ES6 modules in `js/` (IndexedDB service)
- Web Components in `components/` (custom elements like `<Grade>`)
- Common layout partials in `commons/` (head, navigation, foot)
- `functions.php`: Session expiration helper, login request function

**Demo/Testing Interface** (`demo/`):
- Standalone API explorer with full access to all endpoints
- Useful for testing API responses and debugging

## Development Commands

### Setup
- Assumes PHP 8.1+ with cURL extension enabled
- Place in web server document root (or configure virtual host)
- No Composer dependencies (uses native PHP)
- No build step for frontend (ES6 modules served directly)

### Local Development
```bash
# Start PHP built-in server (recommended)
php -S localhost:8000 -t public

# Or configure Apache/Nginx with document root = /public
```

### Common Tasks

**View application**: Open `http://localhost:8000` in browser

**Test API directly**:
- Browse to `http://localhost:8000/demo/` for interactive API explorer
- Or use command line:
```bash
curl -X POST http://localhost:8000/api -d '{"request":"materie","extraInput":{},"cvvArrKey":"subjects","isPost":false}'
```

**Clear sessions**: Delete PHP session files (location depends on `session.save_path`) or restart server

**Simulate offline/testing**: Set `API_DETACH = true` in `public/index.php` to prevent real API calls

## Database

No SQL database. Uses:
- PHP sessions for auth tokens and user identity
- IndexedDB (`gradecraft` DB, `grades` object store) for offline grade caching
- Cookies (`users`) for credential persistence

## Code Style & Conventions

- PHP: Opening `<?php` tag with copyright comment block
- Namespaces: `cvv\` for integration classes
- Classes: PascalCase, methods camelCase
- Constants: UPPER_SNAKE_CASE
- Functions: snake_case for procedural functions in global scope
- JavaScript: ES6+ classes, modules, async/await
- HTML templates: Embedded PHP in views with `BASE_PATH` constants for includes

## Important Configuration Points

1. **Year handling**: `Collegamenti::setGeneric("year", "24")` sets the academic year suffix in Classeviva URLs (`web24.spaggiari.eu`). Update this for new school years.

2. **Theme switching**: `THEME` constant in `index.php`; also stored in `sessionStorage` for frontend

3. **API endpoints**: Map new Classeviva endpoints in `app/core/cvv/apiMethods.php` with `cvvArrKey` to extract specific response keys

4. **URL_PATH**: Should point to the deployed domain (no trailing slash)

## Testing Notes

- No formal test suite exists
- Use `demo/` directory for manual API testing
- Session state persists across requests; clear cookies/sessions for fresh login
- Check browser console for IndexedDB operations

## Known Issues / TODOs

- Some routes commented out in `routes.php` (subjects, agenda, settings) - partially implemented
- Frontend graphs use hardcoded sample data in some places
- Error handling could be more user-friendly
- No input validation on API parameters

## Security Considerations

- Credentials stored in cookies (base64 encoded) - not ideal for production
- Session expiration checked via `checkSessionExpiration()` in `functions.php`
- API token stored in PHP session
- CSRF protection not implemented
- XSS: views output raw data in some places (grade notes) - ensure proper escaping

## Deployment Checklist

1. Update `URL_PATH` in `public/index.php` to production domain
2. Set `THEME` appropriately (0 = light, 1 = dark using ternary, currently inverted logic)
3. Ensure `API_DETACH = false`
4. Configure web server for PHP with proper permissions on session directory
5. Enable HTTPS (required by Classeviva API)
6. Consider moving configuration to environment variables (currently hardcoded)
