<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

if (!isset($response)) { $response = ['error' => '']; }

$currentDate = isset($date_from) ? new DateTime($date_from) : new DateTime();
$giorniIt = ['Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato'];
$mesiIt = ['gen','feb','mar','apr','mag','giu','lug','ago','set','ott','nov','dic'];
$dayOfWeek = $giorniIt[(int)$currentDate->format('w')];
$dayNum = $currentDate->format('j');
$monthIt = $mesiIt[(int)$currentDate->format('n') - 1];
$formattedDate = $dayOfWeek . ', ' . $dayNum . ' ' . $monthIt;
$dateForInput = $currentDate->format('Y-m-d');

$today = new DateTime();
$dateInDays = (date_diff($today, $currentDate)->days);
$dayOffset = ($today > $currentDate) ? ($dateInDays) : -($dateInDays);
?>

<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GradeCraft - Oggi</title>
    <?php include COMMON_HTML_HEAD ?>
    <style>
        .agenda-content { position: relative; overflow: hidden; }
        .agenda-content.transitioning { pointer-events: none; }
        .agenda-content.fade-in {
            animation: fadeIn 0.25s ease-in-out forwards;
            pointer-events: none;
        }
        @keyframes fadeIn {
            0%   { opacity: 0; }
            100% { opacity: 1; }
        }
        .agenda-content.exiting-left {
            animation: slideOutLeft 0.25s ease-in-out forwards;
        }
        .agenda-content.entering-left {
            animation: slideInLeft 0.25s ease-in-out forwards;
        }
        .agenda-content.exiting-right {
            animation: slideOutRight 0.25s ease-in-out forwards;
        }
        .agenda-content.entering-right {
            animation: slideInRight 0.25s ease-in-out forwards;
        }
        @keyframes slideOutLeft {
            0%   { transform: translateX(0); opacity: 1; }
            100% { transform: translateX(-40px); opacity: 0; }
        }
        @keyframes slideInLeft {
            0%   { transform: translateX(40px); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            0%   { transform: translateX(0); opacity: 1; }
            100% { transform: translateX(40px); opacity: 0; }
        }
        @keyframes slideInRight {
            0%   { transform: translateX(-40px); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
        .event-card { position: relative; }
        .event-card .test-toggle-btn {
            position: absolute; top: 0.5rem; right: 0.5rem;
            background: none; border: 1px solid var(--border-color); border-radius: 0.4rem;
            padding: 0.25rem; color: var(--text-secondary); cursor: pointer; opacity: 0.4;
            transition: opacity 0.2s, color 0.2s, border-color 0.2s;
        }
        .event-card .test-toggle-btn:hover { opacity: 1; }
        .event-card.test-event .test-toggle-btn { opacity: 1; color: var(--test-color); border-color: var(--test-color); }
        .event-card.test-event { border-left-color: var(--test-color) !important; }
        .test-badge {
            position: absolute; top: -0.5rem; right: 1rem;
            background: var(--test-color); color: white;
            font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em;
            padding: 0.15rem 0.5rem; border-radius: 1rem; box-shadow: var(--shadow-sm);
        }
        .test-keyword {
            color: var(--test-color);
            font-weight: 600;
        }
        .calendar-strip {
            display: flex; gap: 0.25rem; padding: 0 1rem; overflow-x: auto;
        }
        .calendar-day-link {
            flex-shrink: 0; width: 2.8rem; text-align: center; padding: 0.3rem 0;
            border-radius: 0.5rem; text-decoration: none; color: var(--text-secondary);
            border: 1px solid var(--border-color); transition: background 0.15s;
        }
        .calendar-day-link:hover, .calendar-day-link.active {
            background: var(--accent-blue); color: white; border-color: var(--accent-blue);
        }
        .calendar-day-link.active .cal-dow { color: rgba(255,255,255,0.8); }
        .cal-dow { font-size: 0.65rem; text-transform: uppercase; display: block; }
        .cal-num { font-size: 1rem; font-weight: 600; display: block; line-height: 1.2; }

        /* Mobile */
        @media (max-width: 480px) {
            .container { padding: 0 0.5rem; }
            .event-card { margin: 0.25rem 0.5rem !important; padding: 0.75rem !important; }
            .event-card h3 { font-size: 1rem !important; }
            .event-card p { font-size: 0.8rem !important; }
        }
    </style>
</head>
<body data-theme="<?=THEME?>">
    <div class="container">

        <?php if (!isset($_SESSION['classeviva_ident'])) cvv_sync(); ?>

        <!-- Header -->
        <header style="padding: 2rem 1rem 1rem;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin: 0; line-height: 1.2;">
                <?= $formattedDate ?>
            </h1>
        </header>

        <!-- View Tabs -->
        <div style="display: flex; gap: 0.5rem; padding: 0 1rem; margin-bottom: 0.5rem;">
            <a href="/agenda<?= $dateForInput ? '?date=' . $dateForInput : '' ?>" class="view-tab <?= strpos($dateForInput ?? '', '') !== false ? 'active' : '' ?>" style="flex: 1; text-align: center; padding: 0.5rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 600; background: var(--accent-blue); color: white;">Timeline</a>
            <a href="/agenda/calendar<?= $dateForInput ? '?date=' . $dateForInput : '' ?>" class="view-tab" style="flex: 1; text-align: center; padding: 0.5rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 600; background: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">Calendario</a>
        </div>

        <!-- Date Navigation -->
        <div style="padding: 0 1rem 1rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <button onclick="navigateDate(-1)" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 0.5rem; padding: 0.5rem 1rem; color: var(--text-primary); cursor: pointer; font-size: 1.2rem; line-height: 1;">←</button>
                <button onclick="navigateDate(<?=$dayOffset?>)" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 0.5rem; padding: 0.5rem 1rem; color: var(--text-primary); cursor: pointer; font-size: 0.875rem;">Oggi</button>
                <button onclick="navigateDate(1)" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 0.5rem; padding: 0.5rem 1rem; color: var(--text-primary); cursor: pointer; font-size: 1.2rem; line-height: 1;">→</button>
            </div>
            <div>
                <input type="date" id="datePicker" value="<?= $dateForInput ?>" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 0.5rem; padding: 0.5rem; color: var(--text-primary);">
            </div>
        </div>

        <div class="agenda-content" id="agendaContent">
        <?php if (isset($error) && $error): ?>
            <div class="my-card">
                <p style="color: var(--grade-red); margin: 0;">Errore: <?= htmlspecialchars($error) ?></p>
            </div>
        <?php else: ?>
            <div class="timeline" style="padding-bottom: 5rem;">
                <?php if (empty($lessons) && empty($agenda)): ?>
                    <div class="my-card">
                        <p style="color: var(--text-secondary); margin: 0;">Nessun evento per oggi.</p>
                    </div>
                <?php else: ?>
                    <!-- Lezioni Section -->
                    <?php if (!empty($lessons)): ?>
                        <h3 style="font-size: 1.25rem; font-weight: 600; margin: 1rem 0 0.5rem; color: var(--text-primary); padding: 0 1rem;">Lezioni</h3>
                        <?php foreach ($lessons as $lessonIdx => $lesson):
                            $subject = $lesson['subjectDesc'] ?? $lesson['subject'] ?? 'Lezione';
                            $desc = $lesson['lessonArg'] ?? $lesson['lessonType'] ?? '';
                            $type = $lesson['lessonType'] ?? '';
                            $evtDate = $lesson['evtDate'] ?? '';
                            $evtHPos = $lesson['evtHPos'] ?? null;
                            $duration = $lesson['evtDuration'] ?? 1;
                            $startTime = $lesson['startTime'] ?? $lesson['evtTime'] ?? null;
                            $endTime = $lesson['endTime'] ?? null;
                            if ($startTime && $endTime) {
                                $timeStr = $startTime . ' - ' . $endTime;
                            } elseif ($evtHPos) {
                                $timeStr = $evtHPos . 'ª ora' . ($duration > 1 ? " ({$duration}h)" : '');
                            } else {
                                $timeStr = '';
                            }
                            $isOngoing = false;
                            if ($startTime && $endTime && $evtDate) {
                                try {
                                    $now = new DateTime();
                                    $start = new DateTime($evtDate . ' ' . $startTime);
                                    $end = new DateTime($evtDate . ' ' . $endTime);
                                    $isOngoing = ($now >= $start && $now <= $end);
                                } catch (Exception $e) { $isOngoing = false; }
                            }
                            $evtData = json_encode($lesson, JSON_UNESCAPED_UNICODE);
                        ?>
                            <div class="event-card my-card" data-event-idx="lesson_<?= $lessonIdx ?>" style="margin: 0.5rem 1rem; border-left: <?= $isOngoing ? '4px solid var(--accent-blue)' : '4px solid var(--border-color)' ?>; transition: border-color 0.2s ease;">
                                <?php if ($isOngoing): ?>
                                    <div style="position: absolute; top: -0.5rem; left: 1rem; background: var(--accent-blue); color: white; font-size: 0.75rem; font-weight: 600; padding: 0.125rem 0.5rem; border-radius: 1rem; box-shadow: var(--shadow-sm);">in corso</div>
                                <?php endif; ?>
                                <button class="test-toggle-btn" onclick="window.toggleTest('lesson_<?= $lessonIdx ?>')" title="Segna come verifica">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 2L3 9l4 5 5 8 8-8z"/></svg>
                                </button>
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                    <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0; color: var(--text-primary);"><?= htmlspecialchars($subject) ?></h3>
                                    <?php if ($timeStr): ?>
                                        <span style="font-size: 0.875rem; color: var(--text-secondary); white-space: nowrap; flex-shrink: 0;"><?= htmlspecialchars($timeStr) ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($desc): ?>
                                    <p style="font-size: 0.875rem; color: var(--text-primary); margin: 0; line-height: 1.5;"><?= nl2br(htmlspecialchars($desc)) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($type) && $type != $desc): ?>
                                    <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0.25rem 0 0 0; font-style: italic;"><?= htmlspecialchars($type) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($_SESSION['show_json_debug'])): ?>
                                    <pre style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 0.5rem; overflow: auto; max-height: 200px; height: 100px;"><?= htmlspecialchars(json_encode($evtData, JSON_PRETTY_PRINT)); ?></pre>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Compiti Section -->
                    <?php if (!empty($agenda)): ?>
                        <h3 style="font-size: 1.25rem; font-weight: 600; margin: 1rem 0 0.5rem; color: var(--text-primary); padding: 0 1rem;">Compiti</h3>
                        <?php foreach ($agenda as $eventIdx => $event):
                            $subject = $event['subjectDesc'] ?? $event['subject'] ?? $event['subjectDescription'] ?? $event['authorName'] ?? $event['title'] ?? 'Evento';
                            $notes = $event['notes'] ?? $event['evtDescr'] ?? $event['description'] ?? $event['evtNotes'] ?? $event['text'] ?? '';
                            $datetimeBegin = $event['evtDatetimeBegin'] ?? $event['startTime'] ?? $event['evtTime'] ?? '';
                            $datetimeEnd = $event['evtDatetimeEnd'] ?? $event['endTime'] ?? '';
                            $evtDate = $event['evtDate'] ?? $event['date'] ?? '';
                            $timeStr = '';
                            if ($datetimeBegin) {
                                try {
                                    $dtBegin = new DateTime($datetimeBegin);
                                    if ($datetimeEnd) {
                                        $dtEnd = new DateTime($datetimeEnd);
                                        if ($dtBegin->format('Y-m-d') !== $dtEnd->format('Y-m-d')) {
                                            $timeStr = $dtBegin->format('d/m') . ' → ' . $dtEnd->format('d/m/Y');
                                        } else {
                                            $timeStr = $dtBegin->format('H:i') . ' - ' . $dtEnd->format('H:i');
                                        }
                                    } else { $timeStr = $dtBegin->format('H:i'); }
                                } catch (Exception $e) { $timeStr = $datetimeBegin; }
                            } elseif ($evtDate) {
                                try { $timeStr = (new DateTime($evtDate))->format('d/m/Y'); } catch (Exception $e) { $timeStr = $evtDate; }
                            }
                            $isOngoing = false;
                            if ($datetimeBegin) {
                                try {
                                    $now = new DateTime(); $start = new DateTime($datetimeBegin);
                                    if ($datetimeEnd) { $end = new DateTime($datetimeEnd); $isOngoing = ($now >= $start && $now <= $end); }
                                    else { $isOngoing = ($now >= $start); }
                                } catch (Exception $e) { $isOngoing = false; }
                            }
                            $evtData2 = json_encode($event, JSON_UNESCAPED_UNICODE);
                        ?>
                            <div class="event-card my-card" data-event-idx="agenda_<?= $eventIdx ?>" style="margin: 0.5rem 1rem; border-left: <?= $isOngoing ? '4px solid var(--accent-blue)' : '4px solid var(--border-color)' ?>; transition: border-color 0.2s ease;">
                                <?php if ($isOngoing): ?>
                                    <div style="position: absolute; top: -0.5rem; left: 1rem; background: var(--accent-blue); color: white; font-size: 0.75rem; font-weight: 600; padding: 0.125rem 0.5rem; border-radius: 1rem; box-shadow: var(--shadow-sm);">in corso</div>
                                <?php endif; ?>
                                <button class="test-toggle-btn" onclick="window.toggleTest('agenda_<?= $eventIdx ?>')" title="Segna come verifica">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 2L3 9l4 5 5 8 8-8z"/></svg>
                                </button>
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                    <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0; color: var(--text-primary);"><?= htmlspecialchars($subject) ?></h3>
                                    <?php if ($timeStr): ?>
                                        <span style="font-size: 0.875rem; color: var(--text-secondary); white-space: nowrap; flex-shrink: 0;"><?= htmlspecialchars($timeStr) ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($notes): ?>
                                    <p style="font-size: 0.875rem; color: var(--text-primary); margin: 0; line-height: 1.5;"><?= nl2br(htmlspecialchars($notes)) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($_SESSION['show_json_debug'])): ?>
                                    <pre style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 0.5rem; overflow: auto; max-height: 200px;"><?= htmlspecialchars($evtData2) ?></pre>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        </div>
    </div>

    <script>
    let isAnimating = false;
    const ANIM_DURATION = 250;

    function navigateDate(offset) {
        if (isAnimating) return;
        const content = document.getElementById('agendaContent');
        if (!content) { doNavigate(offset); return; }

        isAnimating = true;
        content.classList.add('transitioning');

        // Slide out
        if (offset < 0) content.classList.add('exiting-right');
        else if (offset > 0) content.classList.add('exiting-left');

        setTimeout(() => {
            content.classList.remove('exiting-left', 'exiting-right');
            doNavigate(offset);
        }, ANIM_DURATION);
    }

    function doNavigate(offset) {
        const datePicker = document.getElementById('datePicker');
        const [year, month, day] = datePicker.value.split('-').map(Number);
        let date = new Date(year, month - 1, day);
        date.setDate(date.getDate() + offset);
        const newDateStr = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
        datePicker.value = newDateStr;
        window.location.href = '?date=' + newDateStr;
    }

    // Fade-in on page load when navigating from a deep link or refresh with date param
    document.addEventListener('DOMContentLoaded', () => {
        const content = document.getElementById('agendaContent');
        if (!content) return;
        const params = new URLSearchParams(window.location.search);
        if (params.has('date')) {
            content.style.opacity = '0';
            requestAnimationFrame(() => {
                content.classList.add('fade-in');
                content.addEventListener('animationend', () => {
                    content.classList.remove('fade-in');
                    content.style.opacity = '';
                    isAnimating = false;
                }, { once: true });
            });
        }        
    });

    document.getElementById('datePicker').addEventListener('change', function() {
        window.location.href = '?date=' + this.value;
    });
    </script>

    <script type="module">
    import { IndexedDBService } from '/js/dbApi.js';
    import { isLikelyTest, applyTestStyle, removeTestStyle, getTestKeyword } from '/js/agendaTests.js';

    const lessons = <?= json_encode($lessons ?? [], JSON_UNESCAPED_UNICODE) ?>;
    const agenda = <?= json_encode($agenda ?? [], JSON_UNESCAPED_UNICODE) ?>;

    const dbService = new IndexedDBService('gradecraft', 1, {
        grades: { keyPath: 'ident', autoIncrement: true },
        agendaAnnotations: {},
    });

    async function initDB() {
        try {
            console.log("aaaaa");
            
            await dbService.init('grades');
            await dbService.init('agendaAnnotations');
            console.log('Database IndexedDB pronto!', 'success');
            applyAllTestStyles();
        } catch (error) {
            console.log(`Errore nell'inizializzazione del database: ${error.message}`, 'error');
        }
    }

    async function applyAllTestStyles() {
        const annotations = await dbService.getAllAnnotations();

        function aggregateText(event) {
            return [
                event.subjectDesc,
                event.notes,
                event.lessonArg,
                event.lessonType,
                event.title,
                event.evtDescr,
                event.description,
                event.evtNotes,
            ].filter(Boolean).join(' ');
        }

        function checkAndStyle(events, prefix) {
            events.forEach((evt, idx) => {
                const key = `${prefix}_${idx}`;
                const card = document.querySelector(`[data-event-idx="${key}"]`);
                if (!card) return;

                const annotation = annotations[key];
                let isTest;
                let keyword = null;
                if (annotation?.isTest !== undefined) {
                    isTest = annotation.isTest;
                    if (isTest) {
                        keyword = getTestKeyword(aggregateText(evt));
                    }
                } else {
                    keyword = getTestKeyword(aggregateText(evt));
                    isTest = !!keyword;
                }

                if (isTest) {
                    console.log("Styling");
                    applyTestStyle(card, keyword);
                } else {
                    console.log("Not styling");
                    removeTestStyle(card);
                }
            });
        }

        checkAndStyle(lessons, 'lesson');
        checkAndStyle(agenda, 'agenda');
    }

    window.toggleTest = async function(key) {
        const card = document.querySelector(`[data-event-idx="${key}"]`);
        if (!card) return;

        const [prefix, idx] = key.split('_');
        const events = prefix === 'lesson' ? lessons : agenda;
        const evt = events[parseInt(idx)];
        if (!evt) return;

        const annotation = await dbService.getAnnotation(key);
        const text = [
            evt.subjectDesc,
            evt.notes,
            evt.lessonArg,
            evt.lessonType,
            evt.title,
            evt.evtDescr,
            evt.description,
            evt.evtNotes,
        ].filter(Boolean).join(' ');
        const autoKeyword = getTestKeyword(text);
        const autoDetected = !!autoKeyword;
        const current = annotation?.isTest ?? autoDetected;

        await dbService.saveAnnotation(key, { isTest: !current });

        if (!current) {
            applyTestStyle(card, autoKeyword);
        } else {
            removeTestStyle(card);
        }
    };

    document.addEventListener("DOMContentLoaded",() => {console.log("DOM loaded"); initDB();});
</script>

    <?php include COMMON_HTML_FOOT ?>
</body>
</html>
