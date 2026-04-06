<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

$firstName = $_SESSION['classeviva_first_name'] ?? 'Utente';
$lastName  = $_SESSION['classeviva_last_name']  ?? '';
$expDate   = isset($_SESSION['classeviva_session_expiration_date'])
    ? date('d/m/Y H:i', strtotime($_SESSION['classeviva_session_expiration_date']))
    : '—';
$year = defined('CLASSEVIVA_YEAR') ? CLASSEVIVA_YEAR : '';

// Conteggio credenziali salvate
$savedUsers = class_exists('CredentialStore') ? count(CredentialStore::listUsers()) : 0;
// Debug toggle
$jsonDebugEnabled = isset($_SESSION['show_json_debug']) && $_SESSION['show_json_debug'];
?>

<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GradeCraft - Impostazioni</title>
    <?php include COMMON_HTML_HEAD ?>
</head>
<body data-theme="<?=THEME?>">
    <div class="container">
        <?php if (!isset($_SESSION['classeviva_ident'])) cvv_sync(); ?>

        <header style="padding: 2rem 1rem 1rem;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin: 0;">Impostazioni</h1>
        </header>

        <!-- Info Account -->
        <div class="my-card" style="margin: 0 1rem; padding: 1rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 0.75rem; color: var(--text-primary);">Account</h3>
            <p style="color: var(--text-primary); margin: 0.25rem 0;"><strong><?= htmlspecialchars($firstName . ' ' . $lastName) ?></strong></p>
            <p style="color: var(--text-secondary); margin: 0.25rem 0; font-size: 0.875rem;">Token scade il: <?= $expDate ?></p>
            <a href="/" style="font-size: 0.875rem; color: var(--accent-blue); text-decoration: none;">Forza ri-sync con Classeviva</a>
        </div>

        <!-- Tema -->
        <div class="my-card" style="margin: 1rem 1rem 0; padding: 1rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 0.75rem; color: var(--text-primary);">Aspetto</h3>
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="color: var(--text-primary);">Tema</span>
                <button id="theme-toggle" class="my-card" style="border: none; cursor: pointer; padding: 0.5rem 1rem; display: flex; align-items: center; gap: 0.5rem; background: var(--card-bg); box-shadow: var(--shadow-md); border-radius: 0.5rem;">
                    <svg width="20" height="20" fill="none" stroke="var(--text-secondary)" viewBox="0 0 24 24" style="flex-shrink: 0;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <span id="theme-label" style="font-weight: 500; color: var(--text-primary);"><?= THEME === 'dark' ? 'Scuro' : 'Chiaro' ?></span>
                </button>
            </div>
        </div>

        <!-- Anno Scolastico -->
        <div class="my-card" style="margin: 1rem 1rem 0; padding: 1rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 0.75rem; color: var(--text-primary);">Anno Scolastico</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.875rem;">Anno corrente: <strong style="color: var(--text-primary);"><?= htmlspecialchars($year ?: 'non impostato') ?></strong></p>
        </div>

        <!-- Debug -->
        <div class="my-card" style="margin: 1rem 1rem 0; padding: 1rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 0.75rem; color: var(--text-primary);">Debug</h3>
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <span style="color: var(--text-primary); display: block;">JSON nelle card</span>
                    <span style="font-size: 0.8rem; color: var(--text-secondary);">Mostra il raw JSON dentro ogni card evento</span>
                </div>
                <button id="json-toggle" class="my-card" style="border: none; cursor: pointer; padding: 0.5rem 1rem; display: flex; align-items: center; gap: 0.5rem; background: var(--card-bg); box-shadow: var(--shadow-md); border-radius: 0.5rem;">
                    <span id="json-label"><?= $jsonDebugEnabled ? 'ON' : 'OFF' ?></span>
                </button>
            </div>
        </div>

        <!-- Argomenti e Verifiche -->
        <div class="my-card" style="margin: 1rem 1rem 0; padding: 1rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 0.75rem; color: var(--text-primary);">Argomenti e Verifiche</h3>
            <details>
                <summary style="font-size: 0.85rem; color: var(--accent-blue); cursor: pointer; padding: 0.5rem 0;">
                    Espandi per gestire argomenti e verifiche
                </summary>
                <div id="topicsPanel" style="margin-top: 0.75rem;">
                    <select id="subjectSelect" style="width: 100%; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 0.5rem; padding: 0.5rem; color: var(--text-primary); font-size: 0.875rem; margin-bottom: 0.75rem;">
                        <option value="">Seleziona materia...</option>
                        <?php foreach ($subjectsList as $id => $desc): ?>
                            <option value="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($desc) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div id="topicsContent" style="min-height: 50px;"></div>
                    <div id="topicsLegend" style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 0.5rem;">
                        <span style="color: var(--test-color);">█</span> = Verifica &nbsp;|&nbsp;
                        Clicca sull'icona ⇄ per segnare/smarcare come verifica
                        <br>
                        <input type="file" id="importAnnotations" accept=".json" style="font-size: 0.75rem; margin-top: 0.5rem; display: none;">
                        <button id="exportBtn" style="font-size: 0.75rem; margin-top: 0.5rem; background: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-primary); padding: 0.3rem 0.6rem; border-radius: 0.4rem; cursor: pointer;">Esporta annotazioni</button>
                        <button id="importBtn" style="font-size: 0.75rem; margin-top: 0.5rem; margin-left: 0.5rem; background: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-primary); padding: 0.3rem 0.6rem; border-radius: 0.4rem; cursor: pointer;">Importa annotazioni</button>
                    </div>
                </div>
            </details>
        </div>

        <!-- Grafico Montagne (Easter Egg) -->
        <div class="my-card" style="margin: 1rem 1rem 0; padding: 1rem;" id="mountainCard">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 0.75rem; color: var(--text-primary);">Montagna dei Voti</h3>
            <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0 0 0.75rem;">
                Il tuo andamento voti ha la forma di una montagna reale. Ecco quale.
            </p>
            <button id="mountainBtn" style="font-size: 0.85rem; background: var(--accent-blue); color: white; border: none; border-radius: 0.5rem; padding: 0.5rem 1rem; cursor: pointer; font-weight: 600;">
                Trova la montagna
            </button>
            <div id="mountainResult" style="margin-top: 1rem; display: none;"></div>
        </div>

        <!-- Credenziali Salvate -->
        <div class="my-card" style="margin: 1rem 1rem 0; padding: 1rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 0.75rem; color: var(--text-primary);">Credenziali</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.875rem;">
                <strong style="color: var(--text-primary);"><?= $savedUsers ?></strong> utente(i) salvato(i) con cifratura.
            </p>
            <button id="clear-credentials" style="margin-top: 0.5rem; font-size: 0.8rem; background: rgba(239,68,68,0.1); color: var(--grade-red); border: none; border-radius: 0.5rem; padding: 0.4rem 0.8rem; cursor: pointer;">
                Rimuovi credenziali salvate
            </button>
        </div>

        <!-- Logout -->
        <div style="padding: 1rem;">
            <button class="my-card" style="width: 100%; border: none; cursor: pointer; text-align: left; padding: 1rem; display: flex; align-items: center; gap: 0.75rem; background: var(--card-bg); box-shadow: var(--shadow-md);" onclick="document.getElementById('logout-form').submit();">
                <svg width="24" height="24" fill="none" stroke="var(--text-secondary)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span style="font-weight: 500; color: var(--text-primary);">Logout</span>
            </button>
            <form id="logout-form" action="/login" method="post" style="display: none;">
                <input type="hidden" name="action" value="logout">
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Tema toggle
        const savedTheme = sessionStorage.getItem('theme');
        if (savedTheme) {
            document.body.setAttribute('data-theme', savedTheme);
            const label = document.getElementById('theme-label');
            if (label) label.textContent = savedTheme === 'dark' ? 'Scuro' : 'Chiaro';
        }
        document.getElementById('theme-toggle')?.addEventListener('click', () => {
            const current = document.body.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            document.body.setAttribute('data-theme', next);
            sessionStorage.setItem('theme', next);
            const label = document.getElementById('theme-label');
            if (label) label.textContent = next === 'dark' ? 'Scuro' : 'Chiaro';
        });

        // Rimuovi credenziali
        document.getElementById('clear-credentials')?.addEventListener('click', async () => {
            if (!confirm('Rimuovere le credenziali salvate? Dovrai effettuare nuovamente il login.')) return;
            try {
                await fetch('/login', { method: 'POST', body: new URLSearchParams({action: 'logout'}) });
                location.reload();
            } catch (e) { alert('Errore nella rimozione'); }
        });

        // Toggle JSON debug
        document.getElementById('json-toggle')?.addEventListener('click', async () => {
            try {
                await fetch('/settings/json', { method: 'POST' });
                location.reload();
            } catch (e) { alert('Errore'); }
        });
    });
    </script>

    <!-- Pannello Argomenti e Montagne -->
    <script type="module">
    import { IndexedDBService } from '/js/dbApi.js';
    import { isLikelyTest } from '/js/agendaTests.js';

    const dbService = new IndexedDBService('gradecraft', 1, { agendaAnnotations: {} });
    const subjectsList = <?= json_encode($subjectsList ?? [], JSON_UNESCAPED_UNICODE) ?>;

    // === PANNELLO ARGOMENTI ===
    const subjectSelect = document.getElementById('subjectSelect');
    const topicsContent = document.getElementById('topicsContent');

    if (subjectSelect) {
        subjectSelect.addEventListener('change', async () => {
            const sid = subjectSelect.value;
            if (!sid) { topicsContent.innerHTML = ''; return; }
            await loadTopicsForSubject(sid);
        });
    }

    async function loadTopicsForSubject(subjectId) {
        topicsContent.innerHTML = '<p style="font-size:0.8rem;color:var(--text-secondary);">Caricamento...</p>';

        try {
            // Recupero overview degli ultimi 3 mesi
            const now = new Date();
            const from = new Date(now.getFullYear() - (now.getMonth() < 8 ? now.getFullYear() - 1 : now.getFullYear()), 8, 1);
            const toStr = now.toISOString().split('T')[0].replace(/-/g, '');
            const fromStr = from.toISOString().split('T')[0].replace(/-/g, '');

            const resp = await fetch('/api', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    request: 'overview',
                    extraInput: { date_from: fromStr, date_to: toStr },
                    cvvArrKey: 'overview',
                    isPost: true
                })
            });
            const data = await resp.json();
            const lessons = (data.response?.lessons || data.lessons || []).filter(l => l.subjectId == subjectId);
            const agenda = (data.response?.agenda || data.agenda || []).filter(a => a.subjectId == subjectId);
            const allEvents = [...lessons, ...agenda];

            if (allEvents.length === 0) {
                topicsContent.innerHTML = '<p style="font-size:0.8rem;color:var(--text-secondary);">Nessun argomento trovato per questa materia.</p>';
                return;
            }

            const annotations = await dbService.getAllAnnotations();
            allEvents.sort((a, b) => (a.evtDate || '').localeCompare(b.evtDate || ''));

            // Raggruppa: gli eventi marcati come verifica diventano separatori
            let html = '';
            let currentGroup = [];
            let groupId = 0;

            function renderGroup(events, isTestGroup, groupName) {
                if (events.length === 0) return;
                if (isTestGroup) {
                    html += `<div style="margin: 0.75rem 0 0.5rem; padding: 0.4rem 0.6rem; background: rgba(168,85,247,0.1); border-radius: 0.4rem; border-left: 3px solid var(--test-color);">
                        <span style="font-size:0.8rem;font-weight:600;color:var(--test-color);">VERIFICA</span>`;
                    if (groupName) html += `<span style="font-size:0.75rem;color:var(--text-secondary);margin-left:0.5rem;">${groupName}</span>`;
                    html += '</div>';
                }
                for (const evt of events) {
                    const date = evt.evtDate ? new Date(evt.evtDate + 'T12:00:00').toLocaleDateString('it-IT') : '';
                    const desc = evt.notes || evt.lessonArg || evt.description || evt.lessonType || '';
                    html += `<div style="font-size:0.8rem;padding:0.25rem 0 0.25rem 0.5rem;border-bottom:1px solid var(--border-color);">
                        <span style="color:var(--text-secondary);font-size:0.7rem;">${date}</span>
                        ${desc ? `<span style="margin-left:0.5rem;">${desc}</span>` : ''}
                    </div>`;
                }
            }

            for (let idx = 0; idx < allEvents.length; idx++) {
                const evt = allEvents[idx];
                const text = [evt.subjectDesc, evt.notes, evt.lessonArg, evt.lessonType, evt.title].filter(Boolean).join(' ');
                const annKey = buildAnnKey(evt, idx, lessons.length);
                const ann = annotations[annKey];
                const isTest = ann?.isTest !== undefined ? ann.isTest : isLikelyTest(text);

                if (isTest) {
                    renderGroup(currentGroup, false);
                    currentGroup = [];
                    renderGroup([evt], true, new Date(evt.evtDate + 'T12:00:00').toLocaleDateString('it-IT'));
                } else {
                    currentGroup.push(evt);
                }
            }
            renderGroup(currentGroup, false);

            topicsContent.innerHTML = html;

        } catch (e) {
            topicsContent.innerHTML = `<p style="font-size:0.8rem;color:var(--grade-red);">Errore: ${e.message}</p>`;
        }
    }

    function buildAnnKey(evt, idx) {
        const prefix = (evt.lessonArg !== undefined) ? 'lesson' : 'agenda';
        return `${prefix}_${idx}`;
    }

    // Esporta annotazioni
    document.getElementById('exportBtn')?.addEventListener('click', async () => {
        try {
            const json = await dbService.exportAnnotations();
            const blob = new Blob([json], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url; a.download = 'gradecraft_annotations.json';
            a.click(); URL.revokeObjectURL(url);
        } catch (e) { alert('Errore export: ' + e.message); }
    });

    // Importa annotazioni
    document.getElementById('importBtn')?.addEventListener('click', () => {
        document.getElementById('importAnnotations').click();
    });
    document.getElementById('importAnnotations')?.addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (!file) return;
        try {
            const text = await file.text();
            await dbService.importAnnotations(text);
            alert('Annotazioni importate con successo!');
            // Ricarica se c'è una materia selezionata
            if (subjectSelect.value) loadTopicsForSubject(subjectSelect.value);
        } catch (err) { alert('Errore import: ' + err.message); }
    });

    // === MATCHING MONTAGNE ===
    document.getElementById('mountainBtn')?.addEventListener('click', async () => {
        const btn = document.getElementById('mountainBtn');
        const result = document.getElementById('mountainResult');
        btn.textContent = 'Ricerca in corso...';
        btn.disabled = true;
        result.style.display = 'block';
        result.innerHTML = '<p style="font-size:0.8rem;color:var(--text-secondary);">Recupero voti...</p>';

        try {
            // Recupera voti
            const gradesResp = await fetch('/api', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    request: 'get_grades',
                    extraInput: {},
                    cvvArrKey: 'grades',
                    isPost: false
                })
            });
            const gradesText = await gradesResp.text();
            // Parsa JSON robusto (potrebbe avere output PHP extra)
            let gradesData;
            try {
                gradesData = JSON.parse(gradesText);
            } catch {
                // Tenta di estrarre JSON dall'output
                const jsonStart = gradesText.indexOf('{');
                const jsonEnd = gradesText.lastIndexOf('}');
                if (jsonStart !== -1 && jsonEnd !== -1) {
                    gradesData = JSON.parse(gradesText.slice(jsonStart, jsonEnd + 1));
                } else {
                    throw new Error('Risposta API non valida. Output: ' + gradesText.slice(0, 200));
                }
            }
            // La risposta può essere un array diretto (cvvArrKey='grades') o un oggetto
            const grades = (Array.isArray(gradesData) ? gradesData : (gradesData.response?.grades || gradesData.grades || [])).filter(g => g.decimalValue != null);

            if (grades.length < 3) {
                result.innerHTML = '<p style="font-size:0.8rem;color:var(--text-secondary);">Servono almeno 3 voti per il matching. Troppa poca roccia da scalare.</p>';
                btn.textContent = 'Trova la montagna'; btn.disabled = false;
                return;
            }

            // Normalizza voti in profilo 0-1
            grades.sort((a, b) => (a.evtDate || '').localeCompare(b.evtDate || ''));
            const values = grades.map(g => parseFloat(g.decimalValue));
            const min = Math.min(...values);
            const max = Math.max(...values);
            const range = max - min || 1;
            const profile = values.map(v => (v - min) / range);

            // Recupera montagne e fetch elevazioni reali da Open-Meteo (SRTM)
            const mtnResp = await fetch('/data/mountains.json');
            const mtnData = await mtnResp.json();
            const mountains = mtnData.mountains || [];

            result.innerHTML = '<p style="font-size:0.8rem;color:var(--text-secondary);">Recupero profili SRTM da Open-Meteo...</p>';

            // Fetch elevazioni per ogni montagna (batch parallelo)
            const mountainsWithProfiles = await Promise.all(
                mountains.map(async (mtn) => {
                    if (!mtn.traverse || mtn.traverse.length === 0) return null;
                    const lats = mtn.traverse.map(p => p[0]);
                    const lons = mtn.traverse.map(p => p[1]);
                    try {
                        const elevResp = await fetch(
                            `https://api.open-meteo.com/v1/elevation?latitude=${lats.join(',')}&longitude=${lons.join(',')}`
                        );
                        const elevData = await elevResp.json();
                        const elevations = elevData.elevation || [];
                        const min = Math.min(...elevations);
                        const max = Math.max(...elevations);
                        const range = max - min || 1;
                        const normalized = elevations.map(v => (v - min) / range);
                        return { ...mtn, elevations, profile: normalized };
                    } catch { return null; }
                })
            ).then(arr => arr.filter(x => x && x.profile));

            if (mountainsWithProfiles.length === 0) {
                result.innerHTML = '<p style="font-size:0.8rem;color:var(--grade-red);">Errore nel recupero delle elevazioni. Controlla la connessione.</p>';
                btn.textContent = 'Trova la montagna'; btn.disabled = false;
                return;
            }

            result.innerHTML = '<p style="font-size:0.8rem;color:var(--text-secondary);">Confronto profili con DTW...</p>';

            const scores = scoreProfileDTW(profile, mountainsWithProfiles);
            const best = scores[0];
            const mtn = mountains[best.idx];

            const photoUrl = mtn.photo;
            const povCoords = `${mtn.pov.lat.toFixed(4)}, ${mtn.pov.lon.toFixed(4)}`;
            const peakCoords = `${mtn.coords.lat.toFixed(4)}, ${mtn.coords.lon.toFixed(4)}`;

            // Score %
            const maxPossibleDist = Math.max(values.length, best.profile.length);
            const matchPct = Math.max(0, Math.round((1 - best.distance / maxPossibleDist) * 100));

            // Mostra profili elevazioni reali
            const elevStr = (best.elevations || []).map(v => `${Math.round(v)}m`).join(' → ');

            result.innerHTML = `
                <div style="background:var(--background-secondary);border-radius:0.75rem;overflow:hidden;">
                    <img src="${photoUrl}" alt="${mtn.name}" style="width:100%;height:12rem;object-fit:cover;" onerror="this.style.display='none'">
                    <div style="padding:0.75rem;">
                        <h4 style="margin:0 0 0.25rem;font-size:1.1rem;font-weight:700;">${mtn.name}</h4>
                        <p style="margin:0 0 0.5rem;font-size:0.8rem;color:var(--text-secondary);">
                            ${mtn.elevation}m — ${mtn.region}
                        </p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;font-size:0.75rem;">
                            <div><strong>Vetta:</strong> ${peakCoords}</div>
                            <div><strong>POV:</strong> ${mtn.pov.name}</div>
                            <div><strong>Coord POV:</strong> ${povCoords}</div>
                            <div><strong>Azimut:</strong> ${mtn.azimuth}°</div>
                            <div><strong>Dati DEM:</strong> SRTM (Open-Meteo)</div>
                            <div><strong>Punti profilo:</strong> ${best.profile.length}</div>
                        </div>
                        <div style="margin-top:0.75rem;">
                            <strong style="font-size:0.75rem;">DTW Distance:</strong>
                            <span style="font-size:0.85rem;color:var(--accent-blue);margin-left:0.5rem;">${best.distance.toFixed(3)}</span>
                            <span style="font-size:0.8rem;margin-left:0.5rem;color:var(--text-secondary);">(${matchPct}% similarità)</span>
                        </div>
                        <div style="margin-top:0.75rem;">
                            <strong style="font-size:0.75rem;">I tuoi voti:</strong>
                            <span style="font-size:0.75rem;margin-left:0.5rem;color:var(--text-secondary);">${values.join(' → ')}</span>
                        </div>
                        <div style="margin-top:0.5rem;">
                            <strong style="font-size:0.75rem;">Elevazioni reali (SRTM):</strong>
                            <span style="font-size:0.75rem;margin-left:0.5rem;color:var(--text-secondary);">${elevStr}</span>
                        </div>
                    </div>
                </div>
            `;

            // Top 3
            if (scores.length > 1) {
                let runners = '<p style="font-size:0.75rem;color:var(--text-secondary);margin-top:0.75rem;">Altre possibilità:</p><ul style="font-size:0.75rem;color:var(--text-secondary);margin:0;padding-left:1.2rem;">';
                for (let i = 1; i < Math.min(3, scores.length); i++) {
                    const rm = mountains[scores[i].idx];
                    const rd = Math.max(0, Math.round((1 - scores[i].distance / maxPossibleDist) * 100));
                    runners += `<li>${rm.name} — ${rm.elevation}m (DTW: ${scores[i].distance.toFixed(3)}, ${rd}%)</li>`;
                }
                runners += '</ul>';
                result.innerHTML += runners;
            }

        } catch (e) {
            result.innerHTML = `<p style="font-size:0.8rem;color:var(--grade-red);">Errore: ${e.message}</p>`;
        }

        btn.textContent = 'Trova la montagna';
        btn.disabled = false;
    });

    /**
     * Dynamic Time Warping (DTW) distance.
     * Lower = più simile. Confronta sequenze che possono variare in velocità/lunghezza.
     */
    function dtwDistance(series1, series2) {
        const n = series1.length;
        const m = series2.length;

        // Matrice DTW
        const dtw = new Array(n + 1);
        for (let i = 0; i <= n; i++) {
            dtw[i] = new Array(m + 1).fill(Infinity);
        }
        dtw[0][0] = 0;

        for (let i = 1; i <= n; i++) {
            for (let j = 1; j <= m; j++) {
                const cost = Math.abs(series1[i - 1] - series2[j - 1]);
                dtw[i][j] = cost + Math.min(
                    dtw[i - 1][j],     // inserzione
                    dtw[i][j - 1],     // cancellazione
                    dtw[i - 1][j - 1]  // match
                );
            }
        }

        // Normalizza per lunghezza del percorso
        return dtw[n][m] / (n + m);
    }

    /**
     * Gradient matching: confronta la derivata prima (pendenza) invece dell'altezza assoluta.
     * Utile per catturare la "forma" indipendentemente dalla quota.
     */
    function gradients(series) {
        const g = [];
        for (let i = 1; i < series.length; i++) {
            g.push(series[i] - series[i - 1]);
        }
        return g;
    }

    /**
     * Score the grade profile against each mountain profile usando:
     * 1. DTW sulla forma (posizione dei picchi/valli)
     * 2. Gradient matching (pendenze)
     * Combina entrambi per uno score più robusto.
     * Returns array of {idx, distance} sorted asc (lower = better).
     */
    function scoreProfileDTW(grades, mountains) {
        const results = [];
        const gradeGradients = gradients(grades);

        for (let mi = 0; mi < mountains.length; mi++) {
            const mtn = mountains[mi];
            const mtnProfile = mtn.profile;
            const mtnGradients = gradients(mtnProfile);

            // DTW sulla forma
            const shapeDist = dtwDistance(grades, mtnProfile);

            // DTW sui gradienti (forma derivata)
            const gradDist = dtwDistance(gradeGradients, mtnGradients);

            // Score combinato (peso 60% forma, 40% gradienti)
            const combined = shapeDist * 0.6 + gradDist * 0.4;
            results.push({ idx: mi, distance: combined, shapeDist, gradDist });
        }

        return results.sort((a, b) => a.distance - b.distance);
    }
    </script>

    <?php include COMMON_HTML_FOOT ?>
</body>
</html>
