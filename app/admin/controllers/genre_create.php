<?php
require_once __DIR__ . '/_actions.php';

function genre_create_index(PDO $pdo, ?string $id = null): string
{
    return admin_genre_create($pdo, $id);
}
