<?php
/**
 * core/router.php
 * Route et dispatch — donne un rôle aux segments et appelle le controller.
 *
 * Deux fonctions :
 *   route()           → donne un sens aux segments HTTP
 *   is_safe_segment() → vérifie qu'un segment est utilisable sans danger
 *   run()             → appelle le bon controller et retourne le HTML
 */

/**
 * Donne un rôle aux segments HTTP selon la convention MangaShelf :
 *   /entity/action/id
 *
 * Convention du projet :
 *   segment 0 → entity  (ex: "manga", "admin", "catalogue")
 *   segment 1 → action  (ex: "show", "index", "login")
 *   segment 2 → id      (ex: "3", "one-piece")
 *
 * Valeurs par défaut si un segment est absent :
 *   entity → "home"
 *   action → "index"
 *   id     → null
 *
 * Exemples :
 *   []                        → ['entity'=>'home',      'action'=>'index', 'id'=>null]
 *   ['manga']                 → ['entity'=>'manga',     'action'=>'index', 'id'=>null]
 *   ['manga','show','3']      → ['entity'=>'manga',     'action'=>'show',  'id'=>'3']
 *   ['admin','login']         → ['entity'=>'admin',     'action'=>'login', 'id'=>null]
 *   ['catalogue']             → ['entity'=>'catalogue', 'action'=>'index', 'id'=>null]
 *
 * @param  array $segments  Résultat de http_in()
 * @return array            Route structurée
 */
function route(array $segments): array
{
    return [
        'entity' => $segments[0] ?? 'home',
        'action' => $segments[1] ?? 'index',
        'id'     => $segments[2] ?? null,
    ];
}

/**
 * Vérifie qu'un segment est utilisable sans danger pour construire
 * un nom de fichier ou de fonction.
 *
 * Accepte : lettres, chiffres, tirets, underscores.
 * Refuse  : slashs, points, espaces, HTML, chemins relatifs (../)
 *
 * Cette vérification est distincte de "est-ce que la page existe ?"
 * Elle répond uniquement à "est-ce que ce segment est propre ?"
 *
 * @param  string $segment
 * @return bool
 */
function is_safe_segment(string $segment): bool
{
    // Autorise uniquement : a-z, A-Z, 0-9, - et _
    return (bool) preg_match('/^[a-zA-Z0-9_-]+$/', $segment);
}

/**
 * Transforme une route structurée en appel réel de controller.
 *
 * Convention de nommage :
 *   entity    → controllers/{entity}.php
 *   entity + action → fonction {entity}_{action}()
 *
 * Exemple :
 *   entity=manga, action=show, id=3
 *   → charge  controllers/manga.php
 *   → appelle manga_show('3')
 *
 * run() retourne une chaîne HTML — elle ne fait pas echo.
 * C'est http_out() dans index.php qui envoie la réponse.
 *
 * @param  array  $route      Résultat de route()
 * @param  string $base_dir   __DIR__ depuis index.php (racine de public/)
 * @return string             HTML produit par le controller
 */
function run(array $route, string $base_dir): string
{
    $entity = $route['entity'];
    $action = $route['action'];
    $id     = $route['id'];

    // ── 1. Vérifier que les segments sont propres ─────────────
    if (!is_safe_segment($entity) || !is_safe_segment($action)) {
        return error_page(400, 'Invalid URL segments.');
    }

    // ── 2. Construire le chemin du controller ─────────────────
    // On remonte d'un niveau depuis public/ pour aller dans app/controllers/
    $controller_path = $base_dir . '/../app/controllers/' . $entity . '.php';

    // ── 3. Vérifier que le fichier controller existe ──────────
    if (!file_exists($controller_path)) {
        return error_page(404, 'Page not found : controller "' . htmlspecialchars($entity) . '" does not exist.');
    }

    // ── 4. Charger le fichier controller ─────────────────────
    require_once $controller_path;

    // ── 5. Construire le nom de la fonction ───────────────────
    // Convention : entity_action() → ex: manga_show(), home_index()
    $function_name = $entity . '_' . $action;

    // ── 6. Vérifier que la fonction existe ────────────────────
    if (!function_exists($function_name)) {
        return error_page(404, 'Action "' . htmlspecialchars($action) . '" not found in "' . htmlspecialchars($entity) . '".');
    }

    // ── 7. Appeler la fonction et retourner le résultat ───────
    // Le controller reçoit l'id (peut être null)
    // et doit retourner une chaîne HTML
    return $function_name($id);
}

/**
 * Génère une page d'erreur HTML simple.
 * Utilisée en interne par run() — retourne une chaîne, ne fait pas echo.
 *
 * @param  int    $code     Code HTTP (400, 404, 500…)
 * @param  string $message  Message d'erreur lisible
 * @return string
 */
function error_page(int $code, string $message): string
{
    http_response_code($code);
    return '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8">
        <title>Erreur ' . $code . ' — MangaShelf</title></head><body>
        <h1>Erreur ' . $code . '</h1>
        <p>' . htmlspecialchars($message) . '</p>
        <p><a href="/">Retour à l\'accueil</a></p>
        </body></html>';
}
