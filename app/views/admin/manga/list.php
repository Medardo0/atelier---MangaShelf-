<h1>Mangas <span>(<?= count($mangas) ?>)</span></h1>
<a href="/admin/manga_create">+ Ajouter un manga</a>

<?php if (empty($mangas)): ?>
<p>Aucun manga dans la base de données.</p>
<?php else: ?>
<table>
  <caption>Liste des mangas</caption>
  <thead>
    <tr>
      <th>Titre</th>
      <th>Auteur</th>
      <th>Tomes</th>
      <th>Série</th>
      <th>Visibilité</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($mangas as $m): ?>
    <tr>
      <td><?= htmlspecialchars($m['title']) ?></td>
      <td><?= htmlspecialchars($m['author']) ?></td>
      <td><?= (int) $m['volumes'] ?></td>
      <td><?= match($m['series_status']) {
        'ongoing'   => 'En cours',
        'completed' => 'Termine',
        'on_hold'   => 'En pause',
        default     => htmlspecialchars($m['series_status'])
      } ?></td>
      <td><?= $m['status'] === 'published' ? 'Publie' : 'Brouillon' ?></td>
      <td>
        <a href="/admin/manga_edit/<?= $m['id'] ?>">Modifier</a>
        <form method="post" action="/admin/manga_delete/<?= $m['id'] ?>" style="display:inline">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
          <?php $confirm = "Supprimer '" . addslashes($m['title']) . "' ?"; ?>
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
