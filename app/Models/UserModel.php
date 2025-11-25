<?php

namespace App\Models;

use Core\Database;
use PDO;

/**
 * Gère les joueurs (mode 'arcade': pseudo unique et sans mot de passe).
 */
class UserModel
{
    /**
     * Retourne un joueur par son pseudo ou null si inexistant.
     */
    public function findByNickname(string $nickname): ?array
    {
        $stmt = Database::getPdo()->prepare(
            'SELECT id, nickname, created_at FROM users WHERE nickname = :nickname'
        );
        $stmt->execute(['nickname' => $nickname]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Crée un joueur avec ce pseudo puis renvoie son enregistrement complet.
     */
    public function create(string $nickname): array
    {
        $stmt = Database::getPdo()->prepare(
            'INSERT INTO users (nickname) VALUES (:nickname)'
        );
        $stmt->execute(['nickname' => $nickname]);

        return [
            'id'        => (int) Database::getPdo()->lastInsertId(),
            'nickname'  => $nickname,
            'created_at'=> date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Récupère le joueur existant ou le crée automatiquement.
     */
    public function findOrCreate(string $nickname): array
    {
        $nickname = trim($nickname);

        if ($nickname === '') {
            throw new \InvalidArgumentException('Le pseudo est obligatoire.');
        }

        $user = $this->findByNickname($nickname);

        return $user ?? $this->create($nickname);
    }
}