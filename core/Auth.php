<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!csrf_is_valid($token)) {
        http_response_code(403);
        exit('Token CSRF invalide.');
    }
}

function csrf_is_valid(?string $token): bool
{
    return is_string($token)
        && $token !== ''
        && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

function current_role(): string
{
    return $_SESSION['role'] ?? '';
}

function require_auth(string $role = ''): void
{
    if (!is_logged_in()) {
        header('Location: /auth/connexion?expired=1');
        exit;
    }
    if ($role !== '' && current_role() !== $role) {
        http_response_code(403);
        exit('Accès refusé.');
    }
}


