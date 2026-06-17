<?php
/**
 * app/controllers/admin.php
 * Controller d'administration.
 *
 * Routes gérées :
 *   GET  /admin              → admin_index()   redirige vers dashboard
 *   GET  /admin/login        → admin_login()
 *   POST /admin/login        → admin_login()
 *   GET  /admin/dashboard    → admin_dashboard()
 *   POST /admin/logout       → admin_logout()
 */

require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../models/manga.php';
require_once __DIR__ . '/../models/tag.php';
require_once __DIR__ . '/../models/message.php';

function upload_cover(string $slug): ?string
{
    if (empty($_FILES['cover']['name']) || $_FILES['cover']['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES['cover'];
    if ($file['error'] !== UPLOAD_ERR_OK) return null;

    $finfo   = new finfo(FILEINFO_MIME_TYPE);
    $mime    = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) return null;
    if ($file['size'] > 2 * 1024 * 1024) return null;

    $filename = $slug . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    $dest     = __DIR__ . '/../../public/assets/uploads/' . $filename;

    return move_uploaded_file($file['tmp_name'], $dest) ? $filename : null;
}

function admin_index(?string $id = null): string
{
    require_auth('admin');

    $stmt = db()->query(
        "SELECT i.id, i.title, i.status, i.created_at,
                (SELECT t.name FROM item_tag it JOIN tag t ON t.id = it.tag_id
                 WHERE it.item_id = i.id AND t.type = 'genre'
                 ORDER BY t.name LIMIT 1) AS genre
         FROM item i ORDER BY i.created_at DESC LIMIT 5"
    );

    $data = [
        'page_title'    => 'Dashboard',
        'active_nav'    => 'dashboard',
        'stats'         => [
            'mangas_published' => (int) db()->query("SELECT COUNT(*) FROM item WHERE status = 'published'")->fetchColumn(),
            'messages_unread'  => message_count_unread(),
            'genres_count'     => (int) db()->query("SELECT COUNT(*) FROM tag")->fetchColumn(),
        ],
        'recent_mangas' => $stmt->fetchAll(),
    ];

    return render_in_layout('admin/dashboard/index', 'layouts/admin', $data);
}

// function admin_login(?string $id = null): string
// {
//     // Déjà connecté → dashboard
//     if (is_logged_in() && current_role() === 'admin') {
//         header('Location: /admin/dashboard');
//         exit;
//     }

//     // Traitement POST
//     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//         verify_csrf();

//         $username = trim($_POST['username'] ?? '');
//         $password = $_POST['password'] ?? '';

//         $stmt = db()->prepare(
//             'SELECT id, username, password, role FROM operator WHERE username = ? LIMIT 1'
//         );
//         $stmt->execute([$username]);
//         $operator = $stmt->fetch();

//         if ($operator && $operator['role'] === 'admin' && password_verify($password, $operator['password'])) {
//             session_regenerate_id(true);
//             $_SESSION['user_id']       = $operator['id'];
//             $_SESSION['username']      = $operator['username'];
//             $_SESSION['role']          = $operator['role'];
//             $_SESSION['last_activity'] = time();
//             $_SESSION['csrf_token']    = bin2hex(random_bytes(32));

//             header('Location: /admin/dashboard');
//             exit;
//         }

//         return render('admin/login/index', ['error' => true]);
//     }

//     // GET → afficher le formulaire
//     return render('admin/login/index', [
//         'error'   => isset($_GET['error']),
//         'expired' => isset($_GET['expired']),
//     ]);
// }


function admin_mangas(?string $id = null): string
{
    require_auth('admin');

    return render_in_layout('admin/manga/list', 'layouts/admin', [
        'page_title' => 'Mangas — Admin',
        'active_nav' => 'mangas',
        'mangas'     => manga_get_all_admin(),
    ]);
}

function admin_manga_create(?string $id = null): string
{
    require_auth('admin');

    $errors = [];
    $old    = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();

        $old    = $_POST;
        $title  = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');

        if ($title  === '') $errors[] = 'Le titre est obligatoire.';
        if ($author === '') $errors[] = "L'auteur est obligatoire.";

        if (empty($errors)) {
            $tag_ids = array_map('intval', (array) ($_POST['tags'] ?? []));
            $slug    = manga_make_slug($title);
            $cover   = upload_cover($slug);
            manga_create([
                'title'             => $title,
                'author'            => $author,
                'volumes'           => (int) ($_POST['volumes'] ?? 0),
                'series_status'     => in_array($_POST['series_status'] ?? '', ['ongoing', 'completed', 'on_hold'], true)
                                           ? $_POST['series_status'] : 'ongoing',
                'content'           => trim($_POST['content'] ?? ''),
                'short_description' => trim($_POST['short_description'] ?? ''),
                'status'            => ($_POST['status'] ?? '') === 'draft' ? 'draft' : 'published',
                'main_image'        => $cover,
            ], $tag_ids);

            header('Location: /admin/mangas');
            exit;
        }
    }

    return render_in_layout('admin/manga/form', 'layouts/admin', [
        'page_title' => 'Ajouter un manga — Admin',
        'active_nav' => 'mangas',
        'errors'     => $errors,
        'old'        => $old,
        'manga'      => null,
        'all_tags'   => tag_get_all(),
    ]);
}

