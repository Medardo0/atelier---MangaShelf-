<section aria-labelledby="hero-titre">
  <h1 id="hero-titre">Découvrez et suivez vos mangas</h1>
  <p>Parcourez le catalogue, consultez les fiches, créez votre collection.</p>
  <form action="/mangashelf/public/catalogue" method="get" role="search">
    <label for="q">Rechercher un manga</label>
    <input type="search" id="q" name="q" placeholder="Titre, auteur, genre…">
    <button type="submit">Rechercher</button>
  </form>
</section>
<section aria-labelledby="genres-titre">
  <h2 id="genres-titre">Parcourir par genre</h2>
  <ul>
    <?php foreach ($genres as $genre): ?>
    <li><a href="/mangashelf/public/catalogue?genre=<?= urlencode(strtolower($genre)) ?>"><?= htmlspecialchars($genre) ?></a></li>
    <?php endforeach; ?>
  </ul>
</section>
<section aria-labelledby="recents-titre">
  <h2 id="recents-titre">Mangas récemment ajoutés</h2>
  <ul>
    <?php foreach ($recent_mangas as $manga): ?>
    <li>
      <article>
        <h3><a href="/mangashelf/public/manga/show/<?= htmlspecialchars($manga['slug']) ?>"><?= htmlspecialchars($manga['title']) ?></a></h3>
        <p>Genre : <a href="/mangashelf/public/catalogue?genre=<?= urlencode(strtolower($manga['genre'])) ?>"><?= htmlspecialchars($manga['genre']) ?></a></p>
      </article>
    </li>
    <?php endforeach; ?>
  </ul>
  <p><a href="/mangashelf/public/catalogue">Voir tout le catalogue</a></p>
</section>
