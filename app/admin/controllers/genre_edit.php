<?php
require_once __DIR__ . '/_actions.php';

function genre_edit_index(PDO $pdo, ?string $id = null): string
{
    return admin_genre_edit($pdo, $id);
}
