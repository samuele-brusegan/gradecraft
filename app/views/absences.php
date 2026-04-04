<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

// Calcola statistiche
$stats = ['ASSENZA' => 0, 'RITARDO' => 0, 'RITARDO_BREVE' => 0, 'USCITA' => 0];
$totalHours = 0;
$justified = 0;
foreach ($events as $ev) {
    $t = $ev['type'] ?? 'ALTRO';
    if (isset($stats[$t])) $stats[$t]++;
    $totalHours += ($ev['hours'] ?? 0);
    if (!empty($ev['isJustified'])) $justified++;
}
?>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GradeCraft - Assenze e Ritardi</title>
    <?php include COMMON_HTML_HEAD ?>
</head>
<body data-theme="<?=THEME?>">
    <div class="container">
        <?php if (!isset($_SESSION['classeviva_ident'])) cvv_sync(); ?>

        <header style="padding: 2rem 1rem 1rem;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin: 0;">Assenze e Ritardi</h1>
        </header>

        <?php if ($error): ?>
            <div class="my-card" style="margin: 0 1rem; padding: 1rem;">
                <p style="color: var(--grade-red); margin: 0;">Errore: <?= htmlspecialchars($error) ?></p>
            </div>
        <?php else: ?>
            <!-- Statistiche -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; padding: 0 1rem; margin-bottom: 1rem;">
                <div class="my-card" style="text-align: center; padding: 1rem;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--grade-red);"><?= $stats['ASSENZA'] ?></div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary);">Assenze</div>
                </div>
                <div class="my-card" style="text-align: center; padding: 1rem;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--grade-yellow);"><?= $stats['RITARDO'] + $stats['RITARDO_BREVE'] ?></div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary);">Ritardi</div>
                </div>
                <div class="my-card" style="text-align: center; padding: 1rem;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-blue);"><?= $stats['USCITA'] ?></div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary);">Uscite</div>
                </div>
                <div class="my-card" style="text-align: center; padding: 1rem;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);"><?= $totalHours ?></div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary);">Ore totali</div>
                </div>
            </div>

            <?php if (empty($events)): ?>
                <div class="my-card" style="margin: 0 1rem; padding: 1rem;">
                    <p style="color: var(--text-secondary); margin: 0;">Nessuna assenza o ritardo registrato.</p>
                </div>
            <?php else: ?>
                <div style="padding: 0 1rem 5rem;">
                    <?php foreach ($events as $ev):
                        $typeColors = [
                            'ASSENZA' => 'var(--grade-red)',
                            'RITARDO' => 'var(--grade-yellow)',
                            'RITARDO_BREVE' => 'var(--grade-yellow)',
                            'USCITA' => 'var(--accent-blue)',
                        ];
                        $color = $typeColors[$ev['type']] ?? 'var(--text-secondary)';
                        $date = isset($ev['evtDate']) ? date('d M Y', strtotime($ev['evtDate'])) : '';
                    ?>
                        <div class="my-card" style="margin-bottom: 0.5rem; padding: 1rem; display: flex; align-items: center; gap: 1rem;">
                            <div style="flex-shrink: 0; width: 0.25rem; height: 2.5rem; background: <?= $color ?>; border-radius: 0.125rem;"></div>
                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-weight: 600; color: var(--text-primary);"><?= htmlspecialchars($ev['type']) ?></span>
                                    <?php if ($ev['isJustified'] ?? false): ?>
                                        <span style="font-size: 0.75rem; padding: 0.15rem 0.5rem; border-radius: 1rem; background: rgba(34,197,94,0.15); color: #22c55e; font-weight: 500;">giustificata</span>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">
                                    <?= $date ?>
                                    <?php if (isset($ev['justifReasonDesc']) && $ev['justifReasonDesc']): ?>
                                        &bull; <?= htmlspecialchars($ev['justifReasonDesc']) ?>
                                    <?php elseif (!empty($ev['hoursAbsence'])): ?>
                                        &bull; <?= count($ev['hoursAbsence']) ?> ora/e
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!empty($_SESSION['show_json_debug'])): ?>
                                <pre style="font-size: 0.65rem; color: var(--text-secondary); margin-top: 0.5rem; overflow: auto; max-height: 200px;"><?= htmlspecialchars(json_encode($ev, JSON_PRETTY_PRINT)) ?></pre>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include COMMON_HTML_FOOT ?>
</body>
</html>
