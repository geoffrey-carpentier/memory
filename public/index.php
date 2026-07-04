<?php


declare(strict_types=1);

// === CONFIGURATION === 
ini_set('display_errors', '1');
error_reporting(E_ALL);


// === AUTOLOAD COMPOSER ===

require_once __DIR__ . '/../vendor/autoload.php'; // Chargement automatique des classes via Composer

session_start();

// === ENV (variables d'environnement) ===

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// === IMPORTS === 
// Importation des classes avec namespaces pour éviter les conflits de noms
use Core\Router;
use App\Controllers\GameController;
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
$router->get('/leaderboard', 'App\\Controllers\\LeaderboardController@index');
$router->get('/profile', 'App\\Controllers\\ProfileController@index');

// Dispatch
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
