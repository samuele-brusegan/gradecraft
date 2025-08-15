<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

global $error;
global $loginResponse;

if (isset($_SESSION['classeviva_ident'])) {
    echo "LOGGED IN";
} else {
    echo "NOT LOGGED IN";
}

function cvv_sync() {
    ?>
    <div class="z-5 p-4 m-3 col-md-5 col-10" style="background: var(--card-color); position: absolute; top: 2em; right: 10px; border-radius: 1rem; box-shadow: 0 10px 20px #0004" id="cvv_sync_form">
        <button class="btn btn-danger" style="position: absolute; right: 10px; top: 10px; aspect-ratio: 1; border-radius: 50%" id="close_cvv_sync_form">X</button>
        <h3>Vuoi effettuare il Sync con classeviva?</h3>
        <h5>Puoi sempre farlo in un secondo momento</h5>

        <form action="/login" method="post" data-bs-theme="dark">
            <div class="mb-3">
                <label for="inputUsername" class="form-label">Email address</label>
                <input type="text" autocomplete="email" class="form-control" id="inputUsername" aria-describedby="emailHelp" name="usr">
                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
            </div>
            <div class="mb-3">
                <label for="inputPassword" class="form-label">Password</label>
                <input autocomplete="current-password" type="password" class="form-control" id="inputPassword" name="pwd">
            </div>
            <div class="col-12 d-flex justify-content-center align-items-center">
                <button type="submit" class="btn btn-primary col-md-2 col-6">Submit</button>
            </div>
        </form>
        <script>
            document.getElementById('close_cvv_sync_form').addEventListener('click', () => {
                document.getElementById('cvv_sync_form').classList.add('hidden');
            });
        </script>
    </div>
    <?php
}

?>


<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>GradeCraft - Home</title>
        <?php include COMMON_HTML_HEAD ?>
    </head>
    <style>
        .main-menu {
            & > .main-menu-card {
                display: flex;
                justify-content: start;
                align-items: center;

                background: var(--card-color);
                border-radius: 1rem;
                padding: 1rem;
                margin-top: .7rem;

                & > .name {
                    padding-left: .7rem;
                    font-size: 2rem;
                }
                & > img {
                    border-radius: 50%;
                    width: 70px;
                    aspect-ratio: 1;
                }
            }
            & > .main-menu-card[data-disabled="true"] {
                opacity: .5;
                cursor: not-allowed;
            }
        }
    </style>
    <body>
        <?php (!isset($_SESSION['classeviva_ident']))?cvv_sync():'' ?>

        <div class="container">

            <div class="main-menu">
                <div class="main-menu-card" data-href="/grades">
                    <img src="https://brusegan.it/assets/placeholder.svg" alt="">
                    <div class="name">Grades</div>
                </div>
                <div class="main-menu-card" data-href="/subjects">
                    <img src="https://brusegan.it/assets/placeholder.svg" alt="">
                    <div class="name">Subjects</div>
                </div>
                <div class="main-menu-card" data-href="/agenda">
                    <img src="https://brusegan.it/assets/placeholder.svg" alt="">
                    <div class="name">Agenda</div>
                </div>
                <script>
                    document.querySelectorAll('.main-menu-card').forEach(card => {
                        card.addEventListener('click', () => {
                            if (card.getAttribute('data-disabled') !== 'true') {
                                window.location.href = card.getAttribute('data-href');
                            }
                        });
                    });
                </script>
            </div>

        </div>


        <div>
            <?php
                if (isset($error))         echo "<h1>ERROR: $error</h1>";
                if (isset($loginResponse)) echo "LoginResponse:" . $loginResponse;
            ?>
        </div>
        <?php include COMMON_HTML_FOOT ?>
    </body>

</html>