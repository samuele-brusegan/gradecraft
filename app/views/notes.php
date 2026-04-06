<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */
?>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GradeCraft - Note Disciplinari</title>
    <?php include COMMON_HTML_HEAD ?>
</head>
<body data-theme="<?=THEME?>">
    <div class="container">
        <?php if (!isset($_SESSION['classeviva_ident'])) cvv_sync(); ?>

        <header style="padding: 2rem 1rem 1rem;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin: 0;">Note Disciplinari</h1>
        </header>

        <?php if ($error): ?>
            <div class="my-card" style="margin: 0 1rem; padding: 1rem;">
                <p style="color: var(--grade-red); margin: 0;">Errore: <?= htmlspecialchars($error) ?></p>
            </div>
            <?php include BASE_PATH . '/app/views/partials/api_debug.php'; ?>
        <?php elseif (empty($allNotes)): ?>
            <div class="my-card" style="margin: 0 1rem; padding: 1rem;">
                <p style="color: var(--text-secondary); margin: 0;">Nessuna nota registrata.</p>
            </div>
        <?php else: ?>
            <div style="padding: 0 1rem 5rem;">
                <?php foreach ($allNotes as $note):
                    $typeColors = [
                        'NTTE' => 'var(--accent-blue)',
                        'NTCL' => 'var(--text-secondary)',
                        'NTWN' => 'var(--grade-yellow)',
                        'NTST' => 'var(--grade-red)',
                    ];
                    $color = $typeColors[$note['typeKey']] ?? 'var(--text-secondary)';
                    $date = isset($note['evtDate']) ? date('d M Y', strtotime($note['evtDate'])) : '';
                    $unread = !($note['readStatus'] ?? true);
                ?>
                    <div class="my-card" style="margin-bottom: 0.5rem; padding: 1rem; <?= $unread ? 'border-left: 4px solid ' . $color : '' ?>">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                    <span style="font-size: 0.75rem; padding: 0.1rem 0.5rem; border-radius: 1rem; background: <?= $color ?>; color: white; font-weight: 500;">
                                        <?= $note['typeLabel'] ?>
                                    </span>
                                    <?php if ($unread): ?>
                                        <span style="font-size: 0.7rem; color: var(--grade-yellow); font-weight: 600;">● non letta</span>
                                    <?php endif; ?>
                                </div>
                                <?php if (isset($note['evtText']) && $note['evtText']): ?>
                                    <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem; color: var(--text-primary); line-height: 1.5;"><?= nl2br(htmlspecialchars($note['evtText'])) ?></p>
                                <?php endif; ?>
                                <div style="margin-top: 0.5rem; font-size: 0.8rem; color: var(--text-secondary); display: flex; gap: 1rem;">
                                    <?php if ($date): ?><span><?= $date ?></span><?php endif; ?>
                                    <?php if (isset($note['authorName']) && $note['authorName']): ?><span><?= htmlspecialchars($note['authorName']) ?></span><?php endif; ?>
                                </div>
                                <?php if (!empty($_SESSION['show_json_debug'])): ?>
                                    <pre style="font-size: 0.65rem; color: var(--text-secondary); margin-top: 0.5rem; overflow: auto; max-height: 200px;"><?= htmlspecialchars(json_encode($note, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include COMMON_HTML_FOOT ?>
</body>
</html>
