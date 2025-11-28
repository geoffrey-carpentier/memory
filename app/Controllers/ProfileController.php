<?php

namespace App\Controllers;

use App\Models\GameModel;
use Core\BaseController;

class ProfileController extends BaseController
{
    private GameModel $games;

    public function __construct()
    {
        $this->games = new GameModel();
    }

    public function index(): void
    {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = 'Connecte-toi pour voir ton profil.';
            header('Location: /login');
            return;
        }

        $stats = $this->games->getUserStats((int) $_SESSION['user_id']); // méthode à ajouter (moyenne, meilleur score…)
        $history = $this->games->getUserGames((int) $_SESSION['user_id'], 25);

        $this->render('profile/index', [
            'stats'   => $stats,
            'history' => $history,
        ]);
    }
}