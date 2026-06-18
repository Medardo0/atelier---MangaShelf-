<?php
require_once __DIR__ . '/../../../app/controllers/auth.php';

function login_index(PDO $pdo, ?string $id = null): string
{
    return auth_connexion($pdo, $id);
}
