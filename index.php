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
                background-color: #f0f2f5;
            }
            .container {
                max-width: 800px;
                margin: 40px auto;
                padding: 20px;
                background-color: #ffffff;
                border-radius: 12px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            .input-field {
                padding: 10px 15px;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                font-size: 1rem;
                width: 100%;
                box-sizing: border-box; /* Include padding and border in the element's total width and height */
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
                border: 2px solid red !important;
            }
            .data-table th, .data-table td {
                border: 1px solid #e5e7eb;
                padding: 10px;
                text-align: left;
            }
            .data-table th {
                background-color: #f9fafb;
                font-weight: 600;
                color: #374151;
            }
            .message-box {
                background-color: #d1fae5;
                color: #065f46;
                padding: 12px;
                border-radius: 8px;
                margin-bottom: 20px;
                text-align: center;
                font-weight: 600;
            }
            .error-message {
                background-color: #fee2e2;
                color: #991b1b;
            }
            .loading-spinner {
                border: 4px solid rgba(0, 0, 0, 0.1);
                border-left-color: #4f46e5;
                border-radius: 50%;
                width: 24px;
                height: 24px;
                animation: spin 1s linear infinite;
                /*display: inline-block;*/
                vertical-align: middle;
                margin-left: 10px;
                display: none; /* Hidden by default */
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    </head>
    <body class="bg-gray-100 flex items-center justify-center min-h-screen">
        
        <div class="/*container*/ w-100 p-8">
            <h1 class="text-3xl font-bold text-center mb-6 text-gray-800">GradeCraft - Integrazione Classeviva</h1>

            <div id="message-area" class="hidden"></div>

            <!-- Login Area -->
            <div class="mb-6" id="login-section">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Accedi a Classeviva</h2>
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-medium mb-2">Username Classeviva:</label>
                    <input type="text" id="username" class="input-field" placeholder="Inserisci il tuo username">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password Classeviva:</label>
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
                <label for="fetch-method" class="block text-gray-700 text-sm font-medium mb-2">Seleziona i dati da visualizzare:</label>
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
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Dati Recuperati da Classeviva</h2>

                <div class="mb-6">
                    <h3 class="text-lg font-medium mb-3 text-gray-700">Dati</h3>
                    <div id="cvv-data-output">
                        <table class="data-table">
                            <thead>
                            <tr class="cvv-data-table-head"></tr>
                            </thead>
                            <tbody class="cvv-data-table-body"></tbody>
                        </table>
                        <div class="cvv-data-buttons"></div>
                        <p id="no-data-message" class="text-gray-600 mt-4 hidden">Nessun dato richiesto trovato.</p>
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
                const gradesTableBody   = document.getElementById('grades-table-body');
                const topicsTableBody   = document.getElementById('topics-table-body');
                const noGradesMessage   = document.getElementById('no-grades-message');
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
                async function fetchClassevivaAll() {
                    hideMessage();
                    toggleLoading(true);

                    try {

                        await fetchClasseviva('get_grades');
                        await fetchClasseviva('subjects');

                    } catch (error) {
                        console.error('Errore:', error);
                        showMessage(`Errore: ${error.message}. `, 'error');
                    } finally {
                        toggleLoading(false);
                    }
                }
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

                        if (methods.hasOwnProperty(requestedMethod)) {
                            method = methods[requestedMethod];
                            path = method['path'];
                            url = method['url'];
                            httpMethod = method['method'];
                            extraInputRequests = method['extraInput'];
                            cvvArrKey = method['cvvArrKey']
                            requestMethod = method['reqMethod'];
                            //console.log(path)
                        } else {
                            throw new Error(`Metodo non supportato: ${requestedMethod}, ${methods}`);
                        }

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
                async function fetchClassevivaVoti() {
                    console.log("Retrive grades")
                    hideMessage();
                    toggleLoading(true);
                    dataSection.classList.add('hidden'); // Nasconde la sezione dati mentre si carica

                    try {
                        const gradesResponse = await fetch(`${backendBaseUrl}grades`, {
                            method: 'GET',
                            headers: { 'Content-Type': 'application/json', },
                        });

                        let result;
                        let responseText = await gradesResponse.text();
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

                        if (result === null) throw new Error('Non ci sono voti.');

                        if ('status' in result && result.status === "non_json_output") {
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


                function displayGrades(grades, page=0) {
                    console.log("Display grades")
                    dataSection.classList.remove('hidden');
                    gradesTableBody.innerHTML = '';
                    if (grades !== null && grades.length > 0) {
                        noGradesMessage.classList.add('hidden');
                        let gradeNum = 0;
                        let PAGE_LIMIT = 10;
                        grades.forEach(grade => {

                            if (PAGE_LIMIT !== -1 && gradeNum >= (page * PAGE_LIMIT) && gradeNum < ((page + 1) * PAGE_LIMIT)) {
                                const row = gradesTableBody.insertRow();
                                row.insertCell().textContent = grade["subjectDesc"];
                                row.insertCell().textContent = grade["displayValue"];
                                row.insertCell().textContent = grade["evtDate"];
                                row.insertCell().textContent = grade["notesForFamily"];
                            }
                            gradeNum++;
                        });

                        //Bottoni per il multi-paging
                        if ( PAGE_LIMIT !== -1 && grades.length > PAGE_LIMIT) {
                            let pageBnts = document.createElement('div');
                            pageBnts.display = 'flex';
                            pageBnts.justifyContent = 'justify-between';

                            //Se c'è una pagina precedente
                            if ( page > 0 ) {
                                let prevPageBtn = document.createElement('button');
                                prevPageBtn.textContent = 'Prev Page';
                                prevPageBtn.classList.add('btn');
                                prevPageBtn.classList.add('btn-primary');
                                prevPageBtn.addEventListener('click', () => displayGrades(grades, page - 1))
                                pageBnts.appendChild(prevPageBtn);
                            }
                            //Se c'è una pagina successiva
                            if ( (page + 1) * PAGE_LIMIT < grades.length ) {
                                let nextPageBtn = document.createElement('button');
                                nextPageBtn.textContent = 'Next Page';
                                nextPageBtn.addEventListener('click', () => displayGrades(grades, page + 1))
                                nextPageBtn.classList.add('btn');
                                nextPageBtn.classList.add('btn-primary');
                                pageBnts.appendChild(nextPageBtn);
                            }

                            gradesTableBody.appendChild(pageBnts);
                        }

                    } else {
                        noGradesMessage.classList.remove('hidden');
                    }
                }
                function displayData(parentTable, data, page=0, isFT = true) {
                    //console.log(table)
                    parentTable.innerHTML = '';
                    console.log("Dovrebbe essere vuoto: " + parentTable.innerHTML)

                    let table = document.createElement('table');
                    table.classList.add('data-table');
                    table.innerHTML = '';
                    parentTable.appendChild(table);

                    let thead = document.createElement('thead');
                    let tr = document.createElement('tr');
                    tr.classList.add('cvv-data-table-head');
                    thead.appendChild(tr);
                    table.appendChild(thead);

                    let tbody = document.createElement('tbody');
                    tbody.classList.add('cvv-data-table-body');
                    table.appendChild(tbody);

                    let pageNavigation = document.createElement('div');
                    pageNavigation.classList.add('cvv-data-buttons');
                    parentTable.appendChild(pageNavigation);


                    let dataTableBody = tbody;
                    let head = thead;

                    console.log("Display generica data")

                    dataSection.classList.remove('hidden');
                    dataTableBody.innerHTML = ''; // Pulisce la tabella
                    head.innerHTML = '';

                    if (data !== null && data.length > 0) {
                        noDataMessage.classList.add('hidden');
                        let i = 0;
                        let PAGE_LIMIT = 10;
                        Object.keys(data[0]).map(prop => {
                            let th = document.createElement('th');
                            th.textContent = prop;
                            head.appendChild(th);
                        })

                        data.forEach(el => {
                            if (isFT) console.log(el);

                            if (PAGE_LIMIT !== -1 && i >= (page * PAGE_LIMIT) && i < ((page + 1) * PAGE_LIMIT)) {
                                let row = dataTableBody.insertRow();
                                Object.keys(el).map(prop => {
                                    //let out = '';
                                    if (el[prop] !== null && el[prop].constructor === Array) {

                                        let subTableCont = document.createElement('div');
                                        displayData(subTableCont, el[prop]);
                                        row.insertCell().appendChild(subTableCont);

                                    } else {
                                        row.insertCell().textContent = el[prop];
                                    }
                                })

                            }
                            i++;
                        });

                        //Bottoni per il multi-paging
                        if ( PAGE_LIMIT !== -1 && data.length > PAGE_LIMIT) {
                            // pageNavigation.classList.add('cvv-data-buttons');
                            pageNavigation.innerHTML = '';

                            //Se c'è una pagina precedente
                            if ( page > 0 ) {
                                let prevPageBtn = document.createElement('button');
                                prevPageBtn.textContent = 'Prev Page';
                                prevPageBtn.classList.add('btn');
                                prevPageBtn.classList.add('btn-primary');
                                prevPageBtn.addEventListener('click', () => displayData(parentTable, data, page - 1, false))
                                pageNavigation.appendChild(prevPageBtn);
                            }
                            //Se c'è una pagina successiva
                            if ( (page + 1) * PAGE_LIMIT < data.length ) {
                                let nextPageBtn = document.createElement('button');
                                nextPageBtn.textContent = 'Next Page';
                                nextPageBtn.addEventListener('click', () => displayData(parentTable, data, page + 1, false))
                                nextPageBtn.classList.add('btn');
                                nextPageBtn.classList.add('btn-primary');
                                pageNavigation.appendChild(nextPageBtn);
                            }
                        }

                    }
                    else if (!data.hasOwnProperty('length')) {

                        //TODO: Fix sim-dup. code

                        let el = data;
                        Object.keys(el).map(prop => {
                            let th = document.createElement('th');
                            th.textContent = prop;
                            head.appendChild(th);
                        })

                        const row = dataTableBody.insertRow();
                        console.log(el)
                        Object.keys(el).map(prop => {
                            let out = '';
                            try {
                                let subTableCont = document.createElement('div');
                                displayData(subTableCont, el[prop]);
                                row.insertCell().appendChild(subTableCont);
                            } catch (e) {
                                out = el[prop];
                            }
                            row.insertCell().textContent = out;
                        })

                    }
                    else {
                        noDataMessage.classList.remove('hidden');
                    }
                }
            });
        </script>
    </body>
</html>
