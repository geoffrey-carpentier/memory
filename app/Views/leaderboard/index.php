<?php

// Vue pour la page du classement : Top 10 joueurs

use Core\Security;
?>

<section class="page leaderboard">
    <header class="leaderboard__header">
        <h1>🏆 Légendes du Memory</h1>
        <p>Classement des 10 meilleurs joueurs par score cumulé</p>
    </header>

    <!-- === TOP 10 EN TABLEAU === -->
    <div class="leaderboard__container">
        <table class="leaderboard__table">
            <thead>
                <tr>
                    <th class="leaderboard__th--rank">Rang</th>
                    <th class="leaderboard__th--player">Joueur</th>
                    <th class="leaderboard__th--score">Score</th>
                    <th class="leaderboard__th--meta">Paires</th>
                    <th class="leaderboard__th--meta">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topScores as $index => $score): 
                    $medal = match($index) {
                        0 => '🥇',
                        1 => '🥈',
                        2 => '🥉',
                        default => null
                    };
                    $podiumClass = $index < 3 ? 'is-podium is-podium--' . match($index) {
                        0 => 'gold',
                        1 => 'silver',
                        2 => 'bronze',
                    } : '';
                ?>
                    <tr class="leaderboard__row <?= $podiumClass; ?>">
                        <td class="leaderboard__rank">
                            <span class="leaderboard__medal"><?= $medal ?? ($index + 1); ?></span>
                        </td>
                        <td class="leaderboard__player">
                            <span class="leaderboard__player-name">
                                <?= htmlspecialchars($score['nickname']); ?>
                            </span>
                        </td>
                        <td class="leaderboard__score">
                            <strong><?= (int) $score['final_score']; ?></strong>
                            <span class="leaderboard__score-unit">pts</span>
                        </td>
                        <td class="leaderboard__meta">
                            <?= (int) $score['difficulty']; ?> paires
                        </td>
                        <td class="leaderboard__meta">
                            <?= date('d/m/Y', strtotime($score['finished_at'])); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- === HISTORIQUE PERSONNEL (si connecté) === -->
    <?php if (!empty($userGames)): ?>
        <section class="leaderboard__history">
            <h2>📋 Vos dernières parties</h2>
            
            <div class="history__summary">
                <div class="history__stat">
                    <span class="history__label">Parties jouées</span>
                    <strong class="history__value"><?= count($userGames); ?></strong>
                </div>
                <div class="history__stat">
                    <span class="history__label">Meilleur score</span>
                    <strong class="history__value">
                        <?= (int) max(array_map(fn($g) => $g['final_score'], $userGames)); ?>
                    </strong>
                </div>
            </div>

            <table class="history__table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Score</th>
                        <th>Paires</th>
                        <th>Erreurs</th>
                        <th>Temps restant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userGames as $game): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($game['finished_at'])); ?></td>
                            <td class="history__score"><?= (int) $game['final_score']; ?> pts</td>
                            <td><?= (int) $game['difficulty']; ?></td>
                            <td><?= (int) $game['errors']; ?></td>
                            <td><?= (int) ($game['time_remaining'] ?? 0); ?> s</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    <?php else: ?>
        <div class="leaderboard__hint">
            <p>👤 <a href="/" class="btn btn--secondary">Connecte-toi</a> pour voir tes statistiques personnelles.</p>
        </div>
    <?php endif; ?>

    <div class="leaderboard__actions">
        <a href="/" class="btn btn--primary">← Retour au jeu</a>
    </div>
</section>