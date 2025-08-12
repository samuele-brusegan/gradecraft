<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

global $error;
global $loginResponse;

?>


<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>GradeCraft - Home</title>
        <?php include BASE_PATH . '/public/head.php'; ?>
        <link  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.min.js" integrity="sha384-RuyvpeZCxMJCqVUGFI0Do1mQrods/hhxYlcVfGPOfQtPJh0JCw12tUAZ/Mv10S7D" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="z-5 p-3 m-3 col-5" style="background: #BBB; position: relative; border-radius: 1rem" id="cvv_sync_form">
            <button class="btn btn-danger" style="position: absolute; right: 10px; top: 10px; aspect-ratio: 1; border-radius: 50%" id="close_cvv_sync_form">X</button>
            <h3>Vuoi effettuare il Sync con classeviva?</h3>
            <h5>Puoi sempre farlo in un secondo momento</h5>

            <form action="/login" method="post">
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Email address</label>
                    <input type="email" autocomplete="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="usr">
                    <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input autocomplete="current-password" type="password" class="form-control" id="exampleInputPassword1" name="pwd">
                </div>
                <div class="col-12 d-flex justify-content-center align-items-center">
                    <button type="submit" class="btn btn-primary col-2">Submit</button>
                </div>
            </form>
            <script>
                document.getElementById('close_cvv_sync_form').addEventListener('click', () => {
                    document.getElementById('cvv_sync_form').classList.add('hidden');
                });
            </script>
        </div>

        <div>
            <?php
                if (isset($error))         echo "<h1>ERROR: $error</h1>";
                if (isset($loginResponse)) echo "LoginResponse:" . $loginResponse;
            ?>
        </div>

        <div style="position: absolute; bottom: 0; left: 0; width: 100%; display: flex; justify-content: space-between; align-items: center; background: #000;">
            <a style="color: black; text-decoration: none; margin: 1rem; border-radius: 1rem; background: #999; padding: .5rem;" href="/grades">Grades</a>
            <a style="color: black; text-decoration: none; margin: 1rem; border-radius: 1rem; background: #999; padding: .5rem;" href="/">home        </a>
            <a style="color: black; text-decoration: none; margin: 1rem; border-radius: 1rem; background: #999; padding: .5rem;" href="/">Settings    </a>
        </div>

    </body>

</html>