<h1>Catalogue de mangas <span>(<?= $total ?>)</span></h1>

<form action="/catalogue" method="get" class="catalogue-filters">

  <div>
    <label for="q">Recherche</label>
    <input type="search" id="q" name="q"
           value="<?= htmlspecialchars($filters['q'] ?? '') ?>"
           placeholder="Titre, auteur…">
  </div>

  <?php if (!empty($genres)): ?>
  <fieldset>
    <legend>Genres</legend>
    <?php foreach ($genres as $genre): ?>
    <label>
      <input type="checkbox" name="genres[]"
             value="<?= htmlspecialchars($genre['name']) ?>"
             <?= in_array($genre['name'], $filters['genres'] ?? [], true) ? 'checked' : '' ?>>
      <?= htmlspecialchars($genre['name']) ?>
    </label>
    <?php endforeach; ?>
  </fieldset>
  <?php endif; ?>

  <div>
    <label for="sort">Trier par</label>
    <select id="sort" name="sort">
      <option value="date"  <?= ($filters['sort'] ?? 'date') === 'date'  ? 'selected' : '' ?>>Date d'ajout</option>
      <option value="title" <?= ($filters['sort'] ?? '') === 'title' ? 'selected' : '' ?>>Titre (A–Z)</option>
    </select>
  </div>

  <div>
    <button type="submit">Filtrer</button>
    <?php if (!empty(array_filter($filters))): ?>
    <a href="/catalogue" style="margin-left:.75rem;font-size:.85rem;color:var(--grey-600);">
      Réinitialiser
    </a>
    <?php endif; ?>
  </div>

</form>

<?php
function pagination_url(array $filters, int $page): string
{
    $params = array_merge($filters, ['page' => $page]);
    unset($params['page']);
    if ($page > 1) $params['page'] = $page;
    return '/catalogue' . ($params ? '?' . http_build_query($params) : '');
}
?>

<?php if (empty($mangas)): ?>
  <p>Aucun manga ne correspond à votre recherche.</p>
<?php else: ?>
<ul class="manga-grid">
  <?php foreach ($mangas as $manga): ?>
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
        <h2>
          <a href="/manga/show/<?= htmlspecialchars($manga['slug']) ?>">
            <?= htmlspecialchars($manga['title']) ?>
          </a>
        </h2>
        <div class="manga-card-meta">
          <?= htmlspecialchars($manga['author']) ?>
          <?php if (!empty($manga['genre'])): ?>
          — <a href="/catalogue?<?= http_build_query(['genres' => [$manga['genre']]]) ?>">
              <?= htmlspecialchars($manga['genre']) ?>
            </a>
          <?php endif; ?>
        </div>
        <div class="manga-card-meta" style="margin-top:.3rem;">
          <?php $status_label = match($manga['status']) {
            'ongoing'   => ['En cours',  'badge-ongoing'],
            'completed' => ['Terminé',   'badge-completed'],
            'on_hold'   => ['En pause',  'badge-on-hold'],
            default     => [htmlspecialchars($manga['status']), ''],
          }; ?>
          <span class="badge <?= $status_label[1] ?>"><?= $status_label[0] ?></span>
          <span><?= (int) $manga['volumes'] ?> tome<?= $manga['volumes'] > 1 ? 's' : '' ?></span>
        </div>
      </div>
    </article>
  </li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if ($total_pages > 1): ?>
<nav aria-label="Pagination">
  <ul>
    <?php if ($current_page > 1): ?>
    <li><a href="<?= pagination_url($filters, $current_page - 1) ?>">← Précédent</a></li>
    <?php endif; ?>

    <?php for ($p = 1; $p <= $total_pages; $p++): ?>
    <li>
      <?php if ($p === $current_page): ?>
      <span aria-current="page"><?= $p ?></span>
      <?php else: ?>
      <a href="<?= pagination_url($filters, $p) ?>"><?= $p ?></a>
      <?php endif; ?>
    </li>
    <?php endfor; ?>

    <?php if ($current_page < $total_pages): ?>
    <li><a href="<?= pagination_url($filters, $current_page + 1) ?>">Suivant →</a></li>
    <?php endif; ?>
  </ul>
</nav>
<?php endif; ?>