function admin_manga_edit(?string $id = null): string
{
    require_auth('admin');

    if (empty($id)) {
        return error_page(400, 'Identifiant manquant.');
    }

    $manga = manga_get_by_id((int) $id);
    if ($manga === null) {
        return error_page(404, 'Manga introuvable.');
    }

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();

        $title  = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');

        if ($title  === '') $errors[] = 'Le titre est obligatoire.';
        if ($author === '') $errors[] = "L'auteur est obligatoire.";

        if (empty($errors)) {
            $tag_ids  = array_map('intval', (array) ($_POST['tags'] ?? []));
            $new_data = [
                'title'             => $title,
                'author'            => $author,
                'volumes'           => (int) ($_POST['volumes'] ?? 0),
                'series_status'     => in_array($_POST['series_status'] ?? '', ['ongoing', 'completed', 'on_hold'], true)
                                           ? $_POST['series_status'] : 'ongoing',
                'content'           => trim($_POST['content'] ?? ''),
                'short_description' => trim($_POST['short_description'] ?? ''),
                'status'            => ($_POST['status'] ?? '') === 'draft' ? 'draft' : 'published',
            ];
            $cover = upload_cover($manga['slug']);
            if ($cover !== null) {
                // Supprimer l'ancienne image si elle existait
                if (!empty($manga['main_image'])) {
                    @unlink(__DIR__ . '/../../public/assets/uploads/' . $manga['main_image']);
                }
                $new_data['main_image'] = $cover;
            }
            manga_update((int) $id, $new_data, $tag_ids);

            header('Location: /admin/mangas');
            exit;
        }

        // Erreur : réafficher avec les valeurs POST
        $manga = array_merge($manga, [
            'title'             => $title,
            'author'            => $author,
            'volumes'           => $_POST['volumes'] ?? $manga['volumes'],
            'series_status'     => $_POST['series_status'] ?? $manga['series_status'],
            'content'           => $_POST['content'] ?? $manga['content'],
            'short_description' => $_POST['short_description'] ?? $manga['short_description'],
            'status'            => $_POST['status'] ?? $manga['status'],
            'tag_ids'           => array_map('intval', (array) ($_POST['tags'] ?? [])),
        ]);
    }

    return render_in_layout('admin/manga/form', 'layouts/admin', [
        'page_title' => 'Modifier ' . $manga['title'] . ' — Admin',
        'active_nav' => 'mangas',
        'errors'     => $errors,
        'old'        => [],
        'manga'      => $manga,
        'all_tags'   => tag_get_all(),
    ]);
}

function admin_manga_delete(?string $id = null): string
{
    require_auth('admin');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($id)) {
        verify_csrf();
        manga_delete((int) $id);
    }

    header('Location: /admin/mangas');
    exit;
}

