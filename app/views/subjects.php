<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

if (!isset($response)) { $response = ['error' => '']; }
if ( isset($response['error'])) { echo "<pre>"; print_r($response); echo "</pre>"; }

function printSubject($subject) {
    $id = $subject['id'];
    $name = $subject['description'];
    $order = $subject['order'];
    if ($id == "215883") $name = "TPSIT";
    $profs = $subject['teachers'];

    $profList = "";
    foreach ($profs as $prof) {
        $profList .= $prof['teacherName'] . ", ";
    }
    $profList = substr($profList, 0, -2);
    
    ?>
    <div class="subject" style="order:<?=$order?>">
        <div><?=$name?></div>
        <hr>
        <div><?=$profList?></div>
    </div>
    <?php
}

?>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Gradecraft - Subjects</title>
        <?php include COMMON_HTML_HEAD ?>
    </head>
    <style>
        .subject {
            background: var(--card-color);
            border-radius: 1rem;
            /*display: flex;*/
            /*flex-direction: column;*/
            /*justify-content: center;*/
            /*align-items: start;*/
            border: 1px solid black;
            margin: 10px;
            padding: 10px;
        }
    </style>
    <body>
        <div class="container">
            <?php
            //        echo "<pre>"; print_r($response); echo "</pre>";
            session_wall();

            foreach ($response as $subject) {
                printSubject($subject);
            }

            ?>
        </div>

        <?php include COMMON_HTML_FOOT; ?>
    </body>
</html>
