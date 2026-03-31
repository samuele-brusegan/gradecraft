/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

class SubjectCard extends HTMLElement {

    static observedAttributes = ['data-name', 'data-description', 'data-href'];

    constructor() {
        super();

        const shadow = this.attachShadow({ mode: 'open' });

        const template = document.createElement('template');
        template.innerHTML = `
        <style>
            :host {
                display: block;
                cursor: pointer;
            }

            .card {
                background: var(--card-bg, #FFFFFF);
                border-radius: var(--border-radius-md, 24px);
                padding: 1.25rem 1.5rem;
                margin: 0.75rem 1rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                box-shadow: var(--shadow-md, 0 4px 6px -1px rgba(0, 0, 0, 0.1));
                border: 1px solid var(--border-color, #E5E7EB);
                transition: all 0.2s ease-in-out;
            }

            :host(:hover) .card,
            :host([data-active]) .card {
                box-shadow: var(--shadow-lg, 0 10px 15px -3px rgba(0, 0, 0, 0.1));
                transform: translateY(-2px);
                border-color: var(--accent-blue, #3B82F6);
            }

            .info {
                flex: 1;
            }

            .title {
                font-size: 1.25rem;
                font-weight: 700;
                color: var(--text-primary, #374151);
                margin: 0 0 0.25rem 0;
                line-height: 1.4;
            }

            .subtitle {
                font-size: 0.875rem;
                color: var(--text-secondary, #6B7280);
                margin: 0;
                line-height: 1.5;
            }

            .chevron {
                width: 1.5rem;
                height: 1.5rem;
                fill: var(--text-secondary, #6B7280);
                transition: fill 0.2s ease, transform 0.2s ease;
                flex-shrink: 0;
            }

            :host(:hover) .chevron,
            :host([data-active]) .chevron {
                fill: var(--accent-blue, #3B82F6);
                transform: translateX(4px);
            }

            /* Stato attivo per la navigazione corrente */
            :host([data-active]) .card {
                background: var(--background-secondary, #F5F5F5);
                border-color: var(--accent-blue, #3B82F6);
            }
        </style>

        <div class="card">
            <div class="info">
                <h6 class="title">Materia</h6>
                <p class="subtitle">Descrizione breve</p>
            </div>
            <svg class="chevron" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
            </svg>
        </div>
        `;

        shadow.appendChild(template.content.cloneNode(true));
    }

    connectedCallback() {
        this._updateContent();
        this._setupClickHandler();
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (oldValue !== newValue) {
            this._updateContent();
        }
    }

    _updateContent() {
        const titleEl = this.shadowRoot.querySelector('.title');
        const subtitleEl = this.shadowRoot.querySelector('.subtitle');

        if (titleEl) {
            titleEl.textContent = this.getAttribute('data-name') || 'Materia';
        }
        if (subtitleEl) {
            subtitleEl.textContent = this.getAttribute('data-description') || '';
        }
    }

    _setupClickHandler() {
        this.addEventListener('click', () => {
            const href = this.getAttribute('data-href');
            if (href) {
                window.location.href = href;
            }
            this.dispatchEvent(new CustomEvent('card-click', {
                bubbles: true,
                composed: true,
                detail: { href: href }
            }));
        });
    }
}

customElements.define('subject-card', SubjectCard);
