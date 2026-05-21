<h1>Catalogue de mangas <span>(<?= $total ?>)</span></h1>
<ul>
  <?php foreach ($mangas as $manga): ?>
  <li>
    <article>
      <h2><a href="/mangashelf/public/manga/show/<?= htmlspecialchars($manga['slug']) ?>"><?= htmlspecialchars($manga['title']) ?></a></h2>
      <dl>
        <dt>Auteur</dt><dd><?= htmlspecialchars($manga['author']) ?></dd>
        <dt>Genre</dt><dd><?= htmlspecialchars($manga['genre']) ?></dd>
        <dt>Tomes</dt><dd><?= $manga['volumes'] ?> (<?= $manga['status'] === 'ongoing' ? 'en cours' : 'terminé' ?>)</dd>
      </dl>
      <a href="/mangashelf/public/manga/show/<?= htmlspecialchars($manga['slug']) ?>">Voir la fiche</a>
    </article>
  </li>
  <?php endforeach; ?>
</ul>
