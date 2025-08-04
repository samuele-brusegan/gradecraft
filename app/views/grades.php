<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

global $grades;

?>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>GradeCraft - Grades</title>
    </head>
    <body>
        <h3>Buongiorno <b>$utente</b>!</h3>
        <?php
            foreach ($grades as $grade) {
                print_r($grade);
                echo "<br>";
            }
        ?>
    </body>
</html>
