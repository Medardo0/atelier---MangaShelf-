<?php
require_once __DIR__ . '/_actions.php';

function message_show_index(PDO $pdo, ?string $id = null): string
{
    return admin_message_show($pdo, $id);
}
