<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

if (!isset($response)) { $response = ['error' => '']; }

// Use the current date from controller (default to today if not set)
$currentDate = isset($date_from) ? new DateTime($date_from) : new DateTime();
$formattedDate = $currentDate->format('j M');
$dateForInput = $currentDate->format('Y-m-d');
?>

<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GradeCraft - Oggi</title>
    <?php include COMMON_HTML_HEAD ?>
</head>
<body data-theme="<?=THEME?>">
    <div class="container">

        <?php if (!isset($_SESSION['classeviva_ident'])) cvv_sync(); ?>

        <!-- Header -->
        <header style="padding: 2rem 1rem 1rem;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin: 0; line-height: 1.2;">
                Oggi, <?= $formattedDate ?>
            </h1>
        </header>

        <!-- Date Navigation -->
        <div style="padding: 0 1rem 1rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <button onclick="navigateDate(-1)" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 0.5rem; padding: 0.5rem 1rem; color: var(--text-primary); cursor: pointer; font-size: 1.2rem; line-height: 1;">←</button>
                <button onclick="navigateDate(0)" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 0.5rem; padding: 0.5rem 1rem; color: var(--text-primary); cursor: pointer; font-size: 0.875rem;">Oggi</button>
                <button onclick="navigateDate(1)" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 0.5rem; padding: 0.5rem 1rem; color: var(--text-primary); cursor: pointer; font-size: 1.2rem; line-height: 1;">→</button>
            </div>
            <div>
                <input type="date" id="datePicker" value="<?= $dateForInput ?>" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 0.5rem; padding: 0.5rem; color: var(--text-primary);">
            </div>
        </div>

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
                        <?php foreach ($lessons as $lesson):
                            $subject = $lesson['subjectDesc'] ?? $lesson['subject'] ?? 'Lezione';
                            $desc = $lesson['lessonArg'] ?? $lesson['lessonType'] ?? '';
                            $type = $lesson['lessonType'] ?? '';
                            $evtDate = $lesson['evtDate'] ?? '';
                            $evtHPos = $lesson['evtHPos'] ?? null;
                            $duration = $lesson['evtDuration'] ?? 1;
                            $startTime = $lesson['startTime'] ?? $lesson['evtTime'] ?? null;
                            $endTime = $lesson['endTime'] ?? null;
                            // Build time string
                            if ($startTime && $endTime) {
                                $timeStr = $startTime . ' - ' . $endTime;
                            } elseif ($evtHPos) {
                                $timeStr = $evtHPos . 'ª ora' . ($duration > 1 ? " ({$duration}h)" : '');
                            } else {
                                $timeStr = '';
                            }
                            // Determine if ongoing if we have times
                            $isOngoing = false;
                            if ($startTime && $endTime && $evtDate) {
                                try {
                                    $now = new DateTime();
                                    $start = new DateTime($evtDate . ' ' . $startTime);
                                    $end = new DateTime($evtDate . ' ' . $endTime);
                                    $isOngoing = ($now >= $start && $now <= $end);
                                } catch (Exception $e) {
                                    $isOngoing = false;
                                }
                            }
                        ?>
                            <div class="event-card my-card" style="margin: 0.5rem 1rem; position: relative; border-left: <?= $isOngoing ? '4px solid var(--accent-blue)' : '4px solid var(--border-color)' ?>; transition: border-color 0.2s ease;">
                                <?php if ($isOngoing): ?>
                                    <div style="position: absolute; top: -0.5rem; left: 1rem; background: var(--accent-blue); color: white; font-size: 0.75rem; font-weight: 600; padding: 0.125rem 0.5rem; border-radius: 1rem; box-shadow: var(--shadow-sm);">in corso</div>
                                <?php endif; ?>
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
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Compiti Section -->
                    <?php if (!empty($agenda)): ?>
                        <h3 style="font-size: 1.25rem; font-weight: 600; margin: 1rem 0 0.5rem; color: var(--text-primary); padding: 0 1rem;">Compiti</h3>
                        <?php foreach ($agenda as $event):
                            // Get subject with multiple fallbacks
                            $subject = $event['subjectDesc'] ?? $event['subject'] ?? $event['subjectDescription'] ?? $event['title'] ?? 'Evento';
                            // Get description/notes with multiple fallbacks (Classeviva uses 'notes' for homework)
                            $notes = $event['notes'] ?? $event['evtDescr'] ?? $event['description'] ?? $event['evtNotes'] ?? $event['text'] ?? '';
                            // Get date/time - Classeviva uses evtDatetimeBegin/End
                            $datetimeBegin = $event['evtDatetimeBegin'] ?? $event['startTime'] ?? $event['evtTime'] ?? '';
                            $datetimeEnd = $event['evtDatetimeEnd'] ?? $event['endTime'] ?? '';
                            $evtDate = $event['evtDate'] ?? $event['date'] ?? '';

                            // Build time string
                            $timeStr = '';
                            if ($datetimeBegin) {
                                try {
                                    $dtBegin = new DateTime($datetimeBegin);
                                    $timeStr = $dtBegin->format('H:i');
                                    if ($datetimeEnd) {
                                        $dtEnd = new DateTime($datetimeEnd);
                                        $timeStr .= ' - ' . $dtEnd->format('H:i');
                                    }
                                } catch (Exception $e) {
                                    $timeStr = $datetimeBegin;
                                }
                            } elseif ($evtDate) {
                                try {
                                    $date = new DateTime($evtDate);
                                    $timeStr = $date->format('d/m/Y');
                                } catch (Exception $e) {
                                    $timeStr = $evtDate;
                                }
                            }

                            // Check if ongoing (if we have times)
                            $isOngoing = false;
                            if ($datetimeBegin) {
                                try {
                                    $now = new DateTime();
                                    $start = new DateTime($datetimeBegin);
                                    if ($datetimeEnd) {
                                        $end = new DateTime($datetimeEnd);
                                        $isOngoing = ($now >= $start && $now <= $end);
                                    } else {
                                        $isOngoing = ($now >= $start);
                                    }
                                } catch (Exception $e) {
                                    $isOngoing = false;
                                }
                            }
                        ?>
                            <div class="event-card my-card" style="margin: 0.5rem 1rem; position: relative; border-left: <?= $isOngoing ? '4px solid var(--accent-blue)' : '4px solid var(--border-color)' ?>; transition: border-color 0.2s ease;">
                                <?php if ($isOngoing): ?>
                                    <div style="position: absolute; top: -0.5rem; left: 1rem; background: var(--accent-blue); color: white; font-size: 0.75rem; font-weight: 600; padding: 0.125rem 0.5rem; border-radius: 1rem; box-shadow: var(--shadow-sm);">in corso</div>
                                <?php endif; ?>
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                    <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0; color: var(--text-primary);"><?= htmlspecialchars($subject) ?></h3>
                                    <?php if ($timeStr): ?>
                                        <span style="font-size: 0.875rem; color: var(--text-secondary); white-space: nowrap; flex-shrink: 0;"><?= htmlspecialchars($timeStr) ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($notes): ?>
                                    <p style="font-size: 0.875rem; color: var(--text-primary); margin: 0; line-height: 1.5;"><?= nl2br(htmlspecialchars($notes)) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
    function navigateDate(offset) {
        const datePicker = document.getElementById('datePicker');
        const [year, month, day] = datePicker.value.split('-').map(Number);
        let date = new Date(year, month - 1, day);
        date.setDate(date.getDate() + offset);
        const newDateStr = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
        datePicker.value = newDateStr;
        window.location.href = '?date=' + newDateStr;
    }
    document.getElementById('datePicker').addEventListener('change', function() {
        window.location.href = '?date=' + this.value;
    });
    </script>

    <?php include COMMON_HTML_FOOT ?>
</body>
</html>
