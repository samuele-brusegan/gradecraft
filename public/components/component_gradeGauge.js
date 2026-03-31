/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

class GradeGauge extends HTMLElement {

    static observedAttributes = ['value', 'max', 'label'];

    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        this._render();
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (oldValue !== newValue) {
            this._render();
        }
    }

    _render() {
        const value = parseFloat(this.getAttribute('value')) || 0;
        const max = parseFloat(this.getAttribute('max')) || 10;
        const label = this.getAttribute('label') || '';

        // Normalize value between 0 and 1
        const percent = Math.min(Math.max(value / max, 0), 1);

        // Choose color based on value (Italian grading: 6+ is passing)
        let color = '#ef4444'; // red
        if (value >= 7) {
            color = '#22c55e'; // green
        } else if (value >= 6) {
            color = '#f59e0b'; // amber
        }

        const radius = 45;
        const centerX = 60;
        const centerY = 60;
        const circumference = 2 * Math.PI * radius;
        const strokeDasharray = `${circumference * percent} ${circumference}`;

        this.shadowRoot.innerHTML = `
            <style>
                :host {
                    display: inline-block;
                    width: 120px;
                    height: 120px;
                    --text-color: var(--text-primary, #000);
                    --secondary-color: var(--text-secondary, #666);
                }
                .gauge-container {
                    position: relative;
                    width: 120px;
                    height: 120px;
                }
                .gauge-label {
                    position: absolute;
                    bottom: 0;
                    width: 100%;
                    text-align: center;
                    font-size: 11px;
                    color: var(--secondary-color);
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                .gauge-value {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    font-size: 28px;
                    font-weight: 700;
                    color: var(--text-color);
                }
                svg {
                    transform: rotate(-90deg);
                    width: 120px;
                    height: 120px;
                    display: block;
                }
                circle {
                    fill: none;
                    stroke-width: 8;
                    stroke-linecap: round;
                }
                .bg {
                    stroke: var(--border-color, #e5e7eb);
                }
                .progress {
                    stroke: ${color};
                    stroke-dasharray: ${strokeDasharray};
                    transition: stroke-dasharray 0.6s ease-out;
                }
            </style>
            <div class="gauge-container">
                <svg viewBox="0 0 120 120">
                    <circle class="bg" cx="${centerX}" cy="${centerY}" r="${radius}"></circle>
                    <circle class="progress" cx="${centerX}" cy="${centerY}" r="${radius}"></circle>
                </svg>
                <div class="gauge-value">${value.toFixed(1)}</div>
                <div class="gauge-label">${label}</div>
            </div>
        `;
    }
}

customElements.define('grade-gauge', GradeGauge);
