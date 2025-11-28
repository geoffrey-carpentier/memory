<?php
// filepath: d:\TOOLS\LARAGON\www\memory\app\Services\GameRules.php
namespace App\Services;

class GameRules
{
    /**
     * Mapping : nombre de paires → dimensions grille [colonnes, lignes].
     */
    private const GRID_DIMENSIONS = [
        3 => [3, 2],   // 3 paires → 3 × 2
        4 => [4, 2],   // 4 paires → 4 × 2
        6 => [4, 3],   // 6 paires → 4 × 3
        8 => [4, 4],   // 8 paires → 4 × 4
        10 => [5, 4],  // 10 paires → 5 × 4
        12 => [6, 4],  // 12 paires → 6 × 4
    ];

    /**
     * Retourne [colonnes, lignes] pour un nombre de paires.
     * @throws \InvalidArgumentException si paires invalide
     */
    public static function getGridDimensions(int $pairs): array
    {
        if (!isset(self::GRID_DIMENSIONS[$pairs])) {
            throw new \InvalidArgumentException(
                "Nombre de paires invalide : {$pairs}. Acceptés : " 
                . implode(', ', array_keys(self::GRID_DIMENSIONS))
            );
        }
        return self::GRID_DIMENSIONS[$pairs];
    }

    /**
     * Valide la difficulté choisie.
     */
    public static function isValidDifficulty(int $difficulty): bool
    {
        return isset(self::GRID_DIMENSIONS[$difficulty]);
    }

    /**
     * Retourne le temps alloué selon la difficulté.
     */
    public static function getTimeAllocated(int $pairs): int
    {
        return match ($pairs) {
            3 => 45,
            4 => 60,
            6 => 90,
            8 => 120,
            10 => 150,
            12 => 200,
            default => 90,
        };
    }

    /**
     * Calcule le score final avec multiplicateur de difficulté.
     */
    public static function computeScore(
        int $pairsFound,
        int $errors,
        int $timeRemaining,
        int $pairsTotal = 3
    ): int {
        $base = ($pairsFound * 100) + ($timeRemaining * 10) - ($errors * 25);

        $multipliers = [
            3 => 1.0,
            4 => 1.2,
            6 => 1.4,
            8 => 1.6,
            10 => 1.8,
            12 => 2.0,
        ];

        $factor = $multipliers[$pairsTotal] ?? 1.0;
        return (int) round($base * $factor);
    }
}
