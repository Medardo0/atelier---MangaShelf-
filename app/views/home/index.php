<!-- Hero Slider -->
<div class="hero-slider" aria-label="Presentation MangaShelf" role="region">
  <div class="hero-slides" id="heroSlides">
    <div class="hero-slide active">
      <div class="hero-slide-diagonal"></div>
      <div class="hero-slide-cover">
        <img src="/assets/uploads/hero-catalogue.png" alt="">
      </div>
      <div class="hero-slide-content">
        <span class="hero-eyebrow">Catalogue MangaShelf</span>
        <h1>Explorez le<br>catalogue manga</h1>
        <p>Trouvez votre prochaine lecture avec une selection claire, filtrable par genre, auteur et statut.</p>
        <div class="hero-actions">
          <a href="/catalogue" class="hero-cta">Parcourir le catalogue</a>
        </div>
      </div>
    </div>

    <div class="hero-slide">
      <div class="hero-slide-diagonal"></div>
      <div class="hero-slide-cover">
        <img src="/assets/uploads/hero-collection.png" alt="">
      </div>
      <div class="hero-slide-content">
        <span class="hero-eyebrow">Votre bibliotheque personnelle</span>
        <h1>Gerez votre<br>collection manga</h1>
        <p>Favoris, wishlist, en cours, termines - organisez votre bibliotheque en quelques clics.</p>
        <div class="hero-actions">
          <a href="/auth/inscription" class="hero-cta">Commencer gratuitement</a>
        </div>
      </div>
    </div>

    <div class="hero-slide">
      <div class="hero-slide-diagonal"></div>
      <div class="hero-slide-cover">
        <img src="/assets/uploads/hero-tracking.png" alt="">
      </div>
      <div class="hero-slide-content">
        <span class="hero-eyebrow">Ne ratez plus aucune sortie</span>
        <h1>Suivez vos series<br>tome par tome</h1>
        <p>Ajoutez un manga a votre liste "en cours" et retrouvez d'un coup d'oeil ou vous en etes.</p>
        <div class="hero-actions">
          <a href="/catalogue" class="hero-cta">Explorer le catalogue</a>
        </div>
      </div>
    </div>
  </div>

  <button class="hero-arrow hero-prev" aria-label="Slide precedente">&#8249;</button>
  <button class="hero-arrow hero-next" aria-label="Slide suivante">&#8250;</button>

  <div class="hero-dots" role="tablist" aria-label="Choisir une slide">
    <button class="hero-dot active" aria-label="Slide 1"></button>
    <button class="hero-dot" aria-label="Slide 2"></button>
    <button class="hero-dot" aria-label="Slide 3"></button>
  </div>
</div>

<?php
if (!function_exists('home_rating_for')) {
function home_rating_for(array $manga): array
{
    $score = 4 + (((int) $manga['id'] % 3) * .25);
    $votes = 180 + ((int) $manga['id'] * 137);

    return [$score, $votes];
}
}
?>

<div class="home-shelves" aria-label="Selections de mangas par genre">
  <?php foreach ($home_shelves as $index => $shelf): ?>
  <?php $shelf_id = 'home-shelf-' . $index; ?>
  <section class="home-shelf" aria-labelledby="<?= $shelf_id ?>-title">
    <div class="home-shelf-header">
      <h2 id="<?= $shelf_id ?>-title"><?= htmlspecialchars($shelf['name']) ?></h2>
      <a href="/catalogue?<?= http_build_query(['genres' => [$shelf['name']]]) ?>">Voir tout</a>
    </div>

    <div class="home-shelf-track-wrap">
      <button class="home-shelf-arrow home-shelf-prev" type="button"
              aria-label="Faire defiler <?= htmlspecialchars($shelf['name']) ?> vers la gauche"
              data-shelf-prev="<?= $shelf_id ?>">&#8249;</button>

      <ul class="home-shelf-track" id="<?= $shelf_id ?>">
        <?php foreach ($shelf['mangas'] as $manga): ?>
        <?php [$rating, $votes] = home_rating_for($manga); ?>
        <li class="home-shelf-item">
          <article class="home-manga-card">
            <a class="home-manga-cover" href="/manga/show/<?= htmlspecialchars($manga['slug']) ?>">
              <?php if (!empty($manga['main_image'])): ?>
              <img src="/assets/uploads/<?= htmlspecialchars($manga['main_image']) ?>"
                   alt="<?= htmlspecialchars($manga['title']) ?>">
              <?php else: ?>
              <span><?= htmlspecialchars($manga['title']) ?></span>
              <?php endif; ?>
              <?php if (($manga['status'] ?? '') === 'ongoing'): ?>
              <span class="home-manga-badge">En cours</span>
              <?php endif; ?>
            </a>
            <h3>
              <a href="/manga/show/<?= htmlspecialchars($manga['slug']) ?>">
                <?= htmlspecialchars($manga['title']) ?>
              </a>
            </h3>
            <div class="home-manga-rating" aria-label="Note <?= number_format($rating, 1, ',', ' ') ?> sur 5">
              <?php for ($star = 1; $star <= 5; $star++): ?>
              <span class="<?= $star <= round($rating) ? 'is-filled' : '' ?>">★</span>
              <?php endfor; ?>
              <small>(<?= (int) $votes ?>)</small>
            </div>
          </article>
        </li>
        <?php endforeach; ?>
      </ul>

      <button class="home-shelf-arrow home-shelf-next" type="button"
              aria-label="Faire defiler <?= htmlspecialchars($shelf['name']) ?> vers la droite"
              data-shelf-next="<?= $shelf_id ?>">&#8250;</button>
    </div>
  </section>
  <?php endforeach; ?>
</div>
