<?php
/**
 * app/views/layouts/admin.php
 * Template admin partagé.
 */
$page_title = $page_title ?? 'Administration';
$active_nav = $active_nav ?? '';
$stats      = $stats ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?> — Admin</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="admin-body">
  <a href="#main-content" class="skip-link">Aller au contenu principal</a>
  <header role="banner">
    <a href="/">Manga<strong>Shelf</strong></a>
    <span style="color:rgba(255,255,255,.35);font-size:.88rem;font-weight:400;">Administration</span>
    <nav aria-label="Session">
      <form method="POST" action="/admin/logout">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <button type="submit">Déconnexion</button>
      </form>
    </nav>
  </header>
  <div class="admin-layout">
    <nav aria-label="Navigation back-office">
      <ul>
        <li><a href="/admin/dashboard" <?= $active_nav==='dashboard'?'aria-current="page"':'' ?>>Dashboard</a></li>
        <li><a href="/admin/mangas"    <?= $active_nav==='mangas'   ?'aria-current="page"':'' ?>>Mangas</a></li>
        <li><a href="/admin/genres"    <?= $active_nav==='genres'   ?'aria-current="page"':'' ?>>Genres &amp; Tags</a></li>
        <li><a href="/admin/messages"  <?= $active_nav==='messages' ?'aria-current="page"':'' ?>>Messages <?php if(!empty($stats['messages_unread'] ?? 0)): ?><span>(<?= $stats['messages_unread'] ?>)</span><?php endif; ?></a></li>
      </ul>
    </nav>
    <main id="main-content"><?= $content ?? '' ?></main>
  </div>
  <footer role="contentinfo"><p><small>&copy; 2025 MangaShelf — Admin</small></p></footer>
</body>
</html>
