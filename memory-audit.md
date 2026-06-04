# 🎯 Audit Technique du Projet Memory - Dossier Professionnel DWWM (RNCP 37674)

## **A. Résumé technique et Algorithmique**

Le projet Memory est une application web PHP structurée selon le modèle MVC, avec une logique de jeu gérée côté serveur via le service `App\Services\GameRules` et côté client via JavaScript (`public/assets/js/memory.js`).

**Gestion des paires et génération de la grille :**
J'ai conçu la logique de génération des cartes pour le jeu dans le service `App\Services\GameRules::generateDeck()`. Cette méthode sélectionne un nombre de cartes (en fonction de la difficulté choisie par le joueur), les duplique pour former les paires, puis les mélange aléatoirement pour créer le "deck" de la partie. Chaque carte est un tableau associatif contenant son `name`, le chemin de son `image` (pointant vers des photographies réalistes de consoles et manettes), et un `emoji` de fallback. Un `pair` identifiant unique est attribué à chaque paire de cartes pour faciliter la comparaison côté client.

**Exemple d'extrait algorithmique de `GameRules::generateDeck()` :**

```php
<?php
// filepath: d:\TOOLS\LARAGON\www\memory\app\Services\GameRules.php
// ...
    public static function generateDeck(int $pairsCount): array
    {
        $cardAssets = [
            // Photographies réalistes de consoles et manettes, avec fallback emoji
            ['name' => 'NES Console', 'image' => '/assets/img/cards/nes_console.png', 'emoji' => '🕹️'],
            ['name' => 'NES Controller', 'image' => '/assets/img/cards/nes_controller.png', 'emoji' => '🎮'],
            // ... jusqu'à 24 entrées pour 12 paires ...
        ];

        $selectedCards = array_slice($cardAssets, 0, $pairsCount); // Sélectionne N cartes uniques
        $deck = [];
        foreach ($selectedCards as $index => $card) {
            $deck[] = ['pair' => $index, 'card' => $card];
            $deck[] = ['pair' => $index, 'card' => $card];
        }
        shuffle($deck);
        return $deck;
    }
// ...
```

**Vérification des paires et maintien de l'état (sessions PHP / JavaScript) :**
La logique de retournement des cartes et de vérification des paires est principalement implémentée en **JavaScript** dans `public/assets/js/memory.js`.

1.  **Clic sur carte :** Le script JavaScript écoute les clics sur les éléments `.card`.
2.  **État des cartes :** Il gère l'état `is-flipped` (face visible) et `is-matched` (paire trouvée).
3.  **Comparaison :** Deux cartes sont comparées en utilisant leur attribut `data-pair` (identifiant unique de la paire, généré côté PHP).
4.  **Mise à jour des statistiques :** Les compteurs de `pairsFound` et `errors` sont mis à jour côté client.
5.  **Fin de partie :** Lorsque `pairsFound === totalPairs` ou `timeRemaining === 0`, le jeu s'arrête.

L'état de la partie en cours (`$_SESSION['current_game']`) est maintenu côté **serveur** en PHP. Cela assure qu'en cas de rafraîchissement ou de retour sur la page, la partie puisse potentiellement être reprise (bien que la logique de reprise ne soit pas pleinement implémentée ici, la base est posée). Les statistiques finales de la partie (paires trouvées, erreurs, temps restant) sont soumises au serveur via un formulaire caché (`#finish-form`) après la fin du jeu, où le `GameController` valide les données et calcule le score final avant de le persister en base de données via `GameModel::finish()`.

**Formule de score (extrait de `GameRules::computeScore()`) :**

```php
<?php
// filepath: d:\TOOLS\LARAGON\www\memory\app\Services\GameRules.php
// ...
    public static function computeScore(
        int $pairsFound,
        int $errors,
        int $timeRemaining,
        int $difficulty
    ): int {
        $baseScore = $pairsFound * 100;
        $errorPenalty = $errors * 10;
        $timeBonus = (int)($timeRemaining * 2);

        $difficultyMultiplier = match($difficulty) { // Coefficient multiplicateur selon la difficulté
            3 => 1.0, 4 => 1.1, 6 => 1.3, 8 => 1.5, 10 => 1.8, 12 => 2.0,
            default => 1.0,
        };

        $finalScore = (int)(($baseScore - $errorPenalty + $timeBonus) * $difficultyMultiplier);
        return max(0, $finalScore); // Le score ne peut pas être négatif
    }
// ...
```

