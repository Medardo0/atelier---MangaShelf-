<?php
/**
 * app/views/layouts/main.php
 * Template public partagé par toutes les vues publiques.
 * Reçoit $content (HTML de la vue) et $page_title.
 */
$page_title = $page_title ?? 'MangaShelf';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  <link rel="stylesheet" href="/mangashelf/public/assets/css/style.css">
</head>
<body>
  <a href="#main-content" class="skip-link">Aller au contenu principal</a>
  <header role="banner">
    <a href="/mangashelf/public/"><strong>MangaShelf</strong></a>
    <nav aria-label="Navigation principale">
      <ul>
        <li><a href="/mangashelf/public/">Accueil</a></li>
        <li><a href="/mangashelf/public/catalogue">Catalogue</a></li>
        <li><a href="/mangashelf/public/contact">Contact</a></li>
      </ul>
    </nav>
    <nav aria-label="Compte utilisateur">
      <ul>
        <?php if (is_logged_in()): ?>
        <li><strong><?= htmlspecialchars($_SESSION['username']) ?></strong></li>
        <li>
          <form method="post" action="/mangashelf/public/auth/logout">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <button type="submit">Déconnexion</button>
          </form>
        </li>
        <?php else: ?>
        <li><a href="/mangashelf/public/auth/connexion">Connexion</a></li>
        <li><a href="/mangashelf/public/auth/inscription">Inscription</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>
  <main id="main-content">
    <?= $content ?? '' ?>
  </main>
  <footer role="contentinfo">
    <p><small>&copy; 2025 MangaShelf</small></p>
  </footer>
  <script src="/mangashelf/public/assets/js/main.js"></script>
</body>
</html>
