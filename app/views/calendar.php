<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

$mesi = [
    1=>'Gennaio', 2=>'Febbraio', 3=>'Marzo', 4=>'Aprile', 5=>'Maggio', 6=>'Giugno',
    7=>'Luglio', 8=>'Agosto', 9=>'Settembre', 10=>'Ottobre', 11=>'Novembre', 12=>'Dicembre'
];

$gruppoPerMese = [];
$statusLabels  = [
    'SD' => 'Giorno scolastico',
    'HD' => 'Vacanza',
    'NW' => 'Non lavorativo',
];
$statusColors  = ['SD' => 'var(--grade-green)', 'HD' => 'var(--grade-red)', 'NW' => 'var(--text-secondary)'];

foreach ($calendar as $day) {
    $date = $day['dayDate'] ?? '';
    $status = $day['dayStatus'] ?? 'SD';
    if ($date) {
        $mese = (int)date('n', strtotime($date));
        $gruppoPerMese[$mese][] = ['date' => $date, 'status' => $status, 'dow' => $day['dayOfWeek'] ?? ''];
    }
}
// Ordina mesi
ksort($gruppoPerMese);
?>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GradeCraft - Calendario Scolastico</title>
    <?php include COMMON_HTML_HEAD ?>
</head>
<body data-theme="<?=THEME?>">
    <div class="container">
        <?php if (!isset($_SESSION['classeviva_ident'])) cvv_sync(); ?>

        <header style="padding: 2rem 1rem 1rem;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin: 0;">Calendario Scolastico</h1>
        </header>

        <!-- Legenda -->
        <div style="display: flex; gap: 1rem; padding: 0 1rem; margin-bottom: 1rem; flex-wrap: wrap;">
            <?php foreach ($statusLabels as $code => $label): ?>
                <span style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.8rem; color: var(--text-secondary);">
                    <span style="width: 0.6rem; height: 0.6rem; border-radius: 50%; background: <?= $statusColors[$code] ?>;"></span>
                    <?= $label ?>
                </span>
            <?php endforeach; ?>
        </div>

        <?php if ($error): ?>
            <div class="my-card" style="margin: 0 1rem; padding: 1rem;">
                <p style="color: var(--grade-red); margin: 0;">Errore: <?= htmlspecialchars($error) ?></p>
            </div>
            <?php include BASE_PATH . '/app/views/partials/api_debug.php'; ?>
        <?php elseif (empty($gruppoPerMese)): ?>
            <div class="my-card" style="margin: 0 1rem; padding: 1rem;">
                <p style="color: var(--text-secondary); margin: 0;">Nessun dato del calendario disponibile.</p>
            </div>
        <?php else: ?>
            <div style="padding: 0 1rem 5rem;">
                <?php foreach ($gruppoPerMese as $mese => $giorni): ?>
                    <h3 style="font-size: 1.125rem; font-weight: 600; margin: 1rem 0 0.5rem; color: var(--text-primary);"><?= $mesi[$mese] ?? 'Mese' ?></h3>
                    <?php foreach ($giorni as $giorno):
                        if ($giorno['status'] !== 'SD') continue; // mostra solo giorni scolastici di default
                        $dow = $giorno['dow'];
                        $dayNum = date('j', strtotime($giorno['date']));
                    ?>
                        <div class="my-card" style="margin-bottom: 0.25rem; padding: 0.75rem 1rem; display: flex; align-items: center; gap: 1rem;">
                            <div style="flex: 1; display: flex; justify-content: space-between;">
                                <span style="font-weight: 500; color: var(--text-primary);"><?= $dayNum ?></span>
                                <span style="font-size: 0.8rem; color: var(--text-secondary);"><?= $giorno['date'] ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <!-- Vacanze -->
                    <?php
                    $vacanze = array_filter($giorni, fn($g) => $g['status'] === 'HD');
                    if (!empty($vacanze)): ?>
                        <details style="margin-top: 0.25rem;">
                            <summary style="font-size: 0.85rem; color: var(--grade-red); cursor: pointer; padding: 0.5rem 0;">
                                <?= count($vacanze) ?> giorni di vacanza
                            </summary>
                            <?php foreach ($vacanze as $vac):
                                $vdNum = date('j', strtotime($vac['date']));
                            ?>
                                <div class="my-card" style="margin-bottom: 0.25rem; padding: 0.5rem 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="width: 0.4rem; height: 0.4rem; border-radius: 50%; background: var(--grade-red); flex-shrink: 0;"></span>
                                    <span style="font-size: 0.875rem; color: var(--text-secondary);"><?= $vdNum ?> <?= date('M', strtotime($vac['date'])) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </details>
                    <?php endif; ?>

            <?php if (!empty($_SESSION['show_json_debug'])): ?>
                <div class="my-card" style="margin: 1rem; padding: 1rem;">
                    <h3 style="font-size: 1rem; color: var(--text-primary); margin-bottom: 0.5rem;">JSON Response</h3>
                    <pre style="font-size: 0.65rem; color: var(--text-secondary); overflow: auto; max-height: 500px;"><?= htmlspecialchars(json_encode($calendar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                </div>
            <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include COMMON_HTML_FOOT ?>
</body>
</html>
