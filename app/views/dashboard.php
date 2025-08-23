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

if (!isset($response)){
    $response = [
        "grades" => [],
        "subjects" => [],
        "error" => "Errore"
    ];
}


function cvv_sync() {
    ?>
    <div class="z-5 p-4 m-3 col-md-5 col-10" style="background: var(--card-color); position: absolute; top: 2em; right: 10px; border-radius: 1rem; box-shadow: 0 10px 20px #0004" id="cvv_sync_form">
        <button class="btn btn-danger" style="position: absolute; right: 10px; top: 10px; aspect-ratio: 1; border-radius: 50%" id="close_cvv_sync_form">X</button>
        <h3>Vuoi effettuare il Sync con classeviva?</h3>
        <h5>Puoi sempre farlo in un secondo momento</h5>

        <form action="/login" method="post" data-bs-theme="<?=THEME?>">
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
function printSubject($subject): void {
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
    <div class="grade mb-2 hidden" data-grade="<?=$grade['decimalValue']?>" data-date="<?=$grade['evtDate']?>" data-period="<?=$grade['periodPos']?>">
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
        @media (max-width: 768px) {
            .graph-2 {
                /*width: 45%;*/
                height: 60%;
            }
        }
        @media (min-width: 768px) {
            & .graph-2 {
                height: 100%;
                width: 100%;
            }
        }
        .media_container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;

            & > div {
                height: 100%;
            }

            & .graph {
                height: 35%;
            }
        }
        .tab-selector {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-color);
            text-decoration: none;
            padding: .5rem;
            border-radius: 1rem;
            margin-bottom: 1rem;
            transition: all .2s ease-in-out;
            &.underline {
                /*border-bottom: 2px solid var(--text-color);*/
                text-decoration: underline;
                text-decoration-thickness: 2px;
                text-underline-offset: 2px;
                text-decoration-color: #535CFF;
            }
        }
        .subject {
            background: var(--card-color);
            border-radius: 1rem;
            /*display: flex;*/
            /*flex-direction: column;*/
            /*justify-content: center;*/
            /*align-items: start;*/
            border: 1px solid whitesmoke;
            margin: 10px;
            padding: 10px;
        }
    </style>
    <body data-theme="<?=THEME?>">

        <div class="grades-container hidden">
            <?php
            $grades = $response['grades'];

            foreach ($grades as $grade) {
                printGrade($grade);
            }
            ?>
        </div>

        <?php (!isset($_SESSION['classeviva_ident']))?cvv_sync():'' ?>

        <div class="container">
            <!-- Bottoni menù in alto -->
            <?php
            $tabs = [
                [
                    "name" => "Grafici",
                    "href" => "graphs",
                ],
                [
                    "name" => "Materie",
                    "href" => "subjects",
                ]
            ];
            foreach ($tabs as $tab) {
                $isSelected = false;
                if (isset($_GET['tab']) && $_GET['tab'] === $tab['href']) {
                    $href = $_GET['tab'];
                    $isSelected = true;
                    $currentTab = $tab['href'];
                }
                else {
                    $isSelected = $tab['href'] === "graphs";
                    $currentTab = "graphs";
                }
                echo "<a class='tab-selector".(($isSelected)?" underline":'')."' href=\"?tab={$tab['href']}\">{$tab['name']}</a>";
            }
            ?>
            
            <!--<div class="main-menu">
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
            </div>-->
            <?php
            if ($currentTab === "graphs") {
                ?>
                <div class="my-card" style="height: 40vh">
                    <div class="media_container mt-2">
                        <!--<h1>Media Voti</h1>-->
                        <div class="d-flex justify-content-between p-md-3 p-2" style="width: 100%">
                            <div class="std-div flex-column flex-md-row">
                                Anno intero
                                <div class="graph-2"> <canvas id="mediaGenerale"></canvas> </div>
                            </div>
                            <div class="std-div flex-column justify-content-between">
                                <div class="order-0">Periodo 1</div>
                                <div class="order-1 graph"> <canvas id="mediaPeriodo1"></canvas> </div>
                                <div class="order-2 order-last">Periodo 3</div>
                                <div class="order-3 graph"> <canvas id="mediaPeriodo3"></canvas> </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="my-card" style="height: 40vh">
                    <div class="" style="width: 100%; height: 100%; display: flex; justify-content: center; align-items: center">
                        <canvas id="averagePerSubject"></canvas>
                    </div>
                </div>
                <script>
                    function hexToRgb(hex) {
                        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
                        return result ? {
                            r: parseInt(result[1], 16),
                            g: parseInt(result[2], 16),
                            b: parseInt(result[3], 16)
                        } : null;
                    }

                    function rgbToHex(r, g, b) {
                        return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
                    }

                    // Funzione per schiarire/scurire un colore HEX
                    // amount: un numero intero, positivo per schiarire, negativo per scurire
                    function changeHexColor(hex, amount) {
                        let rgb = hexToRgb(hex);

                        if (!rgb) {
                            console.error("Colore HEX non valido");
                            return null;
                        }

                        let { r, g, b } = rgb;

                        // Modifica i valori R, G, B
                        r = Math.min(255, Math.max(0, r + amount));
                        g = Math.min(255, Math.max(0, g + amount));
                        b = Math.min(255, Math.max(0, b + amount));

                        return rgbToHex(r, g, b);
                    }

                    function showAvr() {
                        function calcGeneralAvr(){
                            let canvas = document.getElementById('mediaGenerale');

                            // --- Dati di esempio: un array di voti ---
                            let grades = [];
                            let gradesDom = document.querySelectorAll(`.grade`);
                            gradesDom.forEach((grade) => {
                                grade.getAttribute('data-grade') && grades.push(
                                    parseFloat(grade.getAttribute('data-grade'))
                                )
                            })

                            // Calcola la media dei voti
                            let total = grades.reduce((sum, grade) => sum + grade, 0);
                            return [canvas, 8];
                            // return [canvas, total / grades.length];
                        }
                        function calcPeriodAvr(period){
                            let canvas = document.getElementById('mediaPeriodo' + period);
                            let grades = [];
                            let gradesDom = document.querySelectorAll(`.grade[data-period="${period}"]`);
                            gradesDom.forEach((grade) => {
                                grade.getAttribute('data-grade') && grades.push(
                                    parseFloat(grade.getAttribute('data-grade'))
                                )
                            })

                            // Calcola la media dei voti
                            let total = grades.reduce((sum, grade) => sum + grade, 0);
                            // return [canvas, total / grades.length];
                            return [canvas, 7];
                        }

                        const getChartColors = (value, total) => {
                            let gradeColor;
                            if (value >= 6) {
                                gradeColor = '#22c55e'; // Verde
                            } else if (value >= 5) {
                                gradeColor = '#eab308'; // Giallo
                            } else {
                                gradeColor = '#ef4444'; // Rosso
                            }
                            let cardColor = window.getComputedStyle(document.querySelector("body")).getPropertyValue('--card-color');
                            const remainingColor = changeHexColor(cardColor, -50) // Grigio
                            return value === total ? [gradeColor] : [gradeColor, remainingColor];
                        };

                        function drawProgressBar(canvas, average) {
                            const centerTextPlugin = {
                                id: 'centerText',
                                afterDraw: (chart) => {
                                    const { ctx, chartArea: { left, top, width, height } } = chart;
                                    const { averageGrade, backgroundColor } = chart.config.data.datasets[0];

                                    const centerX = left + width / 2;
                                    const centerY = top + height / 2;

                                    ctx.save();
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'middle';
                                    ctx.font = '700 1.8rem Inter, sans-serif';
                                    ctx.fillStyle = backgroundColor[0];

                                    ctx.fillText(averageGrade.toFixed(1), centerX, centerY);
                                    ctx.restore();
                                }
                            };

                            // Valore della media dei voti da visualizzare
                            // const averageGrade = average;
                            const averageGrade = average
                            const maxGrade = 10;

                            const ctx = canvas.getContext('2d');
                            new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    datasets: [{
                                        label: 'Dettagli Voti',
                                        data: [averageGrade, maxGrade - averageGrade],
                                        backgroundColor: getChartColors(averageGrade, maxGrade),
                                        borderColor: '#0000',
                                        // borderRadius: 15,
                                        hoverOffset: 0,
                                        averageGrade: averageGrade
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    cutout: '70%',
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            enabled: false
                                            /*callbacks: {
                                                label: (context) => {
                                                    let label = context.label || '';
                                                    if (label) { label += ': '; }
                                                    if (context.parsed !== null) { label += context.parsed.toFixed(1); }
                                                    return label;
                                                }
                                            }*/
                                        }
                                    }
                                },
                                plugins: [centerTextPlugin]
                            });
                        }

                        // Avvia il disegno con il valore della media
                        window.onload = function () {
                            let tmp;
                            tmp = calcGeneralAvr();
                            drawProgressBar(tmp[0], tmp[1]);
                            tmp = calcPeriodAvr(1);
                            drawProgressBar(tmp[0], tmp[1]);
                            tmp = calcPeriodAvr(3);
                            drawProgressBar(tmp[0], tmp[1]);

                        }
                    }
                    function avrSubject() {
                        let subjects = {
                            subjectID_1: "subject1",
                            subjectID_2: "subject2",
                            subjectID_3: "subject3",
                            subjectID_4: "subject4",
                            subjectID_5: "subject5",
                            subjectID_6: "subject6",
                            subjectID_7: "subject7",
                            subjectID_8: "subject8",
                            subjectID_9: "subject9",
                        }
                        let gradesPerSubject = {
                            subjectID_1: Array.from({length: 10}, () => Math.floor(Math.random() * 9) + 1),
                            subjectID_2: Array.from({length: 10}, () => Math.floor(Math.random() * (9-6)) + 6),
                            subjectID_3: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                            subjectID_4: Array.from({length: 10}, () => Math.floor(Math.random() * 9) + 1),
                            subjectID_5: Array.from({length: 10}, () => Math.floor(Math.random() * (9-6)) + 6),
                            subjectID_6: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                            subjectID_7: Array.from({length: 10}, () => Math.floor(Math.random() * 9) + 1),
                            subjectID_8: Array.from({length: 10}, () => Math.floor(Math.random() * (9-6)) + 6),
                            subjectID_9: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                            // subjectID_10: Array.from({length: 10}, () => Math.floor(Math.random() * 10) + 1),
                            // subjectID_11: Array.from({length: 10}, () => Math.floor(Math.random() * 10) + 6),
                        };
                        let averagePerSubject = {};

                        for (let subjectID in gradesPerSubject) {
                            let subjectName = subjects[subjectID];
                            let grades = gradesPerSubject[subjectID];

                            //Aggiungi la media
                            averagePerSubject[subjectName] = grades.reduce((sum, grade) => sum + grade, 0) / grades.length;
                        }

                        let canvas = document.getElementById('averagePerSubject');
                        const ctx = canvas.getContext('2d');
                        let chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                datasets: [{
                                    label: 'Dettagli Voti',
                                    data: averagePerSubject,
                                    backgroundColor: (context) => {
                                        // console.log(context.parsed)
                                        const average = context.parsed.y;
                                        if (average >= 6) {
                                            return '#22c55e'; // Verde
                                        } else if (average >= 5) {
                                            return '#eab308'; // Giallo
                                        } else {
                                            return '#ef4444'; // Rosso
                                        }
                                    },
                                    borderColor: '#0000',
                                    borderRadius: 0,
                                    hoverOffset: 0,
                                    // averageGrade: averageGrade
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: { display: false },
                                    // tooltip: { enabled: false }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        // min: 0,
                                        // max: 10,
                                    }
                                }
                            },
                        });
                    }
                    //dom content load
                    document.addEventListener('DOMContentLoaded', () => {
                        showAvr();
                        avrSubject();
                    });
                </script>
                <?php
            }
            if ($currentTab === "subjects") {
                ?>
                <div class="" style="">
                    <?php
                    if (isset($response['subjects'])) {
                        foreach ($response['subjects'] as $subject) {
                            printSubject($subject);
                        }
                    }    
                    ?>
                </div>
                <?php

            }
            ?>

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