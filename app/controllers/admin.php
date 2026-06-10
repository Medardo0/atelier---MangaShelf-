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

function admin_index(?string $id = null): string
{
    header('Location: /mangashelf/public/admin/dashboard');
    exit;
}

function admin_login(?string $id = null): string
{
    // Déjà connecté → dashboard
    if (is_logged_in() && current_role() === 'admin') {
        header('Location: /mangashelf/public/admin/dashboard');
        exit;
    }

    // Traitement POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = db()->prepare(
            'SELECT id, username, password, role FROM operator WHERE username = ? LIMIT 1'
        );
        $stmt->execute([$username]);
        $operator = $stmt->fetch();

        if ($operator && $operator['role'] === 'admin' && password_verify($password, $operator['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']       = $operator['id'];
            $_SESSION['username']      = $operator['username'];
            $_SESSION['role']          = $operator['role'];
            $_SESSION['last_activity'] = time();
            $_SESSION['csrf_token']    = bin2hex(random_bytes(32));

            header('Location: /mangashelf/public/admin/dashboard');
            exit;
        }

        return render('admin/login/index', ['error' => true]);
    }

    // GET → afficher le formulaire
    return render('admin/login/index', [
        'error'   => isset($_GET['error']),
        'expired' => isset($_GET['expired']),
    ]);
}

function admin_dashboard(?string $id = null): string
{
    require_auth('admin');

    $data = [
        'page_title'      => 'Dashboard',
        'active_nav'      => 'dashboard',
        'stats'           => [
            'mangas_published' => 6,
            'messages_unread'  => 3,
            'genres_count'     => 13,
            'users_count'      => 2,
        ],
        'recent_mangas'   => [
            ['id' => 6, 'title' => 'Jujutsu Kaisen',  'genre' => 'Shonen', 'status' => 'published', 'created_at' => '2025-05-18'],
            ['id' => 5, 'title' => 'Attack on Titan',  'genre' => 'Shonen', 'status' => 'published', 'created_at' => '2025-05-17'],
            ['id' => 4, 'title' => 'Vinland Saga',     'genre' => 'Seinen', 'status' => 'published', 'created_at' => '2025-05-16'],
        ],
        'unread_messages' => [
            ['id' => 1, 'sender' => 'Jean Dupont',  'subject' => 'Question sur un manga', 'created_at' => '2025-05-19'],
            ['id' => 2, 'sender' => 'Marie Martin', 'subject' => 'Signalement d\'erreur',  'created_at' => '2025-05-18'],
            ['id' => 3, 'sender' => 'Alex Bernard', 'subject' => 'Suggestion d\'ajout',    'created_at' => '2025-05-17'],
        ],
    ];

    return render_in_layout('admin/dashboard/index', 'layouts/admin', $data);
}

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
            manga_create([
                'title'             => $title,
                'author'            => $author,
                'volumes'           => (int) ($_POST['volumes'] ?? 0),
                'series_status'     => in_array($_POST['series_status'] ?? '', ['ongoing', 'completed', 'on_hold'], true)
                                           ? $_POST['series_status'] : 'ongoing',
                'content'           => trim($_POST['content'] ?? ''),
                'short_description' => trim($_POST['short_description'] ?? ''),
                'status'            => ($_POST['status'] ?? '') === 'draft' ? 'draft' : 'published',
            ], $tag_ids);

            header('Location: /mangashelf/public/admin/mangas');
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
            $tag_ids = array_map('intval', (array) ($_POST['tags'] ?? []));
            manga_update((int) $id, [
                'title'             => $title,
                'author'            => $author,
                'volumes'           => (int) ($_POST['volumes'] ?? 0),
                'series_status'     => in_array($_POST['series_status'] ?? '', ['ongoing', 'completed', 'on_hold'], true)
                                           ? $_POST['series_status'] : 'ongoing',
                'content'           => trim($_POST['content'] ?? ''),
                'short_description' => trim($_POST['short_description'] ?? ''),
                'status'            => ($_POST['status'] ?? '') === 'draft' ? 'draft' : 'published',
            ], $tag_ids);

            header('Location: /mangashelf/public/admin/mangas');
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

    header('Location: /mangashelf/public/admin/mangas');
    exit;
}

function admin_genres(?string $id = null): string
{
    require_auth('admin');
    return render_in_layout('admin/genres/index', 'layouts/admin', [
        'page_title' => 'Genres & Tags — Admin',
        'active_nav' => 'genres',
    ]);
}

function admin_messages(?string $id = null): string
{
    require_auth('admin');
    return render_in_layout('admin/messages/index', 'layouts/admin', [
        'page_title' => 'Messages — Admin',
        'active_nav' => 'messages',
    ]);
}

function admin_logout(?string $id = null): string
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
    header('Location: /admin/login');
    exit;
}
