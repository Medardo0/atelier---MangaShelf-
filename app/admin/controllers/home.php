<?php
require_once __DIR__ . '/_actions.php';

function home_index(PDO $pdo, ?string $id = null): string
{
    return admin_index($pdo, $id);
}
