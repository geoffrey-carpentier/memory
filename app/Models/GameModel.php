<?php

namespace App\Models;

use Core\Database;
use PDO;

/**
 * Gère les parties : création, sauvegarde du score, classements, historique...
 */
class GameModel
{
    /**
     * Insère une nouvelle partie (status in_progress) et retourne son id.
     */
    public function create(array $data): int
    {
        $stmt = Database::getPdo()->prepare(
            'INSERT INTO games (user_id, difficulty, pairs_total, time_allocated, status)
             VALUES (:user_id, :difficulty, :pairs_total, :time_allocated, :status)'
        );

        $stmt->execute([
            'user_id'       => $data['user_id'],
            'difficulty'    => $data['difficulty'],
            'pairs_total'   => $data['pairs_total'],
            'time_allocated' => $data['time_allocated'],
            'status'        => $data['status'] ?? 'in_progress',
        ]);

        return (int) Database::getPdo()->lastInsertId();
    }

    /**
     * Marque la partie comme terminée et enregistre le score final.
     */
    public function finish(int $gameId, array $stats): void
    {
        $stmt = Database::getPdo()->prepare(
            'UPDATE games
             SET pairs_found = :pairs_found,
                 errors = :errors,
                 time_spent = :time_spent,
                 time_remaining = :time_remaining,
                 final_score = :final_score,
                 status = :status,
                 finished_at = NOW()
             WHERE id = :id'
        );

        $stmt->execute([
            'pairs_found'   => $stats['pairs_found'],
            'errors'        => $stats['errors'],
            'time_spent'    => $stats['time_spent'],
            'time_remaining' => $stats['time_remaining'],
            'final_score'   => $stats['final_score'],
            'status'        => $stats['status'] ?? 'finished',
            'id'            => $gameId,
        ]);
    }

    /**
     * Top 10 meilleurs joueurs triés par score décroissant puis date DESC.
     */
    public function getTopScores(int $limit = 10): array
    {
        $stmt = Database::getPdo()->prepare(
            'SELECT 
                g.final_score, 
                g.finished_at, 
                g.difficulty, 
                u.nickname
             FROM games g
             INNER JOIN users u ON u.id = g.user_id
             WHERE g.status = :status AND g.final_score IS NOT NULL
             ORDER BY g.final_score DESC, g.finished_at ASC
             LIMIT :limit'
        );
        $stmt->bindValue(':status', 'finished', \PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Dernières parties d’un joueur (historique profil).
     */
    public function getUserGames(int $userId, int $limit = 10): array
    {
        $stmt = Database::getPdo()->prepare(
            'SELECT final_score, difficulty, errors, time_spent, finished_at
             FROM games
             WHERE user_id = :user_id AND final_score IS NOT NULL
             ORDER BY finished_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Statistiques d’un joueur.
     */
    public function getUserStats(int $userId): array
    {
        $stmt = Database::getPdo()->prepare(
            'SELECT 
                COUNT(*) AS games_played,
                MAX(final_score) AS best_score,
                AVG(final_score) AS avg_score
             FROM games
             WHERE user_id = :user_id AND final_score IS NOT NULL'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetch() ?: ['games_played' => 0, 'best_score' => null, 'avg_score' => null];
    }
}
