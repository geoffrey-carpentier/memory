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
        $userId = $_SESSION['user_id'] ?? null;
        $nickname = $_SESSION['nickname'] ?? 'Invité'; // Pseudonyme par défaut pour l'affichage

        if (empty($userId)) {
            // Si aucun utilisateur n'est en session, afficher la page de profil avec un message invitant à jouer
            $this->render('profile/index', [
                'title'     => 'Mon Profil',
                'nickname'  => $nickname,
                'userStats' => ['games_played' => 0, 'best_score' => 0, 'avg_score' => 0],
                'userGames' => [],
                'message'   => 'Veuillez jouer une partie pour créer votre profil et voir vos statistiques.',
            ]);
            return;
        }

        // Si un utilisateur est en session, récupérer ses statistiques et son historique
        $stats = $this->games->getUserStats((int) $userId);
        $history = $this->games->getUserGames((int) $userId, 25);

        $this->render('profile/index', [
            'title'     => 'Mon Profil',
            'nickname'  => $nickname,
            'userStats' => $stats,
            'userGames' => $history,
        ]);
    }
}
