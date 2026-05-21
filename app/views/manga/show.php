<nav aria-label="Fil d'ariane">
  <ol>
    <li><a href="/mangashelf/public/">Accueil</a></li>
    <li><a href="/mangashelf/public/catalogue">Catalogue</a></li>
    <li aria-current="page"><?= htmlspecialchars($manga['title']) ?></li>
  </ol>
</nav>
<article>
  <h1><?= htmlspecialchars($manga['title']) ?></h1>
  <dl>
    <dt>Auteur</dt><dd><?= htmlspecialchars($manga['author']) ?></dd>
    <dt>Tomes</dt><dd><?= $manga['volumes'] ?></dd>
    <dt>Statut</dt><dd><?= match($manga['status']) { 'ongoing' => 'En cours', 'completed' => 'Terminé', 'on_hold' => 'En pause', default => htmlspecialchars($manga['status']) } ?></dd>
    <dt>Genres</dt>
    <dd><ul><?php foreach ($manga['genres'] as $g): ?><li><?= htmlspecialchars($g) ?></li><?php endforeach; ?></ul></dd>
  </dl>
  <section><h2>Synopsis</h2><p><?= htmlspecialchars($manga['synopsis']) ?></p></section>
</article>
<?php if (!empty($similaires)): ?>
<aside>
  <h2>Mangas similaires</h2>
  <ul>
    <?php foreach ($similaires as $s): ?>
    <li><a href="/mangashelf/public/manga/show/<?= htmlspecialchars($s['slug']) ?>"><?= htmlspecialchars($s['title']) ?></a></li>
    <?php endforeach; ?>
  </ul>
</aside>
<?php endif; ?>
