<?php
require_once __DIR__ . '/../models/manga.php';
require_once __DIR__ . '/../models/tag.php';

function home_index(PDO $pdo, ?string $id = null): string
{
    $recent_mangas = manga_get_recent(6);
    $home_shelves = manga_get_home_shelves(6, 5);
    if (empty($home_shelves) && !empty($recent_mangas)) {
        $home_shelves[] = [
            'name' => 'Nouveautes',
            'slug' => 'nouveautes',
            'mangas' => $recent_mangas,
        ];
    }

    return render_in_layout('home/index', '_layout', [
        'page_title'    => 'MangaShelf — Catalogue de mangas',
        'genres'        => tag_get_genres(),
        'recent_mangas' => $recent_mangas,
        'home_shelves'  => $home_shelves,
    ]);
}
