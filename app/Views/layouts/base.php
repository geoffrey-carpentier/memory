<?php

/**
 * Layout principal
 * ================
 * Structure HTML commune à toutes les pages du projet Memory.
 * - Header avec navigation
 * - Contenu dynamique injecté via $content
 * - Footer avec informations légales et lien GitHub
 * - Assets CSS/JS globaux
 */

use Core\Security;

$currentPage = $_SERVER['REQUEST_URI'] ?? '/';
?>
<!doctype html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Memory - Jeu de mémoire solo contre la montre">
  <meta name="theme-color" content="#111827">

  <!-- Titre sécurisé et explicite -->
  <title><?= isset($title) ? Security::escape($title) : 'Memory — Jeu de mémoire'; ?></title>

  <!-- Favicon (à créer) -->
  <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">

  <!-- Styles globaux -->
  <link rel="stylesheet" href="/assets/css/global.css">
</head>

<body>
  <!-- === HEADER === -->
  <header class="site-header">
    <div class="site-header__container">
      <!-- Logo / Titre du site -->
      <div class="site-header__branding">
        <a href="/" class="site-logo" aria-label="Memory - Accueil">
          🧠 Memory
        </a>
      </div>

      <!-- Navigation principale -->
      <nav class="site-nav" aria-label="Navigation principale">
        <ul class="site-nav__list">
          <li>
            <a
              href="/"
              class="site-nav__link <?= $currentPage === '/' ? 'is-active' : ''; ?>"
              aria-current="<?= $currentPage === '/' ? 'page' : 'false'; ?>">
              🎮 Jouer
            </a>
          </li>
          <li>
            <a
              href="/leaderboard"
              class="site-nav__link <?= strpos($currentPage, '/leaderboard') === 0 ? 'is-active' : ''; ?>"
              aria-current="<?= strpos($currentPage, '/leaderboard') === 0 ? 'page' : 'false'; ?>">
              🏆 Classement
            </a>
          </li>
          <li>
            <a
              href="/profile"
              class="site-nav__link <?= strpos($currentPage, '/profile') === 0 ? 'is-active' : ''; ?>"
              aria-current="<?= strpos($currentPage, '/profile') === 0 ? 'page' : 'false'; ?>">
              👤 Profil
            </a>
          </li>
        </ul>
      </nav>

      <!-- Lien GitHub -->
      <div class="site-header__social">
        <a
          href="https://github.com/geoffrey-carpentier/memory"
          class="site-header__github"
          title="Code source sur GitHub"
          target="_blank"
          rel="noopener noreferrer"
          aria-label="Voir le code source sur GitHub">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v 3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
          </svg>
        </a>
      </div>
    </div>
  </header>

  <!-- === CONTENU PRINCIPAL === -->
  <main class="site-main">
    <?= $content; ?>
  </main>

  <!-- === FOOTER === -->
  <footer class="site-footer">
    <div class="site-footer__container">
      <p>
        <strong>Memory</strong> — Jeu de mémoire solo<br>
        Projet pédagogique | PHP 8+ | MVC | MySQL<br>
        <a href="https://github.com/geoffrey-carpentier/memory" target="_blank" rel="noopener">
          Code source
        </a>
      </p>
    </div>
  </footer>

  <!-- Scripts globaux -->
  <script>
    // Initialise le token CSRF si besoin (pour les formulaires XHR futurs)
    window.CSRF_TOKEN = '<?= $_SESSION['csrf_token'] ?? ''; ?>';
  </script>
</body>

</html>