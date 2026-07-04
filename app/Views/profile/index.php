<?php

// Page de profil utilisateur

use Core\Security;

// Données utilisateur
$userId = $_SESSION['user_id'] ?? null;
$nickname = $_SESSION['nickname'] ?? 'Inconnu';
$userStats = $userStats ?? [];
$userGames = $userGames ?? [];
?>

<section class="page profile">
    <!-- === HEADER === -->
    <header class="profile__header">
        <h1>👤 Mon Profil</h1>
        <p class="profile__username"><?= htmlspecialchars($nickname); ?></p>
    </header>

    <!-- === STATISTIQUES PRINCIPALES === -->
    <div class="profile__stats">
        <article class="stat-card">
            <h2 class="stat-card__label">Parties jouées</h2>
            <strong class="stat-card__value"><?= (int) ($userStats['games_played'] ?? 0); ?></strong>
        </article>

        <article class="stat-card">
            <h2 class="stat-card__label">Meilleur score</h2>
            <strong class="stat-card__value"><?= (int) ($userStats['best_score'] ?? 0); ?></strong>
            <span class="stat-card__unit">pts</span>
        </article>

        <article class="stat-card">
            <h2 class="stat-card__label">Score moyen</h2>
            <strong class="stat-card__value"><?= (int) ($userStats['avg_score'] ?? 0); ?></strong>
            <span class="stat-card__unit">pts</span>
        </article>
    </div>

    <!-- === HISTORIQUE DES PARTIES === -->
    <?php if (!empty($userGames)): ?>
        <section class="profile__history">
            <h2 class="profile__subtitle">📋 Historique de vos parties</h2>

            <div class="history__container">
                <table class="history__table">
                    <thead>
                        <tr>
                            <th class="history__th--date">Date</th>
                            <th class="history__th--score">Score</th>
                            <th class="history__th--difficulty">Difficulté</th>
                            <th class="history__th--errors">Erreurs</th>
                            <th class="history__th--time">Temps restant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userGames as $game): ?>
                            <tr class="history__row">
                                <td class="history__date">
                                    <?= date('d/m/Y H:i', strtotime($game['finished_at'])); ?>
                                </td>
                                <td class="history__score">
                                    <strong><?= (int) $game['final_score']; ?></strong>
                                    <span class="history__unit">pts</span>
                                </td>
                                <td class="history__difficulty">
                                    <?= (int) $game['difficulty']; ?> paires
                                </td>
                                <td class="history__errors">
                                    <?= (int) $game['errors']; ?>
                                </td>
                                <td class="history__time">
                                    <?= (int) ($game['time_remaining'] ?? 0); ?> s
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php else: ?>
        <div class="profile__empty">
            <p>📊 Vous n'avez pas encore joué de parties.</p>
            <a href="/" class="btn btn--primary">Commencer une partie</a>
        </div>
    <?php endif; ?>

    <!-- === ACTIONS === -->
    <div class="profile__actions">
        <a href="/" class="btn btn--primary">← Retour au jeu</a>
        <a href="/leaderboard" class="btn btn--secondary">Voir le classement</a>
    </div>
</section>