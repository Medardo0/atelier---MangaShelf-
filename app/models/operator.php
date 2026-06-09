<?php
function operator_find_by_email(string $email): ?array
{
    $stmt = db()->prepare("SELECT * FROM operator WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    return $stmt->fetch() ?: null;
}

function operator_email_exists(string $email): bool
{
    $stmt = db()->prepare("SELECT 1 FROM operator WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    return (bool) $stmt->fetchColumn();
}

function operator_username_exists(string $username): bool
{
    $stmt = db()->prepare("SELECT 1 FROM operator WHERE username = :username LIMIT 1");
    $stmt->execute([':username' => $username]);
    return (bool) $stmt->fetchColumn();
}

function operator_create(string $username, string $email, string $password): int
{
    $stmt = db()->prepare(
        "INSERT INTO operator (username, email, password, role)
         VALUES (:username, :email, :password, 'user')"
    );
    $stmt->execute([
        ':username' => $username,
        ':email'    => $email,
        ':password' => password_hash($password, PASSWORD_BCRYPT),
    ]);
    $user_id = (int) db()->lastInsertId();

    $col = db()->prepare(
        "INSERT INTO collection (operator_id, type) VALUES (:id, :type)"
    );
    foreach (['favorites', 'wishlist', 'reading', 'completed'] as $type) {
        $col->execute([':id' => $user_id, ':type' => $type]);
    }

    return $user_id;
}

function operator_increment_failed(int $id): void
{
    $stmt = db()->prepare(
        "UPDATE operator
         SET failed_attempts = failed_attempts + 1,
             locked_until = IF(failed_attempts + 1 >= 5,
                               DATE_ADD(NOW(), INTERVAL 15 MINUTE),
                               locked_until)
         WHERE id = :id"
    );
    $stmt->execute([':id' => $id]);
}

function operator_reset_failed(int $id): void
{
    $stmt = db()->prepare(
        "UPDATE operator SET failed_attempts = 0, locked_until = NULL WHERE id = :id"
    );
    $stmt->execute([':id' => $id]);
}
