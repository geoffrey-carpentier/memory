<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Classe GameRules
 * ----------------
 * Gère la logique du jeu (génération de deck, calcul de score, etc.)
 */
class GameRules
{
    /**
     * Généère un deck de cartes avec images ou emojis
     * 
     * @param int $pairsCount Nombre de paires à générer
     * @return array Tableau de cartes mélangées
     */
    public static function generateDeck(int $pairsCount): array
    {
        // Tableau de cartes disponibles (images PNG)
        // Les chemins sont relatifs à public/assets/img/cards/
        $cardAssets = [
            ['name' => 'card-01', 'image' => '/assets/img/cards/01.png', 'emoji' => '🎮'],
            ['name' => 'card-02', 'image' => '/assets/img/cards/02.png', 'emoji' => '🎭'],
            ['name' => 'card-03', 'image' => '/assets/img/cards/03.png', 'emoji' => '🚁'],
            ['name' => 'card-04', 'image' => '/assets/img/cards/04.png', 'emoji' => '🎬'],
            ['name' => 'card-05', 'image' => '/assets/img/cards/05.png', 'emoji' => '🎨'],
            ['name' => 'card-06', 'image' => '/assets/img/cards/06.png', 'emoji' => '🎯'],
            ['name' => 'card-07', 'image' => '/assets/img/cards/07.png', 'emoji' => '🎲'],
            ['name' => 'card-08', 'image' => '/assets/img/cards/08.png', 'emoji' => '🎸'],
            ['name' => 'card-09', 'image' => '/assets/img/cards/09.png', 'emoji' => '⚽'],
            ['name' => 'card-10', 'image' => '/assets/img/cards/10.png', 'emoji' => '🎺'],
            ['name' => 'card-11', 'image' => '/assets/img/cards/11.png', 'emoji' => '🎻'],
            ['name' => 'card-12', 'image' => '/assets/img/cards/12.png', 'emoji' => '🥁'],
            ['name' => 'card-13', 'image' => '/assets/img/cards/13.png', 'emoji' => '🎹'],
            ['name' => 'card-14', 'image' => '/assets/img/cards/14.png', 'emoji' => '🏀'],
            ['name' => 'card-15', 'image' => '/assets/img/cards/15.png', 'emoji' => '🏈'],
            ['name' => 'card-16', 'image' => '/assets/img/cards/16.png', 'emoji' => '⚾'],
            ['name' => 'card-17', 'image' => '/assets/img/cards/17.png', 'emoji' => '🎾'],
            ['name' => 'card-18', 'image' => '/assets/img/cards/18.png', 'emoji' => '🏐'],
            ['name' => 'card-19', 'image' => '/assets/img/cards/19.png', 'emoji' => '🏉'],
            ['name' => 'card-20', 'image' => '/assets/img/cards/20.png', 'emoji' => '🥎'],
            ['name' => 'card-21', 'image' => '/assets/img/cards/21.png', 'emoji' => '🚗'],
            ['name' => 'card-22', 'image' => '/assets/img/cards/22.png', 'emoji' => '✈️'],
            ['name' => 'card-23', 'image' => '/assets/img/cards/23.png', 'emoji' => '🎪'],
            ['name' => 'card-24', 'image' => '/assets/img/cards/24.png', 'emoji' => '🚂'],
        ];

        // Prend les N premières cartes selon la difficulté
        $selectedCards = array_slice($cardAssets, 0, $pairsCount);

        // Crée des paires (chaque carte apparaît 2 fois)
        $pairs = [];
        foreach ($selectedCards as $index => $card) {
            // Paire 1
            $pairs[] = [
                'pair' => $index,
                'card' => [
                    'name' => $card['name'],
                    'image' => $card['image'],
                    'emoji' => $card['emoji'],
                ]
            ];
            // Paire 2
            $pairs[] = [
                'pair' => $index,
                'card' => [
                    'name' => $card['name'],
                    'image' => $card['image'],
                    'emoji' => $card['emoji'],
                ]
            ];
        }

        // Mélange le deck
        shuffle($pairs);

        return $pairs;
    }

    /**
     * Récupère les dimensions de la grille (colonnes x lignes)
     */
    public static function getGridDimensions(int $pairsCount): array
    {
        return match ($pairsCount) {
            3 => [3, 2],
            4 => [4, 2],
            6 => [4, 3],
            8 => [4, 4],
            10 => [5, 4],
            12 => [6, 4],
            default => [3, 2],
        };
    }

    /**
     * Temps alloué selon la difficulté
     */
    public static function getTimeAllocated(int $pairsCount): int
    {
        return match ($pairsCount) {
            3 => 60,
            4 => 90,
            6 => 120,
            8 => 150,
            10 => 180,
            12 => 210,
            default => 60,
        };
    }

    /**
     * Calcule le score final
     */
    public static function computeScore(
        int $pairsFound,
        int $errors,
        int $timeRemaining,
        int $difficulty
    ): int {
        // Score de base selon les paires trouvées
        $baseScore = $pairsFound * 100;

        // Bonus/malus erreurs
        $errorPenalty = $errors * 10;

        // Bonus temps restant
        $timeBonus = (int)($timeRemaining * 2);

        // Multiplicateur difficulté
        $difficultyMultiplier = match ($difficulty) {
            3 => 1.0,
            4 => 1.1,
            6 => 1.3,
            8 => 1.5,
            10 => 1.8,
            12 => 2.0,
            default => 1.0,
        };

        $finalScore = (int)(($baseScore - $errorPenalty + $timeBonus) * $difficultyMultiplier);

        return max(0, $finalScore); // Score minimum = 0
    }
}
