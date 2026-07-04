<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Classe Card
 * -----------
 * Représente une carte individuelle du plateau de jeu Memory.
 * Deux instances de Card partagent le même $pairId : c'est ce qui
 * permet de détecter une paire côté client (data-pair="...") sans
 * dupliquer de logique de comparaison côté serveur.
 */
final class Card
{
    public function __construct(
        public readonly int $pairId,
        public readonly string $name,
        public readonly string $image,
        public readonly string $emoji
    ) {
    }

    public function toArray(): array
    {
        return [
            'pairId' => $this->pairId,
            'name'   => $this->name,
            'image'  => $this->image,
            'emoji'  => $this->emoji,
        ];
    }
}
