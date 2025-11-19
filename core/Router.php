<?php

namespace Core;

/**
 * Classe Router
 * -----------------
 * Gère la définition et la résolution des routes HTTP.
 * Elle mappe une URL donnée à une action de contrôleur (ex: "App\Controllers\HomeController@index").
 */
class Router
{
    /**
     * Tableau des routes disponibles, classées par méthode HTTP (GET/POST).
     * Exemple :
     * [
     *   'GET' => ['/articles' => 'App\Controllers\ArticleController@index']
     * ]
     */
    private array $routes = ['GET' => [], 'POST' => []];

    /**
     * Enregistre une route de type GET
     *
     * @param string $path   Chemin de la route (ex: "/articles")
     * @param string $action Action à exécuter (ex: "App\Controllers\ArticleController@index")
     */
    public function get(string $path, string $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    /**
     * Méthode principale qui analyse l'URI demandée
     * et exécute le contrôleur/méthode correspondant si trouvé.
     *
     * @param string $uri    URI de la requête (ex: "/articles")
     * @param string $method Méthode HTTP utilisée (GET, POST, etc.)
     */
    public function dispatch(string $uri, string $method): void
    {
        // On extrait uniquement le chemin (sans paramètres GET ou #ancre)
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Vérifie si une route correspond à ce chemin pour la méthode demandée
        foreach ($this->routes[$method] ?? [] as $route => $action) {
            if ($route === $path) {
                // Sépare le nom complet du contrôleur et la méthode (notation "Controller@method")
                [$class, $method] = explode('@', $action);

                // Instancie dynamiquement le contrôleur
                $controller = new $class();

                // Appelle la méthode du contrôleur
                $controller->$method();
                return;
            }
        }

        // Si aucune route trouvée, on renvoie une erreur 404
        http_response_code(404);
        echo "404 - Page non trouvée";
    }
}

// Exemple d'utilisation
$router = new Router();
$router->get('/', 'App\\Controllers\\HomeController@index');
$router->get('/articles', 'App\\Controllers\\ArticleController@index');
$router->get('/about', 'App\\Controllers\\HomeController@about'); // nouvelle route