function admin_genres(?string $id = null): string
{
    require_auth('admin');
    return render_in_layout('admin/genres/index', 'layouts/admin', [
        'page_title' => 'Genres & Tags — Admin',
        'active_nav' => 'genres',
        'tags'       => tag_get_all(),
    ]);
}

function admin_genre_create(?string $id = null): string
{
    require_auth('admin');

    $errors = [];
    $old    = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();

        $old  = $_POST;
        $name = trim($_POST['name'] ?? '');
        $type = in_array($_POST['type'] ?? '', ['genre', 'tag'], true) ? $_POST['type'] : 'tag';

        if ($name === '') $errors[] = 'Le nom est obligatoire.';

        if (empty($errors)) {
            tag_create($name, $type);
            header('Location: /admin/genres');
            exit;
        }
    }

    return render_in_layout('admin/genres/form', 'layouts/admin', [
        'page_title' => 'Ajouter un genre/tag — Admin',
        'active_nav' => 'genres',
        'errors'     => $errors,
        'old'        => $old,
        'tag'        => null,
    ]);
}

function admin_genre_edit(?string $id = null): string
{
    require_auth('admin');

    if (empty($id)) return error_page(400, 'Identifiant manquant.');

    $tag = tag_get_by_id((int) $id);
    if ($tag === null) return error_page(404, 'Genre/tag introuvable.');

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();

        $name = trim($_POST['name'] ?? '');
        $type = in_array($_POST['type'] ?? '', ['genre', 'tag'], true) ? $_POST['type'] : $tag['type'];

        if ($name === '') $errors[] = 'Le nom est obligatoire.';

        if (empty($errors)) {
            tag_update((int) $id, $name, $type);
            header('Location: /admin/genres');
            exit;
        }

        $tag = array_merge($tag, ['name' => $name, 'type' => $type]);
    }

    return render_in_layout('admin/genres/form', 'layouts/admin', [
        'page_title' => 'Modifier ' . $tag['name'] . ' — Admin',
        'active_nav' => 'genres',
        'errors'     => $errors,
        'old'        => [],
        'tag'        => $tag,
    ]);
}

function admin_genre_delete(?string $id = null): string
{
    require_auth('admin');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($id)) {
        verify_csrf();
        tag_delete((int) $id);
    }

    header('Location: /admin/genres');
    exit;
}

function admin_messages(?string $id = null): string
{
    require_auth('admin');
    return render_in_layout('admin/messages/index', 'layouts/admin', [
        'page_title' => 'Messages — Admin',
        'active_nav' => 'messages',
        'messages'   => message_get_all(),
        'stats'      => ['messages_unread' => message_count_unread()],
    ]);
}

function admin_message_show(?string $id = null): string
{
    require_auth('admin');

    if (empty($id)) return error_page(400, 'Identifiant manquant.');

    $message = message_get_by_id((int) $id);
    if ($message === null) return error_page(404, 'Message introuvable.');

    message_mark_read((int) $id);

    return render_in_layout('admin/messages/show', 'layouts/admin', [
        'page_title' => $message['subject'] . ' — Admin',
        'active_nav' => 'messages',
        'message'    => $message,
        'stats'      => ['messages_unread' => message_count_unread()],
    ]);
}

function admin_message_delete(?string $id = null): string
{
    require_auth('admin');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($id)) {
        verify_csrf();
        message_delete((int) $id);
    }

    header('Location: /admin/messages');
    exit;
}

// function admin_logout(?string $id = null): string
// {
//     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//         verify_csrf();
//         $_SESSION = [];
//         if (ini_get('session.use_cookies')) {
//             $params = session_get_cookie_params();
//             setcookie(session_name(), '', time() - 42000,
//                 $params['path'], $params['domain'],
//                 $params['secure'], $params['httponly']);
//         }
//         session_destroy();
//     }
//     header('Location: /admin/login');
//     exit;
// }
