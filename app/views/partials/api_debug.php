<?php
/* Render API error debug panel. Requires $apiError variable. */
if (!isset($apiError) || !is_array($apiError)) return;
?>
<div class="my-card" style="margin: 0 1rem 1rem; padding: 1rem; border: 1px solid rgba(239,68,68,0.3);">
    <details>
        <summary style="cursor:pointer; color: var(--grade-red); font-weight: 600; font-size: 0.9rem;">
            ⚠ Errore API: <?= htmlspecialchars($apiError['error'] ?? 'Sconosciuto') ?> (HTTP <?= $apiError['status'] ?? '?' ?>)
        </summary>
        <div style="margin-top: 0.75rem; font-size: 0.75rem; color: var(--text-secondary); line-height: 1.6;">
            <div><strong style="color:var(--text-primary)">URL:</strong> <?= htmlspecialchars($apiError['url'] ?? '—') ?></div>
            <div><strong style="color:var(--text-primary)">HTTP Status:</strong> <?= $apiError['status'] ?? '—' ?></div>
            <div><strong style="color:var(--text-primary)">cURL Error:</strong> <?= htmlspecialchars($apiError['curl_error'] ?? $apiError['message'] ?? '—') ?></div>
            <?php if (isset($apiError['curl_errno'])): ?>
                <div><strong style="color:var(--text-primary)">cURL Errno:</strong> <?= $apiError['curl_errno'] ?></div>
            <?php endif; ?>
            <?php if (isset($apiError['body'])): ?>
                <details style="margin-top: 0.5rem;">
                    <summary style="cursor:pointer;">Response Body</summary>
                    <pre style="margin-top:0.25rem; background:rgba(0,0,0,0.3); padding:0.5rem; border-radius:0.375rem; overflow:auto; max-height:300px; font-size:0.65rem;"><?= htmlspecialchars($apiError['body']) ?></pre>
                </details>
            <?php endif; ?>
        </div>
    </details>
</div>
