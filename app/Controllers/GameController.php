<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\GameModel;
use App\Models\CardModel;
use App\Services\GameRules;
use Core\BaseController;
use Core\Security;

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
     * Affiche la page d'accueil : formulaire OU partie en cours OU résumé.
     */
    public function index(): void
    {
        $this->render('game/index', []);
    }

    /**
     * Traite le formulaire « Jouer » : crée une nouvelle partie.
     * POST /start
     */
    public function start(): void
    {
        try {
            // Validation CSRF
            if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                throw new \Exception('Token CSRF invalide.');
            }

            $nickname   = trim($_POST['nickname'] ?? '');
            $difficulty = (int) ($_POST['difficulty'] ?? 0);

            // Validation serveur
            if (strlen($nickname) < 2 || strlen($nickname) > 20) {
                throw new \InvalidArgumentException('Pseudo : 2-20 caractères.');
            }

            if (!GameRules::isValidDifficulty($difficulty)) {
                throw new \InvalidArgumentException('Difficulté invalide.');
            }

            // Récupère ou crée le joueur
            $user = $this->users->findOrCreate($nickname);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nickname'] = $user['nickname'];

            // Détermine le temps alloué
            $timeAllocated = GameRules::getTimeAllocated($difficulty);

            // Crée la partie en BDD
            $gameId = $this->games->create([
                'user_id' => $user['id'],
                'difficulty' => $difficulty,
                'pairs_total' => $difficulty,
                'time_allocated' => $timeAllocated,
            ]);

            // Tire les cartes et construit la grille mélangée
            $pairs = $this->cards->pickRandomPairs($difficulty);
            $deck = $this->buildShuffledDeck($pairs);

            // Stocke la partie en session
            $_SESSION['current_game'] = [
                'id' => $gameId,
                'deck' => $deck,
                'pairs_total' => $difficulty,
                'time_allocated' => $timeAllocated,
                'started_at' => time(),
            ];

            header('Location: /');

        } catch (\InvalidArgumentException $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            header('Location: /');
        } catch (\Exception $e) {
            error_log('GameController::start() - ' . $e->getMessage());
            $_SESSION['flash_error'] = 'Erreur serveur. Réessaye.';
            header('Location: /');
        }
    }

    /**
     * Finalise la partie et enregistre le score.
     * POST /finish
     */
    public function finish(): void
    {
        try {
            // Validation CSRF
            if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                throw new \Exception('Token CSRF invalide.');
            }

            $gameSession = $_SESSION['current_game'] ?? null;

            if (!$gameSession) {
                throw new \Exception('Aucune partie active.');
            }

            $pairsTotal = (int) $gameSession['pairs_total'];
            $timeAllocated = (int) $gameSession['time_allocated'];

            // Validation et nettoyage des données
            $pairsFound = max(0, min($pairsTotal, (int) ($_POST['pairs_found'] ?? 0)));
            $errors = max(0, (int) ($_POST['errors'] ?? 0));
            $timeRemaining = max(0, min($timeAllocated, (int) ($_POST['time_remaining'] ?? 0)));
            $resultType = $_POST['result_type'] ?? 'unknown';

            if (!in_array($resultType, ['win', 'timeout', 'unknown'], true)) {
                throw new \InvalidArgumentException('Type de résultat invalide.');
            }

            $timeSpent = $timeAllocated - $timeRemaining;
            $finalScore = GameRules::computeScore($pairsFound, $errors, $timeRemaining, $pairsTotal);
            $status = $resultType === 'timeout' ? 'aborted' : 'finished';

            // Enregistre le résultat en BDD
            $this->games->finish($gameSession['id'], [
                'pairs_found' => $pairsFound,
                'errors' => $errors,
                'time_spent' => max(0, $timeSpent),
                'time_remaining' => $timeRemaining,
                'final_score' => $finalScore,
                'status' => $status,
            ]);

            // Stocke le résumé pour affichage post-partie
            $_SESSION['last_game_summary'] = [
                'score' => $finalScore,
                'pairs_found' => $pairsFound,
                'pairs_total' => $pairsTotal,
                'errors' => $errors,
                'time_remaining' => $timeRemaining,
                'result_type' => $resultType,
            ];

            // Nettoie la session
            unset($_SESSION['current_game']);

            $_SESSION['flash_success'] = $resultType === 'timeout'
                ? "Temps écoulé. Score : {$finalScore} pts"
                : "Partie complétée ! Score : {$finalScore} pts";

            header('Location: /');

        } catch (\Exception $e) {
            error_log('GameController::finish() - ' . $e->getMessage());
            $_SESSION['flash_error'] = 'Erreur lors de la sauvegarde.';
            header('Location: /');
        }
    }

    /**
     * Construit la grille de cartes : duplique chaque paire et mélange.
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