**Intégration des photographies de produits :**
Les images des cartes sont des chemins relatifs (`/assets/img/cards/nes_console.png`, etc.) définis dans `GameRules::generateDeck()`. La vue `app/Views/game/index.php` vérifie `file_exists()` côté PHP pour afficher l'image ou un emoji de fallback, assurant une robustesse visuelle. Les cartes sont rendues carrées (`width: 110px; height: 110px;`) via `public/assets/css/global.css` pour un aspect harmonieux et moderne.

_(NOTE : Les photographies réelles des produits "consoles et manettes" doivent être placées dans `d:\TOOLS\LARAGON\www\memory\public/assets/img/cards/` et nommées selon les chemins spécifiés dans `GameRules::generateDeck()` pour que les images s'affichent correctement.)_

## **B. Couverture des Compétences REAC (Certifiées DWWM)**

Le projet démontre la maîtrise des compétences suivantes :

- **CP1: Concevoir et développer des interfaces utilisateur web statiques et adaptatives**
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\app/Views/game/index.php`, `d:\TOOLS\LARAGON\www\memory\app/Views/leaderboard/index.php`, `d:\TOOLS\LARAGON\www\memory\app/Views/profile/index.php` (Structure HTML sémantique et conforme).
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\public/assets/css/global.css` (Mise en page responsive avec media queries, variables CSS pour la cohérence visuelle, design moderne et soigné).
- **CP2: Concevoir et développer des interfaces utilisateur web dynamiques**
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\public/assets/js/memory.js` (Logique de jeu interactive côté client : retournement de cartes, détection de paires, gestion du chrono, messages toast, mise en pause du jeu).
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\app/Controllers/GameController.php` (Gestion des sessions PHP pour maintenir l'état de la partie en cours et des messages flash).
- **CP3: Construire une architecture applicative web**
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\core/Router.php` (Implémentation d'un routeur centralisé pour la gestion des requêtes GET/POST).
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\core/BaseController.php` (Classe abstraite de base pour les contrôleurs, assurant la réutilisabilité du rendu des vues).
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\app/Controllers/*` (Séparation des préoccupations selon le modèle MVC : `GameController`, `LeaderboardController`, `ProfileController`, `ErrorController`).
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\app/Models/*` (Séparation des préoccupations avec des modèles spécifiques : `UserModel`, `GameModel`, `CardModel`).
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\app/Services/GameRules.php` (Isolation de la logique métier complexe dans un service dédié).
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\public/index.php` (Rôle de Front-Controller unique, point d'entrée de l'application).
- **CP4: Développer la partie back-end d'une application web**
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\app/Models/*.php` (Interaction avec la base de données MySQL via PDO, implémentation des opérations CRUD : `findOrCreate`, `create`, `finish`, `getTopScores`, `getUserGames`, `getUserStats`).
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\app/Controllers/*.php` (Traitement des requêtes, coordination entre modèles et vues, calculs serveur).
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\app/Services/GameRules.php` (Application des règles de jeu et de scoring côté serveur).
- **CP5: Gérer les données d'une application web**
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\database/memory.sql` (Conception du schéma de base de données : tables `users`, `cards`, `games` avec clés primaires/étrangères et index).
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\core/Database.php` (Gestion de la connexion à la base de données MySQL via PDO en utilisant le pattern Singleton).
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\app/Models/*.php` (Requêtes préparées pour la manipulation des données).
  - **Fichiers :** Sessions PHP (`$_SESSION`) pour la persistance de l'état du joueur et de la partie en cours.
- **CP6: Mettre en œuvre des mesures de sécurité pour une application web**
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\core/Security.php` (Génération et validation des tokens CSRF pour protéger les formulaires POST).
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\core/Validator.php` (Validation serveur des données utilisateurs (pseudo, difficulté) avant tout traitement).
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\app/Controllers/GameController.php` (Utilisation des requêtes préparées PDO pour prévenir les injections SQL).
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\app/Controllers/ErrorController.php` (Gestion centralisée et journalisation des erreurs pour éviter l'affichage d'informations sensibles en production).
  - **Fichiers :** `htmlspecialchars()` appliqué sur toutes les données affichées provenant de l'utilisateur ou de la base de données pour prévenir les attaques XSS.
- **CP7: Tester une application web**
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\tests/GameRulesTest.php` (Présence de tests unitaires avec PHPUnit pour valider la logique algorithmique essentielle du jeu).
- **CP8: Déployer une application web**
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory/composer.json`, `d:\TOOLS\LARAGON\www\memory/vendor/autoload.php` (Utilisation de Composer pour la gestion des dépendances et l'autoloading).
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory\public/index.php` (Point d'entrée unique facilitant la configuration serveur et le déploiement sur un environnement comme Plesk).
  - **Fichiers :** `d:\TOOLS\LARAGON\www\memory/.env` (Gestion des variables d'environnement pour une configuration flexible selon l'environnement).
  - **Dossier :** `d:\TOOLS\LARAGON\www\memory/.git/` (Indique l'utilisation d'un système de gestion de version pour le déploiement collaboratif).

## **C. Actions techniques majeures**

J'AI :

1.  **Conçu et implémenté une architecture MVC robuste** en PHP orienté objet, en créant un routeur dynamique, un contrôleur de base, des contrôleurs métier spécifiques (`GameController`, `LeaderboardController`, `ProfileController`, `ErrorController`), et des modèles PDO pour interagir avec une base de données MySQL.
2.  **Développé une logique de jeu complète et modulaire**, incluant la génération aléatoire de paires de cartes (avec gestion d'assets visuels réalistes et fallback emoji), le calcul de scores pondéré par la difficulté, et la persistance de l'état de la partie en cours via les sessions PHP, puis des résultats finaux en base de données.
3.  **Mis en œuvre des mesures de sécurité fondamentales**, notamment la protection CSRF pour les formulaires POST, la validation serveur des entrées utilisateur (`Validator`), l'échappement systématique des données affichées (`htmlspecialchars`), et la gestion centralisée des erreurs avec journalisation pour une application plus résiliente.
4.  **Réalisé une interface utilisateur moderne et adaptative**, en utilisant un système de couleurs cohérent, des animations CSS subtiles, et un design responsive pour garantir une expérience utilisateur fluide sur desktop et mobile, avec des pages dédiées au jeu, au classement et au profil joueur.

## **D. Preuves et Annexes visuelles**

**1. Extrait d'algorithme de génération de deck (`App\Services\GameRules.php`) :**

```php
<?php
// filepath: d:\TOOLS\LARAGON\www\memory\app\Services\GameRules.php
// ...
    public static function generateDeck(int $pairsCount): array
    {
        $cardAssets = [
            // Photographies réalistes de consoles et manettes, avec fallback emoji
            ['name' => 'NES Console', 'image' => '/assets/img/cards/nes_console.png', 'emoji' => '🕹️'],
            ['name' => 'NES Controller', 'image' => '/assets/img/cards/nes_controller.png', 'emoji' => '🎮'],
            // ... (autres paires jusqu'à 24 entrées)
        ];

        $selectedCards = array_slice($cardAssets, 0, $pairsCount);
        $deck = [];
        foreach ($selectedCards as $index => $card) {
            $deck[] = ['pair' => $index, 'card' => $card];
            $deck[] = ['pair' => $index, 'card' => $card];
        }
        shuffle($deck);
        return $deck;
    }
// ...
```

**2. Extrait de la vue pour l'affichage conditionnel des images/emojis (`app/Views/game/index.php`) :**

```php
<?php
// filepath: d:\TOOLS\LARAGON\www\memory\app\Views\game\index.php
// ...
                <?php foreach ($deck as $index => $card):
                    $cardData = $card['card'];
                    $imageUrl = $cardData['image'] ?? null;
                    $emoji = $cardData['emoji'] ?? '❓'; // Emoji par défaut si non défini
                ?>
                    <button class="card" data-pair="<?= htmlspecialchars($card['pair']); ?>">
                        <div class="card__face card__face--back">🎴</div>
                        <div class="card__face card__face--front">
                            <?php
                            // Vérifie si l'image existe physiquement avant de l'afficher
                            if ($imageUrl && file_exists(__DIR__ . '/../../..' . parse_url($imageUrl, PHP_URL_PATH))):
                            ?>
                                <img src="<?= htmlspecialchars($imageUrl); ?>"
                                    alt="Carte <?= htmlspecialchars($cardData['name'] ?? 'produit'); ?>"
                                    loading="lazy">
                            <?php else: ?>
                                <!-- Fallback emoji si l'image est manquante -->
                                <span class="card__emoji"><?= $emoji; ?></span>
                            <?php endif; ?>
                        </div>
                    </button>
                <?php endforeach; ?>
// ...
```

**3. Extrait de `global.css` pour les cartes carrées et responsives :**

```css
/* filepath: d:\TOOLS\LARAGON\www\memory\public\assets\css\global.css */
/* ... */
.card {
  position: relative;
  width: 110px;
  height: 110px; /* Cartes carrées pour un design épuré */
  /* ... autres styles ... */
}

@media (max-width: 600px) {
  .card {
    width: 90px;
    height: 90px; /* Adaptation pour les petits écrans */
  }
  /* ... */
}
```

## **E. Dette technique et Manquements critiques**

- **Sécurité - Vulnérabilité côté client (Critique) :** La logique de jeu (retournement, vérification de paires, comptage erreurs/paires) est majoritairement gérée en JavaScript. Un utilisateur malveillant pourrait manipuler ces compteurs via les outils de développement du navigateur avant la soumission finale du score.
  - **Impact :** Permet la triche et la falsification des scores, rendant le classement non fiable. C'est un point très faible pour un Dossier Professionnel.
  - **Correction indispensable :** Exiger une vérification et une synchronisation plus fréquentes de l'état du jeu côté serveur. Par exemple, après chaque paire trouvée, envoyer une requête AJAX au serveur pour valider la paire et l'état du jeu. Le serveur devrait être la seule source de vérité pour le score, les erreurs et le temps.
- **Authentification - Simplicité "arcade" (Critique) :** Le mode "arcade NES" choisi (saisie de pseudo sans mot de passe) ne répond pas aux critères standards de "système d’inscription/connexion/profils utilisateurs" requis par l'énoncé s'il est interprété comme un système avec mots de passe sécurisés. L'usurpation d'identité est triviale.
  - **Impact :** Aucune sécurité des comptes, progression non réellement "personnelle" car non protégée. Cela diminue fortement la valeur du projet pour la certification sur les aspects sécurité et gestion utilisateur.
  - **Correction indispensable :** Introduire un système d'authentification robuste avec mots de passe hachés, gestion des sessions sécurisées (timeout, régénération d'ID), et un flux complet d'inscription/connexion/déconnexion.
- **Tests - Couverture limitée (Majeur) :** Bien que des tests unitaires existent pour `GameRules`, ils sont basiques. Il manque des tests d'intégration (vérifiant les interactions contrôleurs/modèles/base de données) et des tests fonctionnels end-to-end.
  - **Impact :** Risque élevé de régressions non détectées lors d'évolutions. La qualité et la fiabilité du code sont difficiles à garantir.
  - **Correction :** Étendre significativement les tests PHPUnit pour les contrôleurs et les modèles. Envisager l'ajout de tests fonctionnels (ex. avec un framework de test fonctionnel PHP ou des outils E2E comme Cypress pour valider les scénarios utilisateur).
- **Performance - Requêtes aléatoires (Mineur) :** L'utilisation de `ORDER BY RAND()` dans `CardModel::pickRandomPairs()` (si elle était encore active) est inefficace pour de grandes bases de données. L'approche actuelle dans `GameRules` avec `array_slice` et `shuffle` est acceptable pour un petit nombre de cartes, mais moins scalable.
  - **Impact :** Potentiel ralentissement de la génération de partie avec un catalogue de cartes très étendu.
  - **Correction :** Pour un grand catalogue, une sélection d'IDs aléatoires (`SELECT id FROM cards ORDER BY id LIMIT N`) puis sélectionner aléatoirement N IDs serait plus performante.
- **Gestion des erreurs - Feedback utilisateur (Mineur) :** L'actuel `ErrorHandler` renvoie vers la page d'accueil avec un message flash générique en cas d'exception non gérée.
  - **Impact :** Expérience utilisateur sous-optimale en cas d'erreur inattendue.
  - **Correction :** Développer des vues `errors/500.php`, `errors/403.php` etc., avec des messages plus spécifiques et utiliser `BaseController::render()` pour les afficher, tout en journalisant les détails techniques.

## **F. Verdict final pour le Dossier Pro**

**[INEXPLOITABLE EN L'ÉTAT]**

Le projet présente une base architecturale (MVC, POO) et une intégration front-end (CSS, JS interactif) prometteuses, démontrant de bonnes compétences.

Cependant, la simplification de l'authentification en "mode arcade" et la gestion majoritairement côté client de la logique de jeu sont des **manquements critiques** au regard des exigences de sécurité et de robustesse d'un "système d’inscription/connexion/profils" typique pour un Dossier Professionnel DWWM. Ces points nécessitent des **retouches importantes** pour être pleinement conformes aux attentes d'un projet professionnel évaluable.

Le style et l'adaptativité de l'interface sont un point fort, démontrant de bonnes compétences en intégration front-end. L'effort sur la pédagogie du code est également appréciable.
