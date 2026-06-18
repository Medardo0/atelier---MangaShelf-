<?php
require_once __DIR__ . '/_actions.php';

function messages_index(PDO $pdo, ?string $id = null): string
{
    return admin_messages($pdo, $id);
}
