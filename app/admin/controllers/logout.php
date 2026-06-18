<?php
require_once __DIR__ . '/../../../app/controllers/auth.php';

function logout_index(PDO $pdo, ?string $id = null): string
{
    return auth_logout($pdo, $id);
}
