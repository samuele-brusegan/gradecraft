<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

$firstName = $_SESSION['classeviva_first_name'] ?? 'Utente';
$lastName  = $_SESSION['classeviva_last_name']  ?? '';
$expDate   = isset($_SESSION['classeviva_session_expiration_date'])
    ? date('d/m/Y H:i', strtotime($_SESSION['classeviva_session_expiration_date']))
    : '—';
$year = defined('CLASSEVIVA_YEAR') ? CLASSEVIVA_YEAR : '';

// Conteggio credenziali salvate
$savedUsers = class_exists('CredentialStore') ? count(CredentialStore::listUsers()) : 0;
// Debug toggle
$jsonDebugEnabled = isset($_SESSION['show_json_debug']) && $_SESSION['show_json_debug'];
?>

<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GradeCraft - Impostazioni</title>
    <?php include COMMON_HTML_HEAD ?>
</head>
<body data-theme="<?=THEME?>">
    <div class="container">
        <?php if (!isset($_SESSION['classeviva_ident'])) cvv_sync(); ?>

        <header style="padding: 2rem 1rem 1rem;">
            <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin: 0;">Impostazioni</h1>
        </header>

        <!-- Info Account -->
        <div class="my-card" style="margin: 0 1rem; padding: 1rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 0.75rem; color: var(--text-primary);">Account</h3>
            <p style="color: var(--text-primary); margin: 0.25rem 0;"><strong><?= htmlspecialchars($firstName . ' ' . $lastName) ?></strong></p>
            <p style="color: var(--text-secondary); margin: 0.25rem 0; font-size: 0.875rem;">Token scade il: <?= $expDate ?></p>
            <a href="/" style="font-size: 0.875rem; color: var(--accent-blue); text-decoration: none;">Forza ri-sync con Classeviva</a>
        </div>

        <!-- Tema -->
        <div class="my-card" style="margin: 1rem 1rem 0; padding: 1rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 0.75rem; color: var(--text-primary);">Aspetto</h3>
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="color: var(--text-primary);">Tema</span>
                <button id="theme-toggle" class="my-card" style="border: none; cursor: pointer; padding: 0.5rem 1rem; display: flex; align-items: center; gap: 0.5rem; background: var(--card-bg); box-shadow: var(--shadow-md); border-radius: 0.5rem;">
                    <svg width="20" height="20" fill="none" stroke="var(--text-secondary)" viewBox="0 0 24 24" style="flex-shrink: 0;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <span id="theme-label" style="font-weight: 500; color: var(--text-primary);"><?= THEME === 'dark' ? 'Scuro' : 'Chiaro' ?></span>
                </button>
            </div>
        </div>

        <!-- Anno Scolastico -->
        <div class="my-card" style="margin: 1rem 1rem 0; padding: 1rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 0.75rem; color: var(--text-primary);">Anno Scolastico</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.875rem;">Anno corrente: <strong style="color: var(--text-primary);"><?= htmlspecialchars($year ?: 'non impostato') ?></strong></p>
        </div>

        <!-- Debug -->
        <div class="my-card" style="margin: 1rem 1rem 0; padding: 1rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 0.75rem; color: var(--text-primary);">Debug</h3>
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <span style="color: var(--text-primary); display: block;">JSON nelle card</span>
                    <span style="font-size: 0.8rem; color: var(--text-secondary);">Mostra il raw JSON dentro ogni card evento</span>
                </div>
                <button id="json-toggle" class="my-card" style="border: none; cursor: pointer; padding: 0.5rem 1rem; display: flex; align-items: center; gap: 0.5rem; background: var(--card-bg); box-shadow: var(--shadow-md); border-radius: 0.5rem;">
                    <span id="json-label"><?= $jsonDebugEnabled ? 'ON' : 'OFF' ?></span>
                </button>
            </div>
        </div>

        <!-- Credenziali Salvate -->
        <div class="my-card" style="margin: 1rem 1rem 0; padding: 1rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 0.75rem; color: var(--text-primary);">Credenziali</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.875rem;">
                <strong style="color: var(--text-primary);"><?= $savedUsers ?></strong> utente(i) salvato(i) con cifratura.
            </p>
            <button id="clear-credentials" style="margin-top: 0.5rem; font-size: 0.8rem; background: rgba(239,68,68,0.1); color: var(--grade-red); border: none; border-radius: 0.5rem; padding: 0.4rem 0.8rem; cursor: pointer;">
                Rimuovi credenziali salvate
            </button>
        </div>

        <!-- Logout -->
        <div style="padding: 1rem;">
            <button class="my-card" style="width: 100%; border: none; cursor: pointer; text-align: left; padding: 1rem; display: flex; align-items: center; gap: 0.75rem; background: var(--card-bg); box-shadow: var(--shadow-md);" onclick="document.getElementById('logout-form').submit();">
                <svg width="24" height="24" fill="none" stroke="var(--text-secondary)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span style="font-weight: 500; color: var(--text-primary);">Logout</span>
            </button>
            <form id="logout-form" action="/login" method="post" style="display: none;">
                <input type="hidden" name="action" value="logout">
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Tema toggle
        const savedTheme = sessionStorage.getItem('theme');
        if (savedTheme) {
            document.body.setAttribute('data-theme', savedTheme);
            const label = document.getElementById('theme-label');
            if (label) label.textContent = savedTheme === 'dark' ? 'Scuro' : 'Chiaro';
        }
        document.getElementById('theme-toggle')?.addEventListener('click', () => {
            const current = document.body.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            document.body.setAttribute('data-theme', next);
            sessionStorage.setItem('theme', next);
            const label = document.getElementById('theme-label');
            if (label) label.textContent = next === 'dark' ? 'Scuro' : 'Chiaro';
        });

        // Rimuovi credenziali
        document.getElementById('clear-credentials')?.addEventListener('click', async () => {
            if (!confirm('Rimuovere le credenziali salvate? Dovrai effettuare nuovamente il login.')) return;
            try {
                await fetch('/login', { method: 'POST', body: new URLSearchParams({action: 'logout'}) });
                location.reload();
            } catch (e) { alert('Errore nella rimozione'); }
        });

        // Toggle JSON debug
        document.getElementById('json-toggle')?.addEventListener('click', async () => {
            try {
                await fetch('/settings/json', { method: 'POST' });
                location.reload();
            } catch (e) { alert('Errore'); }
        });
    });
    </script>

    <?php include COMMON_HTML_FOOT ?>
</body>
</html>
