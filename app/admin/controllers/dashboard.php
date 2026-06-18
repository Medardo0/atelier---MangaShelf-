<?php
require_once __DIR__ . '/_actions.php';

function dashboard_index(PDO $pdo, ?string $id = null): string
{
    return admin_index($pdo, $id);
}
