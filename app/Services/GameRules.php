<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Card;

/**
 * Classe GameRules
 * ----------------
 * Gère la logique du jeu (génération de deck, calcul de score, etc.)
 */
class GameRules
{
    /**
     * Génère un deck de cartes Card mélangées.
     * Les chemins d'images sont relatifs à public/assets/img/cards/
     *
     * @param int $pairsCount Nombre de paires à générer
     * @return Card[] Tableau de cartes mélangées
     */
    public static function generateDeck(int $pairsCount): array
    {
        $cardTemplates = [
            ['name' => 'joystick',  'image' => '/assets/img/cards/joystick.svg',  'emoji' => '🕹️'],
            ['name' => 'gamepad',   'image' => '/assets/img/cards/gamepad.svg',   'emoji' => '🎮'],
            ['name' => 'arcade',    'image' => '/assets/img/cards/arcade.svg',    'emoji' => '👾'],
            ['name' => 'cartridge', 'image' => '/assets/img/cards/cartridge.svg', 'emoji' => '📼'],
            ['name' => 'disc',      'image' => '/assets/img/cards/disc.svg',      'emoji' => '💿'],
            ['name' => 'floppy',    'image' => '/assets/img/cards/floppy.svg',    'emoji' => '💾'],
            ['name' => 'ghost',     'image' => '/assets/img/cards/ghost.svg',     'emoji' => '👻'],
            ['name' => 'invader',   'image' => '/assets/img/cards/invader.svg',   'emoji' => '👽'],
            ['name' => 'dice',      'image' => '/assets/img/cards/dice.svg',      'emoji' => '🎲'],
            ['name' => 'heart',     'image' => '/assets/img/cards/heart.svg',     'emoji' => '❤️'],
            ['name' => 'rocket',    'image' => '/assets/img/cards/rocket.svg',    'emoji' => '🚀'],
            ['name' => 'trophy',    'image' => '/assets/img/cards/trophy.svg',    'emoji' => '🏆'],
        ];

        // Prend les N premiers modèles selon la difficulté
        $selectedTemplates = array_slice($cardTemplates, 0, $pairsCount);

        // Crée des paires de Card (chaque modèle apparaît 2 fois)
        $deck = [];
        foreach ($selectedTemplates as $pairId => $template) {
            $deck[] = new Card($pairId, $template['name'], $template['image'], $template['emoji']);
            $deck[] = new Card($pairId, $template['name'], $template['image'], $template['emoji']);
        }

        // Mélange le deck
        shuffle($deck);

        return $deck;
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
