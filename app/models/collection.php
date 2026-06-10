<?php
function collection_get_user_list(int $user_id): array
{
    $stmt = db()->prepare(
        "SELECT c.id, c.type, COUNT(ci.item_id) AS count
         FROM collection c
         LEFT JOIN collection_item ci ON ci.collection_id = c.id
         WHERE c.operator_id = :user_id
         GROUP BY c.id, c.type
         ORDER BY FIELD(c.type, 'favorites', 'wishlist', 'reading', 'completed')"
    );
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll();
}

function collection_get_items(int $collection_id): array
{
    $stmt = db()->prepare(
        "SELECT i.id, i.title, i.slug, i.author, i.volumes, i.series_status AS status
         FROM collection_item ci
         JOIN item i ON i.id = ci.item_id
         WHERE ci.collection_id = :collection_id
         ORDER BY i.title"
    );
    $stmt->execute([':collection_id' => $collection_id]);
    return $stmt->fetchAll();
}

function collection_get_item_memberships(int $user_id, int $item_id): array
{
    $stmt = db()->prepare(
        "SELECT c.id, c.type,
                (ci.item_id IS NOT NULL) AS has_item
         FROM collection c
         LEFT JOIN collection_item ci ON ci.collection_id = c.id AND ci.item_id = :item_id
         WHERE c.operator_id = :user_id
         ORDER BY FIELD(c.type, 'favorites', 'wishlist', 'reading', 'completed')"
    );
    $stmt->execute([':user_id' => $user_id, ':item_id' => $item_id]);
    return $stmt->fetchAll();
}

function collection_belongs_to_user(int $collection_id, int $user_id): bool
{
    $stmt = db()->prepare(
        "SELECT 1 FROM collection WHERE id = :id AND operator_id = :user_id LIMIT 1"
    );
    $stmt->execute([':id' => $collection_id, ':user_id' => $user_id]);
    return (bool) $stmt->fetchColumn();
}

function collection_item_exists(int $collection_id, int $item_id): bool
{
    $stmt = db()->prepare(
        "SELECT 1 FROM collection_item
         WHERE collection_id = :collection_id AND item_id = :item_id LIMIT 1"
    );
    $stmt->execute([':collection_id' => $collection_id, ':item_id' => $item_id]);
    return (bool) $stmt->fetchColumn();
}

function collection_item_add(int $collection_id, int $item_id): void
{
    if (collection_item_exists($collection_id, $item_id)) return;
    $stmt = db()->prepare(
        "INSERT INTO collection_item (collection_id, item_id) VALUES (:collection_id, :item_id)"
    );
    $stmt->execute([':collection_id' => $collection_id, ':item_id' => $item_id]);
}

function collection_item_remove(int $collection_id, int $item_id): void
{
    $stmt = db()->prepare(
        "DELETE FROM collection_item
         WHERE collection_id = :collection_id AND item_id = :item_id"
    );
    $stmt->execute([':collection_id' => $collection_id, ':item_id' => $item_id]);
}
