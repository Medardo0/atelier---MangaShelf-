<section aria-labelledby="hero-titre">
  <h1 id="hero-titre">Découvrez et suivez vos mangas</h1>
  <p>Parcourez le catalogue, consultez les fiches, créez votre collection.</p>
  <form action="/mangashelf/public/catalogue" method="get" role="search">
    <label for="q">Rechercher un manga</label>
    <input type="search" id="q" name="q" placeholder="Titre, auteur…">
    <button type="submit">Rechercher</button>
  </form>
</section>

<section aria-labelledby="genres-titre">
  <h2 id="genres-titre">Parcourir par genre</h2>
  <ul>
    <?php foreach ($genres as $genre): ?>
    <li>
      <a href="/mangashelf/public/catalogue?<?= http_build_query(['genres' => [$genre['name']]]) ?>">
        <?= htmlspecialchars($genre['name']) ?>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>
</section>

<section aria-labelledby="recents-titre">
  <h2 id="recents-titre">Mangas récemment ajoutés</h2>
  <ul>
    <?php foreach ($recent_mangas as $manga): ?>
    <li>
      <article>
        <h3>
          <a href="/mangashelf/public/manga/show/<?= htmlspecialchars($manga['slug']) ?>">
            <?= htmlspecialchars($manga['title']) ?>
          </a>
        </h3>
        <?php if (!empty($manga['genre'])): ?>
        <p>
          Genre :
          <a href="/mangashelf/public/catalogue?<?= http_build_query(['genres' => [$manga['genre']]]) ?>">
            <?= htmlspecialchars($manga['genre']) ?>
          </a>
        </p>
        <?php endif; ?>
      </article>
    </li>
    <?php endforeach; ?>
  </ul>
  <p><a href="/mangashelf/public/catalogue">Voir tout le catalogue</a></p>
</section>
