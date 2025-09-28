<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

function session_wall(): void {
    if (!isset($_SESSION['classeviva_auth_token']) || $_SESSION['classeviva_auth_token'] == "" || $_SESSION['classeviva_auth_token'] == null) {
        ?>
        <div class="alert alert-danger" data-bs-theme="dark">
            <h4>Non sei loggato!</h4> <hr>
            <div class="d-flex justify-content-between align-items-center">
                In questo momento dello sviluppo, GradeCraft non supporta la funzione richiesta senza login.
                <a class="btn btn-danger mt-2" href='/' style="border-radius: 1rem;">
                    <span class="d-none d-md-block">Torna alla home</span>
                    <span class="d-block d-md-none">
                        <span class="material-symbols-outlined">home</span>
                    </span>
                </a>
            </div>
        </div>

        <?php
        exit;
    }
}
