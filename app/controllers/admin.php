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

function admin_index(?string $id = null): string
{
    header('Location: /admin/dashboard');
    exit;
}

function admin_login(?string $id = null): string
{
    // Déjà connecté → dashboard
    if (is_logged_in() && current_role() === 'admin') {
        header('Location: /admin/dashboard');
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

            header('Location: /admin/dashboard');
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
