<?php
/** 
 * Vue : Liste des articles
 * -------------------------
 * Cette vue reçoit une variable $articles (tableau associatif)
 * transmise par le contrôleur ArticleController.
 * Chaque entrée du tableau contient au minimum : id, title, body.
 */
?>
<h1>Articles</h1>

<ul>
  <?php if (!empty($articles)): ?>
    <?php foreach ($articles as $a): ?>
      <li>
        <!-- On sécurise l'affichage pour éviter les failles XSS -->
        <?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?>
      </li>
    <?php endforeach; ?>
  <?php else: ?>
    <li>Aucun article disponible.</li>
  <?php endif; ?>
</ul>
