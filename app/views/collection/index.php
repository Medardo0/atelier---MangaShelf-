<?php
$labels = [
    'favorites' => 'Favoris',
    'wishlist'  => 'Wishlist',
    'reading'   => 'En cours de lecture',
    'completed' => 'Terminés',
];
$redirect_back = '/mangashelf/public/collection';
?>

<h1>Mes collections</h1>

<?php foreach ($collections as $col): ?>
<section>
  <h2>
    <?= $labels[$col['type']] ?? htmlspecialchars($col['type']) ?>
    <span>(<?= (int) $col['count'] ?>)</span>
  </h2>

  <?php $items = $items_by_collection[$col['id']] ?? []; ?>
  <?php if (empty($items)): ?>
  <p>Cette collection est vide.</p>
  <?php else: ?>
  <ul>
    <?php foreach ($items as $manga): ?>
    <li>
      <a href="/mangashelf/public/manga/show/<?= htmlspecialchars($manga['slug']) ?>">
        <?= htmlspecialchars($manga['title']) ?>
      </a>
      — <?= htmlspecialchars($manga['author']) ?>
      <form method="post" action="/mangashelf/public/collection/remove" style="display:inline">
        <input type="hidden" name="csrf_token"    value="<?= htmlspecialchars(csrf_token()) ?>">
        <input type="hidden" name="collection_id" value="<?= (int) $col['id'] ?>">
        <input type="hidden" name="item_id"       value="<?= (int) $manga['id'] ?>">
        <input type="hidden" name="redirect_to"   value="<?= htmlspecialchars($redirect_back) ?>">
        <button type="submit">Retirer</button>
      </form>
    </li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
</section>
<?php endforeach; ?>
