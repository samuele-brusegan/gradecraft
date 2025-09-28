<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard Voti Scolastici</title>
        <?php include COMMON_HTML_HEAD; ?>
        <!-- Caricamento Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- Caricamento Chart.js per i grafici -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

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

            body {
                font-family: 'Inter', sans-serif;
                background-color: #f3f4f6; /* Sfondo generale come nell'immagine */
                display: flex;
                justify-content: center;
                align-items: flex-start;
            }

            /* Stili per simulare la cornice di un dispositivo mobile */
            .mobile-frame {
                width: 100%;
                /*max-width: 400px;*/
                /*width: 100%;*/
                /*background-color: white;*/
                min-height: 100vh;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                /*padding-top: 50px; !* Spazio per la barra di stato simulata *!*/
            }

            /* Contenitore per il canvas del grafico */
            .chart-container {
                position: relative;
                height: 150px; /* Altezza fissa per il grafico */
                width: 100%;
            }

        </style>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            'primary-blue': '#2563eb', // Colore blu per il quadrimestre attivo
                            'good-grade': '#84cc16', // Verde lime per il voto 7.9
                        }
                    }
                }
            }
        </script>
    </head>
    <body>
        <div class="mobile-frame">
            <div class="p-4 pt-8">

            <?php $navSelected = "t-navbar_2"; include COMMON_HTML_TNAVBAR; ?>

            <!-- Graph Card -->
            <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-100">
                <div class="flex justify-end mb-2 h-5">
                    <!-- Spazio per l'icona rimossa -->
                </div>

                <!-- Grafico Chart.js -->
                <div class="chart-container">
                    <canvas id="gradesChart"></canvas>
                </div>

            </div>

            <!-- Titolo Ultimi Voti -->
            <h2 class="text-2xl font-bold mt-8 mb-4">Ultimi voti</h2>

            <!-- Sezione Ultimi Voti (Grade List) -->
            <div id="latest-grades">
                <!-- Voto 1: Matematica 7.9 -->
                <div class="bg-white rounded-xl shadow-md p-4 my-3 flex items-center">
                    <span class="flex-grow text-lg font-semibold text-gray-800">matematica</span>
                    <div class="relative w-24 h-8 rounded-full overflow-hidden bg-gray-200">
                        <!-- La barra verde che simula il 79% di riempimento (7.9/10) -->
                        <div class="absolute inset-0 bg-good-grade" style="width: 79%;"></div>
                        <span class="absolute right-0 top-0 bottom-0 flex items-center justify-center pr-3 text-white font-bold text-sm">
                            7.9
                        </span>
                    </div>
                </div>

                <!-- Voto 2: Matematica 7.9 -->
                <div class="bg-white rounded-xl shadow-md p-4 my-3 flex items-center">
                    <span class="flex-grow text-lg font-semibold text-gray-800">matematica</span>
                    <div class="relative w-24 h-8 rounded-full overflow-hidden bg-gray-200">
                        <div class="absolute inset-0 bg-good-grade" style="width: 65%;"></div>
                        <span class="absolute right-0 top-0 bottom-0 flex items-center justify-center pr-3 text-white font-bold text-sm">
                            6.5
                        </span>
                    </div>
                </div>

                <!-- Voto 3: Matematica 7.9 -->
                <div class="bg-white rounded-xl shadow-md p-4 my-3 flex items-center">
                    <span class="flex-grow text-lg font-semibold text-gray-800">matematica</span>
                    <div class="relative w-24 h-8 rounded-full overflow-hidden bg-gray-200">
                        <div class="absolute inset-0 bg-good-grade" style="width: 88%;"></div>
                        <span class="absolute right-0 top-0 bottom-0 flex items-center justify-center pr-3 text-white font-bold text-sm">
                            8.8
                        </span>
                    </div>
                </div>
            </div>

            <!-- Titolo Statistiche -->
            <h2 class="text-2xl font-bold mt-8 mb-4 flex items-center">
                statistiche
            </h2>

            <!-- Placeholder per altre statistiche -->
            <div class="h-20 bg-gray-200 rounded-xl mb-12 flex items-center justify-center text-gray-600">
                Altre statistiche qui...
            </div>


        </div>
        </div>

        <script>
            // Plugin personalizzato per la linea verticale di focus
            const verticalLinePlugin = {
                id: 'verticalLine',
                beforeDraw: (chart, args, options) => {
                    // Assicura che i dati del grafico siano pronti
                    const meta = chart.getDatasetMeta(0);
                    if (!meta || meta.data.length === 0) return;

                    if (options.x) {
                        const {ctx, chartArea} = chart;

                        ctx.save();

                        // Linea tratteggiata verticale grigia (come nell'immagine originale)
                        ctx.beginPath();
                        ctx.strokeStyle = '#9ca3af';
                        ctx.lineWidth = 1;
                        ctx.setLineDash([5, 5]);
                        ctx.moveTo(options.x, chartArea.top);
                        ctx.lineTo(options.x, chartArea.bottom);
                        ctx.stroke();

                        // Trova il punto più vicino all'asse X della linea di focus
                        // Usiamo solo il 4° punto dati per simulare il punto centrale quando non si è in hover.
                        const initialPoint = meta.data[3];

                        // Punto sul grafico in corrispondenza della linea
                        ctx.setLineDash([]); // Rimuove il tratteggio
                        ctx.fillStyle = '#1f2937';
                        ctx.beginPath();
                        // Usiamo la y del punto centrale per il cerchio (o la y calcolata se dinamico)
                        ctx.arc(options.x, initialPoint.y, 4, 0, Math.PI * 2);
                        ctx.fill();

                        ctx.restore();
                    }
                }
            };


            document.addEventListener('DOMContentLoaded', () => {
                const ctx = document.getElementById('gradesChart').getContext('2d');

                // Dati simulati per il grafico
                const data = {
                    labels: ['23/3', '24/3', '25/3', '26/3', '27/3', '28/3', '29/3'],
                    datasets: [{
                        label: 'Media Voti',
                        // I valori sono stati scelti per simulare l'andamento del grafico nell'immagine originale
                        data: [7.8, 7.5, 8.2, 7.9, 7.7, 7.9, 7.5],
                        backgroundColor: (context) => {
                            // Crea un gradiente blu scuro/trasparente per simulare l'area del grafico
                            const chart = context.chart;
                            const {ctx, chartArea} = chart;
                            if (!chartArea) return;

                            const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                            // Blu scuro con opacità ridotta in alto
                            gradient.addColorStop(0, 'rgba(55, 65, 81, 0.4)');
                            // Sfondo grigio/trasparente in basso
                            gradient.addColorStop(1, 'rgba(243, 244, 246, 0.8)');
                            return gradient;
                        },
                        borderColor: '#1f2937', // Linea del grafico scura
                        borderWidth: 2,
                        fill: 'start', // Riempi l'area sotto la linea
                        tension: 0.4, // Curva la linea per un aspetto più fluido (come nell'immagine)
                        pointRadius: 0, // Rimuovi i punti sui dati
                        pointHitRadius: 10,
                    }]
                };

                // Configurazione del grafico Chart.js
                const config = {
                    type: 'line',
                    data: data,
                    plugins: [verticalLinePlugin], // AGGIUNTO IL PLUGIN QUI
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false // Nasconde la legenda (che è solo "Media Voti")
                            },
                            tooltip: {
                                // Personalizza il tooltip per mostrare solo il voto
                                callbacks: {
                                    title: (items) => `Data: ${items[0].label}`,
                                    label: (item) => `Voto: ${item.formattedValue}`
                                }
                            },
                            // Inizializzazione delle opzioni del plugin
                            verticalLine: {
                                // Posizione X iniziale, verrà impostata dopo l'inizializzazione del grafico
                                x: null
                            }
                        },
                        scales: {
                            y: {
                                min: 4, // Minimo sul grafico (come nell'immagine)
                                max: 10, // Massimo sul grafico
                                ticks: {
                                    // Mostra solo i tick principali (4, 7, 10)
                                    stepSize: 3,
                                    callback: (value) => (value === 7 || value === 4 || value === 10) ? value : null,
                                    font: {
                                        weight: 'bold'
                                    }
                                },
                                grid: {
                                    color: (context) => {
                                        // Rende le linee della griglia più chiare
                                        if (context.tick.value === 7) return '#9ca3af'; // Linea per la sufficienza
                                        return '#e5e7eb';
                                    },
                                    drawTicks: false
                                },
                                position: 'left',
                                title: {
                                    display: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false, // Nasconde le linee verticali della griglia
                                    drawTicks: false
                                }
                            }
                        },
                        // Aggiunge la linea verticale di focus (simulando l'interazione)
                        onHover: (event, elements, chart) => {
                            if (elements.length) {
                                // Se si è sopra un elemento dati, il punto di focus cambia
                                chart.options.plugins.verticalLine.x = elements[0].element.x;
                                chart.update();
                            } else {
                                // Quando non si è su un elemento, ritorna al punto centrale (indice 3)
                                const initialPoint = chart.getDatasetMeta(0).data[3];
                                chart.options.plugins.verticalLine.x = initialPoint.x;
                                chart.update();
                            }
                        }
                    }
                };

                // Inizializza il grafico
                const gradesChart = new Chart(ctx, config);

                // Imposta la posizione X iniziale della linea verticale di focus (il 4° punto dati)
                // Questo deve avvenire dopo che Chart.js ha disegnato l'elemento per la prima volta.
                const initialPoint = gradesChart.getDatasetMeta(0).data[3];
                gradesChart.options.plugins.verticalLine.x = initialPoint.x;
                gradesChart.update();
            });
        </script>
    </body>
</html>
