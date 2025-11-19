<?php
/**
 * Vue : Détail d'un article
 * --------------------------
 * Cette vue reçoit une variable $article (tableau associatif)
 * transmise par le contrôleur ArticleController.
 * $article contient au minimum : id, title, body.
 */
?>
<h1><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></h1>

<p>
  <!-- 
    On utilise htmlspecialchars() pour éviter les failles XSS.
    nl2br() permet de conserver les retours à la ligne du contenu en <br>. 
  -->
  <?= nl2br(htmlspecialchars($article['body'], ENT_QUOTES, 'UTF-8')) ?>
</p>

<!-- Lien retour vers la liste des articles -->
<p>
  <a href="/articles">← Retour à la liste des articles</a>
</p>
