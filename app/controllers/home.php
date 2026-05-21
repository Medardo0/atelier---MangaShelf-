<?php
/**
 * app/controllers/home.php
 * Controller de la page d'accueil.
 *
 * Routes gérées :
 *   GET / → home_index()
 */

function home_index(?string $id = null): string
{
    $data = [
        'page_title'    => 'MangaShelf — Catalogue de mangas',
        'genres'        => ['Shonen', 'Seinen', 'Shojo'],
        'recent_mangas' => [
            ['id' => 6, 'title' => 'Jujutsu Kaisen',  'slug' => 'jujutsu-kaisen',  'genre' => 'Shonen'],
            ['id' => 5, 'title' => 'Attack on Titan',  'slug' => 'attack-on-titan', 'genre' => 'Shonen'],
            ['id' => 4, 'title' => 'Vinland Saga',     'slug' => 'vinland-saga',    'genre' => 'Seinen'],
            ['id' => 1, 'title' => 'One Piece',        'slug' => 'one-piece',       'genre' => 'Shonen'],
        ],
    ];

    return render_in_layout('home/index', 'layouts/main', $data);
}
