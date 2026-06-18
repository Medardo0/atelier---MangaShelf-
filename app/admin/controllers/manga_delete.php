<?php
require_once __DIR__ . '/_actions.php';

function manga_delete_index(PDO $pdo, ?string $id = null): string
{
    return admin_manga_delete($pdo, $id);
}
