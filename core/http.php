<?php

function http_in(string $request_uri): array
{
    $position = strpos($request_uri, '?');
    $path = $position === false ? $request_uri : substr($request_uri, 0, $position);

    while (str_contains($path, '//')) {
        $path = str_replace('//', '/', $path);
    }

    $path = trim($path, ' /');

    if ($path === '') {
        return [];
    }

    $path = strtolower($path);

    if (strpos($path, '/') === false) {
        return [$path];
    }

    return explode('/', $path);
}

function http_out(int $code, string $body, array $headers = []): void
{
    $current_code = http_response_code();
    if ($code === 200 && $current_code >= 400) {
        $code = $current_code;
    }

    http_response_code($code);

    foreach ($headers as $name => $value) {
        header($name . ': ' . $value);
    }

    echo $body;
}

function redirect(string $url): void
{
    http_out(302, '', ['Location' => $url]);
    exit;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}
