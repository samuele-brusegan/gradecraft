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
        <?php include COMMON_HTML_HEAD; ?>
        <title>GradeCraft - Settings</title>
    </head>
    <body data-theme="<?=THEME?>">
        <div class="container" style="overflow: hidden;">
            <h1>Settings</h1>
            <?php
            echo "Sessione:";
            echo "<pre style='overflow: hidden;'>"; print_r($_SESSION); echo "</pre>";
            echo "Cookie:";
            echo "<pre style='overflow: hidden;'>";
                        
            foreach ($_COOKIE as $key => $value) {
                if ($key != "users") echo "[$key] => $value<br>";
                else echo "[$key] => ".print_r(json_decode($value, true), true) . "<br>";
            }

            echo "</pre>";
//            echo "<pre>"; print_r($_COOKIE['users']); echo "</pre>";
            ?>
        </div>
    <?php include COMMON_HTML_FOOT; ?>
    </body>
</html>
