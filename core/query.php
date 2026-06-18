<?php

function db_insert(PDO $pdo, string $table, array $data): int
{
    $columns = array_keys($data);
    $column_sql = implode(', ', $columns);
    $placeholder_sql = implode(', ', array_fill(0, count($columns), '?'));

    $sql = 'INSERT INTO ' . $table . ' (' . $column_sql . ') VALUES (' . $placeholder_sql . ')';

    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_values($data));

    return (int) $pdo->lastInsertId();
}

function db_update(PDO $pdo, string $table, array $data, string $id_column, int|string $id): int
{
    $sets = [];
    foreach (array_keys($data) as $column) {
        $sets[] = $column . ' = ?';
    }

    $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $id_column . ' = ?';

    $values = array_values($data);
    $values[] = $id;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);

    return $stmt->rowCount();
}
