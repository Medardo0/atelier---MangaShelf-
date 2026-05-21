<?php
/**
 * app/controllers/manga.php
 * Controller d'une fiche manga.
 *
 * Routes gérées :
 *   GET /manga/show/{id} → manga_show($id)
 */

function manga_index(?string $id = null): string
{
    // Pas d'id → redirection vers catalogue
    header('Location: /catalogue');
    exit;
}

function manga_show(?string $id = null): string
{
    if (empty($id)) {
        return error_page(400, 'Missing manga id or slug.');
    }


    $manga = [
        'id'          => 1,
        'title'       => 'One Piece',
        'slug'        => 'one-piece',
        'author'      => 'Eiichiro Oda',
        'volumes'     => 110,
        'status'      => 'ongoing',
        'genres'      => ['Shonen', 'Action', 'Aventure'],
        'tags'        => ['Combat', 'Amitié', 'Pirates'],
        'synopsis'    => 'Monkey D. Luffy rêve de devenir le Roi des Pirates. Après avoir mangé un Fruit du Démon, son corps acquiert les propriétés du caoutchouc. Il part à la conquête du légendaire trésor One Piece.',
        'main_image'  => null,
    ];

    $data = [
        'page_title' => $manga['title'] . ' — MangaShelf',
        'manga'      => $manga,
        'similaires' => [
            ['id' => 6, 'title' => 'Jujutsu Kaisen', 'slug' => 'jujutsu-kaisen', 'genre' => 'Shonen'],
            ['id' => 3, 'title' => 'Demon Slayer',   'slug' => 'demon-slayer',   'genre' => 'Shonen'],
        ],
    ];

    return render_in_layout('manga/show', 'layouts/main', $data);
}
