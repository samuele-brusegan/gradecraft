<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

if (!isset($error)) $error = null;
if (!isset($allEvents)) $allEvents = [];

$mesiIt = [1=>'Gennaio', 2=>'Febbraio', 3=>'Marzo', 4=>'Aprile', 5=>'Maggio', 6=>'Giugno',
           7=>'Luglio', 8=>'Agosto', 9=>'Settembre', 10=>'Ottobre', 11=>'Novembre', 12=>'Dicembre'];
$giorniIt = ['dom', 'lun', 'mar', 'mer', 'gio', 'ven', 'sab'];
$giorniLun = ['lun', 'mar', 'mer', 'gio', 'ven', 'sab', 'dom'];

// Mese corrente visualizzato
$today = new DateTime();
$targetMonth = (int)$today->format('n');
$targetYear = (int)$today->format('Y');
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GradeCraft - Calendario Agenda</title>
    <?php include COMMON_HTML_HEAD ?>
    <style>
    .cal-nav {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 1rem; margin: 1rem 0;
    }
    .cal-nav button {
        background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 0.5rem;
        padding: 0.5rem 1rem; color: var(--text-primary); cursor: pointer; font-size: 1rem;
    }
    .cal-nav button:hover { background: var(--accent-blue); color: white; border-color: var(--accent-blue); }
    .cal-month-title { font-size: 1.25rem; font-weight: 700; color: var(--text-primary); }

    .cal-grid {
        display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; padding: 0 1rem;
    }
    .cal-grid-header {
        text-align: center; font-size: 0.7rem; font-weight: 600; color: var(--text-secondary);
        text-transform: uppercase; padding: 0.25rem 0;
    }
    .cal-day {
        min-height: 3.5rem; padding: 0.25rem; border-radius: 0.4rem;
        border: 1px solid var(--border-color); position: relative;
        background: var(--background-primary);
    }
    .cal-day.has-events { border-color: var(--accent-blue); }
    .cal-day.today { background: rgba(59,130,246,0.08); border-color: var(--accent-blue); }
    .cal-day .day-number {
        font-size: 0.8rem; font-weight: 600; color: var(--text-primary);
        position: absolute; top: 2px; right: 4px;
    }
    .cal-day .day-events {
        display: flex; flex-direction: column; gap: 1px; margin-top: 1.2rem;
    }
    .cal-dot {
        font-size: 0.55rem; display: block; white-space: nowrap; overflow: hidden;
        text-overflow: ellipsis; color: var(--text-secondary); line-height: 1.2;
    }
    .cal-dot.is-test { color: var(--test-color); font-weight: 600; }
    .cal-dot-bar {
        display: inline-block; width: 4px; height: 4px; border-radius: 50%;
        background: var(--accent-blue); margin-right: 2px; vertical-align: middle;
    }
    .cal-dot-bar.test-dot { background: var(--test-color); }
    .cal-day:hover { background: var(--background-secondary); }
    .cal-day.today:hover { background: rgba(59,130,246,0.15); }
    .cal-day.empty { border-color: transparent; background: none; }

    /* Mobile optimizations */
    @media (max-width: 480px) {
        .cal-grid { padding: 0 0.5rem; gap: 1px; }
        .cal-day { min-height: 3rem; padding: 0.15rem; }
        .cal-day .day-number { font-size: 0.7rem; }
        .cal-dot { font-size: 0.5rem; }
        .cal-dot-bar { width: 3px; height: 3px; }
        .cal-grid-header { font-size: 0.6rem; }
        .cal-sheet { padding: 1rem; }
    }

    /* Event detail popup */
    .cal-detail-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.4);
        display: none; z-index: 100; align-items: flex-end; justify-content: center;
    }
    .cal-detail-overlay.open { display: flex; }
    .cal-detail-sheet {
        background: var(--card-bg); border-radius: 1rem 1rem 0 0; padding: 1.5rem;
        width: 100%; max-width: 768px; max-height: 70vh; overflow-y: auto;
    }
    .cal-detail-sheet h3 { margin: 0 0 0.75rem; font-size: 1rem; color: var(--text-primary); }
    .cal-detail-event {
        padding: 0.5rem 0; border-bottom: 1px solid var(--border-color);
    }
    .cal-detail-event:last-child { border-bottom: none; }
    .cal-detail-event h4 { font-size: 0.9rem; margin: 0 0 0.25rem; }
    .cal-detail-event p { font-size: 0.8rem; color: var(--text-secondary); margin: 0; }
    .cal-detail-event.test-event {
        border-left: 3px solid var(--test-color); padding-left: 0.5rem;
    }
    </style>
