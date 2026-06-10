<?php
function tag_get_genres(): array
{
    $stmt = db()->prepare(
        "SELECT id, name, slug FROM tag WHERE type = 'genre' ORDER BY name"
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

function tag_get_all(): array
{
    $stmt = db()->prepare(
        "SELECT id, name, slug, type FROM tag ORDER BY type, name"
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

function tag_get_by_id(int $id): ?array
{
    $stmt = db()->prepare("SELECT id, name, slug, type FROM tag WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

function tag_make_slug(string $name): string
{
    $slug = strtolower($name);
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug) ?: $slug;
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim($slug, '-');
}

function tag_create(string $name, string $type): void
{
    $slug = tag_make_slug($name);
    $stmt = db()->prepare("SELECT COUNT(*) FROM tag WHERE slug LIKE :slug");
    $stmt->execute([':slug' => $slug . '%']);
    $count = (int) $stmt->fetchColumn();
    if ($count > 0) $slug .= '-' . ($count + 1);

    $stmt = db()->prepare(
        "INSERT INTO tag (name, slug, type) VALUES (:name, :slug, :type)"
    );
    $stmt->execute([':name' => $name, ':slug' => $slug, ':type' => $type]);
}

function tag_update(int $id, string $name, string $type): void
{
    $stmt = db()->prepare(
        "UPDATE tag SET name = :name, type = :type WHERE id = :id"
    );
    $stmt->execute([':name' => $name, ':type' => $type, ':id' => $id]);
}

function tag_delete(int $id): void
{
    $del_links = db()->prepare("DELETE FROM item_tag WHERE tag_id = :id");
    $del_links->execute([':id' => $id]);

    $del = db()->prepare("DELETE FROM tag WHERE id = :id");
    $del->execute([':id' => $id]);
}
