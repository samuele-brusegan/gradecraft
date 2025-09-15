/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

class Grade extends HTMLElement {

    static watchedList = [
        {
            attribute: "data-grade",
            htmlElement: "#displayValue",
        },
        {
            attribute: "data-date",
            htmlElement: "#eventDate",
        },
        {
            attribute: "data-period",
            htmlElement: "#period",
        },
        {
            attribute: "data-subject",
            htmlElement: "#subjectName",
        },
        {
            attribute: "data-teacher",
            htmlElement: "#teacherName",
        },
        {
            attribute: "data-notes",
            htmlElement: "#notesForFamily",
        }
    ];
    watchedList;

    constructor() {
        super();

        // 0. Richiedo le variabili globali da sessionStorage
        let URL_PATH = sessionStorage.getItem("url");
        let THEME = sessionStorage.getItem("theme");

        // 1. Crea lo Shadow DOM
        const shadow = this.attachShadow({mode: 'open'});

        // 2. Definisci la struttura interna (HTML) e lo stile (CSS)
        const template = document.createElement('template');
        template.innerHTML = `
        <style>
            .todo {
                border: 1px solid #ccc;
                padding: 10px;
                margin-bottom: 10px;
                border-radius: 5px;
                background-color: #eee;
                position: relative;
                display: flex;
                justify-content: space-between;
            }
        </style>
       
        <!--<div class="grade mb-2 hidden" data-grade="<?=$grade['decimalValue']?>" data-date="<?=$grade['evtDate']?>" data-period="<?=$grade['periodPos']?>">-->
        <p id="subjectName"></p>
        <div class="value" style="background: /*<?=$color?>*/;">
            <div id="displayValue"></div>
        </div>
        <!--<?=($grade['notesForFamily']!="")?"<hr>".$grade['notesForFamily'].'<br>':''?>-->
        <p id="notesForFamily"></p>
        <hr>
        <p id="eventDate"></p>
        <p id="teacherName"></p>
        <!--</div>-->
        `;

        // 3. Clona il contenuto del template e aggiungilo allo Shadow DOM
        shadow.appendChild(template.content.cloneNode(true));
    }

    // Passaggio 2: Gestisci gli attributi e i callback
    static get observedAttributes() {
        let array = [];

        this.watchedList.forEach(element => {
            array.push(element.attribute);
        })

        // return ['data-grade', 'data-date', 'data-period', 'data-subject', 'data-teacher', 'data-notes'];
        return array;
    }

    attributeChangedCallback(name, oldValue, newValue) {

        this.watchedList.forEach(element => {
            if (element.attribute === name) {
                let htmlElement = this.shadowRoot.querySelector(element.htmlElement);
                if (htmlElement) {
                    htmlElement.textContent = newValue;
                }
            }
        })
    }

    connectedCallback() {}
}

// Passaggio 3: Registra il tuo elemento personalizzato
customElements.define('Grade', Grade);
