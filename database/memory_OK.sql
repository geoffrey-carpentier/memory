-- ===========================================================
-- Script de référence pour le projet MEMORY (mode arcade)
-- ===========================================================

-- 1) Création de la base si elle n’existe pas déjà
CREATE DATABASE IF NOT EXISTS memory CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE memory;

-- 2) Table des joueurs (pseudo unique, pas de mot de passe)
CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nickname    VARCHAR(30) NOT NULL UNIQUE,     -- pseudo saisi avant de jouer
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3) Catalogue des cartes disponibles (motifs réutilisables)
CREATE TABLE IF NOT EXISTS cards (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug        VARCHAR(50) NOT NULL UNIQUE,     -- identifiant technique (ex: demon_hunter)
    label       VARCHAR(80) NOT NULL,            -- nom lisible (ex: Chasseur de démon)
    image_path  VARCHAR(255) NOT NULL,           -- chemin vers l’asset (ex: /assets/img/cards/demon.png)
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4) Historique des parties jouées (score + stats)
CREATE TABLE IF NOT EXISTS games (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL,                   -- lien vers le joueur
    difficulty      TINYINT UNSIGNED NOT NULL,               -- nombre de paires (3,4,6,8,10,12)
    pairs_total     TINYINT UNSIGNED NOT NULL,               -- rappel du nombre total de paires
    pairs_found     TINYINT UNSIGNED DEFAULT 0,              -- paires effectivement trouvées
    errors          SMALLINT UNSIGNED DEFAULT 0,             -- nombre de mauvaises tentatives
    time_allocated  SMALLINT UNSIGNED NOT NULL,              -- durée du chrono en secondes
    time_spent      SMALLINT UNSIGNED DEFAULT 0,             -- temps réellement écoulé
    time_remaining  SMALLINT UNSIGNED DEFAULT 0,             -- temps restant à la fin
    final_score     INT DEFAULT NULL,                        -- score calculé via la formule officielle
    status ENUM('in_progress','finished','aborted') NOT NULL DEFAULT 'in_progress',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    finished_at     DATETIME DEFAULT NULL,
    CONSTRAINT fk_games_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5) Disposition facultative des cartes pour chaque partie (utile pour rejouer/analyser)
CREATE TABLE IF NOT EXISTS game_cards (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    game_id       INT UNSIGNED NOT NULL,
    card_id       INT UNSIGNED NOT NULL,
    position_row  TINYINT UNSIGNED NOT NULL,     -- ligne de la grille (0-index ou 1-index à ton choix)
    position_col  TINYINT UNSIGNED NOT NULL,     -- colonne correspondante
    pair_token    CHAR(8) NOT NULL,              -- identifiant commun aux deux cartes d’une paire
    revealed_at   DATETIME DEFAULT NULL,         -- horodatage optionnel lors de la découverte
    matched_at    DATETIME DEFAULT NULL,         -- horodatage quand la paire est validée
    CONSTRAINT fk_gc_game FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    CONSTRAINT fk_gc_card FOREIGN KEY (card_id) REFERENCES cards(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 6) Index pratiques pour les classements et statistiques
CREATE INDEX idx_games_score ON games (final_score DESC, finished_at DESC);
CREATE INDEX idx_games_user ON games (user_id, finished_at DESC);
-- Index pour accélérer les classements
CREATE INDEX idx_games_user_score ON games (user_id, final_score DESC);
CREATE INDEX idx_games_finished ON games (finished_at DESC);
-- 7) Jeu d’exemples pour la table cards (à adapter selon tes visuels)
USE memory;
INSERT INTO cards (slug, label, image_path) VALUES
   # ('arcane_mage',     'Mage arcanique',        '/assets/img/cards/arcane_mage.png'),
   # ('blood_hunter',    'Chasseur sanguin',      '/assets/img/cards/blood_hunter.png'),
   # ('forest_guardian', 'Gardien sylvestre',     '/assets/img/cards/forest_guardian.png'),
   # ('shadow_assassin', 'Assassin de l’ombre',   '/assets/img/cards/shadow_assassin.png'),
   # ('stone_golem',     'Golem de pierre',       '/assets/img/cards/stone_golem.png'),
   # ('storm_rider',     'Cavalier de tempête',   '/assets/img/cards/storm_rider.png'),
   # ('water_spirit',    'Esprit de l’eau',       '/assets/img/cards/water_spirit.png'),
   # ('fire_elemental',  'Élémentaire de feu',    '/assets/img/cards/fire_elemental.png'),
    ('light_paladin',   'Paladin de lumière',    '/assets/img/cards/light_paladin.png'),
    ('dark_necromancer','Nécromancien des ténèbres','/assets/img/cards/dark_necromancer.png'),
    ('wind_dancer',    'Danseur du vent',       '/assets/img/cards/wind_dancer.png'),
    ('earth_shaman',    'Chaman de la terre',    '/assets/img/cards/earth_shaman.png');

-- (Supprime ou remplace ces INSERT selon les thèmes finalement choisis)