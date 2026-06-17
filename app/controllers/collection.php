<?php
require_once __DIR__ . '/../models/collection.php';

function collection_safe_redirect(string $redirect_to): string
{
    if ($redirect_to === '' || $redirect_to[0] !== '/') {
        return '/collection';
    }

    if (str_starts_with($redirect_to, '//') || preg_match('#[\r\n]#', $redirect_to)) {
        return '/collection';
    }

    return $redirect_to;
}

function collection_index(?string $id = null): string
{
    if (!is_logged_in()) {
        header('Location: /auth/connexion');
        exit;
    }

    $user_id     = (int) $_SESSION['user_id'];
    $collections = collection_get_user_list($user_id);

    $items_by_collection = [];
    foreach ($collections as $col) {
        $items_by_collection[$col['id']] = collection_get_items((int) $col['id']);
    }

    return render_in_layout('collection/index', 'layouts/main', [
        'page_title'          => 'Mes collections — MangaShelf',
        'collections'         => $collections,
        'items_by_collection' => $items_by_collection,
    ]);
}

function collection_add(?string $id = null): string
{
    if (!is_logged_in()) {
        header('Location: /auth/connexion');
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /collection');
        exit;
    }
    verify_csrf();

    $user_id       = (int) $_SESSION['user_id'];
    $collection_id = (int) ($_POST['collection_id'] ?? 0);
    $item_id       = (int) ($_POST['item_id'] ?? 0);
    $redirect_to   = collection_safe_redirect($_POST['redirect_to'] ?? '/collection');

    if ($collection_id && $item_id && collection_belongs_to_user($collection_id, $user_id)) {
        collection_item_add($collection_id, $item_id);
    }

    header('Location: ' . $redirect_to);
    exit;
}

function collection_remove(?string $id = null): string
{
    if (!is_logged_in()) {
        header('Location: /auth/connexion');
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /collection');
        exit;
    }
    verify_csrf();

    $user_id       = (int) $_SESSION['user_id'];
    $collection_id = (int) ($_POST['collection_id'] ?? 0);
    $item_id       = (int) ($_POST['item_id'] ?? 0);
    $redirect_to   = collection_safe_redirect($_POST['redirect_to'] ?? '/collection');

    if ($collection_id && $item_id && collection_belongs_to_user($collection_id, $user_id)) {
        collection_item_remove($collection_id, $item_id);
    }

    header('Location: ' . $redirect_to);
    exit;
}
