<?php

/**
 * filepath: d:\TOOLS\LARAGON\www\memory\app\Views\game\index.php
 * 
 * Page d'accueil du jeu Memory
 */

use App\Services\GameRules;
use Core\Security;

// Récupération des données de session
$currentGame = $_SESSION['current_game'] ?? null;
$deck        = $currentGame['deck'] ?? [];
$pairsTotal  = $currentGame['pairs_total'] ?? 0;

// Calcul des dimensions de la grille
[$columns, $rows] = !empty($pairsTotal) ? GameRules::getGridDimensions($pairsTotal) : [3, 2];
?>

<section class="memory <?= $currentGame ? 'memory--playing' : ''; ?>">
    <!-- === SECTION INTRO + FORMULAIRE === -->
    <?php if (!$currentGame): ?>
        <div class="memory__welcome">
            <!-- Header intro -->
            <header class="memory__header">
                <h1>Memory</h1>
                <p>Entrez votre pseudo, selectionnez la difficulté, jouez !</p>
            </header>

            <!-- Formulaire de démarrage -->
            <form class="memory__form" method="post" action="/start">
                <!-- Protection CSRF -->
                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken(); ?>">

                <!-- Conteneur flex pour les deux champs -->
                <div class="form-row">
                    <!-- Groupe 1 : Pseudo -->
                    <div class="form-group">
                        <label for="nickname" class="form-label">Joueur</label>
                        <input
                            type="text"
                            id="nickname"
                            name="nickname"
                            class="form-input"
                            maxlength="20"
                            placeholder="Votre pseudo..."
                            value="<?= htmlspecialchars($_SESSION['nickname'] ?? ''); ?>"
                            required
                            aria-required="true"
                            aria-describedby="nickname-hint">
                        <small id="nickname-hint" class="form-hint">2-20 caractères</small>
                    </div>

                    <!-- Groupe 2 : Difficulté -->
                    <div class="form-group">
                        <label for="difficulty" class="form-label">Difficulté</label>
                        <select
                            id="difficulty"
                            name="difficulty"
                            class="form-input"
                            required
                            aria-required="true"
                            aria-describedby="difficulty-hint">
                            <option value="">-- Choisir --</option>
                            <?php foreach ([3, 4, 6, 8, 10, 12] as $pairs): ?>
                                <option value="<?= $pairs; ?>"><?= $pairs; ?> paires</option>
                            <?php endforeach; ?>
                        </select>
                        <small id="difficulty-hint" class="form-hint">Nombre de paires</small>
                    </div>
                </div>

                <!-- Bouton -->
                <button type="submit" class="btn btn--primary btn--large btn--block">
                    🎮 Jouer!
                </button>
            </form>
        </div>

        <!-- === SECTION JEUX EN COURS === -->
    <?php else: ?>
        <div class="memory__game-active">
            <!-- Stats du jeu -->
            <div class="board-stats">
                <div class="stat">
                    <span>Joueur: <strong><?= htmlspecialchars($_SESSION['nickname'] ?? 'Anonyme'); ?></strong></span>
                </div>
                <div class="stat">
                    <span id="stat-pairs">Paires trouvées : 0 / <?= $pairsTotal; ?></span>
                </div>
                <div class="stat">
                    <span id="stat-errors">Erreurs : 0</span>
                </div>
                <div class="stat stat--timer">
                    <span id="stat-timer">Temps restant : <?= GameRules::getTimeAllocated($pairsTotal); ?> s</span>
                </div>
                <button type="button" id="sound-btn" class="btn btn--sound" aria-pressed="false">🔊 Son</button>
                <button type="button" id="pause-btn" class="btn btn--pause">⏸ Pause</button>
            </div>

            <!-- Grille de jeu -->
            <div class="board-grid"
                data-total-pairs="<?= $pairsTotal; ?>"
                data-time="<?= GameRules::getTimeAllocated($pairsTotal); ?>"
                style="grid-template-columns: repeat(<?= $columns; ?>, 1fr); --cols: <?= $columns; ?>; --rows: <?= $rows; ?>;">

                <?php
                // Chaque paire est distinguée par son emoji : les couleurs natives des emojis
                // sont bien plus contrastées entre elles qu'une même icône simplement teintée.
                foreach ($deck as $index => $card):
                    $emoji = $card->emoji ?: '❓';
                ?>
                    <button class="card" data-pair="<?= htmlspecialchars((string) $card->pairId); ?>">
                        <div class="card__face card__face--back">?</div>
                        <div class="card__face card__face--front">
                            <span class="card__emoji"><?= $emoji; ?></span>
                        </div>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Toast notification -->
            <div id="board-toast" class="board-toast" role="status" aria-live="polite"></div>

            <!-- Formulaire caché pour la fin de partie -->
            <form id="finish-form" method="post" action="/finish" style="display: none;">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken(); ?>">
                <input type="hidden" name="pairs_found" value="">
                <input type="hidden" name="errors" value="">
                <input type="hidden" name="time_remaining" value="">
                <input type="hidden" name="result_type" value="">
            </form>
        </div>
    <?php endif; ?>

    <!-- === RÉSUMÉ POST-PARTIE === -->
    <?php if (!empty($_SESSION['last_game_summary']) && !$currentGame): ?>
        <div class="memory__summary">
            <h2>
                <?php
                $summary = $_SESSION['last_game_summary'];
                $resultType = $summary['result_type'] ?? 'unknown';

                if ($resultType === 'win') {
                    echo '🎉 Partie complétée !';
                } elseif ($resultType === 'timeout') {
                    echo '⏱️ Temps écoulé !';
                } else {
                    echo '📊 Résumé de la partie';
                }
                ?>
            </h2>

            <ul class="summary-list">
                <li>
                    <span class="summary-label">Score</span>
                    <span class="summary-value"><?= (int) $summary['score']; ?> pts</span>
                </li>
                <li>
                    <span class="summary-label">Paires trouvées</span>
                    <span class="summary-value">
                        <?= $summary['pairs_found']; ?> / <?= $summary['pairs_total']; ?>
                    </span>
                </li>
                <li>
                    <span class="summary-label">Erreurs</span>
                    <span class="summary-value"><?= $summary['errors']; ?></span>
                </li>
                <li>
                    <span class="summary-label">Temps restant</span>
                    <span class="summary-value"><?= $summary['time_remaining']; ?> s</span>
                </li>
            </ul>

            <div class="summary-actions">
                <a href="/leaderboard" class="btn btn--secondary">Voir le classement</a>
                <a href="/" class="btn btn--primary">Nouvelle partie</a>
            </div>

            <?php unset($_SESSION['last_game_summary']); ?>
        </div>
    <?php endif; ?>
</section>

<!-- Scripts -->
<script src="/assets/js/sound.js" defer></script>
<script src="/assets/js/memory.js" defer></script>