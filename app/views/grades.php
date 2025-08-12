<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

if (!isset($grades)) { $grades = []; }

function printGrade(mixed $grade): void {
//    if ($grade['noAverage'] == "") return;
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
    if (!isset($color)) $color = "var(--grade-gray)";
    ?>
    <div class="grade" data-grade="<?=$grade['decimalValue']?>" data-date="<?=$grade['evtDate']?>" data-period="<?=$grade['periodPos']?>">
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
        <?php require BASE_PATH . '/public/head.php';?>
        <link rel="stylesheet" href="css/grades.css">
    </head>
    <style>
        .media_container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: #2a2a2a;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        h1 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        canvas {
            border-radius: 50%; /* Rende l'area del canvas visivamente rotonda */
        }
    </style>
    <body>
        <div class="container">
            <h3>Buongiorno <b><?=ucwords(strtolower($_SESSION['classeviva_first_name']))?></b>!</h3>

            <div class="media_container">
                <h1>Media Voti</h1>
                <div class="d-flex flex-column flex-md-row justify-content-center justify-content-md-between" style="width: 100%;">
                    <div class="std-div">
                        Media generale
                        <canvas id="mediaGenerale" width="200" height="200" style=""></canvas>
                    </div>
                    <div class="std-div flex-column">
                        <div>
                            Media periodo 1
                            <canvas id="mediaPeriodo1" width="200" height="200"></canvas>
                        </div>
                        <div>
                            Media periodo 3
                            <canvas id="mediaPeriodo3" width="200" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="media_container mt-2">
                <h1>Stat. Voti</h1>
                <div class="d-flex flex-column flex-md-row justify-content-center justify-content-md-between" style="width: 100%; height: 400px; padding-bottom: 15px;">
                    <div class="std-div" style="position: relative; height: 200px; margin-top: 100px; width: calc(200px + 11em);">
                        Anno Intero
                        <canvas id="statGenerale" style=""></canvas>
                    </div>
                    <div class="std-div flex-column">
                        <div class="h-50 mb-3">
                            Periodo 1
                            <canvas id="statPeriodo1"></canvas>
                        </div>
                        <div class="h-50 mt-2">
                            Periodo 3
                            <canvas id="statPeriodo3"></canvas>
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
                    echo "<br>";
                }
            ?>
        </div>
    </body>
    <script>

        function showStat() {


            function getAllGrades(){
                let grades = [];
                let gradesDom = document.querySelectorAll(`.grade`);
                gradesDom.forEach((grade, index) => {
                    grade.getAttribute('data-grade') && grades.push(
                        parseFloat(grade.getAttribute('data-grade'))
                    )
                })
                return grades;
            }
            function getGradesPerPeriod(period){
                let grades = [];
                let gradesDom = document.querySelectorAll(`.grade[data-period="${period}"]`);
                gradesDom.forEach((grade, index) => {
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
                        maintainAspectRatio: false,
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
                let gradeChart = new Chart( document.getElementById(id), config );
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
                gradesDom.forEach((grade, index) => {
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
                gradesDom.forEach((grade, index) => {
                    grade.getAttribute('data-grade') && grades.push(
                        parseFloat(grade.getAttribute('data-grade'))
                    )
                })

                // Calcola la media dei voti
                let total = grades.reduce((sum, grade) => sum + grade, 0);
                return [canvas, total / grades.length];
            }

            // --- Funzione per ottenere il colore in base al voto ---
            function getColor(value) {
                if (value >= 6) {
                    return '#4caf50'; // Verde
                } else if (value >= 5 && value < 6) {
                    return '#ffc107'; // Giallo
                } else {
                    return '#f44336'; // Rosso
                }
            }

            // --- Funzione per disegnare la barra e il testo ---
            function drawProgressBar(canvas, value) {
                let averageGrade = value;
                let ctx = canvas.getContext('2d');
                let centerX = canvas.width / 2;
                let centerY = canvas.height / 2;
                let radius = 80;
                let lineWidth = 12;
                let maxVal = 10;
                // Ottieni il colore in base al valore della media
                const progressColor = getColor(value);

                // Pulisce l'intero canvas per il prossimo frame
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                // --- Disegna il cerchio di sfondo ---
                ctx.beginPath();
                ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
                ctx.lineWidth = lineWidth;
                ctx.strokeStyle = '#3a3a3a';
                ctx.stroke();

                // --- Disegna la barra di progresso colorata ---
                const startAngle = -0.5 * Math.PI; // L'angolo iniziale (ore 12)
                const endAngle = startAngle + (value / maxVal) * (2 * Math.PI);

                ctx.beginPath();
                ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                ctx.lineWidth = lineWidth;
                ctx.strokeStyle = progressColor;
                ctx.lineCap = 'round'; // Rende le estremità della linea arrotondate
                ctx.stroke();

                // --- Disegna il numero al centro ---
                // Mostra il numero arrotondato a una cifra decimale
                ctx.fillStyle = progressColor;
                ctx.font = 'bold 3rem Arial';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(averageGrade.toFixed(1), centerX, centerY);
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

        //Calcolo la media dei voti
        document.addEventListener('DOMContentLoaded', () => {
            showAvr()
            showStat()
        });
    </script>
</html>
