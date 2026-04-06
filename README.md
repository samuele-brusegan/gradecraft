# GradeCraft

Interfaccia web per Classeviva — voti, assenze, agenda, calendario e note, con capacità offline lato client.

## Funzionalità

- 📊 Voti — panoramica per materia, medie con barre di progresso lineari, dettaglio per periodo
- 📅 Agenda — timeline giornaliera e vista calendario (grid desktop + lista mobile) con rilevamento compiti e verifiche
- ❌ Assenze — lista categorizzata con conteggio ore
- 📌 Bacheca — lista avvisi con lettore PDF integrato e download allegati
- 📖 Documenti — documenti e pagelle
- 📝 Note — note insegnante, annotazioni, richiami e disciplinari
- ⚙️ Impostazioni — toggle tema chiaro/scuro, debug JSON, sincronizzazione credenziali
- 🗄️ Offline — IndexedDB per caching voti e allegati bacheca

## Stack

| Layer | Tecnologia |
|-------|-----------|
| Backend | PHP 8.1+ (no framework), API Classeviva via cURL |
| Frontend | Vanilla JS ES6, Web Components (custom elements), Chart.js |
| Styling | Bootstrap 5, Tailwind CSS |
| Storage | IndexedDB (client), PHP session, credenziali cifrate AES-256-CBC |

## Avvio

### Docker Compose (consigliato)
```bash
docker compose up -d
# Access at https://gradecraft.test
```

### PHP built-in server
```bash
php -S localhost:8000 -t public
```

## Configurazione

| Costante | File | Descrizione |
|----------|------|-------------|
| `BASE_PATH` | `public/index.php` | Percorso assoluto della root |
| `URL_PATH` | `public/index.php` | Dominio di deploy (niente slash finale) |
| `THEME` | `public/index.php` | `'dark'` o `'light'` |
| `API_DETACH` | `public/index.php` | `true` per disabilitare le chiamate API reali |
| `CLASSEVIVA_YEAR` | `.env` | Suffisso anno scolastico per Classeviva (default: `''`) |

## Debug

| Strumento | URL | Descrizione |
|-----------|-----|-------------|
| JSON debug | `/settings/json` | Mostra risposte API raw in ogni card |
| Debug API | `/debug/api` | Lista endpoint → JSON raw non filtrato da Classeviva |

## Struttura

```
├── app/
│   ├── controllers/     # Controller.php, ApiController.php
│   ├── core/
│   │   ├── cvv/         # Classeviva integration (User, CvvIntegration, Collegamenti)
│   │   └── Router.php
│   └── views/           # Template PHP per ogni pagina
├── config/              # CryptoHelper, CredentialStore
├── public/              # Front controller, CSS, JS, routes
│   ├── css/
│   ├── js/
│   ├── components/      # Web Components (GradeGauge, etc.)
│   └── commons/         # Partial (head, bottom nav)
├── storage/             # Sessioni CLI
└── data/                # Credenziali cifrate (gitignored)
```

## Licenza

Questo progetto è rilasciato sotto la licenza MIT. Vedere il file [LICENSE](LICENSE.md) per maggiori dettagli.
