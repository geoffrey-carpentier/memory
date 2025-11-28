<?php
?>
<section class="page profile">
    <header>
        <h1>Profil de <?= htmlspecialchars($_SESSION['nickname'] ?? ''); ?></h1>
        <p>Vue d’ensemble de votre progression.</p>
    </header>

    <div class="profile__stats">
        <article>
            <h2>Parties jouées</h2>
            <strong><?= (int) ($stats['games_played'] ?? 0); ?></strong>
        </article>
        <article>
            <h2>Meilleur score</h2>
            <strong><?= (int) ($stats['best_score'] ?? 0); ?> pts</strong>
        </article>
        <article>
            <h2>Score moyen</h2>
            <strong><?= (int) round($stats['avg_score'] ?? 0); ?> pts</strong>
        </article>
    </div>

    <section class="profile__history">
        <h2>Historique</h2>
        <?php if (empty($history)): ?>
            <p>Aucune partie enregistrée pour l’instant.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th><th>Score</th><th>Paires</th><th>Erreurs</th><th>Temps restant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $game): ?>
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
        <?php endif; ?>
    </section>

    <p><a class="btn" href="/">← Retour au jeu</a></p>
</section>