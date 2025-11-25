<?php

namespace App\Models;

use Core\Database;

/**
 * Gère le catalogue des cartes et leur tirage aléatoire.
 */
class CardModel
{
    /**
     * Retourne toutes les cartes disponibles.
     */
    public function all(): array
    {
        $stmt = Database::getPdo()->query(
            'SELECT id, slug, label, image_path FROM cards ORDER BY id ASC'
        );

        return $stmt->fetchAll();
    }

    /**
     * Tire N cartes uniques aléatoires (pour constituer les paires).
     */
    public function pickRandomPairs(int $pairs): array
    {
        $stmt = Database::getPdo()->prepare(
            'SELECT id, slug, label, image_path
             FROM cards
             ORDER BY RAND()  # tirage aléatoire généré ici
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $pairs, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}