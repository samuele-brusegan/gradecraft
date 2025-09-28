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
    <style>
        @media (max-width: 768px) {
            .graph-2 {
                /*width: 45%;*/
                height: 60%;
            }
        }
        @media (min-width: 768px) {
            .graph-2 {
                height: 100%;
                width: 100%;
            }
        }

        /*.media_container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 4rem;
            background: var(--card-color);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            & > div  {
                width: 100%;
                !*height: 400px;*!
                !*padding-bottom: 15rem;*!
                !*padding-top: 15rem;*!
            }
            & .graph {
                height: 35%;
            }
        }*/
        .my-card > .media_container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            /*padding: 0;*/
            /*background: transparent;*/
            /*box-shadow: none;*/

            & > div {
                height: 100%;
            }

            & .graph {
                height: 35%;
            }
        }

        h1 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        /*.graph {
            position: relative;
            !*height: 20%;*!
            width: 200px;
            height: 200px;
            margin: 20px;
            !*margin-top: 100px;*!
        }
        .graph-2 {
            position: relative;
            !*height: 20%;*!
            width:  calc(200px * 1.67);
            height: calc(200px * 1.67);
            margin: 20px;
            !*margin-top: 100px;*!
        }
        @media only screen and (max-width: 768px) {
            .graph-2 {
                width:  calc(200px * 1);
                height: calc(200px * 1);
            }
        }*/

        canvas {
            /*border-radius: 50%; !* Rende l'area del canvas visivamente rotonda *!*/
        }
    </style>
    <body data-theme="<?=THEME?>">
        <div class="container">
            <?php
            session_wall();
            ?>
            <h3>Buongiorno <b><?=ucwords(strtolower($_SESSION['classeviva_first_name']))?></b>!</h3>

            <!--<div class="media_container mt-2">
                <h1>Media Voti</h1>
                <div class="d-flex flex-column flex-md-row justify-content-center justify-content-md-between px-5 p-md-5">
                    <div class="std-div flex-column flex-md-row">
                        Anno intero
                        <div class="graph-2"> <canvas id="mediaGenerale"></canvas> </div>
                    </div>
                    <div class="std-div flex-column">
                        <div class="order-0">Periodo 1</div>
                        <div class="order-1 graph"> <canvas id="mediaPeriodo1"></canvas> </div>
                        <div class="order-2 order-md-last">Periodo 3</div>
                        <div class="order-3 graph"> <canvas id="mediaPeriodo3"></canvas> </div>
                    </div>
                </div>
            </div>-->
            <div class="my-card" style="height: 40vh">
                <div class="media_container mt-2">
                    <!--<h1>Media Voti</h1>-->
                    <div class="d-flex justify-content-between p-md-3 p-2" style="width: 100%">
                        <div class="std-div flex-column flex-md-row">
                            Anno intero
                            <div class="graph-2"> <canvas id="mediaGenerale"></canvas> </div>
                        </div>
                        <div class="std-div flex-column justify-content-between">
                            <div class="w-fit order-0">Periodo 1</div>
                            <div class="w-fit order-1 graph"> <canvas id="mediaPeriodo1"></canvas> </div>
                            <div class="w-fit order-2 order-last">Periodo 3</div>
                            <div class="w-fit order-3 graph"> <canvas id="mediaPeriodo3"></canvas> </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--<div class="media_container mt-2">
                <h1>Stat. Voti</h1>
                <div class="d-flex flex-column flex-md-row justify-content-center justify-content-md-between px-5 p-md-5">
                    <div class="std-div flex-column flex-md-row">
                        Anno intero
                        <div class="graph-2"> <canvas id="statGenerale"></canvas> </div>
                    </div>
                    <div class="std-div flex-column">
                        <div class="order-0">Periodo 1</div>
                        <div class="order-1 graph"> <canvas id="statPeriodo1"></canvas> </div>
                        <div class="order-2 order-md-last">Periodo 3</div>
                        <div class="order-3 graph"> <canvas id="statPeriodo3"></canvas> </div>
                    </div>
                </div>
            </div>-->
            <div class="my-card" style="height: 40vh">
                <div class="media_container mt-2">
                    <!--<h1>Media Voti</h1>-->
                    <div class="d-flex justify-content-between p-md-3 p-2" style="width: 100%">
                        <div class="std-div flex-column flex-md-row">
                            Anno intero
                            <div class="graph-2"> <canvas id="statGenerale"></canvas> </div>
                        </div>
                        <div class="std-div flex-column justify-content-between">
                            <div class="order-0">Periodo 1</div>
                            <div class="order-1 graph"> <canvas id="statPeriodo1"></canvas> </div>
                            <div class="order-2 order-last">Periodo 3</div>
                            <div class="order-3 graph"> <canvas id="statPeriodo3"></canvas> </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
                //Ordino per data decrescente
                usort($grades, function($a, $b) {
                    return ($a['evtDate'] <=> $b['evtDate'])*-1;
                });
                foreach ($grades as $grade) {
                    printGrade($grade);
                }
            ?>
            <?php include BASE_PATH."/public/commons/pagination.php"; ?>
        </div>
        <?php include COMMON_HTML_FOOT ?>
    </body>
    <!--suppress JSUnresolvedReference -->
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


        function showStat() {

            function getAllGrades(){
                let grades = [];
                let gradesDom = document.querySelectorAll(`.grade`);
                gradesDom.forEach((grade) => {
                    grade.getAttribute('data-grade') && grades.push(
                        parseFloat(grade.getAttribute('data-grade'))
                    )
                })
                return grades;
            }
            function getGradesPerPeriod(period){
                let grades = [];
                let gradesDom = document.querySelectorAll(`.grade[data-period="${period}"]`);
                gradesDom.forEach((grade) => {
                    grade.getAttribute('data-grade') && grades.push(
                        parseFloat(grade.getAttribute('data-grade'))
                    )
                })
                return grades;
            }
            function show(id, grades) {
                // Inizializza i contatori per ogni categoria
                let sufficienti = 0;
                let quasiSufficienti = 0;
                let insufficienti = 0;

                // Calcola la distribuzione dei voti
                grades.forEach(grade => {
                    if (grade >= 6) {
                        sufficienti++;
                    } else if (grade >= 5 && grade < 6) {
                        quasiSufficienti++;
                    } else {
                        insufficienti++;
                    }
                });

                // Prepara i dati per Chart.js
                const data = {
                    labels: ['Sufficienti', 'Quasi Sufficienti', 'Gravemente Insufficienti'],
                    datasets: [{
                        borderColor: "#0000",
                        data: [sufficienti, quasiSufficienti, insufficienti],
                        backgroundColor: [
                            '#4caf50', // Verde
                            '#ffc107', // Giallo
                            '#f44336'  // Rosso
                        ],
                        // hoverOffset: 0 // Impostato a 0 per rimuovere il bordo bianco al passaggio del mouse
                    }]
                };

                // Configura le opzioni del grafico
                const config = {
                    type: 'doughnut', // Tipo di grafico: "doughnut" per la ciambella
                    data: data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: false,
                                position: 'bottom',
                                labels: {
                                    color: '#f0f0f0' // Colore delle etichette nella legenda
                                }
                            },
                            title: {
                                display: false,
                            }
                        }
                    }
                };

                // Inizializza il grafico
                /*let gradeChart = */new Chart( document.getElementById(id), config );
            }

            show('statGenerale', getAllGrades())
            show('statPeriodo1', getGradesPerPeriod(1))
            show('statPeriodo3', getGradesPerPeriod(3))
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
                return [canvas, total / grades.length];
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
                return [canvas, total / grades.length];
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
                let cardColor = window.getComputedStyle(document.querySelector("body")).getPropertyValue('--card-color');;
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
        function paginate() {
            function updatePageLimits() {
                paginateStart = (paginateCurrentPage - 1) * paginatePerPage;
                paginateEnd = paginateStart + paginatePerPage;
            }
            function updatePaginationButtons() {
                nextPage.setAttribute("data-page", (paginateCurrentPage + 1) + "");
                prevPage.setAttribute("data-page", (paginateCurrentPage - 1) + "");

                paginationButtons.forEach((el) => {
                    if (el.getAttribute("data-page") <= 0 || el.getAttribute("data-page") > pages || el.getAttribute("data-page") === paginateCurrentPage+"") {
                        el.classList.add("disabled");
                    } else {
                        el.classList.remove("disabled");
                    }
                })
                document.querySelectorAll('.pagination-buttons>div').forEach((el) => {
                    el.textContent = paginateCurrentPage;
                })
            }
            function updateHiddenGrades() {
                grades.forEach((el) => {
                    el.classList.add('hidden')
                })

                for (let i = paginateStart; i < paginateEnd; i++) {
                    grades[i].classList.remove('hidden')
                }
            }
            let grades = document.querySelectorAll('.grade');
            let paginateCount = grades.length;
            let paginatePerPage = 3;
            let pages = Math.ceil(paginateCount / paginatePerPage);
            let paginateCurrentPage = 1;
            let paginateStart;
            let paginateEnd;
            let nextPage  = document.getElementById('next-page');
            let prevPage  = document.getElementById('prev-page');
            let firstPage = document.getElementById('frst-page');
            let lastPage  = document.getElementById('last-page');
            let paginationButtons = document.querySelectorAll('.pagination-buttons>.btn');

            firstPage.setAttribute("data-page", 1 + "");
            lastPage.setAttribute("data-page", pages + "");

            updatePageLimits();
            updatePaginationButtons();
            updateHiddenGrades();

            nextPage.addEventListener('click', () => {
                paginateCurrentPage = Number( nextPage.getAttribute('data-page') );
                console.log(paginateCurrentPage);
                updatePaginationButtons();
                updatePageLimits();
                updateHiddenGrades()
            })
            prevPage.addEventListener('click', () => {
                paginateCurrentPage = Number( prevPage.getAttribute('data-page') );
                console.log(paginateCurrentPage);
                updatePaginationButtons();
                updatePageLimits();
                updateHiddenGrades();
            })
            firstPage.addEventListener('click', () => {
                paginateCurrentPage = 1;
                updatePaginationButtons();
                updatePageLimits();
                updateHiddenGrades();
            })
            lastPage.addEventListener('click', () => {
                paginateCurrentPage = pages;
                updatePaginationButtons();
                updatePageLimits();
                updateHiddenGrades();
            })

        }

        //Calcolo la media dei voti
        document.addEventListener('DOMContentLoaded', () => {
            showAvr()
            showStat()
            paginate();
        });
    </script>
</html>
