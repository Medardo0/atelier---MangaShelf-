<?php
require_once __DIR__ . '/_actions.php';

function manga_create_index(PDO $pdo, ?string $id = null): string
{
    return admin_manga_create($pdo, $id);
}
