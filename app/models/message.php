<?php
function message_create(string $name, string $email, string $subject, string $body): void
{
    $stmt = db()->prepare(
        "INSERT INTO message (sender_name, sender_email, subject, body)
         VALUES (:name, :email, :subject, :body)"
    );
    $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':subject' => $subject,
        ':body'    => $body,
    ]);
}

function message_get_all(): array
{
    $stmt = db()->query(
        "SELECT id, sender_name, sender_email, subject, is_read, created_at
         FROM message
         ORDER BY is_read ASC, created_at DESC"
    );
    return $stmt->fetchAll();
}

function message_get_by_id(int $id): ?array
{
    $stmt = db()->prepare(
        "SELECT id, sender_name, sender_email, subject, body, is_read, created_at
         FROM message WHERE id = :id"
    );
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

function message_mark_read(int $id): void
{
    $stmt = db()->prepare("UPDATE message SET is_read = 1 WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

function message_count_unread(): int
{
    $stmt = db()->query("SELECT COUNT(*) FROM message WHERE is_read = 0");
    return (int) $stmt->fetchColumn();
}

function message_delete(int $id): void
{
    $stmt = db()->prepare("DELETE FROM message WHERE id = :id");
    $stmt->execute([':id' => $id]);
}
