<?php
namespace App\Models;

use Core\Database;

/**
 * Classe ArticleModel
 * ---------------------
 * Gère l'accès aux données pour l'entité "Article".
 * Elle encapsule les requêtes SQL liées aux articles et retourne des données
 * prêtes à être utilisées par les contrôleurs.
 */
class ArticleModel
{
    /**
     * Récupère tous les articles depuis la base
     *
     * @return array Liste des articles sous forme de tableau associatif
     *               Chaque entrée contient : ['id' => ..., 'title' => ..., 'body' => ...]
     */
    public function all(): array
    {
        // Exécute une requête SQL directe pour récupérer tous les articles
        $stmt = Database::getPdo()->query(
            'SELECT id, title, body FROM articles ORDER BY id DESC'
        );

        // Retourne tous les résultats sous forme de tableau associatif
        return $stmt->fetchAll();
    }

    /**
     * Récupère un article spécifique par son identifiant
     *
     * @param int $id Identifiant unique de l'article
     * @return array|null Retourne l'article trouvé ou null si aucun résultat
     */
    public function find(int $id): ?array
    {
        // Prépare une requête SQL sécurisée (évite les injections SQL via PDO)
        $stmt = Database::getPdo()->prepare(
            'SELECT id, title, body FROM articles WHERE id = :id'
        );

        // Exécution avec liaison de paramètre
        $stmt->execute(['id' => $id]);

        // Récupère une seule ligne
        $row = $stmt->fetch();

        // Retourne l'article si trouvé, sinon null
        return $row ?: null;
    }
}
