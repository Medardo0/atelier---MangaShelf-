<?php
function manga_get_all(array $filters = []): array
{
    $params = [];

    $sql = "SELECT i.id, i.title, i.slug, i.author, i.volumes,
                   i.series_status AS status, i.short_description, i.main_image,
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
        "SELECT i.id, i.title, i.slug, i.main_image, i.series_status AS status,
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

function manga_get_home_shelves(int $items_per_shelf = 6, int $max_shelves = 4): array
{
    $stmt = db()->query(
        "SELECT i.id, i.title, i.slug, i.author, i.main_image,
                i.series_status AS status, t.name AS genre, t.slug AS genre_slug
         FROM item i
         JOIN item_tag it ON it.item_id = i.id
         JOIN tag t ON t.id = it.tag_id AND t.type = 'genre'
         WHERE i.status = 'published'
         ORDER BY
           CASE
             WHEN t.name IN ('Romance', 'Fantasy', 'Science-fiction', 'Shonen', 'Seinen', 'Action', 'Aventure', 'Drame')
             THEN FIELD(t.name, 'Romance', 'Fantasy', 'Science-fiction', 'Shonen', 'Seinen', 'Action', 'Aventure', 'Drame')
             ELSE 999
           END,
           t.name ASC,
           i.created_at DESC,
           i.title ASC"
    );

    $shelves = [];
    foreach ($stmt->fetchAll() as $manga) {
        $genre = $manga['genre'];
        if (!isset($shelves[$genre])) {
            if (count($shelves) >= $max_shelves) {
                continue;
            }
            $shelves[$genre] = [
                'name' => $genre,
                'slug' => $manga['genre_slug'],
                'mangas' => [],
            ];
        }

        if (count($shelves[$genre]['mangas']) < $items_per_shelf) {
            $shelves[$genre]['mangas'][] = $manga;
        }
    }

    return array_values($shelves);
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

// ── Admin CRUD ────────────────────────────────────────────────

function manga_get_all_admin(): array
{
    $stmt = db()->query(
        "SELECT id, title, slug, author, volumes, series_status, status, created_at
         FROM item
         ORDER BY created_at DESC"
    );
    return $stmt->fetchAll();
}

function manga_get_by_id(int $id): ?array
{
    $stmt = db()->prepare(
        "SELECT id, title, slug, author, volumes, series_status,
                content, short_description, main_image, status
         FROM item WHERE id = :id"
    );
    $stmt->execute([':id' => $id]);
    $manga = $stmt->fetch();
    if (!$manga) return null;

    $stmt2 = db()->prepare("SELECT tag_id FROM item_tag WHERE item_id = :id");
    $stmt2->execute([':id' => $id]);
    $manga['tag_ids'] = array_column($stmt2->fetchAll(), 'tag_id');

    return $manga;
}

function manga_make_slug(string $title): string
{
    $slug = strtolower($title);
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug) ?: $slug;
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim($slug, '-');
}

function manga_sync_tags(int $manga_id, array $tag_ids): void
{
    $del = db()->prepare("DELETE FROM item_tag WHERE item_id = :id");
    $del->execute([':id' => $manga_id]);

    if (empty($tag_ids)) return;

    $ins = db()->prepare("INSERT INTO item_tag (item_id, tag_id) VALUES (:item_id, :tag_id)");
    foreach ($tag_ids as $tag_id) {
        $ins->execute([':item_id' => $manga_id, ':tag_id' => (int) $tag_id]);
    }
}

function manga_create(array $data, array $tag_ids): int
{
    $slug = manga_make_slug($data['title']);
    $stmt = db()->prepare("SELECT COUNT(*) FROM item WHERE slug LIKE :slug");
    $stmt->execute([':slug' => $slug . '%']);
    $count = (int) $stmt->fetchColumn();
    if ($count > 0) $slug .= '-' . ($count + 1);

    $stmt = db()->prepare(
        "INSERT INTO item (title, slug, author, volumes, series_status, content, short_description, main_image, status)
         VALUES (:title, :slug, :author, :volumes, :series_status, :content, :short_description, :main_image, :status)"
    );
    $stmt->execute([
        ':title'             => $data['title'],
        ':slug'              => $slug,
        ':author'            => $data['author'],
        ':volumes'           => (int) ($data['volumes'] ?? 0),
        ':series_status'     => $data['series_status'],
        ':content'           => $data['content'] ?? '',
        ':short_description' => $data['short_description'] ?? '',
        ':main_image'        => $data['main_image'] ?? null,
        ':status'            => $data['status'] ?? 'published',
    ]);
    $id = (int) db()->lastInsertId();
    manga_sync_tags($id, $tag_ids);
    return $id;
}

function manga_update(int $id, array $data, array $tag_ids): void
{
    $image_sql = array_key_exists('main_image', $data) ? ', main_image=:main_image' : '';

    $stmt = db()->prepare(
        "UPDATE item
         SET title=:title, author=:author, volumes=:volumes,
             series_status=:series_status, content=:content,
             short_description=:short_description, status=:status
             $image_sql
         WHERE id=:id"
    );
    $params = [
        ':title'             => $data['title'],
        ':author'            => $data['author'],
        ':volumes'           => (int) ($data['volumes'] ?? 0),
        ':series_status'     => $data['series_status'],
        ':content'           => $data['content'] ?? '',
        ':short_description' => $data['short_description'] ?? '',
        ':status'            => $data['status'] ?? 'published',
        ':id'                => $id,
    ];
    if (array_key_exists('main_image', $data)) {
        $params[':main_image'] = $data['main_image'];
    }
    $stmt->execute($params);
    manga_sync_tags($id, $tag_ids);
}

function manga_delete(int $id): void
{
    $del_tags = db()->prepare("DELETE FROM item_tag WHERE item_id = :id");
    $del_tags->execute([':id' => $id]);

    $del = db()->prepare("DELETE FROM item WHERE id = :id");
    $del->execute([':id' => $id]);
}
