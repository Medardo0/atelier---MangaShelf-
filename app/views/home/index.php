<!-- ── Hero Slider ───────────────────────────────────────── -->
<div class="hero-slider" aria-label="Présentation MangaShelf" role="region">

  <div class="hero-slides" id="heroSlides">

    <!-- Slide 1 — Catalogue -->
    <div class="hero-slide active">
      <div class="hero-slide-diagonal"></div>
      <div class="hero-slide-cover">
        <img src="/assets/uploads/hero-catalogue.png" alt="">
      </div>
      <div class="hero-slide-content">
        <span class="hero-eyebrow">Catalogue MangaShelf</span>
        <h1>Explorez le<br>catalogue manga</h1>
        <p>Trouvez votre prochaine lecture avec une sélection claire, filtrable par genre, auteur et statut.</p>
        <div class="hero-actions">
          <a href="/catalogue" class="hero-cta">Parcourir le catalogue</a>
        </div>
      </div>
    </div>

    <!-- Slide 2 — Collections -->
    <div class="hero-slide">
      <div class="hero-slide-diagonal"></div>
      <div class="hero-slide-cover">
        <img src="/assets/uploads/hero-collection.png" alt="">
      </div>
      <div class="hero-slide-content">
        <span class="hero-eyebrow">Votre bibliothèque personnelle</span>
        <h1>Gérez votre<br>collection manga</h1>
        <p>Favoris, wishlist, en cours, terminés — organisez votre bibliothèque en quelques clics.</p>
        <div class="hero-actions">
          <a href="/auth/inscription" class="hero-cta">Commencer gratuitement</a>
        </div>
      </div>
    </div>

    <!-- Slide 3 — Suivi -->
    <div class="hero-slide">
      <div class="hero-slide-diagonal"></div>
      <div class="hero-slide-cover">
        <img src="/assets/uploads/hero-tracking.png" alt="">
      </div>
      <div class="hero-slide-content">
        <span class="hero-eyebrow">Ne ratez plus aucune sortie</span>
        <h1>Suivez vos séries<br>tome par tome</h1>
        <p>Ajoutez un manga à votre liste "en cours" et retrouvez d'un coup d'œil où vous en êtes.</p>
        <div class="hero-actions">
          <a href="/catalogue" class="hero-cta">Explorer le catalogue</a>
        </div>
      </div>
    </div>

  </div><!-- /.hero-slides -->

  <!-- Contrôles -->
  <button class="hero-arrow hero-prev" aria-label="Slide précédente">&#8249;</button>
  <button class="hero-arrow hero-next" aria-label="Slide suivante">&#8250;</button>

  <div class="hero-dots" role="tablist" aria-label="Choisir une slide">
    <button class="hero-dot active" aria-label="Slide 1"></button>
    <button class="hero-dot" aria-label="Slide 2"></button>
    <button class="hero-dot" aria-label="Slide 3"></button>
  </div>

</div><!-- /.hero-slider -->


<!-- ── Genres ──────────────────────────────────────────── -->
<section aria-labelledby="genres-titre">
  <h2 id="genres-titre" class="section-title">Parcourir par genre</h2>
  <ul class="genre-list">
    <?php foreach ($genres as $genre): ?>
    <li>
      <a href="/catalogue?<?= http_build_query(['genres' => [$genre['name']]]) ?>">
        <?= htmlspecialchars($genre['name']) ?>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>
</section>

<!-- ── Récents ─────────────────────────────────────────── -->
<section aria-labelledby="recents-titre">
  <h2 id="recents-titre" class="section-title">Récemment ajoutés</h2>
  <ul class="manga-grid">
    <?php foreach ($recent_mangas as $manga): ?>
    <li>
      <article class="manga-card">
        <div class="manga-card-img">
          <?php if (!empty($manga['main_image'])): ?>
          <img src="/assets/uploads/<?= htmlspecialchars($manga['main_image']) ?>"
               alt="<?= htmlspecialchars($manga['title']) ?>">
          <?php else: ?>
          <div class="manga-card-no-img">Pas d'image</div>
          <?php endif; ?>
          <div class="manga-card-img-overlay">
            <a href="/manga/show/<?= htmlspecialchars($manga['slug']) ?>"
               class="overlay-btn">Voir la fiche</a>
          </div>
        </div>
        <div class="manga-card-body">
          <h3>
            <a href="/manga/show/<?= htmlspecialchars($manga['slug']) ?>">
              <?= htmlspecialchars($manga['title']) ?>
            </a>
          </h3>
          <?php if (!empty($manga['genre'])): ?>
          <div class="manga-card-meta">
            <a href="/catalogue?<?= http_build_query(['genres' => [$manga['genre']]]) ?>">
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
    <a href="/catalogue" class="section-more">Voir tout le catalogue</a>
  </p>
</section>
