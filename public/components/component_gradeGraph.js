/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

class gradeGraph extends HTMLElement {

    static watchedList = [
        {
            attribute: "data-decimal",
            htmlElement: "#circle_1",
            innerAttribute: "data-decimal",
        },
        {
            attribute: "data-period-name",
            htmlElement: "#periodName",
            innerAttribute: "",
        }
    ];
    watchedList;

    constructor() {
        super();

        /*
        // 0. Richiedo le variabili globali da sessionStorage
        let URL_PATH = sessionStorage.getItem("url");
        let THEME = sessionStorage.getItem("theme");
        */

        // 1. Crea lo Shadow DOM
        const shadow = this.attachShadow({mode: 'open'});

        // 2. Definisci la struttura interna (HTML) e lo stile (CSS)
        const template = document.createElement('template');
        template.innerHTML = `

        <style>
            :host {
                display: block;
            }
            
            #gradeValue {
                position: absolute; 
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                display: flex;
                align-items: center;
                justify-content: center;

                /* Stili di testo */
                font-size: 1.5rem;
                line-height: 2.25rem;
                font-weight: 700; 
                color: #1f2937;
                text-align: center;
            }
            
            #periodName {
                font-size: 0.75rem;
                line-height: 1rem;
                color: #4b5563;
                text-align: center;
                margin: 0;
            }

            #circleContainer {
                position: relative;
                width: 4rem;
                height: 4rem;
            }
            
            .arc-rotation {
                transform: rotate(135deg); 
            }
        </style>

        <div class="flex flex-col items-center">
            <div id="circleContainer">
                <svg class="w-full h-full arc-rotation" viewBox="0 0 100 100">
                    <circle id="bg_circle" cx="50" cy="50" r="45" fill="none" stroke="#ACACAC" stroke-width="10" stroke-linecap="round" stroke-dasharray="212.05 282.74"></circle>
                    <circle id="fg_circle" cx="50" cy="50" r="45" fill="none" stroke="#5DD700" stroke-width="10" stroke-linecap="round"></circle>
                </svg>
                <!-- ELEMENTO VOTO: RIPOSIZIONATO ALL'INTERNO PER IL CENTRAMENTO -->
                <span id="gradeValue">---</span>
            </div>
            
            <!-- ELEMENTO PERIODO (SOTTO): Non è stato toccato -->
            <p id="periodName">---</p>
        </div>

<!--        <div id="circle_1" class="flex flex-col items-center" data-decimal>
            <div class="circleContainer">
                <svg class="w-full h-full arc-rotation" viewBox="0 0 100 100">
                    &lt;!&ndash; TODO: Aggiorna i colori con variabili CSS &ndash;&gt;
                    <circle id="bg_circle" cx="50" cy="50" r="45" fill="none" stroke="#AEAEAE" stroke-width="10" stroke-linecap="round" stroke-dasharray="212.05 282.74"></circle>
                    <circle id="fg_circle" cx="50" cy="50" r="45" fill="none" stroke="#22C55E" stroke-width="10" stroke-linecap="round" data-progress=""></circle>
                </svg>
                <span id="gradeValue"></span>
            </div>
        </div>
        <p id="periodName"></p>-->
        `;

        // 3. Clona il contenuto del template e aggiungilo allo Shadow DOM
        shadow.appendChild(template.content.cloneNode(true));
    }

    // Passaggio 2: Gestisci gli attributi e i callback
    static get observedAttributes() {
        let array = [];

        gradeGraph.watchedList.forEach(element => {
            array.push(element.attribute);
        })
        return array;
    }

    // Funzione per animare il cerchio di progresso SVG
    animateProgressCircle(fgCircle, progressValue, color) {
        if (!fgCircle || isNaN(progressValue)) return; // Uscita di sicurezza

        // Calcoli geometrici costanti per un arco di 270 gradi (R=45)
        const radius = 45;
        const fullCircumference = 2 * Math.PI * radius; // 282.74
        const arcLength = (270 / 360) * fullCircumference; // 212.05

        // progressValue è un valore tra 0 e 100 (dove 7.4 -> 74).
        // Calcola l'offset basandosi sulla percentuale rispetto all'arco visibile.
        const offset = arcLength - (progressValue / 100) * arcLength;

        // Aggiorna il colore se fornito
        if (color) {
            fgCircle.setAttribute('stroke', color);
        }

        // 1. Disabilita la transizione e imposta l'arco iniziale (arco completamente vuoto)
        fgCircle.style.transition = 'none';
        fgCircle.style.strokeDasharray = `${arcLength} ${fullCircumference}`;
        fgCircle.style.strokeDashoffset = arcLength;

        // 2. Forza un reflow (cruciale per innescare l'animazione)
        void fgCircle.offsetWidth;

        // 3. Applica la transizione e l'offset finale per l'animazione
        fgCircle.style.transition = 'stroke-dashoffset 1s ease-out';
        fgCircle.style.strokeDashoffset = offset;
    }

    attributeChangedCallback(name, oldValue, newValue) {
        const gradeValueEl = this.shadowRoot.querySelector('#gradeValue');
        const periodNameEl = this.shadowRoot.querySelector('#periodName');
        const fgCircle     = this.shadowRoot.querySelector('#fg_circle');

        // Variabili comuni
        const grade        = parseFloat(this.getAttribute('data-decimal'));
        const progressValue= grade * 10;
        const currentColor  = this.getAttribute('data-color');


        switch (name) {
            case "data-decimal":
                if (gradeValueEl && fgCircle) {
                    // 1. Aggiorna il valore numerico visualizzato
                    gradeValueEl.textContent = grade.toFixed(1);

                    // 2. Avvia l'animazione
                    this.animateProgressCircle(fgCircle, progressValue, currentColor);
                }
                break;

            case "data-period-name":
                if (periodNameEl) {
                    periodNameEl.textContent = newValue;
                }
                break;

            case "data-color":
                if (fgCircle) {
                    // Rilancia l'animazione per applicare il nuovo colore
                    this.animateProgressCircle(fgCircle, progressValue, newValue);
                }
                break;
        }
    }

    connectedCallback() {
        // Assicurati che l'animazione parta all'inserimento nel DOM
        const grade = parseFloat(this.getAttribute('data-decimal'));
        const progressValue = grade * 10;
        const color = this.getAttribute('data-color');
        const fgCircle = this.shadowRoot.querySelector('#fg_circle');

        this.animateProgressCircle(fgCircle, progressValue, color);
    }
}

// Passaggio 3: Registra il tuo elemento personalizzato
customElements.define('grade-graph', gradeGraph);