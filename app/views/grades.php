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

            <div style="overflow: auto; display: flex;" id="t-navbar">
                <div id="t-navbar_1" style="margin: 1rem; width: auto; text-wrap: nowrap; padding: 10px 20px;border-radius: 1.5rem" data-selected> materie </div>
                <div id="t-navbar_2" style="margin: 1rem; width: auto; text-wrap: nowrap; padding: 10px 20px;border-radius: 1.5rem" > 1° quadrimestre </div>
                <div id="t-navbar_3" style="margin: 1rem; width: auto; text-wrap: nowrap; padding: 10px 20px;border-radius: 1.5rem" > 2° quadrimestre </div>
            </div>
            <script>
                let tNavbarBnts = document.querySelectorAll("#t-navbar>div");

                refresh_tNavbar();

                function refresh_tNavbar() {
                    tNavbarBnts.forEach(btn => {
                        if (btn.hasAttribute("data-selected")) {
                            btn.style.backgroundColor = "#4170D7";
                            btn.style.color = "white";
                        } else {
                            btn.style.backgroundColor = "";
                            btn.style.color = "";
                        }
                    })
                }

                tNavbarBnts.forEach(btn => {
                    btn.addEventListener("click", () => {
                        if (btn.hasAttribute("data-selected")) {
                            refresh_tNavbar();
                        } else {
                            tNavbarBnts.forEach(btn => {
                                btn.removeAttribute("data-selected");
                            })
                            btn.setAttribute("data-selected", "");
                            refresh_tNavbar();
                        }
                    })
                })
            </script>

            <?php
            for ($i = 0; $i < 10; $i++) {
                ?>

                <div style="background: #D3D3D3; border-radius: 1rem; padding: .3rem 1rem; margin: .7rem; display: flex; justify-content: space-between;">
                    <div>
                        <h6 style="font-size: 1.375rem;">matematica</h6>
                        <p style="font-size: 0.563rem;">
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
