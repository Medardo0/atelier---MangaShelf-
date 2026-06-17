<?php
/**
 * public/index.php — Front controller MangaShelf
 *
 * Point d'entrée unique de l'application.
 * Toutes les requêtes arrivent ici via public/.htaccess
 *
 * Rôle : orchestrer le trajet d'une requête HTTP
 *   1. Lire la demande HTTP          → http_in()
 *   2. Donner un sens aux segments   → route()
 *   3. Appeler le bon controller     → run()
 *   4. Envoyer la réponse            → http_out()
 *
 * Ce fichier ne contient pas de logique métier.
 * Il organise les étapes — les détails sont dans core/.
 */

// ── Chargement des fichiers core ────────────────────────────
require_once __DIR__ . '/../core/http.php';
require_once __DIR__ . '/../core/router.php';
require_once __DIR__ . '/../core/html.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Database.php';

// ── 1. Lire la demande HTTP ─────────────────────────────────
$raw_uri  = $_SERVER['REQUEST_URI'] ?? '/';
$segments = http_in($raw_uri ?: '/');

// ── 2. Donner un sens aux segments ─────────────────────────
// route() applique la convention /entity/action/id
// Ex: ['manga','show','3'] → ['entity'=>'manga','action'=>'show','id'=>'3']
$route = route($segments);

// ── 3. Appeler le bon controller ────────────────────────────
// run() charge controllers/{entity}.php et appelle {entity}_{action}($id)
// run() retourne du HTML — il ne fait pas echo
$html = run($route, __DIR__);

// ── 4. Envoyer la réponse ───────────────────────────────────
// http_out() est le seul endroit qui fait echo dans l'application
http_out(200, $html);
