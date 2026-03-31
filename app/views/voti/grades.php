<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

if (!isset($grades)) { $grades = ['error' => '']; }

$subjectId = $_GET['subject'] ?? null;

// Se abbiamo $subject, significa che siamo in modalità dettaglio
if (isset($subject) && $subject):
    // Ordinamento dei voti per data (più recenti prima)
    usort($grades, function($a, $b) {
        return strtotime($b['evtDate']) - strtotime($a['evtDate']);
    });

    // Trova voto più alto
    $maxGrade = null;
    foreach ($grades as $g) {
        if ($maxGrade === null || $g['decimalValue'] > $maxGrade['decimalValue']) {
            $maxGrade = $g;
        }
    }
?>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GradeCraft - <?= htmlspecialchars($subject['description'] ?? 'Materia') ?></title>
    <?php include COMMON_HTML_HEAD ?>
</head>
<body data-theme="<?=THEME?>">
    <div class="container">

        <?php if (!isset($_SESSION['classeviva_ident'])) cvv_sync(); ?>

        <!-- Header Materia -->
        <header style="padding: 2rem 1rem 1rem;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin: 0; line-height: 1.2;">
                <?= htmlspecialchars($subject['description'] ?? 'Materia') ?>
            </h1>
        </header>

        <?php if (isset($grades['error'])): ?>
            <div class="my-card">
                <p style="color: var(--grade-red);">Errore: <?= htmlspecialchars($grades['error']) ?></p>
            </div>
        <?php else: ?>

            <!-- Box Voto più Alto -->
            <?php if ($maxGrade): ?>
                <section class="max-grade" style="padding: 0 1rem; margin-bottom: 1rem;">
                    <div class="my-card" style="text-align: center; padding: 2rem 1rem;">
                        <p style="color: var(--text-secondary); margin: 0 0 0.5rem 0; font-size: 0.875rem;">Voto più alto</p>
                        <?php
                        $maxVal = $maxGrade['decimalValue'];
                        if ($maxGrade['color'] == 'blue') {
                            $maxColor = 'var(--accent-blue)';
                        } else if ($maxVal >= 6) {
                            $maxColor = 'var(--grade-green)';
                        } else if ($maxVal >= 5) {
                            $maxColor = 'var(--grade-yellow)';
                        } else {
                            $maxColor = 'var(--grade-red)';
                        }
                        ?>
                        <div style="
                            width: 5rem;
                            height: 5rem;
                            border-radius: 50%;
                            background: <?= $maxColor ?>;
                            color: white;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 2rem;
                            font-weight: 700;
                            margin: 0 auto;
                            box-shadow: var(--shadow-md);
                        ">
                            <?= $maxVal ?>
                        </div>
                        <p style="color: var(--text-secondary); margin: 0.5rem 0 0 0; font-size: 0.875rem;">
                            <?= date('j M', strtotime($maxGrade['evtDate'])) ?>
                        </p>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Grafico Specifico (Area Chart) -->
            <section class="chart" style="padding: 0 1rem; margin-bottom: 1rem;">
                <div class="my-card" style="height: 300px;">
                    <h3 style="margin-top: 0; font-size: 1.125rem; font-weight: 600; color: var(--text-primary);">
                        Andamento voti
                    </h3>
                    <?php if (count($grades) > 0): ?>
                        <average-area-chart data-grades='<?= htmlspecialchars(json_encode($grades)) ?>'></average-area-chart>
                    <?php else: ?>
                        <div style="display: flex; align-items: center; justify-content: center; height: 220px; color: var(--text-secondary);">
                            <p>Nessun voto disponibile</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Lista Voti -->
            <section class="grades-list" style="padding: 0 1rem 5rem;">
                <h2 style="font-size: 1.25rem; font-weight: 600; margin: 1rem 0 0.5rem; color: var(--text-primary);">
                    Ultimi voti
                </h2>
                <?php if (count($grades) > 0): ?>
                    <?php foreach ($grades as $grade):
                        $value = $grade['decimalValue'];
                        if ($grade['color'] == 'blue') {
                            $color = 'var(--accent-blue)';
                        } else if ($value >= 6) {
                            $color = 'var(--grade-green)';
                        } else if ($value >= 5) {
                            $color = 'var(--grade-yellow)';
                        } else {
                            $color = 'var(--grade-red)';
                        }
                        $date = date('d/m', strtotime($grade['evtDate']));
                    ?>
                        <div class="grade-item my-card" style="display: flex; align-items: center; gap: 1rem; margin: 0.5rem 0; padding: 1rem;">
                            <div style="
                                min-width: 3.5rem;
                                height: 3.5rem;
                                border-radius: 50%;
                                background: <?= $color ?>;
                                color: white;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-weight: 700;
                                font-size: 1.125rem;
                            ">
                                <?= $value ?>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;">
                                    <?= htmlspecialchars($grade['componentDesc'] ?? 'Voto') ?>
                                </div>
                                <div style="font-size: 0.875rem; color: var(--text-secondary);">
                                    <?= $date ?> • <?= htmlspecialchars($grade['teacherName'] ?? '') ?>
                                </div>
                                <?php if (!empty($grade['notesForFamily'])): ?>
                                    <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0.5rem 0 0 0; line-height: 1.4;">
                                        <?= htmlspecialchars($grade['notesForFamily']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="my-card">
                        <p style="color: var(--text-secondary); margin: 0;">Nessun voto presente per questa materia.</p>
                    </div>
                <?php endif; ?>
            </section>

        <?php endif; ?>

    </div>

    <script>
    function toggleDetails(subjectId) {
        const details = document.getElementById('details-' + subjectId);
        const chevron = document.getElementById('chevron-' + subjectId);
        if (details.style.display === 'none' || !details.style.display) {
            details.style.display = 'block';
            chevron.style.transform = 'rotate(180deg)';
        } else {
            details.style.display = 'none';
            chevron.style.transform = 'rotate(0deg)';
        }
    }
    </script>

    <?php include COMMON_HTML_FOOT ?>

</body>
</html>

<?php
else:
?>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GradeCraft - Voti</title>
    <?php include COMMON_HTML_HEAD ?>
</head>
<body data-theme="<?=THEME?>">
    <div class="container">
        <?php if (!isset($_SESSION['classeviva_ident'])): ?>
            <?php cvv_sync(); ?>
        <?php else: ?>
            <header style="padding: 2rem 1rem 1rem;">
                <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin: 0;">Voti</h1>
            </header>

            <?php if (isset($error) && $error): ?>
                <div class="my-card">
                    <p style="color: var(--grade-red);">Errore: <?= htmlspecialchars($error) ?></p>
                </div>
            <?php elseif (empty($subjectAverages)): ?>
                <div class="my-card">
                    <p style="color: var(--text-secondary); margin: 0;">Nessun voto disponibile.</p>
                </div>
            <?php else: ?>
                <?php if ($overallAvg !== null): ?>
                    <div class="my-card" style="text-align: center; padding: 1.5rem; margin-bottom: 1rem;">
                        <h3 style="margin: 0 0 0.5rem 0; font-size: 1.125rem; font-weight: 600; color: var(--text-primary);">
                            Media Generale
                        </h3>
                        <grade-gauge value="<?= number_format($overallAvg, 2) ?>" max="10" label=""></grade-gauge>
                    </div>
                <?php endif; ?>

                <?php if (!empty($globalPeriods)): ?>
                    <div class="my-card" style="padding: 1rem; margin-bottom: 1rem;">
                        <h3 style="margin: 0 0 1rem 0; font-size: 1.125rem; font-weight: 600; color: var(--text-primary);">
                            Medie per Periodo
                        </h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 1rem;">
                            <?php foreach ($globalPeriods as $pDesc => $pData): ?>
                                <div style="text-align: center;">
                                    <grade-gauge value="<?= number_format($pData['avg'], 2) ?>" max="10" label="<?= htmlspecialchars($pDesc) ?>"></grade-gauge>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div style="padding: 0 1rem 5rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 600; margin: 1rem 0 0.5rem; color: var(--text-primary);">
                        Materie
                    </h2>
                    <?php foreach ($subjectAverages as $sid => $subj): ?>
                        <div class="my-card" style="margin-bottom: 0.5rem; padding: 1rem;">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <grade-gauge id="gauge-<?= $sid ?>" value="<?= number_format($subj['total_avg'], 2) ?>" max="10" label="<?= htmlspecialchars($subj['name']) ?>"></grade-gauge>
                                <button onclick="toggleDetails(<?= $sid ?>)"
                                        style="background: none; border: none; color: var(--accent-blue); font-size: 1.5rem; cursor: pointer; padding: 0.5rem; display: flex; align-items: center; justify-content: center; transition: transform 0.2s;"
                                        id="chevron-<?= $sid ?>" aria-label="Mostra dettagli">
                                    ⇩
                                </button>
                            </div>
                            <div id="details-<?= $sid ?>" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                                <h4 style="margin: 0 0 0.75rem 0; font-size: 0.875rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">
                                    Medie per periodo
                                </h4>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 0.75rem;">
                                    <?php foreach ($subj['periods'] as $pDesc => $pData): ?>
                                        <div style="text-align: center;">
                                            <grade-gauge value="<?= number_format($pData['avg'], 2) ?>" max="10" label="<?= htmlspecialchars($pDesc) ?>"></grade-gauge>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
    </div>
    <?php include COMMON_HTML_FOOT ?>
</body>
</html>
<?php
endif;
?>