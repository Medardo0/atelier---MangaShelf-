<?php

function route(array $segments): array
{
    return [
        'entity' => $segments[0] ?? 'home',
        'action' => $segments[1] ?? 'index',
        'id'     => $segments[2] ?? null,
    ];
}

function run(array $route, string $base_path, PDO $pdo): string
{
    $base_path = rtrim($base_path, '/\\');

    $entity = $route['entity'];
    $action = $route['action'];
    $id     = $route['id'];

    if (!is_safe_segment($entity) || !is_safe_segment($action)) {
        return error_page(400, 'Invalid URL segments.');
    }

    $controller_path = $base_path . '/controllers/' . $entity . '.php';
    $function_name = $entity . '_' . $action;

    if (!is_file($controller_path)) {
        return error_page(404, 'Controller not found: ' . htmlspecialchars($entity));
    }

    require_once $controller_path;

    if (!function_exists($function_name)) {
        return error_page(404, 'Controller function not found: ' . htmlspecialchars($function_name));
    }

    if ($id !== null) {
        return $function_name($pdo, $id);
    }

    return $function_name($pdo);
}

function is_safe_segment(string $segment): bool
{
    if ($segment === '') {
        return false;
    }

    $allowed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-';
    $length = strlen($segment);

    for ($i = 0; $i < $length; $i++) {
        if (strpos($allowed, $segment[$i]) === false) {
            return false;
        }
    }

    return true;
}

function error_page(int $code, string $message): string
{
    http_response_code($code);

    return '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8">' .
        '<title>Erreur ' . $code . ' - MangaShelf</title></head><body>' .
        '<h1>Erreur ' . $code . '</h1>' .
        '<p>' . htmlspecialchars($message) . '</p>' .
        '<p><a href="/">Retour a l\'accueil</a></p>' .
        '</body></html>';
}
