<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GradeCraft - Bacheca</title>
    <?php include COMMON_HTML_HEAD ?>
</head>
<body data-theme="<?=THEME?>">
    <div class="container">
        <?php if (!isset($_SESSION['classeviva_ident'])) cvv_sync(); ?>

        <header style="padding: 2rem 1rem 1rem;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin: 0;">Bacheca</h1>
        </header>

        <?php if ($error): ?>
            <div class="my-card" style="margin: 0 1rem; padding: 1rem;">
                <p style="color: var(--grade-red); margin: 0;">Errore: <?= htmlspecialchars($error) ?></p>
            </div>
        <?php elseif (empty($items)): ?>
            <div class="my-card" style="margin: 0 1rem; padding: 1rem;">
                <p style="color: var(--text-secondary); margin: 0;">Nessuna comunicazione in bacheca.</p>
            </div>
        <?php else: ?>
            <div style="padding: 0 1rem 5rem;">
                <?php foreach ($items as $item):
                    $date = isset($item['pubDT']) ? date('d M Y', strtotime($item['pubDT'])) : '';
                    $unread = !($item['readStatus'] ?? true);
                    $attachCount = isset($item['attachments']) && is_array($item['attachments']) ? count($item['attachments']) : 0;
                    $evtCode = $item['evtCode'] ?? '';
                    $pubId = $item['pubId'] ?? '';
                    $detailUrl = $evtCode && $pubId ? "/noticeboard/read?evtCode=" . urlencode($evtCode) . "&pubId=" . urlencode($pubId) : '#';
                    // Title: usa 'title' se diverso da cntTitle, altrimenti cntTitle
                    $displayTitle = (!empty($item['title']) && $item['title'] !== $item['cntTitle']) ? $item['title'] : ($item['cntTitle'] ?? 'Comunicazione');
                    // Text: mostra se esiste ed è diverso dal titolo
                    $showText = '';
                    $rawText = $item['text'] ?? $item['cntText'] ?? $item['description'] ?? $item['content'] ?? '';
                    if ($rawText && $rawText !== $displayTitle) $showText = $rawText;
                ?>
                    <a href="<?= $detailUrl ?>" class="my-card" style="display: block; margin-bottom: 0.5rem; padding: 1rem; text-decoration: none; <?= $unread ? 'border-left: 4px solid var(--accent-blue);' : '' ?>">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                    <?php if ($unread): ?>
                                        <span style="width: 0.5rem; height: 0.5rem; border-radius: 50%; background: var(--accent-blue); flex-shrink: 0;"></span>
                                    <?php endif; ?>
                                    <span style="font-weight: 600; color: var(--text-primary);"><?= htmlspecialchars($displayTitle) ?></span>
                                </div>
                                <?php if (isset($item['cntCategory']) && $item['cntCategory']): ?>
                                    <span style="font-size: 0.75rem; color: var(--text-secondary);"><?= htmlspecialchars($item['cntCategory']) ?></span>
                                <?php endif; ?>
                                <?php if ($showText): ?>
                                    <p style="margin-top: 0.25rem; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?= htmlspecialchars($showText) ?>
                                    </p>
                                <?php endif; ?>
                                <div style="margin-top: 0.25rem; font-size: 0.8rem; color: var(--text-secondary); display: flex; gap: 0.75rem;">
                                    <?php if ($date): ?><span><?= $date ?></span><?php endif; ?>
                                    <?php if ($attachCount > 0): ?>
                                        <span><span class="material-symbols-outlined" style="font-size: 0.85rem; vertical-align: middle;">attach_file</span> <?= $attachCount ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($_SESSION['show_json_debug'])): ?>
                                    <pre style="font-size: 0.65rem; color: var(--text-secondary); margin-top: 0.5rem; overflow: auto; max-height: 200px; position: relative; pointer-events: auto;" onclick="event.stopPropagation();"><?= htmlspecialchars(json_encode($item, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include COMMON_HTML_FOOT ?>
</body>
</html>
