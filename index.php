<?php
session_start();

require __DIR__ . '/core/http.php';
require __DIR__ . '/core/router.php';
require __DIR__ . '/core/html.php';
require __DIR__ . '/core/query.php';
require __DIR__ . '/core/Auth.php';

require __DIR__ . '/config/database.php';

$base = __DIR__ . '/app';

$segments = http_in($_SERVER['REQUEST_URI'] ?? '/');

if (isset($segments[0]) && $segments[0] === 'admin') {
    $base = __DIR__ . '/app/admin';
    array_shift($segments);

    $admin_id_routes = [
        'manga_edit',
        'manga_delete',
        'genre_edit',
        'genre_delete',
        'message_show',
        'message_delete',
    ];

    if (isset($segments[0], $segments[1]) && in_array($segments[0], $admin_id_routes, true)) {
        $segments = [$segments[0], 'index', $segments[1]];
    }
}

$route = route($segments);

$body = run($route, $base, $pdo);

http_out(200, $body);
