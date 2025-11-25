<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\GameModel;
use App\Models\CardModel;
use Core\BaseController;

/**
 * Contrôleur principal : une page unique + actions de démarrage/fin de partie.
 */
class GameController extends BaseController
{
    private UserModel $users;
    private GameModel $games;
    private CardModel $cards;

    public function __construct()
    {
        $this->users = new UserModel();
        $this->games = new GameModel();
        $this->cards = new CardModel();
    }

    /**
     * Affiche la page unique du jeu avec pseudo courant, top 10, etc.
     */
    public function index(): void
    {
        $topScores = $this->games->getTopScores();
        $userGames = [];

        if (!empty($_SESSION['user_id'])) {
            $userGames = $this->games->getUserGames((int) $_SESSION['user_id']);
        }

        $this->render('game/index', [
            'topScores' => $topScores,
            'userGames' => $userGames,
            'nickname'  => $_SESSION['nickname'] ?? '',
        ]);
    }

    /**
     * Traite le formulaire « Jouer » : pseudo + difficulté.
     */
    public function start(): void
    {
        $nickname   = trim($_POST['nickname'] ?? '');
        $difficulty = (int) ($_POST['difficulty'] ?? 0); // nombre de paires

        if ($nickname === '' || !in_array($difficulty, [3, 4, 6, 8, 10, 12], true)) {
            $_SESSION['flash_error'] = 'Pseudo ou difficulté invalide.';
            header('Location: /');
            return;
        }

        $user       = $this->users->findOrCreate($nickname);
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['nickname'] = $user['nickname'];

        $timeAllocated = $this->computeTimer($difficulty);

        $gameId = $this->games->create([
            'user_id'       => $user['id'],
            'difficulty'    => $difficulty,
            'pairs_total'   => $difficulty,
            'time_allocated' => $timeAllocated,
        ]);

        // Tirage aléatoire des paires, duplication pour former les 2 cartes/pair et mélange.
        $pairs = $this->cards->pickRandomPairs($difficulty);
        $deck  = $this->buildShuffledDeck($pairs);

        $_SESSION['current_game'] = [
            'id'             => $gameId,
            'deck'           => $deck,
            'pairs_total'    => $difficulty,
            'time_allocated' => $timeAllocated,
            'started_at'     => time(),
            'pairs_found'    => 0,
            'errors'         => 0,
        ];
        unset($_SESSION['last_game_summary']); // on efface l’ancien récap

        header('Location: /'); // La vue lira la session pour afficher la grille.
    }

    /**
     * Sauvegarde la partie terminée (appelée quand toutes les paires sont trouvées ou chrono écoulé).
     */
    public function finish(): void
    {
        $gameSession = $_SESSION['current_game'] ?? null;

        if (!$gameSession) {
            header('Location: /');
            return;
        }

        $pairsTotal   = (int) $gameSession['pairs_total'];
        $timeAllocated = (int) $gameSession['time_allocated'];

        $pairsFound    = max(0, min($pairsTotal, (int) ($_POST['pairs_found'] ?? 0)));
        $errors        = max(0, (int) ($_POST['errors'] ?? 0));
        $timeRemaining = max(0, min($timeAllocated, (int) ($_POST['time_remaining'] ?? 0)));
        $timeSpent     = max(0, $timeAllocated - $timeRemaining);
        $resultType    = $_POST['result_type'] ?? 'win';
        $status        = $resultType === 'timeout' ? 'aborted' : 'finished';

        $finalScore = $this->computeScore($pairsFound, $errors, $timeRemaining);

        $this->games->finish($gameSession['id'], [
            'pairs_found'    => $pairsFound,
            'errors'         => $errors,
            'time_spent'     => $timeSpent,
            'time_remaining' => $timeRemaining,
            'final_score'    => $finalScore,
            'status'         => $status,
        ]);

        $_SESSION['last_game_summary'] = [
            'score'         => $finalScore,
            'pairs_found'   => $pairsFound,
            'pairs_total'   => $pairsTotal,
            'errors'        => $errors,
            'time_remaining'=> $timeRemaining,
            'result_type'   => $resultType,
        ];

        unset($_SESSION['current_game']);

        $_SESSION['flash_success'] = $resultType === 'timeout'
            ? 'Tu es lent, beaucoup trop lent... Score (quand-même) enregistré.'
            : "Ton score  : {$finalScore} points";

        header('Location: /');
    }

    /**
     * Calcul du chrono selon la difficulté (ajuste librement).
     */
    private function computeTimer(int $pairs): int
    {
        return match ($pairs) {
            3 => 20,
            4 => 30,
            6 => 45,
            8 => 60,
            10 => 80,
            12 => 100,
            default => 60,
        };
    }

    /**
     * Formule officielle : (paires * 100) + (temps restant * 10) - (erreurs * 25).
     */
    private function computeScore(int $pairsFound, int $errors, int $timeRemaining): int
    {
        return ($pairsFound * 100) + ($timeRemaining * 10) - ($errors * 25);
    }

    /**
     * Duplique chaque carte tirée, attribue un identifiant de paire et mélange.
     */
    private function buildShuffledDeck(array $pairs): array
    {
        $deck = [];

        foreach ($pairs as $card) {
            $pairId = uniqid('pair_', false);
            $deck[] = ['pair' => $pairId, 'card' => $card];
            $deck[] = ['pair' => $pairId, 'card' => $card];
        }

        shuffle($deck);

        return $deck;
    }
}
