<?php
/**
 * core/html.php
 * Production du HTML — la pièce manquante évoquée en fin de session 8.
 *
 * Rôle : permettre à un controller d'utiliser une vue PHP
 * sans écrire lui-même tout le HTML dans une chaîne.
 *
 * Convention :
 *   render('manga/show', ['manga' => $manga])
 *   → charge app/views/manga/show.php
 *   → injecte les données comme variables locales
 *   → retourne le HTML produit (ne fait pas echo)
 *
 * Règle importante :
 *   render() retourne une chaîne.
 *   C'est toujours http_out() qui envoie au navigateur.
 */

/**
 * Charge une vue, injecte les données et retourne le HTML produit.
 *
 * @param  string $view   Chemin relatif depuis app/views/ sans .php
 *                        Ex: 'home/index', 'manga/show', 'admin/dashboard/index'
 * @param  array  $data   Variables à injecter dans la vue
 * @return string         HTML produit par la vue
 */
function render(string $view, array $data = []): string
{
    // Construire le chemin complet de la vue
    $view_path = __DIR__ . '/../app/views/' . $view . '.php';

    // Vérifier que la vue existe
    if (!file_exists($view_path)) {
        return error_page(500, 'View "' . htmlspecialchars($view) . '" not found.');
    }

    // Extraire le tableau $data en variables locales
    // ['manga' => $obj, 'title' => 'One Piece']
    // devient : $manga = $obj; $title = 'One Piece';
    extract($data, EXTR_SKIP);

    // Capturer la sortie de la vue dans un buffer
    // ob_start() : commence la capture
    // include    : exécute la vue (elle peut faire echo ou utiliser des balises PHP)
    // ob_get_clean() : récupère le contenu capturé et vide le buffer
    ob_start();
    include $view_path;
    return ob_get_clean();
}

/**
 * Charge une vue dans un layout (template partagé).
 *
 * Utilisation typique :
 *   render_in_layout('manga/show', 'layouts/main', ['manga' => $manga])
 *
 * La vue produit le $content, le layout l'affiche via <?= $content ?>
 *
 * @param  string $view     Vue du contenu principal
 * @param  string $layout   Vue du layout (ex: 'layouts/main', 'layouts/admin')
 * @param  array  $data     Données partagées entre vue et layout
 * @return string           HTML complet (layout + contenu)
 */
function render_in_layout(string $view, string $layout, array $data = []): string
{
    // 1. Produire le contenu de la vue
    $content = render($view, $data);

    // 2. Injecter $content dans les données du layout
    $data['content'] = $content;

    // 3. Produire le layout complet avec le contenu dedans
    return render($layout, $data);
}
