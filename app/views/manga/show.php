<nav aria-label="Fil d'ariane">
  <ol>
    <li><a href="/mangashelf/public/">Accueil</a></li>
    <li><a href="/mangashelf/public/catalogue">Catalogue</a></li>
    <li aria-current="page"><?= htmlspecialchars($manga['title']) ?></li>
  </ol>
</nav>
<article>
  <?php if (!empty($manga['main_image'])): ?>
  <img src="/mangashelf/public/assets/uploads/<?= htmlspecialchars($manga['main_image']) ?>"
       alt="Couverture de <?= htmlspecialchars($manga['title']) ?>"
       style="max-width:200px;float:right;margin:0 0 1rem 1rem">
  <?php endif; ?>
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
<?php if (!empty($memberships)): ?>
<?php
$collection_labels = [
    'favorites' => 'Favoris',
    'wishlist'  => 'Wishlist',
    'reading'   => 'En cours de lecture',
    'completed' => 'Terminés',
];
$redirect_back = '/mangashelf/public/manga/show/' . htmlspecialchars($manga['slug']);
?>
<section aria-label="Mes collections">
  <h2>Ajouter à une collection</h2>
  <ul>
    <?php foreach ($memberships as $col): ?>
    <li>
      <?php if ($col['has_item']): ?>
      <form method="post" action="/mangashelf/public/collection/remove" style="display:inline">
        <input type="hidden" name="csrf_token"    value="<?= htmlspecialchars(csrf_token()) ?>">
        <input type="hidden" name="collection_id" value="<?= (int) $col['id'] ?>">
        <input type="hidden" name="item_id"       value="<?= (int) $manga['id'] ?>">
        <input type="hidden" name="redirect_to"   value="<?= $redirect_back ?>">
        <button type="submit">✓ <?= $collection_labels[$col['type']] ?? htmlspecialchars($col['type']) ?> — Retirer</button>
      </form>
      <?php else: ?>
      <form method="post" action="/mangashelf/public/collection/add" style="display:inline">
        <input type="hidden" name="csrf_token"    value="<?= htmlspecialchars(csrf_token()) ?>">
        <input type="hidden" name="collection_id" value="<?= (int) $col['id'] ?>">
        <input type="hidden" name="item_id"       value="<?= (int) $manga['id'] ?>">
        <input type="hidden" name="redirect_to"   value="<?= $redirect_back ?>">
        <button type="submit">+ <?= $collection_labels[$col['type']] ?? htmlspecialchars($col['type']) ?></button>
      </form>
      <?php endif; ?>
    </li>
    <?php endforeach; ?>
  </ul>
</section>
<?php endif; ?>

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
