<?php


namespace App\Controllers;

use App\Models\GameModel;
use Core\BaseController;

class LeaderboardController extends BaseController
{
    private GameModel $games;

    public function __construct()
    {
        $this->games = new GameModel(); // modèle déjà PDO + requêtes préparées
    }

    public function index(): void
    {
        $topScores = $this->games->getTopScores(10);     // top 10 global
        $userGames = [];

        if (!empty($_SESSION['user_id'])) {              // historique personnel si connecté
            $userGames = $this->games->getUserGames((int) $_SESSION['user_id'], 20);
        }

        $this->render('leaderboard/index', [
            'topScores' => $topScores,
            'userGames' => $userGames,
        ]);
    }
}