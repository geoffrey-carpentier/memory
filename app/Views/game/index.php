<?php

/**
 * filepath: d:\TOOLS\LARAGON\www\memory\app\Views\game\index.php
 * 
 * Vue principale du jeu Memory.
 * Affiche le formulaire de démarrage OU la partie en cours OU le résumé.
 * Pas de classement ici (voir /ranking).
 */

use App\Services\GameRules;
use Core\Security;

// Récupération des données de session
$currentGame = $_SESSION['current_game'] ?? null;
$deck        = $currentGame['deck'] ?? [];
$pairsTotal  = $currentGame['pairs_total'] ?? 0;

// Calcul des dimensions de la grille via service dédié
[$columns, $rows] = !empty($pairsTotal) ? GameRules::getGridDimensions($pairsTotal) : [3, 2];

// Résumé de la dernière partie (pour affichage post-partie)
$lastGameSummary = $_SESSION['last_game_summary'] ?? null;
?>

<section class="memory">
    <!-- === ZONE ALERTES === -->
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert--error" role="alert">
            <?= htmlspecialchars($_SESSION['flash_error']); ?>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert--success" role="alert">
            <?= htmlspecialchars($_SESSION['flash_success']); ?>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <!-- === EN-TÊTE PRINCIPAL === -->
    <header class="memory__intro">
        <h1>Memory Time-Attack</h1>
        <p>Entrez votre pseudo, choisissez la difficulté, jouez !</p>
    </header>

    <!-- === FORMULAIRE DE DÉMARRAGE (caché si partie en cours) === -->
    <?php if (!$currentGame): ?>
        <form class="memory__start" method="post" action="/start">
            <!-- Protection CSRF : token généré et vérifié côté serveur -->
            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken(); ?>">

            <label for="nickname">Joueur</label>
            <input
                type="text"
                id="nickname"
                name="nickname"
                maxlength="20"
                placeholder="Votre pseudo..."
                value="<?= htmlspecialchars($_SESSION['nickname'] ?? ''); ?>"
                required
                aria-required="true">

            <label for="difficulty">Difficulté (nombre de paires)</label>
            <select id="difficulty" name="difficulty" required aria-required="true">
                <option value="">-- Choisir --</option>
                <?php foreach ([3, 4, 6, 8, 10, 12] as $pairs): ?>
                    <option value="<?= $pairs; ?>"><?= $pairs; ?> paires</option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn--primary">Jouer!</button>
        </form>
    <?php endif; ?>

    <!-- === ZONE JEU (visible seulement si partie en cours) === -->
    <?php if ($currentGame): ?>
        <section class="memory__board" aria-label="Zone de jeu active">
            <header>
                <h2>Partie en cours</h2>
                <p>
                    <strong><?= htmlspecialchars($_SESSION['nickname'] ?? 'Joueur'); ?></strong> —
                    <?= $pairsTotal; ?> paires —
                    Chrono initial : <?= $currentGame['time_allocated']; ?> s
                </p>
            </header>

            <!-- Barre de statistiques en temps réel -->
            <div class="board-stats" aria-live="polite" aria-atomic="true">
                <span id="stat-pairs" class="stat stat--pairs">
                    Paires trouvées : 0 / <?= $pairsTotal; ?>
                </span>
                <span id="stat-errors" class="stat stat--errors">
                    Erreurs : 0
                </span>
                <span id="stat-timer" class="stat stat--timer">
                    Temps restant : <?= $currentGame['time_allocated']; ?> s
                </span>
                <button
                    type="button"
                    id="pause-btn"
                    class="btn btn--pause"
                    aria-pressed="false"
                    title="Pause (barre espace)">
                    ⏸ Pause
                </button>
            </div>

            <!-- Zone de messages flash (succès/échec paires) -->
            <div
                id="board-toast"
                class="board-toast"
                aria-live="assertive"
                aria-atomic="true"
                role="status"></div>

            <!-- Grille de cartes : dimensions calculées dynamiquement -->
            <div
                class="board-grid"
                data-total-pairs="<?= $pairsTotal; ?>"
                data-time="<?= $currentGame['time_allocated'] ?? 0; ?>"
                style="
                    grid-template-columns: repeat(<?= $columns; ?>, 110px);
                    grid-template-rows: repeat(<?= $rows; ?>, 150px);
                "
                role="region"
                aria-label="Grille de jeu">
                <?php foreach ($deck as $index => $item): ?>
                    <button
                        type="button"
                        class="card"
                        data-index="<?= $index; ?>"
                        data-pair="<?= htmlspecialchars($item['pair']); ?>"
                        aria-label="Carte <?= ($index + 1); ?> sur <?= count($deck); ?>"
                        tabindex="0">
                        <!-- Face cachée (dos) -->
                        <span class="card__face card__face--back" aria-hidden="true">?</span>

                        <!-- Face visible (image) -->
                        <span class="card__face card__face--front">
                            <img
                                src="<?= htmlspecialchars($item['card']['image_path']); ?>"
                                alt="<?= htmlspecialchars($item['card']['label']); ?>"
                                loading="lazy">
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Formulaire caché : données envoyées au serveur lors de la fin de partie -->
            <form id="finish-form" class="finish-form" method="post" action="/finish">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken(); ?>">
                <input type="hidden" name="pairs_found" value="0">
                <input type="hidden" name="errors" value="0">
                <input type="hidden" name="time_remaining" value="<?= $currentGame['time_allocated']; ?>">
                <input type="hidden" name="result_type" value="win">
            </form>
        </section>
    <?php endif; ?>

    <!-- === RÉSUMÉ POST-PARTIE (visible si partie terminée) === -->
    <?php if (!$currentGame && $lastGameSummary): ?>
        <section class="memory__summary" aria-label="Résumé de la dernière partie">
            <h2>Résumé de la dernière partie</h2>
            <ul class="summary-list">
                <li>
                    <span class="summary-label">Score :</span>
                    <strong class="summary-value"><?= (int) $lastGameSummary['score']; ?> pts</strong>
                </li>
                <li>
                    <span class="summary-label">Paires trouvées :</span>
                    <strong class="summary-value">
                        <?= $lastGameSummary['pairs_found']; ?>/<?= $lastGameSummary['pairs_total']; ?>
                    </strong>
                </li>
                <li>
                    <span class="summary-label">Erreurs :</span>
                    <strong class="summary-value"><?= $lastGameSummary['errors']; ?></strong>
                </li>
                <li>
                    <span class="summary-label">Temps restant :</span>
                    <strong class="summary-value"><?= $lastGameSummary['time_remaining']; ?> s</strong>
                </li>
            </ul>

            <div class="summary-actions">
                <a href="/ranking" class="btn btn--secondary">Voir le classement</a>
                <a href="/" class="btn btn--primary">Nouvelle partie</a>
            </div>
        </section>

        <?php unset($_SESSION['last_game_summary']); ?>
    <?php endif; ?>
</section>

<!-- Inclusion du script de jeu uniquement si une partie est en cours -->
<?php if ($currentGame): ?>
    <script src="/assets/js/memory.js" defer></script>
<?php endif; ?>