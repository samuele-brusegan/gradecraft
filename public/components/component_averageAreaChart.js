/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

class AverageAreaChart extends HTMLElement {

    static observedAttributes = ['data-grades'];

    constructor() {
        super();

        this.attachShadow({ mode: 'open' });

        const template = document.createElement('template');
        template.innerHTML = `
        <style>
            :host {
                display: block;
                width: 100%;
                height: 100%;
                min-height: 250px;
            }

            canvas {
                width: 100% !important;
                height: 100% !important;
            }
        </style>
        <canvas></canvas>
        `;

        this.shadowRoot.appendChild(template.content.cloneNode(true));
    }

    connectedCallback() {
        this._renderChart();
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (name === 'data-grades' && oldValue !== newValue) {
            this._renderChart();
        }
    }

    _renderChart() {
        const gradesJSON = this.getAttribute('data-grades');
        if (!gradesJSON) return;

        let grades;
        try {
            grades = JSON.parse(gradesJSON);
        } catch (e) {
            console.error('Invalid grades JSON', e);
            return;
        }

        // Filtra solo i voti con decimalValue numerico (esclude stringhe con +, -, ecc.)
        grades = grades.filter(g => {
            if (!g || typeof g.decimalValue === 'undefined' || g.decimalValue === null) return false;
            const val = parseFloat(g.decimalValue);
            return !isNaN(val) && isFinite(val);
        });

        if (grades.length === 0) {
            // Nessun voto valido, mostrare messaggio? il container già gestisce tramite if nel PHP
            return;
        }

        // Ordina per data
        grades.sort((a, b) => new Date(a.evtDate) - new Date(b.evtDate));

        const labels = grades.map(g => {
            const date = new Date(g.evtDate);
            return `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth()+1).toString().padStart(2, '0')}`;
        });
        const values = grades.map(g => parseFloat(g.decimalValue));

        const canvas = this.shadowRoot.querySelector('canvas');
        const ctx = canvas.getContext('2d');

        // Distruzione del chart precedente se esiste
        if (this._chart) {
            this._chart.destroy();
        }

        // Creazione gradiente
        const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height || 400);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');   // Blu chiaro
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');   // Trasparente

        this._chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Media voti',
                    data: values,
                    fill: true,
                    backgroundColor: gradient,
                    borderColor: '#2563EB',
                    borderWidth: 2,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#2563EB',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return `Voto: ${context.parsed.y.toFixed(1)}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxTicksLimit: 6,
                            font: { size: 10 }
                        }
                    },
                    y: {
                        min: 1,
                        max: 10,
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            stepSize: 1,
                            font: { size: 10 }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'nearest'
                }
            },
            plugins: [{
                id: 'horizontalAverageLine',
                afterDraw: (chart) => {
                    const { ctx, chartArea: { left, right, top, bottom }, scales: { y } } = chart;
                    const data = chart.data.datasets[0].data;
                    if (!data || data.length === 0) return;
                    const sum = data.reduce((a, b) => a + b, 0);
                    const avg = sum / data.length;
                    const avgPx = y.getPixelForValue(avg);
                    ctx.save();
                    ctx.beginPath();
                    ctx.moveTo(left, avgPx);
                    ctx.lineTo(right, avgPx);
                    ctx.lineWidth = 2;
                    ctx.strokeStyle = 'rgba(239, 68, 68, 0.6)'; // red line for average
                    ctx.setLineDash([5, 5]);
                    ctx.stroke();
                    ctx.restore();
                }
            }]
        });
    }
}

customElements.define('average-area-chart', AverageAreaChart);
