<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

$gradeColor = fn(float $v): string =>
    $v >= 7 ? 'var(--grade-green)' : ($v >= 6 ? 'var(--grade-yellow)' : 'var(--grade-red)');
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GradeCraft - <?= htmlspecialchars($subject['description'] ?? 'Materia') ?></title>
    <?php include COMMON_HTML_HEAD ?>
</head>
<body data-theme="<?=THEME?>">
    <div class="page-container page-votes">
        <?php if (!isset($_SESSION['classeviva_ident'])) cvv_sync(); ?>

        <?php if (is_array($grades) && isset($grades['error'])): ?>
            <p class="error-text"><?= htmlspecialchars($grades['error']) ?></p>
        <?php else: ?>

            <?php
            // Trova voto più alto
            $maxGrade = null;
            foreach ($grades as $g) {
                if ($maxGrade === null || $g['decimalValue'] > $maxGrade['decimalValue']) {
                    $maxGrade = $g;
                }
            }

            $maxVal = $maxGrade ? $maxGrade['decimalValue'] : 0;
            $maxColor = $maxGrade && ($maxGrade['color'] ?? '') == 'blue'
                ? 'var(--accent-blue)'
                : $gradeColor($maxVal);
            ?>

            <!-- Header -->
            <header class="top-bar">
                <a href="/grades" class="back-link" aria-label="Torna ai voti">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M13 16L6 10L13 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <div class="top-bar-info">
                    <h1 class="top-title"><?= htmlspecialchars($subject['description']) ?></h1>
                    <span class="top-subtitle"><?= count($grades) ?> vot<?= count($grades) === 1 ? 'o' : 'i' ?></span>
                </div>
            </header>

            <!-- Voto più alto -->
            <?php if ($maxGrade): ?>
                <div class="max-grade-strip">
                    <div class="max-grade-label">Più alto</div>
                    <?php
                    $maxPct = min(max($maxVal / 10, 0), 1) * 100;
                    ?>
                    <div class="bar-wrap highlight">
                        <div class="bar-top">
                            <span class="bar-value"><?= $maxVal ?></span>
                            <span class="bar-suffix">/10</span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill" style="width:<?= $maxPct ?>%;background:<?= $maxColor ?>"></div>
                        </div>
                    </div>
                    <div class="max-grade-date"><?= date('d/m/Y', strtotime($maxGrade['evtDate'])) ?></div>
                </div>
            <?php endif; ?>

            <!-- Lista voti -->
            <section class="grade-list">
                <?php if (count($grades) > 0): ?>
                    <?php foreach ($grades as $grade):
                        $value = $grade['decimalValue'];
                        $pct = min(max($value / 10, 0), 1) * 100;
                        $clr = ($grade['color'] ?? '') == 'blue' ? 'var(--accent-blue)' : $gradeColor($value);
                        $date = date('d/m/Y', strtotime($grade['evtDate']));
                    ?>
                        <div class="grade-row">
                            <div class="grade-pill" style="background:<?= $clr ?>">
                                <span><?= $value ?></span>
                            </div>
                            <div class="grade-body">
                                <div class="grade-top">
                                    <span class="grade-type"><?= htmlspecialchars($grade['componentDesc'] ?? 'Voto') ?></span>
                                    <span class="grade-date"><?= $date ?></span>
                                </div>
                                <div class="grade-micro-track">
                                    <div class="grade-micro-fill" style="width:<?= $pct ?>%;background:<?= $clr ?>"></div>
                                </div>
                                <?php if (!empty($grade['teacherName'])): ?>
                                    <div class="grade-teacher"><?= htmlspecialchars($grade['teacherName']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($grade['notesForFamily'])): ?>
                                    <div class="grade-notes"><?= htmlspecialchars($grade['notesForFamily']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="empty-text">Nessun voto presente.</p>
                <?php endif; ?>
            </section>

        <?php endif; ?>
    </div>
    <?php include COMMON_HTML_FOOT ?>
</body>
</html>
