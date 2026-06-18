<?php
require_once __DIR__ . '/../models/manga.php';
require_once __DIR__ . '/../models/collection.php';

function manga_index(PDO $pdo, ?string $id = null): string
{
    header('Location: /catalogue');
    exit;
}

function manga_show(PDO $pdo, ?string $id = null): string
{
    if (empty($id)) {
        return error_page(400, 'Identifiant manquant.');
    }

    $manga = manga_get_one($id);

    if ($manga === null) {
        return error_page(404, 'Manga introuvable.');
    }

    $similaires   = manga_get_similar($manga['id']);
    $memberships  = is_logged_in()
        ? collection_get_item_memberships((int) $_SESSION['user_id'], $manga['id'])
        : [];

    return render_in_layout('manga/show', '_layout', [
        'page_title'  => $manga['title'] . ' — MangaShelf',
        'manga'       => $manga,
        'similaires'  => $similaires,
        'memberships' => $memberships,
    ]);
}
