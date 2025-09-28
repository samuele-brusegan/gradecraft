<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */



if (!isset($grades)) { $grades = ['error' => '']; }
if ( isset($grades['error'])) { echo "<pre>"; print_r($grades); echo "</pre>"; }

function printGrade(mixed $grade): void {
    if ($grade['color'] == "blue") {
        $color = "var(--grade-blue)";
    } else {
        if ($grade['decimalValue'] >= 6) {
            $color = "var(--grade-green)";
        } else if ($grade['decimalValue'] >= 5) {
            $color = "var(--grade-yellow)";
        } else {
            $color = "var(--grade-red)";
        }
    }
    ?>
    <div class="grade mb-2" data-grade="<?=$grade['decimalValue']?>" data-date="<?=$grade['evtDate']?>" data-period="<?=$grade['periodPos']?>">
        <?=$grade['subjectDesc']?>
        <div class="value" style="background: <?=$color?>;">
            <div><?=$grade['displayValue']?></div>
        </div>
        <?=($grade['notesForFamily']!="")?"<hr>".$grade['notesForFamily'].'<br>':''?>
        <hr>
        <?=$grade['evtDate']?><br>
        <?=$grade['teacherName']?>
        <?php
//        echo "<pre style='overflow: hidden;'>"; print_r($grade); echo "</pre>";
        ?>
    </div>
    <?php
}

?>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>GradeCraft - Grades</title>
        <?php include COMMON_HTML_HEAD ?>
        <link rel="stylesheet" href="<?=URL_PATH?>/css/grades.css">
    </head>
    <body data-theme="<?=THEME?>">
        <div class="container">
            <?php
            session_wall();
            ?>
            <style>
                #t-navbar>div{
                    height: fit-content;
                    font-weight: 500;
                    font-size: 1.188rem;
                    margin: 1rem;
                    width: auto;
                    text-wrap: nowrap;
                    padding: 7px 20px;
                    border-radius: 1.5rem
                }
            </style>

            <?php $navSelected = "t-navbar_1"; include COMMON_HTML_TNAVBAR; ?>

            <?php
            for ($i = 0; $i < 10; $i++) {
                ?>
                <div style="background: #D3D3D3; border-radius: 1rem; padding: .3rem 1rem; margin: .7rem; display: flex; justify-content: space-between; box-shadow: 0 4px 9px -2px rgba(0,0,0,0.25);">
                    <div>
                        <h6 style="font-size: 1.375rem; font-weight: 700;">matematica</h6>
                        <p style="font-size: 0.7rem;">
                            32 voti <br>
                            100% sufficienti <br>
                            boh
                        </p>
                    </div>

                    <div class="flex items-center">
                        <grade-graph style="margin: 5px;" data-decimal="7.4" data-period-name="1° quad."></grade-graph>
                        <grade-graph style="margin: 5px;" data-decimal="7.9" data-period-name="2° quad."></grade-graph>
                    </div>
                </div>

                <?php
            }
            ?>



        </div>

        <?php include COMMON_HTML_FOOT ?>
    </body>
</html>
