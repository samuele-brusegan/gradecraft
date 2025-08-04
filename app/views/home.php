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
        <title>GradeCraft - Home</title>
        <?php include BASE_PATH . '/public/head.php'; ?>
        <link  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.min.js" integrity="sha384-RuyvpeZCxMJCqVUGFI0Do1mQrods/hhxYlcVfGPOfQtPJh0JCw12tUAZ/Mv10S7D" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="z-5 p-3 m-3" style="background: #BBB; position: relative;">
            <button class="btn btn-danger" style="position: absolute; right: 10px; top: 10px;">X</button>
            <h3>Vuoi effettuare il Sync con classeviva?</h3>
            <form action="<?=$_SERVER['PHP_SELF']?>">
                <label>
                    <span>Email</span>
                    <input type="text" name="email" autocomplete="email">
                </label>
                <label>
                    <span>Password</span>
                    <input type="password" name="password" autocomplete="current-password">
                </label>
                <input type="submit" value="Login">
                <input type="hidden" name="action" value="login">
            </form>
        </div>

        <div style="position: absolute; bottom: 0; left: 0; width: 100%; display: flex; justify-content: space-between; align-items: center; background: #000;">
            <a style="color: black; text-decoration: none; margin: 1rem; border-radius: 1rem; background: #999; padding: .5rem;" href="/">home        </a>
            <a style="color: black; text-decoration: none; margin: 1rem; border-radius: 1rem; background: #999; padding: .5rem;" href="/grades">Grades</a>
            <a style="color: black; text-decoration: none; margin: 1rem; border-radius: 1rem; background: #999; padding: .5rem;" href="/">Settings    </a>
        </div>

    </body>
</html>