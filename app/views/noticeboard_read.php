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
    <title>GradeCraft - <?= htmlspecialchars($item['title'] ?? $item['cntTitle'] ?? 'Circolare') ?></title>
    <?php include COMMON_HTML_HEAD ?>
</head>
<body data-theme="<?=THEME?>">
    <div class="container">
        <?php if (!isset($_SESSION['classeviva_ident'])) cvv_sync(); ?>

        <header style="padding: 2rem 1rem 1rem;">
            <a href="/noticeboard" style="color: var(--accent-blue); text-decoration: none; font-size: 0.875rem;">&larr; Torna alla bacheca</a>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-top: 0.5rem; line-height: 1.3;">
                <?= htmlspecialchars($item['title'] ?? $item['cntTitle'] ?? 'Circolare') ?>
            </h1>
            <div style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.25rem;">
                <?php if (isset($item['cntCategory'])): ?>
                    <span><?= htmlspecialchars($item['cntCategory']) ?></span> &bull;
                <?php endif; ?>
                <?php if (isset($item['pubDT'])): ?>
                    <?= date('d/m/Y', strtotime($item['pubDT'])) ?>
                <?php endif; ?>
            </div>
        </header>

        <?php if ($error): ?>
            <div class="my-card" style="margin: 0 1rem; padding: 1rem;">
                <p style="color: var(--grade-red); margin: 0;">Errore: <?= htmlspecialchars($error) ?></p>
            </div>
        <?php elseif ($item): ?>

            <?php
            $hasAttachments = !empty($item['attachments']) && is_array($item['attachments']) && count($item['attachments']) > 0;
            // Determina il contenuto testuale
            $textDisplay = '';
            if (!empty($item['title']) && $item['title'] === ($item['cntTitle'] ?? '')) {
                $textDisplay = '';
            } else {
                $textDisplay = $item['cntText'] ?? $item['description'] ?? $item['content'] ?? $item['text'] ?? '';
            }
            $hasText = !empty($textDisplay);
            ?>

            <!-- Testo -->
            <?php if ($hasText): ?>
                <div class="my-card" style="margin: 0 1rem 1rem; padding: 1.5rem;">
                    <p style="color: var(--text-primary); line-height: 1.7; white-space: pre-wrap;">
                        <?= htmlspecialchars($textDisplay) ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Allegati -->
            <?php if ($hasAttachments): ?>
                <div style="padding: 0 1rem;">
                    <h3 style="font-size: 1.1rem; color: var(--text-primary); margin: 1rem 0 0.5rem;">Allegati</h3>
                    <?php foreach ($item['attachments'] as $att): ?>
                        <?php
                        $filename = $att['fileName'] ?? 'allegato';
                        $attachNum = $att['attachNum'] ?? '';
                        $isPdf = stripos($filename, '.pdf') !== false;
                        ?>
                        <div class="my-card" style="margin-bottom: 1rem; padding: 1rem;">
                            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                                <span style="font-weight: 600; color: var(--text-primary); font-size: 0.9rem;">
                                    <span class="material-symbols-outlined" style="font-size: 1.2rem; vertical-align: middle; margin-right: 0.3rem; color: var(--grade-red);">picture_as_pdf</span>
                                    <?= htmlspecialchars($filename) ?>
                                </span>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button onclick="loadPdf('<?= $attachNum ?>')" style="font-size: 0.8rem; padding: 0.3rem 0.7rem; border: none; border-radius: 0.5rem; background: var(--accent-blue); color: white; cursor: pointer;">
                                        <span class="material-symbols-outlined" style="font-size: 1rem; vertical-align: middle;">file_open</span> Apri
                                    </button>
                                    <button onclick="downloadPdf('<?= $attachNum ?>', '<?= htmlspecialchars($filename) ?>')" style="font-size: 0.8rem; padding: 0.3rem 0.7rem; border: none; border-radius: 0.5rem; background: rgba(34,197,94,0.15); color: #22c55e; cursor: pointer;">
                                        <span class="material-symbols-outlined" style="font-size: 1rem; vertical-align: middle;">download</span> Scarica
                                    </button>
                                    <button onclick="sharePdf('<?= $attachNum ?>', '<?= htmlspecialchars($filename) ?>')" style="font-size: 0.8rem; padding: 0.3rem 0.7rem; border: none; border-radius: 0.5rem; background: rgba(168,85,247,0.15); color: #a855f7; cursor: pointer;">
                                        <span class="material-symbols-outlined" style="font-size: 1rem; vertical-align: middle;">share</span> Condividi
                                    </button>
                                </div>
                            </div>
                        </div>

                        <?php if ($isPdf): ?>
                            <div id="pdf-container-<?= $attachNum ?>" class="my-card" style="display: none; margin-bottom: 1rem; padding: 0; min-height: 60vh;">
                                <div id="pdf-loading-<?= $attachNum ?>" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                                    <p>Caricamento PDF...</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['show_json_debug'])): ?>
                <div class="my-card" style="margin: 0 1rem; padding: 1rem;">
                    <h3 style="font-size: 1rem; color: var(--text-primary); margin-bottom: 0.5rem;">JSON Response</h3>
                    <pre style="font-size: 0.65rem; color: var(--text-secondary); overflow: auto; max-height: 500px;"><?= htmlspecialchars(json_encode($item, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
    window.attachMeta = {
        evtCode: '<?= $item['evtCode'] ?? '' ?>',
        pubId: '<?= $item['pubId'] ?? '' ?>'
    };

    async function fetchPdf(attachNum) {
        const url = `/noticeboard/attach?evtCode=${window.attachMeta.evtCode}&pubId=${window.attachMeta.pubId}&attachNum=${attachNum}`;
        const resp = await fetch(url);
        if (!resp.ok) throw new Error('Download fallito: ' + resp.status);
        return resp.blob();
    }

    window.loadPdf = async function(attachNum) {
        const container = document.getElementById('pdf-container-' + attachNum);
        const loading = document.getElementById('pdf-loading-' + attachNum);
        if (!container || !loading) return;
        container.style.display = 'block';
        loading.style.display = 'block';

        try {
            const blob = await fetchPdf(attachNum);
            const url = URL.createObjectURL(blob);
            loading.style.display = 'none';
            const iframe = document.createElement('iframe');
            iframe.src = url;
            iframe.style.width = '100%';
            iframe.style.minHeight = '60vh';
            iframe.style.border = 'none';
            container.innerHTML = '';
            container.appendChild(iframe);
            container._objectUrl = url;
        } catch (e) {
            loading.innerHTML = '<p style="color: var(--grade-red);">Errore: ' + e.message + '</p>';
        }
    };

    window.downloadPdf = async function(attachNum, filename) {
        try {
            const blob = await fetchPdf(attachNum);
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = filename;
            a.click();
            URL.revokeObjectURL(a.href);
        } catch (e) {
            alert('Download fallito: ' + e.message);
        }
    };

    window.sharePdf = async function(attachNum, filename) {
        try {
            const blob = await fetchPdf(attachNum);
            const file = new File([blob], filename, { type: 'application/pdf' });
            if (navigator.share && navigator.canShare({ files: [file] })) {
                await navigator.share({ files: [file], title: filename });
            } else {
                window.downloadPdf(attachNum, filename);
            }
        } catch (e) {
            if (e.name !== 'AbortError') {
                alert('Condivisione fallita: ' + e.message);
            }
        }
    };
    </script>

    <?php include COMMON_HTML_FOOT ?>
</body>
</html>
