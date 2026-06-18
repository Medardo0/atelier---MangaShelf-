<?php
require_once __DIR__ . '/_actions.php';

function message_delete_index(PDO $pdo, ?string $id = null): string
{
    return admin_message_delete($pdo, $id);
}
