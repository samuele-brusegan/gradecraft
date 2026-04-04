<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

if (!isset($grades)) { $grades = ['error' => '']; }
if (!isset($subjectAverages)) $subjectAverages = [];
if (!isset($overallAvg)) $overallAvg = null;
if (!isset($globalPeriods)) $globalPeriods = [];

$gradeColor = fn(float $v): string =>
    $v >= 7 ? 'var(--grade-green)' : ($v >= 6 ? 'var(--grade-yellow)' : 'var(--grade-red)');

$progressBar = function(float $value, float $max, string $label = '', string $extraClass = '') use ($gradeColor): string {
    $pct = min(max($value / $max, 0), 1) * 100;
    $clr = $gradeColor($value);
    $safeLabel = htmlspecialchars($label);
    $labelHtml = $safeLabel !== '' ? '<span class="bar-label">' . $safeLabel . '</span>' : '';
    return '<div class="bar-wrap ' . $extraClass . '">'
         . '<div class="bar-top"><span class="bar-value">' . $value . '</span><span class="bar-suffix">/10</span></div>'
         . '<div class="bar-track"><div class="bar-fill" style="width:' . $pct . '%;background:' . $clr . '"></div></div>'
         . $labelHtml
         . '</div>';
};

$subjectId = $_GET['subject'] ?? null;

// Se abbiamo $subject, siamo in modalità dettaglio
if (isset($subject) && $subject):
    usort($grades, function($a, $b) {
        return strtotime($b['evtDate']) - strtotime($a['evtDate']);
    });

    $maxGrade = null;
    foreach ($grades as $g) {
        if ($maxGrade === null || $g['decimalValue'] > $maxGrade['decimalValue']) {
            $maxGrade = $g;
        }
    }

    $maxVal = $maxGrade ? $maxGrade['decimalValue'] : 0;
    $maxColor = $maxGrade && $maxGrade['color'] == 'blue'
        ? 'var(--accent-blue)'
        : $gradeColor($maxVal);
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

        <?php if (isset($grades['error'])): ?>
            <p class="error-text"><?= htmlspecialchars($grades['error']) ?></p>
        <?php else: ?>

            <!-- Header -->
            <header class="top-bar">
                <a href="/grades" class="back-link" aria-label="Torna indietro">
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
                    <div class="bar-wrap highlight">
                        <div class="bar-top">
                            <span class="bar-value"><?= $maxVal ?></span>
                            <span class="bar-suffix">/10</span>
                        </div>
                        <?php
                        $maxPct = min(max($maxVal / 10, 0), 1) * 100;
                        ?>
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
                        $clr = $grade['color'] == 'blue' ? 'var(--accent-blue)' : $gradeColor($value);
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

<?php
else:
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GradeCraft - Voti</title>
    <?php include COMMON_HTML_HEAD ?>
</head>
<body data-theme="<?=THEME?>">
    <div class="page-container page-votes">

        <?php if (isset($error) && $error): ?>
            <p class="error-text"><?= htmlspecialchars($error) ?></p>
        <?php elseif (empty($subjectAverages)): ?>
            <p class="empty-text">Nessun voto disponibile.</p>
        <?php else: ?>

            <!-- Header -->
            <header class="top-bar">
                <h1 class="top-title">Voti</h1>
            </header>

            <!-- Media generale -->
            <?php if ($overallAvg !== null): ?>
                <div class="overall-strip">
                    <?= $progressBar(round($overallAvg, 1), 10, 'Media generale', 'highlight') ?>
                </div>
            <?php endif; ?>

            <!-- Medie per periodo -->
            <?php if (!empty($globalPeriods)): ?>
                <div class="period-strip">
                    <?php foreach ($globalPeriods as $pDesc => $pData): ?>
                        <div class="period-mini">
                            <?= $progressBar(round($pData['avg'], 1), 10, $pDesc, 'mini') ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Materie -->
            <section class="subject-list">
                <?php foreach ($subjectAverages as $sid => $subj): ?>
                    <div class="subject-row" onclick="window.location.href='/grades/subject/<?= $sid ?>'" style="cursor:pointer">
                        <div class="subject-top">
                            <span class="subject-name"><?= htmlspecialchars($subj['name']) ?></span>
                            <span class="subject-arrow">
                                <svg viewBox="0 0 24 24" fill="none" width="18" height="18">
                                    <path d="M9 5L16 12L9 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>
                        <?= $progressBar(round($subj['total_avg'], 1), 10) ?>
                    </div>
                <?php endforeach; ?>
            </section>

        <?php endif; ?>

    </div>
    <?php include COMMON_HTML_FOOT ?>
</body>
</html>
<?php
endif;
?>
