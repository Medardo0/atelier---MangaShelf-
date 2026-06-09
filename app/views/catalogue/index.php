<h1>Catalogue de mangas <span>(<?= $total ?>)</span></h1>

<form action="/mangashelf/public/catalogue" method="get">

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

  <button type="submit">Filtrer</button>

  <?php if (!empty(array_filter($filters))): ?>
  <a href="/mangashelf/public/catalogue">Réinitialiser les filtres</a>
  <?php endif; ?>

</form>

<?php if (empty($mangas)): ?>
  <p>Aucun manga ne correspond à votre recherche.</p>
<?php else: ?>
<ul>
  <?php foreach ($mangas as $manga): ?>
  <li>
    <article>
      <h2>
        <a href="/mangashelf/public/manga/show/<?= htmlspecialchars($manga['slug']) ?>">
          <?= htmlspecialchars($manga['title']) ?>
        </a>
      </h2>
      <dl>
        <dt>Auteur</dt>
        <dd><?= htmlspecialchars($manga['author']) ?></dd>

        <?php if (!empty($manga['genre'])): ?>
        <dt>Genre</dt>
        <dd>
          <a href="/mangashelf/public/catalogue?<?= http_build_query(['genres' => [$manga['genre']]]) ?>">
            <?= htmlspecialchars($manga['genre']) ?>
          </a>
        </dd>
        <?php endif; ?>

        <dt>Tomes</dt>
        <dd>
          <?= (int) $manga['volumes'] ?>
          (<?= match($manga['status']) {
            'ongoing'   => 'en cours',
            'completed' => 'terminé',
            'on_hold'   => 'en pause',
            default     => htmlspecialchars($manga['status'])
          } ?>)
        </dd>
      </dl>
      <a href="/mangashelf/public/manga/show/<?= htmlspecialchars($manga['slug']) ?>">Voir la fiche</a>
    </article>
  </li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>
