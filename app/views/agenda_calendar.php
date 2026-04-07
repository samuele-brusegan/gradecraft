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
        display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; padding: 0 1rem; width: 100%;
        box-sizing: border-box;
    }
    .cal-grid-header, .cal-day {
        min-width: 0; overflow: hidden;
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

    /* Desktop-only grid, mobile list view */
    @media (max-width: 600px) {
        .cal-grid-view { display: none; }
        .cal-list-view { display: block; }
        .cal-nav { padding: 0 0.5rem; }
        .cal-detail-sheet { width: 95%; padding: 1rem; }
    }
    @media (min-width: 601px) {
        .cal-list-view { display: none; }
    }

    .cal-list-group-title {
        font-size: 0.75rem; font-weight: 700; color: var(--text-secondary);
        text-transform: uppercase; padding: 0.75rem 1rem 0.25rem; letter-spacing: 0.05em;
    }
    .cal-list-item {
        display: flex; align-items: flex-start; gap: 0.75rem;
        padding: 0.6rem 1rem; border-bottom: 1px solid var(--border-color);
        cursor: pointer; transition: background 0.15s;
    }
    .cal-list-item:hover { background: var(--background-secondary); }
    .cal-list-item:active { background: rgba(59,130,246,0.08); }
    .cal-list-date {
        min-width: 2.5rem; text-align: center;
    }
    .cal-list-date .day-num {
        display: block; font-size: 1.25rem; font-weight: 700; color: var(--text-primary); line-height: 1;
    }
    .cal-list-date .dow {
        font-size: 0.65rem; color: var(--text-secondary); text-transform: uppercase;
    }
    .cal-list-events { flex: 1; }
    .cal-list-event {
        font-size: 0.85rem; padding: 0.2rem 0; color: var(--text-primary);
        border-left: 3px solid var(--accent-blue); padding-left: 0.5rem; margin-bottom: 0.3rem;
    }
    .cal-list-event.is-test { border-left-color: var(--test-color); font-weight: 600; color: var(--test-color); }
    .cal-list-event-subj {
        font-size: 0.75rem; color: var(--text-secondary);
    }
    .cal-list-empty {
        text-align: center; padding: 2rem 1rem; color: var(--text-secondary); font-size: 0.85rem;
    }

    /* Event detail popup */
    .cal-detail-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.4);
        display: none; z-index: 100; align-items: center; justify-content: center;
    }
    .cal-detail-overlay.open { display: flex; }
    .cal-detail-sheet {
        background: var(--card-bg); border-radius: 1rem; padding: 1.5rem;
        width: 90%; max-width: 768px; max-height: 70vh; overflow-y: auto;
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
    .test-keyword {
        color: var(--test-color);
        font-weight: 600;
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

            <!-- Calendar Grid (desktop) -->
            <div class="cal-grid-view">
                <div class="cal-grid" id="calGrid"></div>
            </div>

            <!-- Calendar List (mobile) -->
            <div class="cal-list-view" id="calList"></div>

            <div class="cal-detail-overlay" id="calDetail" onclick="closeDetail(event)">
                <div class="cal-detail-sheet" id="calSheet">
                    <h3 id="calSheetTitle">Eventi del giorno</h3>
                    <div id="calSheetContent"></div>
                </div>
            </div>

            <div style="padding-bottom: 5rem;"></div>
        <?php endif; ?>
    </div>

    <!-- Render status (filled by JS after renderCalendar) -->
    <details id="calDebug" style="margin: 0 1rem 1rem;" open>
        <summary style="cursor: pointer; font-size: 0.85rem; color: var(--text-secondary); font-weight:600;">Render Status</summary>
    </details>

    <script type="module">
    import { isLikelyTest, getTestKeyword, highlightKeywordInText } from '/js/agendaTests.js';

    const allEvents = <?= json_encode($allEvents, JSON_UNESCAPED_UNICODE) ?>;
    const mesiIt = ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'];
    const giorniLun = ['lun','mar','mer','gio','ven','sab','dom'];

    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();

    function renderCalendar() {
        const grid = document.getElementById('calGrid');
        if (!grid) { console.error('[cal] calGrid not found!'); return; }
        grid.innerHTML = '';

        const firstDay = new Date(currentYear, currentMonth, 1);
        let startDow = firstDay.getDay();
        startDow = startDow === 0 ? 6 : startDow - 1;
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const monthName = mesiIt[currentMonth] || '?';

        // Headers
        for (const g of giorniLun) {
            const h = document.createElement('div');
            h.className = 'cal-grid-header';
            h.textContent = g;
            grid.appendChild(h);
        }

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
        let renderedDays = 0;
        let renderedWithEvents = 0;
        for (let d = 1; d <= daysInMonth; d++) {
            const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
            const c = document.createElement('div');
            c.className = 'cal-day';
            c.dataset.date = dateStr;

            if (dateStr === todayStr) c.classList.add('today');

            const events = allEvents[dateStr] || null;
            if (events && events.agenda.length > 0) {
                renderedWithEvents++;
                c.classList.add('has-events');
                const evDiv = document.createElement('div');
                evDiv.className = 'day-events';

                const agendaOnly = events.agenda;
                const bySubject = {};
                for (const evt of agendaOnly) {
                    const subjId = evt.subjectId || evt.subjectDesc || evt.authorName || '';
                    const notes = evt.notes || evt.title || '';
                    const isT = isLikelyTest(notes);
                    if (!bySubject[subjId] || isT) {
                        bySubject[subjId] = isT ? { ...evt, _isTest: true } : evt;
                    }
                }

                const uniqueEvents = Object.values(bySubject).slice(0, 4);
                for (const evt of uniqueEvents) {
                    const subj = evt.subjectDesc || evt.title || '';
                    const author = evt.authorName || '';
                    const notes = evt.notes || '';
                    let displayText = subj || author || 'Evento';
                    const isT = evt._isTest || isLikelyTest(notes);

                    const dot = document.createElement('span');
                    dot.className = 'cal-dot' + (isT ? ' is-test' : '');
                    dot.innerHTML = `<span class="cal-dot-bar${isT ? ' test-dot' : ''}"></span>${displayText}`;
                    evDiv.appendChild(dot);
                }

                const more = agendaOnly.length - uniqueEvents.length;
                if (more > 0) {
                    const moreSpan = document.createElement('span');
                    moreSpan.style.cssText = 'font-size:0.55rem;color:var(--text-secondary);';
                    moreSpan.textContent = `+${more}`;
                    evDiv.appendChild(moreSpan);
                }

                c.appendChild(evDiv);
                c.addEventListener('click', () => showDayDetail(dateStr, events.agenda));
            } else {
                c.addEventListener('click', () => {
                    window.location.href = `/agenda?date=${dateStr}`;
                });
            }

            const num = document.createElement('span');
            num.className = 'day-number';
            num.textContent = d;
            c.insertBefore(num, c.firstChild);

            grid.appendChild(c);
            renderedDays++;
        }

        // Mobile list view
        renderList(todayStr);

        const calDebug = document.getElementById('calDebug');
        if (calDebug) {
            calDebug.innerHTML = `
                <summary style="cursor:pointer; font-size:0.85rem; color:var(--text-secondary); font-weight:600;">Render Status</summary>
                <div style="padding:1rem; font-size:0.8rem; font-family:monospace;">
                    <p style="color:var(--grade-green)">renderCalendar() COMPLETED</p>
                    <p>month = ${monthName} ${currentYear}, days = ${daysInMonth}, startDow = ${startDow}</p>
                    <p>rendered days = ${renderedDays}, with events = ${renderedWithEvents}</p>
                    <p>grid children = ${grid.children.length}</p>
                </div>
            `;
        }
    }

    function renderList(todayStr) {
        const list = document.getElementById('calList');
        if (!list) return;
        list.innerHTML = '';

        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        let hasAnyEvents = false;
        let currentDowHeader = '';

        for (let d = 1; d <= daysInMonth; d++) {
            const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
            const events = allEvents[dateStr] || null;
            const agenda = events ? events.agenda : [];

            // Day-of-week separator
            const dt = new Date(dateStr + 'T12:00:00');
            const dowLabel = dt.toLocaleDateString('it-IT', { weekday: 'long' });
            const dow = giorniLun[(dt.getDay() + 6) % 7]; // lun=0

            if (d === 1) {
                const title = document.createElement('div');
                title.className = 'cal-list-group-title';
                title.textContent = dow;
                list.appendChild(title);
                currentDowHeader = dow;
            }

            if (agenda.length > 0) {
                hasAnyEvents = true;
                const item = document.createElement('div');
                item.className = 'cal-list-item' + (dateStr === todayStr ? ' today' : '');
                item.dataset.date = dateStr;

                const dateCol = document.createElement('div');
                dateCol.className = 'cal-list-date';
                dateCol.innerHTML = `<span class="day-num">${d}</span><span class="dow">${dow}</span>`;

                const evts = document.createElement('div');
                evts.className = 'cal-list-events';

                for (const evt of agenda) {
                    const subj = evt.subjectDesc || evt.title || evt.authorName || 'Evento';
                    const notes = evt.notes || '';
                    const isT = isLikelyTest(notes);
                    const div = document.createElement('div');
                    div.className = 'cal-list-event' + (isT ? ' is-test' : '');
                    div.textContent = subj;
                    evts.appendChild(div);
                }

                item.appendChild(dateCol);
                item.appendChild(evts);
                item.addEventListener('click', () => showDayDetail(dateStr, agenda));
                list.appendChild(item);
            }
        }

        if (!hasAnyEvents) {
            list.innerHTML = '<div class="cal-list-empty">Nessun evento questo mese.</div>';
        }
    }

    function showDayDetail(dateStr, agendaEvents) {
        const sheet = document.getElementById('calDetail');
        const d = new Date(dateStr + 'T12:00:00');
        document.getElementById('calSheetTitle').textContent =
            d.toLocaleDateString('it-IT', { weekday: 'long', day: 'numeric', month: 'long' });

        let html = '';
        for (const a of agendaEvents) {
            const subj = a.subjectDesc || a.title || a.authorName || 'Evento';
            const desc = a.notes || a.evtDescr || a.description || '';
            const combined = (subj + ' ' + desc);
            const keyword = getTestKeyword(combined);
            let eventClass = '';
            let highlightedSubj = subj;
            let highlightedDesc = desc;
            if (keyword) {
                eventClass = ' test-event';
                highlightedSubj = highlightKeywordInText(subj, keyword);
                highlightedDesc = highlightKeywordInText(desc, keyword);
            }
            html += `<div class="cal-detail-event${eventClass}">
                <h4>${highlightedSubj}</h4>
                ${highlightedDesc ? `<p>${highlightedDesc}</p>` : ''}
            </div>`;
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
