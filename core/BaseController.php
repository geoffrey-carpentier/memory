<?php

namespace Core;

/**
 * Classe BaseController
 * ---------------------
 * Classe mère dont hériteront tous les contrôleurs.
 * Elle centralise les fonctionnalités communes, notamment le rendu des vues.
 */
class BaseController
{
    /**
     * Méthode de rendu d'une vue
     *
     * @param string $view   Nom du fichier de la vue (ex: "leaderboard/index")
     * @param array  $params Tableau associatif de variables à injecter dans la vue
     *
     * @return void
     */
    protected function render(string $view, array $params = []): void
    {
        // Chemin du fichier de la vue
        $viewPath = __DIR__ . '/../app/Views/' . $view . '.php';

        // Vérification de l'existence du fichier
        if (!file_exists($viewPath)) {
            error_log("Vue introuvable : {$viewPath}");
            throw new \Exception("Vue introuvable : {$viewPath}");
        }

        // Extraction des paramètres en variables locales
        extract($params);

        // Récupération du contenu de la vue
        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        // Inclusion du layout principal
        $layoutPath = __DIR__ . '/../app/Views/layouts/base.php';
        include $layoutPath;
    }
}
