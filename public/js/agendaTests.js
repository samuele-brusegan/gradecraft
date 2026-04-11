/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

// Keywords per auto-rilevamento verifiche (whole-word, case-insensitive)
const TEST_KEYWORDS = ['verifica', 'test', 'compito', 'interrogazione', 'prova', 'quiz', 'valutazione', 'esame'];

/**
 * Estrae la keyword di verifica se presente
 * @return {string|null} La keyword trovata, o null
 */
export function getTestKeyword(text) {
    if (!text) return null;
    const lower = text.toLowerCase();
    for (const kw of TEST_KEYWORDS) {
        const regex = new RegExp(`\\b${escapeRegex(kw)}\\b`, 'i');
        if (regex.test(lower)) {
            return kw;
        }
    }
    return null;
}

/**
 * Evidenzia la keyword nel testo, restituendo HTML con la keyword avvolta in <span class="test-keyword">
 */
export function highlightKeywordInText(text, keyword) {
    if (!text || !keyword) return text;
    const escaped = escapeRegex(keyword);
    const regex = new RegExp(`(${escaped})`, 'gi');
    return text.replace(regex, '<span class="test-keyword">$1</span>');
}

/**
 * Controlla se il testo contiene una keyword di verifica (whole-word match)
 */
export function isLikelyTest(text) {
    return getTestKeyword(text) !== null;
}

function escapeRegex(str) {
    return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

/**
 * Determina se un evento dell'agenda è una verifica,
 * combinando auto-rilevamento e annotazioni utente da IndexedDB.
 * Le annotazioni utente hanno priorità sull'auto-rilevamento.
 */
export async function checkIsTest(event, annotations) {
    const eventId = buildEventId(event);
    const annotation = annotations?.[eventId];

    // Se l'utente ha esplicitamente segnato/tolto il flag
    if (annotation?.isTest !== undefined) {
        return annotation.isTest;
    }

    // Auto-detect
    const textToCheck = [
        event.subjectDesc,
        event.notes,
        event.lessonArg,
        event.lessonType,
        event.title,
        event.evtDescr,
        event.description,
        event.evtNotes,
    ].filter(Boolean).join(' ');

    return isLikelyTest(textToCheck);
}

/**
 * Costruisce un ID univoco per un evento dell'agenda
 */
function buildEventId(event) {
    return `${event.evtDate || ''}-${event.evtCode || ''}-${event.subjectId || ''}-${event.subject || ''}`;
}

/**
 * Evidenzia tutte le occorrenze della keyword nel testo, avvolgendole in <span class="test-keyword">
 */
// export function highlightKeywordInText(text, keyword) {
//     if (!text || !keyword) return text;
//     const escaped = escapeRegex(keyword);
//     const regex = new RegExp(`(${escaped})`, 'gi');
//     return text.replace(regex, '<span class="test-keyword">$1</span>');
// }

/**
 * Applica lo stile "verifica" a una card HTML, evidenziando la keyword se fornita
 */
export function applyTestStyle(card, keyword = null) {
    card.classList.add('test-event');
    const existingBadge = card.querySelector('.test-badge');
    if (!existingBadge) {
        const badge = document.createElement('span');
        badge.className = 'test-badge';
        badge.textContent = 'VERIFICA';
        card.style.position = 'relative';
        card.insertBefore(badge, card.firstChild);
    }
    if (!keyword) return;

    // Highlights all occurrences of the keyword in visible text nodes within the card
    const walker = document.createTreeWalker(card, NodeFilter.SHOW_TEXT, {
        acceptNode: function(node) {
            if (node.parentElement.closest('.test-badge, .test-keyword')) {
                return NodeFilter.FILTER_REJECT;
            }
            return NodeFilter.FILTER_ACCEPT;
        }
    });
    const textNodes = [];
    let node;
    while (node = walker.nextNode()) {
        textNodes.push(node);
    }
    textNodes.forEach(textNode => {
        const text = textNode.textContent;
        if (!text) return;
        const escaped = escapeRegex(keyword);
        const regex = new RegExp(`(${escaped})`, 'gi');
        if (regex.test(text)) {
            const frag = document.createDocumentFragment();
            let lastIndex = 0;
            let match;
            regex.lastIndex = 0;
            while ((match = regex.exec(text)) !== null) {
                const before = text.slice(lastIndex, match.index);
                if (before) frag.appendChild(document.createTextNode(before));
                const span = document.createElement('span');
                span.className = 'test-keyword';
                span.textContent = match[0];
                frag.appendChild(span);
                lastIndex = regex.lastIndex;
            }
            const after = text.slice(lastIndex);
            if (after) frag.appendChild(document.createTextNode(after));
            textNode.parentNode.replaceChild(frag, textNode);
        }
    });
}

/**
 * Rimuove lo stile "verifica" da una card, compresi gli evidenziamenti delle keyword
 */
export function removeTestStyle(card) {
    card.classList.remove('test-event');
    const badge = card.querySelector('.test-badge');
    if (badge) badge.remove();
    // Ripristina i nodi testo rimuovendo gli span .test-keyword
    const keywordSpans = card.querySelectorAll('.test-keyword');
    keywordSpans.forEach(span => {
        const text = document.createTextNode(span.textContent);
        span.parentNode.replaceChild(text, span);
    });
}

/**
 * Toggla manualmente lo stato di verifica di un evento.
 * Salva in IndexedDB e aggiorna la UI.
 */
export async function toggleTestStatus(event, card, dbService) {
    const eventId = buildEventId(event);
    const textToCheck = [
        event.subjectDesc, event.notes, event.lessonArg,
        event.lessonType, event.title, event.evtDescr,
        event.description, event.evtNotes,
    ].filter(Boolean).join(' ');
    const autoDetected = isLikelyTest(textToCheck);
    const annotation = await dbService.getAnnotation(eventId);

    // Calcola lo stato corrente attuale
    let currentIsTest = annotation?.isTest ?? autoDetected;

    // Inverti lo stato
    const newState = !currentIsTest;
    await dbService.saveAnnotation(eventId, { isTest: newState });

    // Aggiorna UI
    if (newState) {
        applyTestStyle(card);
    } else {
        removeTestStyle(card);
    }
}

/**
 * Applica gli stili verifica a tutte le card di una lista di eventi
 */
export async function applyTestStylesToEvents(events, containerSelector, dbService) {
    const annotations = await dbService.getAllAnnotations();
    const cards = document.querySelectorAll(containerSelector);

    cards.forEach((card, idx) => {
        if (events[idx]) {
            checkIsTest(events[idx], annotations).then(test => {
                if (test) applyTestStyle(card);
            });
        }
    });
}

/**
 * Costruisce l'ID per un evento (esportato per uso esterno)
 */
export { buildEventId as getEventId };
