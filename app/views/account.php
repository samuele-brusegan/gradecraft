<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

if (!defined('USR')) {
    // USR non definito? fallback
    $user = null;
} else {
    $user = USR;
    // Assicurati che $user sia un oggetto, altrimenti impostalo a null
    if (!is_object($user)) {
        $user = null;
    }
}
?>

<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GradeCraft - Account</title>
    <?php include COMMON_HTML_HEAD ?>
</head>
<body data-theme="<?=THEME?>">
    <div class="container">

        <?php if (!isset($_SESSION['classeviva_ident'])): ?>
            <?php cvv_sync(); ?>
        <?php else: ?>
            <!-- Header -->
            <header style="padding: 2rem 1rem 1rem;">
                <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin: 0;">Account</h1>
            </header>

            <!-- Profilo utente -->
            <div class="my-card" style="display: flex; align-items: center; gap: 1.5rem;">
                <div style="
                    width: 5rem;
                    height: 5rem;
                    border-radius: 50%;
                    background: var(--accent-blue);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-size: 2rem;
                    font-weight: 700;
                    flex-shrink: 0;
                    box-shadow: var(--shadow-md);
                ">
                    <?php
                    $firstName = $user?->firstName ?? 'U';
                    echo htmlspecialchars(strtoupper(substr($firstName, 0, 1)));
                    ?>
                </div>
                <div style="flex: 1;">
                    <h2 style="font-size: 1.5rem; font-weight: 600; margin: 0 0 0.25rem 0; color: var(--text-primary);">
                        <?php
                        $first = $user?->firstName ?? 'Utente';
                        $last = $user?->lastName ?? '';
                        echo htmlspecialchars($first . ' ' . $last);
                        ?>
                    </h2>
                    <p style="color: var(--text-secondary); margin: 0; font-size: 0.875rem;">
                        <?php
                        // Username/email from session or user object
                        $email = $user?->uid ?? $_SESSION['classeviva_username'] ?? '';
                        echo htmlspecialchars($email);
                        ?>
                    </p>
                </div>
            </div>

            <!-- Statistiche rapide -->
            <div class="my-card" style="
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
                text-align: center;
                margin-top: 1rem;
            ">
                <div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-blue);">--</div>
                    <div style="font-size: 0.875rem; color: var(--text-secondary);">Media</div>
                </div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-blue);">--</div>
                    <div style="font-size: 0.875rem; color: var(--text-secondary);">Assenze</div>
                </div>
            </div>

            <!-- Account secondari -->
            <div class="my-card" style="margin-top: 1rem;">
                <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 1rem 0; color: var(--text-primary);">
                    Account secondari
                </h3>
                <p style="color: var(--text-secondary); margin: 0;">Nessun account aggiuntivo configurato.</p>
                <!-- Future: list of secondary accounts -->
            </div>

            <!-- Impostazioni Tema -->
            <div class="my-card" style="margin-top: 1rem;">
                <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 1rem 0; color: var(--text-primary);">
                    Aspetto
                </h3>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span>Tema</span>
                    <button id="theme-toggle" class="my-card" style="
                        border: none;
                        cursor: pointer;
                        padding: 0.5rem 1rem;
                        margin: 0;
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                        background: var(--card-bg);
                        box-shadow: var(--shadow-md);
                        border-radius: 0.5rem;
                    ">
                        <svg width="20" height="20" fill="none" stroke="var(--text-secondary)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <span id="theme-label" style="font-weight: 500; color: var(--text-primary);"><?= THEME === 'dark' ? 'Scuro' : 'Chiaro' ?></span>
                    </button>
                </div>
            </div>

            <!-- Logout -->
            <div style="padding: 1rem;">
                <button class="my-card" style="
                    width: 100%;
                    border: none;
                    cursor: pointer;
                    text-align: left;
                    padding: 1rem;
                    margin: 0;
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    background: var(--card-bg);
                    box-shadow: var(--shadow-md);
                " onclick="document.getElementById('logout-form').submit();">
                    <svg width="24" height="24" fill="none" stroke="var(--text-secondary)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span style="font-weight: 500; color: var(--text-primary);">Logout</span>
                </button>
                <form id="logout-form" action="/login?logout" method="post" style="display: none;"></form>
            </div>

        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = sessionStorage.getItem('theme');
            if (savedTheme) {
                document.body.setAttribute('data-theme', savedTheme);
                const label = document.getElementById('theme-label');
                if (label) label.textContent = savedTheme === 'dark' ? 'Scuro' : 'Chiaro';
            }

            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    const body = document.body;
                    const currentTheme = body.getAttribute('data-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    body.setAttribute('data-theme', newTheme);
                    sessionStorage.setItem('theme', newTheme);
                    const label = document.getElementById('theme-label');
                    if (label) label.textContent = newTheme === 'dark' ? 'Scuro' : 'Chiaro';
                });
            }
        });
    </script>
    <?php include COMMON_HTML_FOOT ?>
</body>
</html>
