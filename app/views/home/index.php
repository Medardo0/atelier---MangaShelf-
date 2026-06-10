<div class="home-hero">
  <div class="home-hero-content">
    <h1>Manga<em>Shelf</em></h1>
    <p>Découvrez, suivez et gérez votre collection de mangas.</p>
    <a href="/mangashelf/public/catalogue" class="hero-cta">Parcourir le catalogue</a>
  </div>
</div>

<section aria-labelledby="genres-titre">
  <h2 id="genres-titre" class="section-title">Parcourir par genre</h2>
  <ul class="genre-list">
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
  <h2 id="recents-titre" class="section-title">Récemment ajoutés</h2>
  <ul class="manga-grid">
    <?php foreach ($recent_mangas as $manga): ?>
    <li>
      <article class="manga-card">
        <div class="manga-card-img">
          <?php if (!empty($manga['main_image'])): ?>
          <img src="/mangashelf/public/assets/uploads/<?= htmlspecialchars($manga['main_image']) ?>"
               alt="<?= htmlspecialchars($manga['title']) ?>">
          <?php else: ?>
          <div class="manga-card-no-img">Pas d'image</div>
          <?php endif; ?>
          <div class="manga-card-img-overlay">
            <a href="/mangashelf/public/manga/show/<?= htmlspecialchars($manga['slug']) ?>"
               class="overlay-btn">Voir la fiche</a>
          </div>
        </div>
        <div class="manga-card-body">
          <h3>
            <a href="/mangashelf/public/manga/show/<?= htmlspecialchars($manga['slug']) ?>">
              <?= htmlspecialchars($manga['title']) ?>
            </a>
          </h3>
          <?php if (!empty($manga['genre'])): ?>
          <div class="manga-card-meta">
            <a href="/mangashelf/public/catalogue?<?= http_build_query(['genres' => [$manga['genre']]]) ?>">
              <?= htmlspecialchars($manga['genre']) ?>
            </a>
          </div>
          <?php endif; ?>
        </div>
      </article>
    </li>
    <?php endforeach; ?>
  </ul>
  <p style="margin-top:2rem;text-align:center;">
    <a href="/mangashelf/public/catalogue" class="section-more">Voir tout le catalogue</a>
  </p>
</section>
