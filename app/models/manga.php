<?php
function manga_get_all(array $filters = []): array
{
    $params = [];

    $sql = "SELECT i.id, i.title, i.slug, i.author, i.volumes,
                   i.series_status AS status, i.short_description,
                   (SELECT t.name FROM item_tag it JOIN tag t ON t.id = it.tag_id
                    WHERE it.item_id = i.id AND t.type = 'genre'
                    ORDER BY t.name LIMIT 1) AS genre
            FROM item i
            WHERE i.status = 'published'";

    if (!empty($filters['q'])) {
        $sql .= " AND (i.title LIKE :q_title OR i.author LIKE :q_author)";
        $q = '%' . trim($filters['q']) . '%';
        $params[':q_title']  = $q;
        $params[':q_author'] = $q;
    }

    if (!empty($filters['genres'])) {
        $in = [];
        foreach ($filters['genres'] as $k => $g) {
            $key = ':genre_' . $k;
            $in[]        = $key;
            $params[$key] = $g;
        }
        $sql .= " AND EXISTS (
            SELECT 1 FROM item_tag it JOIN tag t ON t.id = it.tag_id
            WHERE it.item_id = i.id AND t.type = 'genre'
            AND t.name IN (" . implode(',', $in) . "))";
    }

    if (!empty($filters['tags'])) {
        $in = [];
        foreach ($filters['tags'] as $k => $t) {
            $key = ':tag_' . $k;
            $in[]        = $key;
            $params[$key] = $t;
        }
        $sql .= " AND EXISTS (
            SELECT 1 FROM item_tag it2 JOIN tag t2 ON t2.id = it2.tag_id
            WHERE it2.item_id = i.id AND t2.type = 'tag'
            AND t2.name IN (" . implode(',', $in) . "))";
    }

    $allowed_sorts = ['title' => 'i.title ASC', 'date' => 'i.created_at DESC'];
    $sql .= " ORDER BY " . ($allowed_sorts[$filters['sort'] ?? ''] ?? 'i.created_at DESC');

    $page     = max(1, (int)($filters['page'] ?? 1));
    $per_page = 12;
    $offset   = ($page - 1) * $per_page;

    $stmt = db()->prepare($sql . " LIMIT :limit OFFSET :offset");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit',  $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset,   PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function manga_count(array $filters = []): int
{
    $params = [];

    $sql = "SELECT COUNT(DISTINCT i.id) FROM item i WHERE i.status = 'published'";

    if (!empty($filters['q'])) {
        $sql .= " AND (i.title LIKE :q_title OR i.author LIKE :q_author)";
        $q = '%' . trim($filters['q']) . '%';
        $params[':q_title']  = $q;
        $params[':q_author'] = $q;
    }

    if (!empty($filters['genres'])) {
        $in = [];
        foreach ($filters['genres'] as $k => $g) {
            $key = ':genre_' . $k;
            $in[]        = $key;
            $params[$key] = $g;
        }
        $sql .= " AND EXISTS (
            SELECT 1 FROM item_tag it JOIN tag t ON t.id = it.tag_id
            WHERE it.item_id = i.id AND t.type = 'genre'
            AND t.name IN (" . implode(',', $in) . "))";
    }

    if (!empty($filters['tags'])) {
        $in = [];
        foreach ($filters['tags'] as $k => $t) {
            $key = ':tag_' . $k;
            $in[]        = $key;
            $params[$key] = $t;
        }
        $sql .= " AND EXISTS (
            SELECT 1 FROM item_tag it2 JOIN tag t2 ON t2.id = it2.tag_id
            WHERE it2.item_id = i.id AND t2.type = 'tag'
            AND t2.name IN (" . implode(',', $in) . "))";
    }

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

function manga_get_one(string $slug): ?array
{
    $stmt = db()->prepare(
        "SELECT i.id, i.title, i.slug, i.author, i.volumes,
                i.series_status AS status, i.content AS synopsis, i.main_image
         FROM item i
         WHERE i.slug = :slug AND i.status = 'published'"
    );
    $stmt->execute([':slug' => $slug]);
    $manga = $stmt->fetch();

    if (!$manga) {
        return null;
    }

    $stmt2 = db()->prepare(
        "SELECT t.name, t.type FROM tag t
         JOIN item_tag it ON it.tag_id = t.id
         WHERE it.item_id = :id
         ORDER BY t.type, t.name"
    );
    $stmt2->execute([':id' => $manga['id']]);

    $manga['genres'] = [];
    $manga['tags']   = [];
    foreach ($stmt2->fetchAll() as $tag) {
        if ($tag['type'] === 'genre') {
            $manga['genres'][] = $tag['name'];
        } else {
            $manga['tags'][] = $tag['name'];
        }
    }

    return $manga;
}

function manga_get_recent(int $limit = 4): array
{
    $stmt = db()->prepare(
        "SELECT i.id, i.title, i.slug, i.series_status AS status,
                (SELECT t.name FROM item_tag it JOIN tag t ON t.id = it.tag_id
                 WHERE it.item_id = i.id AND t.type = 'genre'
                 ORDER BY t.name LIMIT 1) AS genre
         FROM item i
         WHERE i.status = 'published'
         ORDER BY i.created_at DESC
         LIMIT :limit"
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function manga_get_similar(int $manga_id, int $limit = 3): array
{
    $stmt = db()->prepare(
        "SELECT DISTINCT i.id, i.title, i.slug
         FROM item i
         JOIN item_tag it ON it.item_id = i.id
         WHERE i.id != :id
           AND i.status = 'published'
           AND it.tag_id IN (SELECT tag_id FROM item_tag WHERE item_id = :id2)
         ORDER BY i.created_at DESC
         LIMIT :limit"
    );
    $stmt->bindValue(':id',    $manga_id, PDO::PARAM_INT);
    $stmt->bindValue(':id2',   $manga_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit,    PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}