<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

global $error;
global $loginResponse;

// Debug: log errors if present
if (isset($error) && $error) {
    error_log("Dashboard error: " . $error);
}

// Calcolo data odierna in italiano
$giorni = ['Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato','Domenica'];
$oggi = $giorni[date('N')-1] . ' ' . date('j');

// Carica e processa i voti
$allGrades = $response['grades'] ?? [];
error_log("Dashboard: allGrades count = " . count($allGrades));
error_log("Dashboard: response keys = " . implode(', ', array_keys($response)));
if (!empty($allGrades) && is_array($allGrades)) {
    $first = reset($allGrades);
    if ($first !== false) {
        error_log("First grade sample: " . print_r($first, true));
    } else {
        error_log("allGrades array is empty after reset");
    }
} else {
    error_log("Dashboard: allGrades empty or not array. Type: " . gettype($allGrades));
}
$validGrades = array_filter($allGrades, function($g) {
    return isset($g['decimalValue']) && is_numeric($g['decimalValue']);
});
error_log("Dashboard: validGrades count = " . count($validGrades));

// Calcola media generale
$overallAvg = null;
if (count($validGrades) > 0) {
    $sum = 0;
    foreach ($validGrades as $g) {
        $sum += $g['decimalValue'];
    }
    $overallAvg = $sum / count($validGrades);
    error_log("Dashboard: overallAvg = " . $overallAvg);
} else {
    error_log("Dashboard: no valid grades, overallAvg = null");
}

// Calcola medie per periodo
$globalPeriods = [];
foreach ($validGrades as $g) {
    $value = $g['decimalValue'];
    $pDesc = $g['periodDesc'] ?? ($g['period'] ?? '?');
    $pPos = $g['periodPos'] ?? 0;
    if (!isset($globalPeriods[$pDesc])) {
        $globalPeriods[$pDesc] = ['sum' => 0, 'count' => 0, 'pos' => $pPos];
    }
    $globalPeriods[$pDesc]['sum'] += $value;
    $globalPeriods[$pDesc]['count']++;
}
foreach ($globalPeriods as &$p) {
    $p['avg'] = $p['count'] > 0 ? $p['sum'] / $p['count'] : 0;
    unset($p['sum'], $p['count'], $p['pos']);
}
unset($p);
?>

<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gradecraft - Home</title>
    <?php include COMMON_HTML_HEAD ?>
