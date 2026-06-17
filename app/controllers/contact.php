<?php
require_once __DIR__ . '/../models/message.php';

function contact_index(?string $id = null): string
{
    $errors = [];
    $old    = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();

        $old     = $_POST;
        $name    = trim($_POST['name']    ?? '');
        $email   = trim($_POST['email']   ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $body    = trim($_POST['body']    ?? '');

        if ($name    === '') $errors[] = 'Votre nom est obligatoire.';
        if ($email   === '') $errors[] = 'Votre adresse e-mail est obligatoire.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Adresse e-mail invalide.';
        if ($subject === '') $errors[] = 'Le sujet est obligatoire.';
        if ($body    === '') $errors[] = 'Le message est obligatoire.';

        if (empty($errors)) {
            message_create($name, $email, $subject, $body);
            header('Location: /contact?sent=1');
            exit;
        }
    }

    return render_in_layout('contact/index', 'layouts/main', [
        'page_title' => 'Contact — MangaShelf',
        'errors'     => $errors,
        'old'        => $old,
        'sent'       => isset($_GET['sent']),
    ]);
}
