/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

class Subject extends HTMLElement {
    static observedAttributes = ["name", "id", "professor"];

    constructor() {
        super();
    }
}
customElements.define("Subject", Subject);