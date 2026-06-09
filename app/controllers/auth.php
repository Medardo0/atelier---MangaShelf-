<?php
require_once __DIR__ . '/../models/operator.php';

function auth_connexion(?string $id = null): string
{
    if (is_logged_in()) {
        header('Location: /mangashelf/public/');
        exit;
    }

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $errors[] = 'Veuillez remplir tous les champs.';
        } else {
            $user = operator_find_by_email($email);

            if (!$user) {
                $errors[] = 'Email ou mot de passe incorrect.';
            } elseif (!empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
                $errors[] = 'Compte temporairement verrouillé. Réessayez dans quelques minutes.';
            } elseif (!password_verify($password, $user['password'])) {
                operator_increment_failed($user['id']);
                $errors[] = 'Email ou mot de passe incorrect.';
            } else {
                operator_reset_failed($user['id']);
                session_regenerate_id(true);
                $_SESSION['user_id']       = $user['id'];
                $_SESSION['username']      = $user['username'];
                $_SESSION['role']          = $user['role'];
                $_SESSION['last_activity'] = time();
                $_SESSION['csrf_token']    = bin2hex(random_bytes(32));
                header('Location: /mangashelf/public/');
                exit;
            }
        }
    }

    return render_in_layout('auth/connexion', 'layouts/main', [
        'page_title' => 'Connexion — MangaShelf',
        'errors'     => $errors,
    ]);
}

function auth_inscription(?string $id = null): string
{
    if (is_logged_in()) {
        header('Location: /mangashelf/public/');
        exit;
    }

    $errors = [];
    $old    = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();

        $username         = trim($_POST['username'] ?? '');
        $email            = trim($_POST['email'] ?? '');
        $password         = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        $old = compact('username', 'email');

        if (empty($username) || empty($email) || empty($password)) {
            $errors[] = 'Veuillez remplir tous les champs.';
        }
        if (strlen($username) < 3 || strlen($username) > 30) {
            $errors[] = 'Le pseudo doit contenir entre 3 et 30 caractères.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Adresse e-mail invalide.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        if ($password !== $password_confirm) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }
        if (empty($errors) && operator_email_exists($email)) {
            $errors[] = 'Cette adresse e-mail est déjà utilisée.';
        }
        if (empty($errors) && operator_username_exists($username)) {
            $errors[] = 'Ce pseudo est déjà pris.';
        }

        if (empty($errors)) {
            $user_id = operator_create($username, $email, $password);
            session_regenerate_id(true);
            $_SESSION['user_id']       = $user_id;
            $_SESSION['username']      = $username;
            $_SESSION['role']          = 'user';
            $_SESSION['last_activity'] = time();
            $_SESSION['csrf_token']    = bin2hex(random_bytes(32));
            header('Location: /mangashelf/public/');
            exit;
        }
    }

    return render_in_layout('auth/inscription', 'layouts/main', [
        'page_title' => 'Inscription — MangaShelf',
        'errors'     => $errors,
        'old'        => $old,
    ]);
}

function auth_logout(?string $id = null): string
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
    header('Location: /mangashelf/public/');
    exit;
}
