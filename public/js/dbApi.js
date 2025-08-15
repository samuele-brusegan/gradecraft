/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

export class IndexedDBService {
    constructor(dbName, version, objectStoresConfig) {
        this.dbName = dbName;
        this.version = version;
        this.db = null; // Il riferimento al database IndexedDB
        this.objectStoresConfig = objectStoresConfig; // Configurazione degli object store
    }

    /**
     * Initializes the database and creates an object store if it does not already exist.
     *
     * @param {string} table - The name of the table (object store) to be created or accessed in the database.
     * @return {Promise<IDBDatabase>} A Promise that resolves to the opened IndexedDB database instance.
     */
    async init(table) {
        this.db = new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.version);

            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                if (!db.objectStoreNames.contains(table)) {
                    db.createObjectStore(table, this.objectStoresConfig[table]);
                }
            }

            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        })
        return this.db;
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
        })
    }
}