/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

export class IndexedDBService {
    constructor(dbName, version, objectStoresConfig) {
        this.dbName = dbName;
        this.version = version;
        this.objectStoresConfig = objectStoresConfig;
        this._dbPromise = null;
    }

    /**
     * Initializes the database and creates all object stores.
     * Auto-increments version if new stores are needed.
     */
    async init(specificTable = null) {
        if (this._dbPromise) return this._dbPromise;

        const stores = specificTable ? [specificTable] : Object.keys(this.objectStoresConfig);

        this._dbPromise = (async () => {
            // Prima apri per scoprire la versione corrente
            let currentVersion = this.version;
            let storeNames = [];
            await new Promise((resolve) => {
                const req = indexedDB.open(this.dbName);
                req.onupgradeneeded = () => {
                    // DB nuovo, sarà creato con this.version nell'open successivo
                };
                req.onsuccess = () => {
                    currentVersion = req.result.version;
                    storeNames = Array.from(req.result.objectStoreNames);
                    req.result.close();
                };
                req.onerror = () => resolve();
            });

            const missingStores = stores.filter(s => !storeNames.includes(s));
            const version = missingStores.length > 0 ? Math.max(currentVersion, this.version) + 1 : Math.max(currentVersion, this.version);

            return new Promise((resolve, reject) => {
                const request = indexedDB.open(this.dbName, version);
                request.onupgradeneeded = (event) => {
                    const db = event.target.result;
                    const allStores = Object.keys(this.objectStoresConfig);
                    for (const table of allStores) {
                        if (!db.objectStoreNames.contains(table)) {
                            db.createObjectStore(table, this.objectStoresConfig[table]);
                        }
                    }
                };
                request.onsuccess = () => resolve(request.result);
                request.onerror = () => reject(request.error);
            });
        })();

        return this._dbPromise;
    }

    /**
     * Retrieves all records from the specified table.
     *
     * @param {string} table - The name of the table to retrieve records from.
     * @return {Promise<any[]>} A promise that resolves to an array of all records in the specified table.
     */
    async getAll(table) {
        const db = await this.init(table);
        return new Promise((resolve, reject) => {
            const transaction = db.transaction(table, 'readonly');
            const objectStore = transaction.objectStore(table);
            const request = objectStore.getAll();

            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    // === Agenda Annotations ===

    async getAnnotation(eventId) {
        const db = await this.init('agendaAnnotations');
        return new Promise((resolve, reject) => {
            const tx = db.transaction('agendaAnnotations', 'readonly');
            const req = tx.objectStore('agendaAnnotations').get(eventId);
            req.onsuccess = () => resolve(req.result || null);
            req.onerror = () => reject(req.error);
        });
    }

    async saveAnnotation(eventId, data) {
        const db = await this.init('agendaAnnotations');
        return new Promise((resolve, reject) => {
            const tx = db.transaction('agendaAnnotations', 'readwrite');
            tx.objectStore('agendaAnnotations').put({ id: eventId, ...data }, eventId);
            tx.oncomplete = () => resolve();
            tx.onerror = () => reject(tx.error);
        });
    }

    async removeAnnotation(eventId) {
        const db = await this.init('agendaAnnotations');
        return new Promise((resolve, reject) => {
            const tx = db.transaction('agendaAnnotations', 'readwrite');
            tx.objectStore('agendaAnnotations').delete(eventId);
            tx.oncomplete = () => resolve();
            tx.onerror = () => reject(tx.error);
        });
    }

    async getAllAnnotations() {
        const all = await this.getAll('agendaAnnotations');
        const result = {};
        for (const item of all) {
            const { id, ...rest } = item;
            result[id] = rest;
        }
        return result;
    }

    async exportAnnotations() {
        const annotations = await this.getAllAnnotations();
        return JSON.stringify(annotations, null, 2);
    }

    async importAnnotations(jsonString) {
        const data = JSON.parse(jsonString);
        for (const [id, item] of Object.entries(data)) {
            await this.saveAnnotation(id, item);
        }
    }
}
