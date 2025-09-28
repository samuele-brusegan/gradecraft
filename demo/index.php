<!--
  ~ Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
  ~ Questo file fa parte di GradeCraft ed è rilasciato
  ~ sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
  -->

<?php
    $loggedIn = false;
    $username = '';
    $password = '';
    if (isset($_SESSION['classeviva_auth_token']) && (isset($_SESSION['classeviva_ident']) || isset($_SESSION['classeviva_username'])) ) {
        if($_SESSION['classeviva_ident'] == $this -> usr -> uid || $_SESSION['classeviva_username'] == $this -> usr -> uid) {
            $loggedIn = true;
            $username = $_SESSION['classeviva_username'];
            $password = $_SESSION['classeviva_password'];
        }
    }
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GradeCraft - Integrazione Classeviva</title>
        <link  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.min.js" integrity="sha384-RuyvpeZCxMJCqVUGFI0Do1mQrods/hhxYlcVfGPOfQtPJh0JCw12tUAZ/Mv10S7D" crossorigin="anonymous"></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<!--        <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">-->
        <style>
            body {
                font-family: 'Inter', sans-serif;
                background-color: #1a1a1a;

                color: #e5e5e5;
            }

            .input-field {
                padding: 10px 15px;
                border: 1px solid #404040;
                border-radius: 8px;
                font-size: 1rem;
                width: 100%;
                box-sizing: border-box;
                background-color: #262626;
                color: #e5e5e5;
            }

            .btn {
                padding: 10px 20px;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                transition: background-color 0.2s ease-in-out;
            }

            .btn-primary {
                background-color: #4f46e5;
                color: white;
            }

            .btn-primary:hover {
                background-color: #4338ca;
            }

            .btn-danger {
                background-color: #dc2626;
                color: white;
                margin-left: 1rem;
            }

            .btn-danger:hover {
                background-color: #b91c1c;
            }

            .data-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
                border: 2px solid #404040 !important;
            }

            .data-table th, .data-table td {
                border: 1px solid #404040;
                padding: 10px;
                text-align: left;
            }

            .data-table th {
                background-color: #262626;
                font-weight: 600;
                color: #e5e5e5;
            }

            .message-box {
                background-color: #065f46;
                color: #d1fae5;
                padding: 12px;
                border-radius: 8px;
                margin-bottom: 20px;
                text-align: center;
                font-weight: 600;
            }

            .error-message {
                background-color: #991b1b;
                color: #fee2e2;
            }

            .loading-spinner {
                border: 4px solid rgba(255, 255, 255, 0.1);
                border-left-color: #4f46e5;
                border-radius: 50%;
                width: 24px;
                height: 24px;
                animation: spin 1s linear infinite;
                vertical-align: middle;
                margin-left: 10px;
                display: none;
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }
                100% {
                    transform: rotate(360deg);
                }
            }
        </style>
    </head>
    <body class="flex items-center justify-center min-h-screen">
        
        <div class="/*container*/ w-100 p-8">
            <h1 class="text-3xl font-bold text-center mb-6 text-gray-100">GradeCraft - Integrazione Classeviva</h1>

            <div id="message-area" class="message-box error-message hidden">

            </div>

            <!-- Login Area -->
            <div class="mb-6" id="login-section">
                <h2 class="text-xl font-semibold mb-4 text-gray-200">Accedi a Classeviva</h2>
                <div class="mb-4">
                    <label for="username" class="block text-gray-200 text-sm font-medium mb-2">Username Classeviva:</label>
                    <input type="text" id="username" class="input-field" placeholder="Inserisci il tuo username">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-200 text-sm font-medium mb-2">Password Classeviva:</label>
                    <input type="password" id="password" class="input-field" placeholder="Inserisci la tua password">
                </div>
                <button id="loginBtn" class="btn btn-primary w-full flex items-center justify-center">
                    Accedi e Carica Dati
                    <span id="loadingSpinner" class="loading-spinner"></span>
                </button>
            </div>
            <button id="logoutBtn" class="btn btn-danger hidden">Logout</button>

            <hr class="my-8 border-gray-200">

            <!-- Dropdown per chiamare una funzione di fetch specifico -->
            <div class="mb-4 hidden" id="select-method">
                <label for="fetch-method" class="block text-gray-300 text-sm font-medium mb-2">Seleziona i dati da visualizzare:</label>
                <div class="flex gap-2">
                    <select id="fetch-method" class="input-field">
                        <?php
                        include_once 'api/apiMethods.php';
                        global $methods;
                        foreach ($methods as $method) {
                            if ($method['name'] == 'login') continue;
                            echo "<option value='{$method['name']}'>{$method['name']}</option>";
                        }
                        ?>
                    </select>
                    <button id="fetchBtn" class="btn btn-primary">
                        Carica Dati
                        <span class="loading-spinner"></span>
                    </button>
                </div>
            </div>
            <div id="data-section" class="hidden">
                <h2 class="text-xl font-semibold mb-4 text-gray-200">Dati Recuperati da Classeviva</h2>

                <div class="mb-6">
                    <h3 class="text-lg font-medium mb-3 text-gray-200">Dati</h3>
                    <div id="cvv-data-output">
                        <table class="data-table">
                            <thead>
                            <tr class="cvv-data-table-head"></tr>
                            </thead>
                            <tbody class="cvv-data-table-body"></tbody>
                        </table>
                        <div class="cvv-data-buttons"></div>
                        <p id="no-data-message" class="text-gray-400 mt-4 hidden">Nessun dato richiesto trovato.</p>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function atomDateParser(atomDate) {
                let yy   = atomDate.substring(0,4);
                let mo   = atomDate.substring(5,7);
                let dd   = atomDate.substring(8,10);
                let hh   = atomDate.substring(11,13);
                let mi   = atomDate.substring(14,16);
                let ss   = atomDate.substring(17,19);
                //Time zone
                let tzs  = atomDate.substring(19,20);
                let tzh  = atomDate.substring(20,22);
                let tzm  = atomDate.substring(23,25);
                let myUTC = Date.UTC(yy-0,mo-1,dd-0,hh-0,mi-0,ss-0);
                let tzOff = (tzs+(tzh * 60 + tzm * 1)) * 60000;
                return new Date(myUTC-tzOff);
            }

            document.addEventListener('DOMContentLoaded', () => {
                const usernameInput     = document.getElementById('username');
                const passwordInput     = document.getElementById('password');
                const loginBtn          = document.getElementById('loginBtn');
                const dataSection       = document.getElementById('data-section');
                const noDataMessage     = document.getElementById('no-data-message');
                const messageArea       = document.getElementById('message-area');
                const loadingSpinner    = document.getElementById('loadingSpinner');

                const backendBaseUrl = 'api/api.php?path='; // Adatta questo al tuo setup PHP

                //Se in sessione sono già registrato e la sessione di classeviva non è espirata
                tryToRetriveUsr();

                function tryToRetriveUsr() {
                    if (sessionStorage.getItem('lastLoginResponse') && sessionStorage.getItem('lastLoginResponse') !== 'undefined' ) {
                        let llrNotParsed = sessionStorage.getItem('lastLoginResponse');
                        let llr = JSON.parse(JSON.parse(llrNotParsed));
                        // console.log(llr)
                        let expires = atomDateParser(llr['expire']);
                        let release = atomDateParser(llr['release']);
                        
                        if (expires > new Date()) {
                            console.log("retrive")
                            //Allora richiedo e mostro i dati
                            fetchClassevivaFastLogin(llr);
                            document.getElementById('login-section').classList.add('hidden');
                            //fetchClassevivaAll();
                        } else {
                            //Check if more than 60 seconds passed since last request
                            let timeDiff = (new Date() - release) / 1000;
                            if (timeDiff > 60) {
                                //Request new session
                                const encryptedPassword = sessionStorage.getItem('classeviva_password');
                                const passwordDecrypted = atob(encryptedPassword);
                                console.log("Password: " + passwordDecrypted)
                                fetchClassevivaLogin(llr.ident, llr.ident, passwordDecrypted);
                            } else {
                                console.log("Session expired but need to wait before requesting new one");
                            }
                        }
                        document.getElementById('login-section').classList.add('hidden');
                        document.getElementById('select-method').classList.remove('hidden');
                        document.getElementById('logoutBtn').classList.remove('hidden');
                    }
                }

                //=== UI Helpers ===
                // Funzione per mostrare messaggi
                function showMessage(message, type = 'success') {
                    messageArea.innerHTML += message + "<br/>";
                    messageArea.className = 'message-box'; // Reset class
                    if (type === 'error') {
                        messageArea.classList.add('error-message');
                    } else {
                        messageArea.classList.add('message-box'); // Default for success/info
                    }
                    messageArea.classList.remove('hidden');
                }
                // Funzione per nascondere messaggi
                function hideMessage() {
                    messageArea.classList.add('hidden');
                    messageArea.textContent = '';
                }
                // Funzione per mostrare o nascondere lo spinner di caricamento
                function toggleLoading(isLoading) {
                    if (isLoading) {
                        loadingSpinner.style.display = 'inline-block';
                        loginBtn.disabled = true;
                    } else {
                        loadingSpinner.style.display = 'none';
                        loginBtn.disabled = false;
                    }
                }

                //=== Login ===
                async function fetchClassevivaFastLogin(loginResponseIn) {

                    console.log("Retrive user from session: " + loginResponseIn.ident)

                    if (!loginResponseIn) {
                        showMessage('Sessione corrotta', 'error');
                        return;
                    }

                    hideMessage();
                    toggleLoading(true);
                    dataSection.classList.add('hidden'); // Nasconde la sezione dati mentre si carica

                    try {
                        const loginResponse = await fetch(`${backendBaseUrl}fastLogin`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', },
                            body: JSON.stringify( loginResponseIn ),
                        });

                        let responseText = await loginResponse.text();
                        console.log(responseText);

                    } catch (error) {
                        console.error('Errore:', error);
                        showMessage(`Errore: ${error.message}. `, 'error');
                    } finally {
                        toggleLoading(false);
                    }
                }
                async function fetchClassevivaLogin(ident = null, username = null, password = null) {
                    if(username === null) username = usernameInput.value;
                    if (ident !== null) username = ident;
                    if(password === null) password = passwordInput.value;


                    console.log("username: " + username + " password: " + password + " ident: " + ident + "")

                    if (!username || !password) {
                        showMessage('Per favore, inserisci username e password.', 'error');
                        return;
                    }

                    hideMessage();
                    toggleLoading(true);
                    dataSection.classList.add('hidden');

                    try {
                        const loginResponse = await fetch(`${backendBaseUrl}login`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', },
                            body: JSON.stringify({ username, password, ident }),
                        });

                        let result;
                        let responseText = await loginResponse.text();
                        console.log(responseText);
                        try {
                            result = JSON.parse(responseText);
                        } catch (e) {
                            console.error("Errore di parsing JSON. La risposta grezza è:", responseText);
                            result = {
                                status: "non_json_output",
                                message: "Il backend ha inviato un output non JSON. Controlla la console per il debug del PHP.",
                                rawResponse: responseText,
                            };
                        }

                        if (result.status === "non_json_output") {
                            showMessage(`Errore: ${result.message}`, 'error');
                            messageArea.innerHTML += result.rawResponse;
                            console.warn("Risposta PHP non JSON:", result.rawResponse);
                        }
                        else if (result.status === "602") {
                            showMessage(`Sei già registrato!`);
                        }
                        else {
                            //Salvo info in sessione
                            sessionStorage.setItem('lastLoginResponse', responseText);

                            // Encrypt password using Base64 encoding before storing
                            const encryptedPassword = btoa(password);
                            sessionStorage.setItem('classeviva_password', encryptedPassword);

                        }

                    } catch (error) {
                        console.error('Errore:', error);
                        showMessage(`Errore: ${error.message}. `, 'error');
                    } finally {
                        toggleLoading(false);
                    }
                }

                //=== Dati ===
                async function fetchClasseviva(requestedMethod) {
                    hideMessage();
                    toggleLoading(true);

                    try {
                        //Creo un grosso array per la gestione dei metodi in PHP
                        const methods = <?php
                            include_once 'api/apiMethods.php';
                            global $methods;
                            echo json_encode($methods);
                        ?>;

                        let method              = null;
                        let path                = null;
                        let httpMethod          = null;
                        let url                 = null;
                        let extraInputRequests  = null;
                        let cvvArrKey           = null;
                        let requestMethod       = null;

                        if (methods) {
                            if (methods.hasOwnProperty(requestedMethod)) {
                                method = methods[requestedMethod];
                                path = method['path'];
                                url = method['url'];
                                httpMethod = method['method'];
                                extraInputRequests = method['extraInput'];
                                cvvArrKey = method['cvvArrKey']
                                requestMethod = method['reqMethod'];
                            } else {
                                throw new Error(`Metodo non supportato: ${requestedMethod}, ${methods}`);
                            }
                        } else { throw new Error(`Metodi non trovati ?!`); }

                        let extraInput = {};
                        extraInputRequests.forEach(req => {
                            let inp;
                            while (true) {
                                inp = prompt(`Inserisci ${req}:`);
                                if (inp !== null && inp !== '') break;
                            }
                            extraInput[req] = inp;
                        })
                        console.log(extraInput)

                        const apiResponse = await fetch(`${backendBaseUrl}${path}`, {
                            method: `${httpMethod}`,
                            headers: { 'Content-Type': 'application/json', },
                            body: JSON.stringify( {'request':url, 'extraInput':extraInput, "cvvArrKey":cvvArrKey, "isPost":(requestMethod === 'POST')} ),
                        });

                        // Tentativo di leggere la risposta come JSON.
                        let result;
                        let responseText = await apiResponse.text();
                        //console.log(responseText);
                        try {
                            result = JSON.parse(responseText);
                            console.log(result)
                        } catch (e) {
                            console.error("Errore di parsing JSON. La risposta grezza è:", responseText);
                            result = {
                                status: "non_json_output",
                                message: "Il backend ha inviato un output non JSON. Controlla la console per il debug del PHP.",
                                rawResponse: responseText,
                            };
                        }

                        if (result.status === "non_json_output") {
                            // Gestisce il caso in cui il PHP stampa debug output
                            showMessage(`Errore: ${result.message}`, 'error');
                            messageArea.innerHTML += result.rawResponse;
                            console.warn("Risposta PHP non JSON:", result.rawResponse);
                            // Non procedere con l'elaborazione dei dati se la risposta non è JSON valida.
                        }
                        return result;

                    } catch (error) {
                        console.error('Errore:', error);
                        showMessage(`Errore: ${error.message}. `, 'error');
                    } finally {
                        toggleLoading(false);
                    }
                }

                loginBtn.addEventListener('click', () => {
                    fetchClassevivaLogin()
                    document.getElementById('login-section').classList.add('hidden');
                    document.getElementById('select-method').classList.remove('hidden');
                    document.getElementById('logoutBtn').classList.remove('hidden');
                });

                document.getElementById('logoutBtn').addEventListener('click', async () => {
                    sessionStorage.clear();
                    document.getElementById('login-section').classList.remove('hidden');
                    document.getElementById('select-method').classList.add('hidden');
                    document.getElementById('logoutBtn').classList.add('hidden');
                    document.getElementById('data-section').classList.add('hidden');

                    //Delete php sessions
                    try {
                        const loginResponse = await fetch(`${backendBaseUrl}rmSession`, {
                            method: 'GET',
                            headers: {'Content-Type': 'application/json',},
                        });

                        let responseText = await loginResponse.text();
                        console.log(responseText);


                    } catch (error) {
                        console.error('Errore:', error);
                        showMessage(`Errore: ${error.message}. `, 'error');
                    }
                });
                document.getElementById('fetchBtn').addEventListener('click', async () => {
                    const selectedMethod = document.getElementById('fetch-method').value;
                    let resp = await fetchClasseviva(selectedMethod);
                    if (resp.status === "non_json_output") { return; }
                    let parTab = document.querySelector('#cvv-data-output')
                    displayData(parTab, resp);
                });

                function displayData(parentTable, data, page = 0) {
                    parentTable.innerHTML = '';

                    // Se è una stringa semplice, mostrala direttamente
                    if (typeof data === 'string') {
                        const p = document.createElement('p');
                        p.textContent = data;
                        parentTable.appendChild(p);
                        return;
                    }

                    const table = document.createElement('table');
                    table.classList.add('data-table');

                    const thead = document.createElement('thead');
                    const tr = document.createElement('tr');
                    tr.classList.add('cvv-data-table-head');
                    thead.appendChild(tr);
                    table.appendChild(thead);

                    const tbody = document.createElement('tbody');
                    tbody.classList.add('cvv-data-table-body');
                    table.appendChild(tbody);

                    const pageNavigation = document.createElement('div');
                    pageNavigation.classList.add('cvv-data-buttons');

                    parentTable.appendChild(table);
                    parentTable.appendChild(pageNavigation);

                    dataSection.classList.remove('hidden');

                    // Gestione dati vuoti
                    if (!data) {
                        noDataMessage.classList.remove('hidden');
                        return;
                    }

                    noDataMessage.classList.add('hidden');

                    // Normalizza i dati in array per gestione uniforme
                    const dataArray = Array.isArray(data) ? data : [data];
                    if (dataArray.length === 0) {
                        noDataMessage.classList.remove('hidden');
                        return;
                    }

                    // Costanti per paginazione
                    const PAGE_LIMIT = 10;
                    let startIndex = page * PAGE_LIMIT;
                    let endIndex = startIndex + PAGE_LIMIT;

                    // Determina le intestazioni in base al tipo di dati nel primo elemento
                    let headers = [];
                    if (typeof dataArray[startIndex] === 'string') {
                        // Se l'array contiene stringhe, usa una singola intestazione "Value"
                        headers = ['Value'];
                    } else if (typeof dataArray[startIndex] === 'object' && dataArray[startIndex] !== null) {
                        // Se è un oggetto, usa le sue chiavi come intestazioni
                        headers = Object.keys(dataArray[startIndex]);
                    } else {
                        // Per altri tipi primitivi (number, boolean), usa "Value"
                        headers = ['Value'];
                    }

                    headers.forEach(prop => {
                        const th = document.createElement('th');
                        th.textContent = prop;
                        tr.appendChild(th);
                    });

                    // Popolamento righe
                    dataArray.slice(startIndex, PAGE_LIMIT !== -1 ? endIndex : undefined).forEach(el => {
                        const row = tbody.insertRow();

                        if (typeof el === 'string' || typeof el === 'number' || typeof el === 'boolean') {
                            // Se l'elemento è una stringa o un primitivo, crea una singola cella per il suo valore
                            const cell = row.insertCell();
                            cell.textContent = el ?? '';
                        } else if (typeof el === 'object' && el !== null) {
                            // Se l'elemento è un oggetto, itera sulle sue chiavi
                            headers.forEach(prop => {
                                const cell = row.insertCell();
                                const value = el[prop];

                                if (value !== null && typeof value === 'object') {
                                    // Gestione di array e oggetti annidati
                                    if (Array.isArray(value) || Object.keys(value).length > 0) {
                                        const subTableContainer = document.createElement('div');
                                        displayData(subTableContainer, value); // Chiamata ricorsiva
                                        cell.appendChild(subTableContainer);
                                    } else {
                                        cell.textContent = ''; // Oggetto vuoto
                                    }
                                } else {
                                    cell.textContent = value ?? '';
                                }
                            });
                        } else {
                            // Gestione di altri casi non previsti, potrebbe essere null/undefined
                            const cell = row.insertCell();
                            cell.textContent = el ?? '';
                        }
                    });

                    // Gestione paginazione
                    if (PAGE_LIMIT !== -1 && dataArray.length > PAGE_LIMIT) {
                        pageNavigation.innerHTML = '';

                        if (page > 0) {
                            const prevPageBtn = createPageButton('Prev Page', () =>
                                displayData(parentTable, data, page - 1));
                            pageNavigation.appendChild(prevPageBtn);
                        }

                        if (endIndex < dataArray.length) {
                            const nextPageBtn = createPageButton('Next Page', () =>
                                displayData(parentTable, data, page + 1));
                            pageNavigation.appendChild(nextPageBtn);
                        }
                    }
                }

                function createPageButton(text, onClick) {
                    const button = document.createElement('button');
                    button.textContent = text;
                    button.classList.add('btn', 'btn-primary');
                    button.addEventListener('click', onClick);
                    return button;
                }
            });
        </script>
    </body>
</html>
