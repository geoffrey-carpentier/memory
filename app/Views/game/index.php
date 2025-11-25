<?php

/**
 * Vue principale du Memory (mode 'arcade')
 * - formulaire pseudo + difficulté
 * - affichage de la partie en cours (si $_SESSION['current_game'] défini)
 * - panneaux classement et historique
 */
$currentGame = $_SESSION['current_game'] ?? null;
$deck        = $currentGame['deck'] ?? [];
$pairsTotal  = $currentGame['pairs_total'] ?? (int) (count($deck) / 2);

// mapping officiel
$columnsMap = [3=>3, 4=>4, 6=>4, 8=>4, 10=>5, 12=>6];
$rowsMap    = [3=>2, 4=>2, 6=>3, 8=>4, 10=>4, 12=>4];

$columns = $columnsMap[$pairsTotal] ?? 4;
$rows    = $rowsMap[$pairsTotal] ?? 3;
?>
<section class="memory">
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert--error"><?= htmlspecialchars($_SESSION['flash_error']); ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert--success"><?= htmlspecialchars($_SESSION['flash_success']); ?></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <header class="memory__intro">
        <h1>Memory Time-Attack</h1>
        <p>Entrez un pseudo, choisissez la difficulté, jouez immédiatement.</p>
    </header>

    <form class="memory__start" method="post" action="/start">
        <label>
            Joueur
            <input type="text" name="nickname" maxlength="20" value="<?= htmlspecialchars($nickname ?? '') ?>" required>
        </label>

        <label>
            Difficulté (nombre de cartes)
            <select name="difficulty" required>
                <?php foreach ([3,4,6,8,10,12] as $pairs): ?>
                    <option value="<?= $pairs ?>"><?= $pairs ?> paires</option>
                <?php endforeach; ?>
            </select>
        </label>

        <button type="submit">Jouer!</button>
    </form>

    <?php if ($currentGame): ?>
        <section class="memory__board">
            <header>
                <h2>Partie en cours</h2>
                <p><?= htmlspecialchars($_SESSION['nickname']); ?> — <?= $pairsTotal; ?> paires — chrono initial <?= $currentGame['time_allocated']; ?> s</p>
            </header>

            <div class="board-stats">
                <span id="stat-pairs">Paires trouvées : 0 / <?= $currentGame['pairs_total']; ?></span>
                <span id="stat-errors">Erreurs : 0</span>
                <span id="stat-timer">Temps restant : <?= $currentGame['time_allocated']; ?> s</span>
            </div>

            <div
                class="board-grid"
                data-total-pairs="<?= $pairsTotal; ?>"
                data-time="<?= $currentGame['time_allocated']; ?>"
                style="
                    grid-template-columns: repeat(<?= $columns; ?>, 110px);
                    grid-template-rows: repeat(<?= $rows; ?>, 140px);
                "
            >
                <?php foreach ($deck as $index => $item): ?>
                    <button
                        type="button"
                        class="card"
                        data-index="<?= $index; ?>"
                        data-pair="<?= htmlspecialchars($item['pair']); ?>"
                        aria-label="Carte numéro <?= $index + 1; ?>"
                    >
                        <span class="card__face card__face--back">?</span>
                        <span class="card__face card__face--front">
                            <img src="<?= htmlspecialchars($item['card']['image_path']); ?>" alt="<?= htmlspecialchars($item['card']['label']); ?>">
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>

            <form id="finish-form" class="finish-form" method="post" action="/finish">
                <input type="hidden" name="pairs_found" value="0">
                <input type="hidden" name="errors" value="0">
                <input type="hidden" name="time_remaining" value="<?= $currentGame['time_allocated']; ?>">
            </form>
        </section>
    <?php endif; ?>

    <section class="memory__sidebar">
        <article class="panel">
            <h3>Top 10 des Légendes du Memory</h3>
            <ol>
                <?php foreach ($topScores as $score): ?>
                    <li>
                        <strong><?= htmlspecialchars($score['nickname']); ?></strong>
                        <span><?= (int) $score['final_score']; ?> points</span>
                        <small><?= (int) $score['difficulty']; ?> paires</small>
                    </li>
                <?php endforeach; ?>
            </ol>
        </article>

        <?php if (!empty($userGames)): ?>
            <article class="panel">
                <h3>Vos dernières parties</h3>
                <ul>
                    <?php foreach ($userGames as $game): ?>
                        <li>
                            <?= (int) $game['final_score']; ?> pts —
                            <?= (int) $game['difficulty']; ?> paires —
                            <?= (int) $game['errors']; ?> erreurs
                        </li>
                    <?php endforeach; ?>
                </ul>
            </article>
        <?php endif; ?>
    </section>
</section>

<?php if ($currentGame): ?>
    <script src="/assets/js/memory.js" defer></script>
<?php endif; ?>