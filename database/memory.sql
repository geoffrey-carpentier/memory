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

-- 3) Historique des parties jouées (score + stats)
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

-- 4) Index pour accélérer les classements
CREATE INDEX idx_games_user_score ON games (user_id, final_score DESC);
CREATE INDEX idx_games_finished ON games (finished_at DESC);