<?php
/**
 * app/views/_layout.php
 * Template public partagé par toutes les vues publiques.
 * Reçoit $content (HTML de la vue) et $page_title.
 */
$page_title = $page_title ?? 'MangaShelf';
$css_version = filemtime(__DIR__ . '/../../public/assets/css/style.css') ?: time();
$js_version  = filemtime(__DIR__ . '/../../public/assets/js/main.js') ?: time();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  <link rel="stylesheet" href="/assets/css/style.css?v=<?= $css_version ?>">
</head>
<body>
  <a href="#main-content" class="skip-link">Aller au contenu principal</a>
  <header role="banner">
    <a href="/">Manga<strong>Shelf</strong></a>
    <nav aria-label="Navigation principale">
      <ul>
        <li><a href="/">Accueil</a></li>
        <li><a href="/catalogue">Catalogue</a></li>
        <li><a href="/contact">Contact</a></li>
      </ul>
    </nav>
    <form class="header-search" action="/catalogue" method="get" role="search">
      <input type="search" name="q" placeholder="Rechercher un manga…"
             aria-label="Rechercher un manga"
             value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
      <button type="submit" aria-label="Lancer la recherche">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
      </button>
    </form>
    <nav aria-label="Compte utilisateur">
      <ul>
        <?php if (is_logged_in()): ?>
        <li><strong><?= htmlspecialchars($_SESSION['username']) ?></strong></li>
        <li><a href="/collection">Mes collections</a></li>
        <li>
          <form method="post" action="/auth/logout">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <button type="submit">Déconnexion</button>
          </form>
        </li>
        <?php else: ?>
        <li><a href="/auth/connexion">Connexion</a></li>
        <li><a href="/auth/inscription">Inscription</a></li>
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
  <script src="/assets/js/main.js?v=<?= $js_version ?>"></script>
</body>
</html>
