# 🧠 Memory — Jeu de mémoire (Time-Attack)

Jeu de paires (Memory) en PHP orienté objet, développé en architecture MVC "maison" dans le cadre d'un projet pédagogique. Le joueur choisit une difficulté, retourne les cartes contre la montre, puis retrouve son score dans un classement des 10 meilleurs joueurs et sur sa page de profil personnelle.

## 🎮 Règles du jeu

- Le joueur choisit un pseudo (2 à 20 caractères) et une difficulté avant de commencer.
- La difficulté correspond au **nombre de paires** à retrouver : 3, 4, 6, 8, 10 ou 12 paires (soit de 6 à 24 cartes), conformément aux bornes minimum (3 paires) et maximum (12 paires) du cahier des charges.
- Chaque difficulté dispose d'un temps alloué et d'une grille dédiés :

  | Paires | Grille | Temps alloué |
  | :----: | :----: | :----------: |
  |   3    |  3×2   |     60 s     |
  |   4    |  4×2   |     90 s     |
  |   6    |  4×3   |    120 s     |
  |   8    |  4×4   |    150 s     |
  |   10   |  5×4   |    180 s     |
  |   12   |  6×4   |    210 s     |

- Le jeu se termine dès que toutes les paires sont trouvées (victoire) ou que le temps est écoulé (timeout).
- **Score** : `(paires trouvées × 100 − erreurs × 10 + temps restant × 2) × multiplicateur de difficulté`. Le multiplicateur augmente avec la difficulté choisie (de ×1 pour 3 paires à ×2 pour 12 paires), afin de récompenser les parties les plus exigeantes.

## ✨ Fonctionnalités

- **Time-Attack** : 6 niveaux de difficulté, chrono, mise en pause.
- **Hall of Fame** (`/leaderboard`) : classement des 10 meilleurs scores, avec podium et historique personnel pour le joueur connecté.
- **Profil joueur** (`/profile`) : nombre de parties jouées, meilleur score, score moyen et historique détaillé des parties.
- **Identification par pseudo** via les sessions PHP (pas de mot de passe, cohérent avec un jeu "arcade" sans compte à créer).
- **Effets sonores rétro** générés en Web Audio API (aucun fichier audio), avec bouton muet/son mémorisé localement.
- Interface responsive (le plateau de jeu s'adapte à la taille de l'écran).

## 🛠️ Technologies utilisées

- **PHP 8.1+** orienté objet, architecture **MVC** développée sans framework
- **MySQL / MariaDB** via PDO (requêtes préparées)
- **Composer** pour l'autoload PSR-4 et la gestion des dépendances ([vlucas/phpdotenv](https://github.com/vlucas/phpdotenv) pour la configuration via `.env`)
- **JavaScript natif** (ES6+) pour la logique de jeu côté client et les effets sonores (Web Audio API)
- **CSS natif** (variables CSS, grid/flexbox, sans framework front-end)
- **PHPUnit** pour quelques tests unitaires de la logique de score (`tests/`)

## 📂 Structure du projet (MVC)

```
memory/
├── app/
│   ├── Controllers/     # GameController, LeaderboardController, ProfileController, ErrorController
│   ├── Models/          # Card, UserModel, GameModel (accès aux données)
│   ├── Services/        # GameRules : logique métier du jeu (deck, score, grille, chrono)
│   └── Views/           # Templates PHP (game, leaderboard, profile, errors, layouts)
├── core/                # Noyau maison : Router, Database (PDO), BaseController, Security (CSRF), Validator, ErrorHandler
├── database/            # Script SQL de création de la base (memory.sql)
├── public/              # Racine web : index.php (point d'entrée unique), assets CSS/JS/images
├── tests/               # Tests unitaires PHPUnit
├── .env.example         # Modèle de configuration de la base de données
└── composer.json
```

Le routage est déclaré dans `public/index.php` et résolu par `Core\Router`, qui associe chaque URL à une méthode de contrôleur (`Controller@methode`). Chaque contrôleur récupère les données via les modèles/services puis délègue l'affichage à `BaseController::render()`, qui injecte la vue demandée dans le layout commun (`app/Views/layouts/base.php`).

## 🚀 Installation en local

### Prérequis

- PHP 8.1 ou supérieur
- MySQL ou MariaDB
- [Composer](https://getcomposer.org/)
- Un serveur web (Laragon, XAMPP, WAMP...) ou le serveur intégré de PHP

### Étapes

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/geoffrey-carpentier/memory.git
   cd memory
   ```

2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

3. **Configurer la base de données**

   Copier le fichier d'exemple puis l'adapter à votre environnement :
   ```bash
   cp .env.example .env
   ```
   Par défaut, `.env.example` correspond à une base `memory` sur `127.0.0.1:3306` avec l'utilisateur `root` sans mot de passe (configuration standard Laragon/XAMPP).

4. **Créer la base de données**

   Importer le script SQL fourni, qui crée la base `memory` ainsi que les tables nécessaires :
   ```bash
   mysql -u root -p < database/memory.sql
   ```

5. **Pointer le document root vers `public/`**

   Le point d'entrée unique de l'application est `public/index.php`. Configurez votre virtual host (ou le dossier servi par Laragon) sur le dossier `public/` du projet.

   Avec le serveur intégré de PHP, depuis la racine du projet :
   ```bash
   php -S localhost:8000 -t public
   ```

6. **Lancer le jeu**

   Ouvrez `http://localhost:8000` (ou l'URL de votre virtual host) : choisissez un pseudo, une difficulté, et jouez !

### Lancer les tests

```bash
composer require --dev phpunit/phpunit
vendor/bin/phpunit tests
```

## 👤 Auteur

Geoffrey Carpentier — projet réalisé dans le cadre de la formation développeur web à La Plateforme_.

Dépôt GitHub : https://github.com/geoffrey-carpentier/memory
