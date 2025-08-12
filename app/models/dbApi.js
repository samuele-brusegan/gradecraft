/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

class IndexedDBService {
    constructor(dbName, version, objectStoresConfig) {
        this.dbName = dbName;
        this.version = version;
        this.db = null; // Il riferimento al database IndexedDB
        this.objectStoresConfig = objectStoresConfig; // Configurazione degli object store
    }

    /**
     * Apre il database IndexedDB. Se il database non esiste o la versione è maggiore,
     * viene chiamato onupgradeneeded per creare o aggiornare gli object store.
     * @returns {Promise<IDBDatabase>} Una Promise che si risolve con l'istanza del database.
     */
    open() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.version);

            request.onupgradeneeded = (event) => {
                this.db = event.target.result;
                console.log(`Aggiornamento del database alla versione ${this.version}`);

                // Itera sulla configurazione degli object store e creali se non esistono
                for (const storeConfig of this.objectStoresConfig) {
                    if (!this.db.objectStoreNames.contains(storeConfig.name)) {
                        const objectStore = this.db.createObjectStore(storeConfig.name, storeConfig.options);
                        // Crea indici se specificati nella configurazione
                        if (storeConfig.indexes) {
                            for (const indexConfig of storeConfig.indexes) {
                                objectStore.createIndex(indexConfig.name, indexConfig.keyPath, indexConfig.options);
                            }
                        }
                        console.log(`Object Store '${storeConfig.name}' creato.`);
                    }
                }
            };

            request.onsuccess = (event) => {
                this.db = event.target.result;
                console.log(`Database '${this.dbName}' aperto con successo.`);
                resolve(this.db);
            };

            request.onerror = (event) => {
                console.error("Errore nell'apertura del database:", event.target.error);
                reject(event.target.error);
            };

            request.onblocked = (event) => {
                console.warn("L'apertura del database è stata bloccata. Chiudi altre schede o applicazioni che utilizzano questo database.");
            };
        });
    }

    /**
     * Esegue una transazione sul database.
     * @param {string} storeName Il nome dell'object store.
     * @param {string} mode La modalità della transazione ('readonly' o 'readwrite').
     * @returns {Promise<IDBObjectStore>} Una Promise che si risolve con l'object store.
     */
    _getTransactionStore(storeName, mode) {
        return new Promise((resolve, reject) => {
            if (!this.db) {
                // Se il database non è ancora aperto, prova ad aprirlo
                this.open()
                    .then(() => {
                        const transaction = this.db.transaction(storeName, mode);
                        const objectStore = transaction.objectStore(storeName);
                        transaction.oncomplete = () => resolve(objectStore);
                        transaction.onerror = (event) => reject(event.target.error);
                        transaction.onabort = (event) => reject(new Error('Transaction aborted'));
                    })
                    .catch(reject);
            } else {
                const transaction = this.db.transaction(storeName, mode);
                const objectStore = transaction.objectStore(storeName);
                transaction.oncomplete = () => resolve(objectStore);
                transaction.onerror = (event) => reject(event.target.error);
                transaction.onabort = (event) => reject(new Error('Transaction aborted'));
            }
        });
    }

    /**
     * Aggiunge un nuovo oggetto all'object store specificato.
     * @param {string} storeName Il nome dell'object store.
     * @param {object} data L'oggetto da aggiungere.
     * @returns {Promise<any>} Una Promise che si risolve con la chiave dell'oggetto aggiunto.
     */
    add(storeName, data) {
        return new Promise((resolve, reject) => {
            this._getTransactionStore(storeName, 'readwrite')
                .then(objectStore => {
                    const request = objectStore.add(data);
                    request.onsuccess = (event) => resolve(event.target.result);
                    request.onerror = (event) => reject(event.target.error);
                })
                .catch(reject);
        });
    }

    /**
     * Recupera un oggetto dall'object store specificato tramite la sua chiave.
     * @param {string} storeName Il nome dell'object store.
     * @param {any} key La chiave dell'oggetto da recuperare.
     * @returns {Promise<object|undefined>} Una Promise che si risolve con l'oggetto o undefined se non trovato.
     */
    get(storeName, key) {
        return new Promise((resolve, reject) => {
            this._getTransactionStore(storeName, 'readonly')
                .then(objectStore => {
                    const request = objectStore.get(key);
                    request.onsuccess = (event) => resolve(event.target.result);
                    request.onerror = (event) => reject(event.target.error);
                })
                .catch(reject);
        });
    }

    /**
     * Aggiorna un oggetto esistente nell'object store specificato.
     * @param {string} storeName Il nome dell'object store.
     * @param {object} data L'oggetto da aggiornare. Deve contenere la chiave primaria.
     * @returns {Promise<any>} Una Promise che si risolve con la chiave dell'oggetto aggiornato.
     */
    update(storeName, data) {
        return new Promise((resolve, reject) => {
            this._getTransactionStore(storeName, 'readwrite')
                .then(objectStore => {
                    const request = objectStore.put(data);
                    request.onsuccess = (event) => resolve(event.target.result);
                    request.onerror = (event) => reject(event.target.error);
                })
                .catch(reject);
        });
    }

    /**
     * Elimina un oggetto dall'object store specificato tramite la sua chiave.
     * @param {string} storeName Il nome dell'object store.
     * @param {any} key La chiave dell'oggetto da eliminare.
     * @returns {Promise<void>} Una Promise che si risolve quando l'eliminazione è completata.
     */
    delete(storeName, key) {
        return new Promise((resolve, reject) => {
            this._getTransactionStore(storeName, 'readwrite')
                .then(objectStore => {
                    const request = objectStore.delete(key);
                    request.onsuccess = () => resolve();
                    request.onerror = (event) => reject(event.target.error);
                })
                .catch(reject);
        });
    }

    /**
     * Recupera tutti gli oggetti da un object store.
     * @param {string} storeName Il nome dell'object store.
     * @returns {Promise<object[]>} Una Promise che si risolve con un array di tutti gli oggetti.
     */
    getAll(storeName) {
        return new Promise((resolve, reject) => {
            this._getTransactionStore(storeName, 'readonly')
                .then(objectStore => {
                    const request = objectStore.getAll();
                    request.onsuccess = (event) => resolve(event.target.result);
                    request.onerror = (event) => reject(event.target.error);
                })
                .catch(reject);
        });
    }

    /**
     * Svuota completamente un object store.
     * @param {string} storeName Il nome dell'object store.
     * @returns {Promise<void>} Una Promise che si risolve quando l'operazione è completata.
     */
    clearStore(storeName) {
        return new Promise((resolve, reject) => {
            this._getTransactionStore(storeName, 'readwrite')
                .then(objectStore => {
                    const request = objectStore.clear();
                    request.onsuccess = () => resolve();
                    request.onerror = (event) => reject(event.target.error);
                })
                .catch(reject);
        });
    }
}