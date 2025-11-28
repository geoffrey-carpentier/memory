<?php

declare(strict_types=1);

namespace Core;

use App\Controllers\ErrorController; // 👈 Import depuis app\Controllers

/**
 * Classe Router
 * -----------------
 * Gère la définition et la résolution des routes HTTP.
 */
class Router
{
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
    public function dispatch(string $uri, string $method = 'GET'): void
    {
        // Nettoie l'URI (supprime les query strings)
        $path = parse_url($uri, PHP_URL_PATH);

        // Cherche la route
        if (isset($this->routes[$method][$path])) {
            $this->executeRoute($this->routes[$method][$path]);
        } else {
            // Si aucune route ne correspond → 404
            $errorController = new ErrorController();
            $errorController->notFound();
        }
    }

    /**
     * Exécute une route trouvée
     */
    private function executeRoute(string $action): void
    {
        [$controller, $method] = explode('@', $action);

        if (!class_exists($controller)) {
            throw new \Exception("Contrôleur introuvable : {$controller}");
        }

        $instance = new $controller();

        if (!method_exists($instance, $method)) {
            throw new \Exception("Méthode introuvable : {$controller}@{$method}");
        }

        $instance->$method();
    }

    /**
     * Ajoute une route HTTP de type POST.
     * Cette méthode permet de définir un chemin associé à une action
     * qui sera exécutée lorsqu'une requête POST correspondra à ce chemin.
     *
     * @param string $path   Chemin de la route (ex. "/articles")
     * @param string $action Action à exécuter (ex. "App\Controllers\ArticleController@index")
     */
    public function post(string $path, string $action): void
    {
        $this->routes['POST'][$path] = $action;
    }
}

// // Exemple d'utilisation
// $router = new Router();
// $router->get('/', 'App\\Controllers\\HomeController@index');
// $router->get('/articles', 'App\\Controllers\\ArticleController@index');
// $router->get('/about', 'App\\Controllers\\HomeController@about'); // nouvelle route
