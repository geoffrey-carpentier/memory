<?php

namespace App\Controllers;

use App\Models\GameModel;
use App\Models\UserModel;
use Core\BaseController;

class LeaderboardController extends BaseController
{
    private GameModel $games;
    private UserModel $users;

    public function __construct()
    {
        $this->games = new GameModel();
        $this->users = new UserModel();
    }

    /**
     * Affiche le classement global (Top 10)
     */
    public function index(): void
    {
        try {
            // Récupère le Top 10 des meilleurs scores
            $topScores = $this->games->getTopScores(10);

            // Récupère l'historique personnel si connecté
            $userId = $_SESSION['user_id'] ?? null;
            $userGames = [];

            if ($userId) {
                $userGames = $this->games->getUserGames($userId);
            }

            $this->render('leaderboard/index', [
                'title' => 'Classement — Memory',
                'topScores' => $topScores,
                'userGames' => $userGames,
            ]);
        } catch (\Exception $e) {
            error_log('LeaderboardController::index() - ' . $e->getMessage());
            $_SESSION['flash_error'] = 'Erreur lors du chargement du classement.';
            header('Location: /');
        }
    }
}
