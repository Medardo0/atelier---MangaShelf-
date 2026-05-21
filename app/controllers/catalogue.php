<?php
/**
 * app/controllers/catalogue.php
 * Controller du catalogue de mangas.
 *
 * Routes gérées :
 *   GET /catalogue → catalogue_index()
 */

function catalogue_index(?string $id = null): string
{
 
    $data = [
        'page_title' => 'Catalogue',
        'mangas'     => [
            ['id' => 1, 'title' => 'One Piece',      'slug' => 'one-piece',      'author' => 'Eiichiro Oda',    'genre' => 'Shonen', 'volumes' => 110, 'status' => 'ongoing'],
            ['id' => 2, 'title' => 'Berserk',         'slug' => 'berserk',         'author' => 'Kentaro Miura',   'genre' => 'Seinen', 'volumes' => 42,  'status' => 'ongoing'],
            ['id' => 3, 'title' => 'Demon Slayer',    'slug' => 'demon-slayer',    'author' => 'Koyoharu Gotouge','genre' => 'Shonen', 'volumes' => 23,  'status' => 'completed'],
            ['id' => 4, 'title' => 'Vinland Saga',    'slug' => 'vinland-saga',    'author' => 'Makoto Yukimura', 'genre' => 'Seinen', 'volumes' => 27,  'status' => 'ongoing'],
            ['id' => 5, 'title' => 'Attack on Titan', 'slug' => 'attack-on-titan', 'author' => 'Hajime Isayama',  'genre' => 'Shonen', 'volumes' => 34,  'status' => 'completed'],
            ['id' => 6, 'title' => 'Jujutsu Kaisen',  'slug' => 'jujutsu-kaisen',  'author' => 'Gege Akutami',    'genre' => 'Shonen', 'volumes' => 26,  'status' => 'ongoing'],
        ],
        'genres'     => ['Shonen', 'Seinen', 'Shojo', 'Isekai', 'Action', 'Romance', 'Horreur'],
        'total'      => 6,
    ];

    return render_in_layout('catalogue/index', 'layouts/main', $data);
}
