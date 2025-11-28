<?php
// filepath: d:\TOOLS\LARAGON\www\memory\app\Views\ranking\index.php
?>
<section class="leaderboard">
    <header>
        <h1> — Légendes du Memory</h1>
        <p>Classement basé sur les meilleurs scores deu Top 10 joueurs.</p>
    </header>

    <ol class="ranking__table">
        <?php foreach ($topScores as $index => $score): ?>
            <li class="ranking__row ranking__row--<?= $index < 3 ? 'podium' : 'classic'; ?>">
                <span class="ranking__rank"><?= $index + 1; ?></span>
                <span class="ranking__player"><?= htmlspecialchars($score['nickname']); ?></span>
                <span class="ranking__points"><?= (int) $score['final_score']; ?> pts</span>
                <span class="ranking__meta"><?= (int) $score['difficulty']; ?> paires — <?= date('d/m/Y', strtotime($score['finished_at'])); ?></span>
            </li>
        <?php endforeach; ?>
    </ol>

    <?php if (!empty($userGames)): ?>
        <section class="ranking__history">
            <h2>Vos dernières parties</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th><th>Score</th><th>Paires</th><th>Erreurs</th><th>Temps restant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userGames as $game): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($game['finished_at'])); ?></td>
                            <td><?= (int) $game['final_score']; ?></td>
                            <td><?= (int) $game['difficulty']; ?></td>
                            <td><?= (int) $game['errors']; ?></td>
                            <td><?= (int) $game['time_remaining']; ?> s</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    <?php else: ?>
        <p class="ranking__hint">Connectez-vous pour voir vos statistiques personnelles.</p>
    <?php endif; ?>

    <p><a class="btn" href="/">← Retour au jeu</a></p>
</section>