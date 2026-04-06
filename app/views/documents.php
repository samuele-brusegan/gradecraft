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
    <title>GradeCraft - Documenti e Pagelle</title>
    <?php include COMMON_HTML_HEAD ?>
</head>
<body data-theme="<?=THEME?>">
    <div class="container">
        <?php if (!isset($_SESSION['classeviva_ident'])) cvv_sync(); ?>

        <header style="padding: 2rem 1rem 1rem;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin: 0;">Documenti e Pagelle</h1>
        </header>

        <?php if ($error): ?>
            <div class="my-card" style="margin: 0 1rem; padding: 1rem;">
                <p style="color: var(--grade-red); margin: 0;">Errore: <?= htmlspecialchars($error) ?></p>
            </div>
            <?php include BASE_PATH . '/app/views/partials/api_debug.php'; ?>
        <?php else: ?>
            <?php if (empty($documents) && empty($schoolReports)): ?>
                <div class="my-card" style="margin: 0 1rem; padding: 1rem;">
                    <p style="color: var(--text-secondary); margin: 0;">Nessun documento disponibile.</p>
                </div>
            <?php endif; ?>

            <!-- Pagelle -->
            <?php if (!empty($schoolReports)): ?>
                <h3 style="font-size: 1.25rem; font-weight: 600; margin: 1rem 1rem 0.5rem; color: var(--text-primary);">Pagelle</h3>
                <?php foreach ($schoolReports as $report): ?>
                    <div class="my-card" style="margin: 0 1rem 0.5rem; padding: 1rem;">
                        <span style="font-weight: 600; color: var(--text-primary);"><?= htmlspecialchars($report['desc']) ?></span>
                        <div style="margin-top: 0.5rem;">
                            <?php if (isset($report['viewLink']) && $report['viewLink']): ?>
                                <a href="<?= htmlspecialchars($report['viewLink']) ?>" target="_blank" class="my-card" style="font-size: 0.875rem; padding: 0.3rem 0.75rem; color: var(--accent-blue); text-decoration: none;">
                                    <span class="material-symbols-outlined" style="font-size: 1rem; vertical-align: middle;">open_in_new</span> Visualizza
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Documenti -->
            <?php if (!empty($documents)): ?>
                <h3 style="font-size: 1.25rem; font-weight: 600; margin: 1.5rem 1rem 0.5rem; color: var(--text-primary);">Documenti</h3>
                <?php foreach ($documents as $doc):
                    $hash = $doc['hash'] ?? '';
                ?>
                    <div class="my-card" style="margin: 0 1rem 0.5rem; padding: 1rem; display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-weight: 500; color: var(--text-primary);"><?= htmlspecialchars($doc['desc']) ?></span>
                        <?php if ($hash): ?>
                            <a href="/documents/view?doc=<?= htmlspecialchars($hash) ?>" class="my-card" style="font-size: 0.875rem; padding: 0.3rem 0.75rem; color: var(--accent-blue); text-decoration: none; flex-shrink: 0;">
                               <span class="material-symbols-outlined" style="font-size: 1rem; vertical-align: middle;">file_open</span> Apri
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['show_json_debug'])): ?>
                <div class="my-card" style="margin: 1rem; padding: 1rem;">
                    <h3 style="font-size: 1rem; color: var(--text-primary); margin-bottom: 0.5rem;">JSON Response</h3>
                    <pre style="font-size: 0.65rem; color: var(--text-secondary); overflow: auto; max-height: 500px;"><?= htmlspecialchars(json_encode(['documents' => $documents, 'schoolReports' => $schoolReports], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include COMMON_HTML_FOOT ?>
</body>
</html>
