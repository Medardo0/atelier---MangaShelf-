<?php
/**
 * core/http.php
 * Entrée et sortie HTTP — ne connaît pas les controllers ni les routes.
 *
 * Rôle : prendre la demande HTTP brute et en extraire les segments propres.
 * Ne sait pas ce que signifient "entity", "action" ou "id".
 * Sait seulement découper un chemin URL en morceaux utilisables.
 */

/**
 * Lit le chemin HTTP et retourne un tableau de segments propres.
 *
 * Exemples :
 *   "/manga/show/3"        → ['manga', 'show', '3']
 *   "/catalogue?genre=shonen" → ['catalogue']
 *   "/admin/login"         → ['admin', 'login']
 *   "/"                    → []
 *   "//manga//show"        → ['manga', 'show']
 *
 * @param  string $request_uri  Valeur de $_SERVER['REQUEST_URI']
 * @return array                Segments du chemin, sans query string
 */
function http_in(string $request_uri): array
{
    // 1. Supprimer la query string (?q=..., ?genre=..., etc.)
    //    La query string ne sert pas à choisir le controller.
    $path = parse_url($request_uri, PHP_URL_PATH);

    // 2. Supprimer les slashs de début et de fin
    $path = trim($path, '/');

    // 3. Si le chemin est vide (racine "/"), retourner tableau vide
    if ($path === '') {
        return [];
    }

    // 4. Découper par "/" et supprimer les segments vides
    //    (protège contre les doubles slashs "//manga//show")
    $segments = array_values(
        array_filter(
            explode('/', $path),
            fn($s) => $s !== ''
        )
    );

    return $segments;
}

/**
 * Envoie la réponse HTTP au navigateur.
 *
 * Rôle : seul endroit dans l'application qui fait echo.
 * Tous les controllers retournent du HTML — c'est ici qu'il part.
 *
 * @param  int    $status  Code HTTP (200, 404, 500…)
 * @param  string $html    Contenu HTML à envoyer
 */
function http_out(int $status, string $html): void
{
    $current_status = http_response_code();
    if ($status === 200 && $current_status >= 400) {
        $status = $current_status;
    }

    http_response_code($status);
    echo $html;
}
