<?php
require_once __DIR__ . '/../models/manga.php';
require_once __DIR__ . '/../models/tag.php';

function catalogue_index(?string $id = null): string
{
    $filters = [];

    if (!empty($_GET['q'])) {
        $filters['q'] = trim($_GET['q']);
    }
    if (!empty($_GET['genres'])) {
        $filters['genres'] = array_map('trim', (array) $_GET['genres']);
    }
    if (!empty($_GET['tags'])) {
        $filters['tags'] = array_map('trim', (array) $_GET['tags']);
    }
    if (!empty($_GET['page'])) {
        $filters['page'] = max(1, (int) $_GET['page']);
    }
    if (!empty($_GET['sort']) && in_array($_GET['sort'], ['title', 'date'], true)) {
        $filters['sort'] = $_GET['sort'];
    }

    $per_page    = 12;
    $total       = manga_count($filters);
    $total_pages = max(1, (int) ceil($total / $per_page));
    $current_page = $filters['page'] ?? 1;

    return render_in_layout('catalogue/index', 'layouts/main', [
        'page_title'   => 'Catalogue — MangaShelf',
        'mangas'       => manga_get_all($filters),
        'total'        => $total,
        'total_pages'  => $total_pages,
        'current_page' => $current_page,
        'genres'       => tag_get_genres(),
        'filters'      => $filters,
    ]);
}