</head>
<body data-theme="<?=THEME?>">
    <div class="container">
        <?php if (!isset($_SESSION['classeviva_ident'])) cvv_sync(); ?>

        <header style="padding: 2rem 1rem 1rem;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin: 0;">Calendario</h1>
        </header>

        <!-- View Tabs -->
        <div style="display: flex; gap: 0.5rem; padding: 0 1rem; margin-bottom: 0.5rem;">
            <a href="/agenda" style="flex: 1; text-align: center; padding: 0.5rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 600; background: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">Timeline</a>
            <a href="/agenda/calendar" style="flex: 1; text-align: center; padding: 0.5rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 600; background: var(--accent-blue); color: white;">Calendario</a>
        </div>

        <?php if ($error): ?>
            <div class="my-card">
                <p style="color: var(--grade-red); margin: 0;">Errore: <?= htmlspecialchars($error) ?></p>
            </div>
        <?php else: ?>
            <!-- Month Navigation -->
            <div class="cal-nav">
                <button onclick="changeMonth(-1)">←</button>
                <span class="cal-month-title" id="monthTitle"></span>
                <button onclick="changeMonth(1)">→</button>
            </div>

            <!-- Calendar Grid -->
            <div class="cal-grid" id="calGrid"></div>

            <div class="cal-detail-overlay" id="calDetail" onclick="closeDetail(event)">
                <div class="cal-detail-sheet" id="calSheet">
                    <h3 id="calSheetTitle">Eventi del giorno</h3>
                    <div id="calSheetContent"></div>
                </div>
            </div>

            <div style="padding-bottom: 5rem;"></div>
        <?php endif; ?>
    </div>

    <script type="module">
    import { IndexedDBService } from '/js/dbApi.js';
    import { isLikelyTest } from '/js/agendaTests.js';

    const dbService = new IndexedDBService('gradecraft', 1, { agendaAnnotations: {} });

    const allEvents = <?= json_encode($allEvents, JSON_UNESCAPED_UNICODE) ?>;
    const mesiIt = ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'];
    const giorniLun = ['lun','mar','mer','gio','ven','sab','dom'];

    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();

    let annotations = {};
    try {
        annotations = await dbService.getAllAnnotations();
    } catch (e) {
        console.warn('[agenda_calendar] IndexedDB fallita, continuo senza annotazioni:', e);
    }

    async function isTestEvent(evt) {
        const text = [evt.subjectDesc, evt.notes, evt.lessonArg, evt.lessonType, evt.title, evt.evtDescr, evt.description, evt.evtNotes].filter(Boolean).join(' ');
        if (isLikelyTest(text)) return true;
        // Check annotations
        const keys = Object.keys(annotations);
        for (const key of keys) {
            if (annotations[key]?.isTest && text.includes(evt.subject ?? '')) return true;
        }
        return false;
    }

    function renderCalendar() {
        const grid = document.getElementById('calGrid');
        grid.innerHTML = '';

        // Headers
        for (const g of giorniLun) {
            const h = document.createElement('div');
            h.className = 'cal-grid-header';
            h.textContent = g;
            grid.appendChild(h);
        }

        const firstDay = new Date(currentYear, currentMonth, 1);
        let startDow = firstDay.getDay(); // 0=dom
        startDow = startDow === 0 ? 6 : startDow - 1; // Converti a lunedì=0

        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const today = new Date();
        const todayStr = today.toISOString().split('T')[0];

        document.getElementById('monthTitle').textContent = mesiIt[currentMonth] + ' ' + currentYear;

        // Celle vuote prima del primo
        for (let i = 0; i < startDow; i++) {
            const c = document.createElement('div');
            c.className = 'cal-day empty';
            grid.appendChild(c);
        }

        // Giorni
        for (let d = 1; d <= daysInMonth; d++) {
            const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
            const c = document.createElement('div');
            c.className = 'cal-day';
            c.dataset.date = dateStr;

            if (dateStr === todayStr) c.classList.add('today');

            const events = allEvents[dateStr] || null;
            if (events && (events.lessons.length || events.agenda.length)) {
                c.classList.add('has-events');
                const evDiv = document.createElement('div');
                evDiv.className = 'day-events';

                const allDay = [...(events.lessons || []), ...(events.agenda || [])].slice(0, 4);
                for (const evt of allDay) {
                    const isT = isLikelyTest(evt.notes || evt.lessonArg || evt.title || '');
                    const dot = document.createElement('span');
                    dot.className = 'cal-dot' + (isT ? ' is-test' : '');
                    const subj = evt.subjectDesc || evt.subject || evt.title || '';
                    dot.innerHTML = `<span class="cal-dot-bar${isT ? ' test-dot' : ''}"></span>${subj || 'Evento'}`;
                    evDiv.appendChild(dot);
                }

                const more = (events.lessons || []).length + (events.agenda || []).length - 4;
                if (more > 0) {
                    const moreSpan = document.createElement('span');
                    moreSpan.style.cssText = 'font-size:0.55rem;color:var(--text-secondary);';
                    moreSpan.textContent = `+${more}`;
                    evDiv.appendChild(moreSpan);
                }

                c.appendChild(evDiv);

                // Click → detail
                c.addEventListener('click', () => showDayDetail(dateStr, events));
            } else {
                // Click → naviga alla timeline per quel giorno
                c.addEventListener('click', () => {
                    window.location.href = `/agenda?date=${dateStr}`;
                });
            }

            const num = document.createElement('span');
            num.className = 'day-number';
            num.textContent = d;
            c.insertBefore(num, c.firstChild);

            grid.appendChild(c);
        }
    }

    function showDayDetail(dateStr, events) {
        const sheet = document.getElementById('calDetail');
        const d = new Date(dateStr + 'T12:00:00');
        document.getElementById('calSheetTitle').textContent =
            d.toLocaleDateString('it-IT', { weekday: 'long', day: 'numeric', month: 'long' });

        let html = '';
        if (events.lessons && events.lessons.length) {
            html += '<p style="font-size:0.75rem;font-weight:600;color:var(--text-secondary);text-transform:uppercase;margin:0.5rem 0 0.25rem;">Lezioni</p>';
            for (const l of events.lessons) {
                const isT = isLikelyTest(l.notes || l.lessonArg || '');
                const subj = l.subjectDesc || l.subject || 'Lezione';
                const desc = l.lessonArg || l.lessonType || '';
                const time = l.evtHPos ? l.evtHPos + 'ª ora' : (l.startTime || '');
                html += `<div class="cal-detail-event${isT ? ' test-event' : ''}">
                    <h4>${subj}${time ? ' — ' + time : ''}</h4>
                    ${desc ? `<p>${desc}</p>` : ''}
                </div>`;
            }
        }
        if (events.agenda && events.agenda.length) {
            html += '<p style="font-size:0.75rem;font-weight:600;color:var(--text-secondary);text-transform:uppercase;margin:0.5rem 0 0.25rem;">Compiti</p>';
            for (const a of events.agenda) {
                const isT = isLikelyTest(a.notes || a.evtDescr || a.title || '');
                const subj = a.subjectDesc || a.subject || a.title || 'Evento';
                const desc = a.notes || a.evtDescr || a.description || '';
                html += `<div class="cal-detail-event${isT ? ' test-event' : ''}">
                    <h4>${subj}</h4>
                    ${desc ? `<p>${desc}</p>` : ''}
                </div>`;
            }
        }

        document.getElementById('calSheetContent').innerHTML = html || '<p style="color:var(--text-secondary);">Nessun evento.</p>';
        sheet.classList.add('open');
    }

    window.closeDetail = function(e) {
        if (e.target === document.getElementById('calDetail')) {
            document.getElementById('calDetail').classList.remove('open');
        }
    };

    window.changeMonth = function(offset) {
        currentMonth += offset;
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        if (currentMonth < 0) { currentMonth = 11; currentYear--; }
        renderCalendar();
    };

    renderCalendar();
    </script>

    <?php include COMMON_HTML_FOOT ?>
</body>
</html>
