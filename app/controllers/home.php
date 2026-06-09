<?php
require_once __DIR__ . '/../models/manga.php';
require_once __DIR__ . '/../models/tag.php';

function home_index(?string $id = null): string
{
    return render_in_layout('home/index', 'layouts/main', [
        'page_title'    => 'MangaShelf — Catalogue de mangas',
        'genres'        => tag_get_genres(),
        'recent_mangas' => manga_get_recent(4),
    ]);
}
