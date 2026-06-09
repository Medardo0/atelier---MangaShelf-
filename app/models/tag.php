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
