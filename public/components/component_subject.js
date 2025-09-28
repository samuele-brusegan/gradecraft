/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato 
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

class Subject extends HTMLElement {

    static watchedList = [
        {
            attribute: "data-name",
            htmlElement: ".subj>div>h6",
            innerAttribute: "",
        },
        {

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
            .subj {
                background: #D3D3D3; 
                border-radius: 1rem; 
                padding: .3rem 1rem; 
                margin: .7rem; 
                display: flex; 
                justify-content: space-between;
            }
        </style>
        <div class="subj">
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
        `;

        // 3. Clona il contenuto del template e aggiungilo allo Shadow DOM
        shadow.appendChild(template.content.cloneNode(true));
    }

    // Passaggio 2: Gestisci gli attributi e i callback
    static get observedAttributes() {
        let array = [];

        Subject.watchedList.forEach(element => {
            array.push(element.attribute);
        })

        return array;
    }

    attributeChangedCallback(name, oldValue, newValue) {

        Subject.watchedList.forEach(element => {
            if (element.attribute === name) {
                let htmlElement = this.shadowRoot.querySelector(element.htmlElement);
                console.log(htmlElement)
                if (htmlElement) {
                    if (element.innerAttribute !== "") {
                        let tag = element.innerAttribute;
                        htmlElement[tag] = newValue;
                    } else {
                        htmlElement.textContent = newValue;
                    }
                }
            }
        })
    }

    connectedCallback() {
    }
}

// Passaggio 3: Registra il tuo elemento personalizzato
customElements.define('subject', Subject);