</head>
<body data-theme="<?=THEME?>">

    <?php if (!isset($_SESSION['classeviva_ident'])): ?>
        <?php cvv_sync(); ?>
    <?php endif; ?>

    <div class="page-container">

        <!-- Header Dinamico -->
        <header style="padding: 2rem 1rem 1rem;">
            <h1 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary); margin: 0; line-height: 1.2;">
                <?= htmlspecialchars($oggi) ?>
            </h1>
        </header>

        <?php
        // Mostra errori se presenti
        if (isset($response['error_subjects']) || isset($response['error_grades'])):
        ?>
            <div style="padding: 0 1rem; margin-bottom: 1rem;">
                <?php if (isset($response['error_subjects'])): ?>
                    <div class="my-card" style="margin-bottom: 0.5rem; padding: 1rem; background: rgba(239, 68, 68, 0.1); border-left: 4px solid var(--grade-red);">
                        <p style="color: var(--grade-red); margin: 0;">Errore caricamento materie</p>
                    </div>
                <?php endif; ?>
                <?php if (isset($response['error_grades'])): ?>
                    <div class="my-card" style="margin-bottom: 0.5rem; padding: 1rem; background: rgba(239, 68, 68, 0.1); border-left: 4px solid var(--grade-red);">
                        <p style="color: var(--grade-red); margin: 0;">Errore caricamento voti</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Riepilogo medie -->
        <section class="summary-gauges" style="padding: 0 1rem; margin-bottom: 1rem;">
            <?php if ($overallAvg !== null): ?>
                <div class="my-card" style="display: flex; flex-direction: column; align-items: center; padding: 1.5rem 1rem; margin-bottom: 1rem;">
                    <h3 style="margin: 0 0 1rem 0; font-size: 1.125rem; font-weight: 600; color: var(--text-primary);">Media Generale</h3>
                    <grade-gauge value="<?= number_format($overallAvg, 2) ?>" max="10" label=""></grade-gauge>
                </div>
            <?php endif; ?>
            <?php if (!empty($globalPeriods)): ?>
                <div class="my-card" style="padding: 1rem;">
                    <h3 style="margin: 0 0 1rem 0; font-size: 1.125rem; font-weight: 600; color: var(--text-primary);">Medie per Periodo</h3>
                    <div style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: space-around;">
                        <?php foreach ($globalPeriods as $pDesc => $pData):
                            // Determine if this is primo trimestre for margin-top
                            $isFirstPeriod = ($pDesc === 'primo trimestre');
                        ?>
                            <div style="text-align: center; display: flex; flex-direction: column; align-items: center; <?= $isFirstPeriod ? 'margin-top: 1rem;' : '' ?>">
                                <grade-gauge value="<?= number_format($pData['avg'], 2) ?>" max="10" label="<?= htmlspecialchars($pDesc) ?>"></grade-gauge>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <!-- Widget Statistico -->
        <section class="widget-stats" style="padding: 0 1rem;">
            <!-- Grafico storico voti -->
            <div class="my-card" style="height: 300px;">
                <h3 style="margin-top: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-primary);">
                    Andamento voti
                </h3>
                <?php if (count($validGrades) > 0): ?>
                    <average-area-chart data-grades='<?= htmlspecialchars(json_encode(array_values($validGrades))) ?>'></average-area-chart>
                <?php else: ?>
                    <div style="display: flex; align-items: center; justify-content: center; height: 220px; color: var(--text-secondary);">
                        <p>Nessun voto disponibile</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Sezione Urgenze -->
        <section class="urgenze" style="padding: 1rem;">
            <subject-card
                data-name="Comunicazione importante"
                data-description="Controlla le ultime comunicazioni della scuola"
                data-href="/settings">
            </subject-card>
        </section>

        <!-- Lista Compiti (Materie) -->
        <section class="subject-list" style="padding: 0 1rem 5rem;">
            <div style="display: flex; align-items: baseline; justify-content: space-between; margin: 1rem 0 0.5rem;">
                <h2 style="font-size: 1.5rem; font-weight: 600; margin: 0; color: var(--text-primary);">
                    Materie
                </h2>
                <a href="/subjects" style="font-size: 0.9rem; color: var(--accent-blue); text-decoration: none;">Vedi Altro &rsaquo;</a>
            </div>
            <?php if (isset($response['subjects']) && !isset($response['subjects']['error']) && count($response['subjects']) > 0): ?>
                <?php
                    $signedSubjects = array_filter($response['subjects'], function($s) {
                        return !empty($s['teachers']);
                    });
                    $recentSigned = array_slice(array_reverse($signedSubjects), 0, 3);
                    $recentSigned = array_reverse($recentSigned);
                ?>
                <?php foreach ($recentSigned as $subject):
                    $id = $subject['id'];
                    $name = ($subject['id'] == '215883') ? 'TPSIT' : $subject['description'];
                    $profs = $subject['teachers'];
                    $profList = implode(', ', array_column($profs, 'teacherName'));
                    $desc = $profList ?: 'Nessun docente';
                ?>
                    <subject-card
                        data-name="<?= htmlspecialchars($name) ?>"
                        data-description="<?= htmlspecialchars($desc) ?>"
                        data-href="/grades?subject=<?= $id ?>">
                    </subject-card>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="my-card">
                    <p style="color: var(--text-secondary); margin: 0;">Nessuna materia disponibile</p>
                </div>
            <?php endif; ?>
        </section>

    </div>

    <?php include COMMON_HTML_FOOT ?>

</body>
</html>
