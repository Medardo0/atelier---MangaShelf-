<nav aria-label="Fil d'ariane">
  <ol>
    <li><a href="/mangashelf/public/">Accueil</a></li>
    <li><a href="/mangashelf/public/catalogue">Catalogue</a></li>
    <li aria-current="page"><?= htmlspecialchars($manga['title']) ?></li>
  </ol>
</nav>

<div class="manga-show">
  <div class="manga-show-cover">
    <?php if (!empty($manga['main_image'])): ?>
    <img src="/mangashelf/public/assets/uploads/<?= htmlspecialchars($manga['main_image']) ?>"
         alt="Couverture de <?= htmlspecialchars($manga['title']) ?>">
    <?php else: ?>
    <div class="manga-show-cover-placeholder">Pas de couverture</div>
    <?php endif; ?>
  </div>

  <div class="manga-show-info">
    <h1><?= htmlspecialchars($manga['title']) ?></h1>
    <dl>
      <dt>Auteur</dt>
      <dd><?= htmlspecialchars($manga['author']) ?></dd>
      <dt>Tomes</dt>
      <dd><?= (int) $manga['volumes'] ?></dd>
      <dt>Statut</dt>
      <dd><?= match($manga['status']) {
        'ongoing'   => 'En cours',
        'completed' => 'Terminé',
        'on_hold'   => 'En pause',
        default     => htmlspecialchars($manga['status'])
      } ?></dd>
      <?php if (!empty($manga['genres'])): ?>
      <dt>Genres</dt>
      <dd>
        <?php foreach ($manga['genres'] as $g): ?>
        <a href="/mangashelf/public/catalogue?<?= http_build_query(['genres' => [$g]]) ?>">
          <?= htmlspecialchars($g) ?>
        </a>
        <?php endforeach; ?>
      </dd>
      <?php endif; ?>
    </dl>

    <?php if (!empty($manga['synopsis'])): ?>
    <div class="manga-show-synopsis">
      <h2>Synopsis</h2>
      <p><?= nl2br(htmlspecialchars($manga['synopsis'])) ?></p>
    </div>
    <?php endif; ?>

    <?php if (!empty($memberships)): ?>
    <?php
    $collection_labels = [
      'favorites' => 'Favoris',
      'wishlist'  => 'Wishlist',
      'reading'   => 'En cours',
      'completed' => 'Terminé',
    ];
    $redirect_back = '/mangashelf/public/manga/show/' . htmlspecialchars($manga['slug']);
    ?>
    <div class="manga-collections">
      <h2>Mes collections</h2>
      <ul>
        <?php foreach ($memberships as $col): ?>
        <li>
          <?php if ($col['has_item']): ?>
          <form method="post" action="/mangashelf/public/collection/remove">
            <input type="hidden" name="csrf_token"    value="<?= htmlspecialchars(csrf_token()) ?>">
            <input type="hidden" name="collection_id" value="<?= (int) $col['id'] ?>">
            <input type="hidden" name="item_id"       value="<?= (int) $manga['id'] ?>">
            <input type="hidden" name="redirect_to"   value="<?= $redirect_back ?>">
            <button type="submit" style="background:var(--red);color:var(--white);border-color:var(--red);">
              ✓ <?= $collection_labels[$col['type']] ?? htmlspecialchars($col['type']) ?>
            </button>
          </form>
          <?php else: ?>
          <form method="post" action="/mangashelf/public/collection/add">
            <input type="hidden" name="csrf_token"    value="<?= htmlspecialchars(csrf_token()) ?>">
            <input type="hidden" name="collection_id" value="<?= (int) $col['id'] ?>">
            <input type="hidden" name="item_id"       value="<?= (int) $manga['id'] ?>">
            <input type="hidden" name="redirect_to"   value="<?= $redirect_back ?>">
            <button type="submit" style="background:transparent;color:var(--black);border-color:var(--grey-200);">
              + <?= $collection_labels[$col['type']] ?? htmlspecialchars($col['type']) ?>
            </button>
          </form>
          <?php endif; ?>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php if (!empty($similaires)): ?>
<aside class="manga-show-similar">
  <h2 class="section-title">Mangas similaires</h2>
  <ul>
    <?php foreach ($similaires as $s): ?>
    <li>
      <a href="/mangashelf/public/manga/show/<?= htmlspecialchars($s['slug']) ?>">
        <?= htmlspecialchars($s['title']) ?>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>
</aside>
<?php endif; ?>
