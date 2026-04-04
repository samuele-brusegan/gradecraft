<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */


class Router {
    private array $routes = [];

    // Aggiunge una nuova rotta. I segmenti che iniziano con ":" sono parametri.
    public function add($url, $controller, $action): void {
        $this->routes[] = [
            'pattern' => $this->buildRegex($url),
            'controller' => $controller,
            'action' => $action,
        ];
    }

    private function buildRegex(string $url): string {
        $trimmed = trim($url, '/');
        if ($trimmed === '') {
            // Per la root "/" gestisci caso speciale
            return '#^/$#';
        }
        $segments = explode('/', $trimmed);
        $pattern = '';
        foreach ($segments as $seg) {
            if ($seg[0] === ':') {
                $pattern .= '/([^/]+)';
            } else {
                $pattern .= '/' . preg_quote($seg, '/');
            }
        }
        return '#^' . $pattern . '$#';
    }

    // Smista la richiesta all'azione corretta
    public function dispatch($url): void {
        $path = rtrim(parse_url($url, PHP_URL_PATH), '/');
        if ($path === '') {
            $path = '/';
        }

        foreach ($this->routes as $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                $controllerName = $route['controller'];
                $actionName = $route['action'];

                // Rimuove il match completo e popola i parametri
                array_shift($matches);
                if (!empty($matches)) {
                    $_GET = array_merge($_GET, $matches);
                }

                $controller = new $controllerName();
                $controller->$actionName(...$matches);
                return;
            }
        }

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
