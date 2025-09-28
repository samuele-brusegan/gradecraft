<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */


class Router {
    private array $routes = [];

    // Aggiunge una nuova rotta
    public function add($url, $controller, $action): void {
        $this->routes[$url] = [
            'controller' => $controller,
            'action' => $action
        ];
    }

    // Smista la richiesta all'azione corretta
    public function dispatch($url): void {
        $path = rtrim(parse_url($url, PHP_URL_PATH), '/');
        if ($path === '') {
            $path = '/';
        }

        if (array_key_exists($path, $this->routes)) {
            $route = $this->routes[$path];
            $controllerName = $route['controller'];
            $actionName = $route['action'];

            // Crea il controller e chiama l'azione
            $controller = new $controllerName();
            $controller->$actionName();
        } else {
            // Gestione errore 404
            header("HTTP/1.0 404 Not Found");
            echo "<head>"; require COMMON_HTML_HEAD; echo "</head>";

            echo "<div class='container'>";
            echo    "<pre style='font-size: 4rem;'>";
            echo    "Pagina non trovata! <br>";
            echo    "Path:" . $path . "<br>";
            echo    "Url :" . $url . "<br>";
            echo    "</pre>";
            echo "</div>";
            require COMMON_HTML_FOOT;
        }
    }
}
