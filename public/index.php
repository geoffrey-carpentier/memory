<?php


declare(strict_types=1);

// === CONFIGURATION === 
ini_set('display_errors', '1');
error_reporting(E_ALL);


// Chargement manuel des fichiers nécessaires au fonctionnement de l'application
// require __DIR__ . '/../core/Router.php';           // Classe responsable de la gestion des routes
// require __DIR__ . '/../core/BaseController.php';  // Classe de base pour tous les contrôleurs
// require __DIR__ . '/../core/Database.php';        // Gestion de la connexion et des requêtes à la base de données
// require __DIR__ . '/../app/Controllers/HomeController.php';   // Contrôleur de la page d'accueil
// require __DIR__ . '/../app/Models/ArticleModel.php';          // Modèle pour la gestion des articles

session_start();

// === AUTOLOAD COMPOSER === 

require_once __DIR__ . '/../vendor/autoload.php'; // Chargement automatique des classes via Composer

// === ENV (variables d'environnement) === 

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// === IMPORTS === 
// Importation des classes avec namespaces pour éviter les conflits de noms
use Core\Router;
use App\Controllers\GameController;
//# use App\Controllers\HomeController;
//# */ use App\Controllers\ArticleController;
use App\Controllers\LeaderboardController;
use App\Controllers\ProfileController;
use Core\ErrorHandler;

ErrorHandler::register();

// === ROUTEUR === 
$router = new Router();

// Routes du jeu
$router->get('/', 'App\\Controllers\\GameController@index');
$router->post('/start', 'App\\Controllers\\GameController@start');
$router->post('/finish', 'App\\Controllers\\GameController@finish');

// Routes classement & profil
$router->get('/leaderboard', 'App\\Controllers\\LeaderboardController@index'); // ✅ Corriger si 'ranking'
$router->get('/profile', 'App\\Controllers\\ProfileController@index');

// Dispatch
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
