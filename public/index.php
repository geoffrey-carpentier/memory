<?php


declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);


// Chargement manuel des fichiers nécessaires au fonctionnement de l'application
// require __DIR__ . '/../core/Router.php';           // Classe responsable de la gestion des routes
// require __DIR__ . '/../core/BaseController.php';  // Classe de base pour tous les contrôleurs
// require __DIR__ . '/../core/Database.php';        // Gestion de la connexion et des requêtes à la base de données
// require __DIR__ . '/../app/Controllers/HomeController.php';   // Contrôleur de la page d'accueil
// require __DIR__ . '/../app/Models/ArticleModel.php';          // Modèle pour la gestion des articles

session_start();
require_once __DIR__ . "/../vendor/autoload.php"; // Chargement automatique des classes via Composer
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// Importation des classes avec namespaces pour éviter les conflits de noms
use Core\Router;
use App\Controllers\GameController;
use App\Controllers\HomeController;
use App\Controllers\ArticleController;
use App\Controllers\LeaderboardController;
use App\Controllers\ProfileController;

// Initialisation du routeur
$router = new Router();

// Définition des routes de l'application (GET / POST)

# $router->get('/about', 'App\\Controllers\\HomeController@about'); // nouvelle route
$router->get('/', GameController::class . '@index');
$router->post('/start', GameController::class . '@start');
$router->post('/finish', GameController::class . '@finish');
$router->get('/ranking', LeaderboardController::class . '@index');
$router->get('/profile', ProfileController::class . '@index');

// Exécution du routeur :
// On analyse l'URI et la méthode HTTP pour appeler le contrôleur et la méthode correspondants
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
