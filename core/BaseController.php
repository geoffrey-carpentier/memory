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
     * @param string $view   Nom du fichier de la vue (sans extension, ex: "home/index")
     * @param array  $params Tableau associatif de variables à injecter dans la vue
     *
     * @return void
     */
    protected function render(string $view, array $params = []): void
    {
        // Transforme les clés du tableau $params en variables utilisables directement dans la vue
        // Exemple : ['title' => 'Accueil'] devient $title = 'Accueil'
        extract($params, EXTR_SKIP);

        // Démarre la temporisation de sortie afin de capturer le contenu généré par la vue
        ob_start();

        // Inclusion de la vue principale (ex: home/index.php)
        // Les variables extraites plus haut sont accessibles dans cette vue
        require __DIR__ . '/../app/Views/' . $view . '.php';

        // Récupère le contenu généré par la vue et l'enregistre dans $content
        // Puis nettoie le tampon
        $content = ob_get_clean();

        // Inclusion du layout de base, qui va utiliser la variable $content pour afficher la vue
        require __DIR__ . '/../app/Views/layouts/base.php';
    }
}
