<?php
require_once __DIR__ . '/_actions.php';

function genres_index(PDO $pdo, ?string $id = null): string
{
    return admin_genres($pdo, $id);
}
