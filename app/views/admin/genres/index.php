<h1>Genres &amp; Tags</h1>
<a href="/admin/genre_create">+ Ajouter</a>

<?php if (empty($tags)): ?>
<p>Aucun genre ou tag dans la base de données.</p>
<?php else: ?>
<table>
  <caption>Liste des genres et tags</caption>
  <thead>
    <tr><th>Nom</th><th>Type</th><th>Slug</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($tags as $t): ?>
    <tr>
      <td><?= htmlspecialchars($t['name']) ?></td>
      <td><?= $t['type'] === 'genre' ? 'Genre' : 'Tag' ?></td>
      <td><?= htmlspecialchars($t['slug']) ?></td>
      <td>
        <a href="/admin/genre_edit/<?= $t['id'] ?>">Modifier</a>
        <form method="post" action="/admin/genre_delete/<?= $t['id'] ?>" style="display:inline">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
          <?php $confirm = "Supprimer '" . addslashes($t['name']) . "' ? Les mangas associes perdront ce tag."; ?>
          <button type="submit" onclick="return confirm('<?= htmlspecialchars($confirm) ?>')">
            Supprimer
          </button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
