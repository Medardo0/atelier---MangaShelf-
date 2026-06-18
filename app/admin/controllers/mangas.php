<?php
require_once __DIR__ . '/_actions.php';

function mangas_index(PDO $pdo, ?string $id = null): string
{
    return admin_mangas($pdo, $id);
}
