<?php
$is_edit  = $tag !== null;
$name_val = $is_edit ? $tag['name'] : ($old['name'] ?? '');
$type_val = $is_edit ? $tag['type'] : ($old['type'] ?? 'tag');
$form_action = $is_edit
    ? '/admin/genre_edit/' . (int) $tag['id']
    : '/admin/genre_create';
?>

<h1><?= $is_edit ? 'Modifier ' . htmlspecialchars($tag['name']) : 'Ajouter un genre / tag' ?></h1>

<?php if (!empty($errors)): ?>
<div role="alert">
  <ul>
    <?php foreach ($errors as $e): ?>
    <li><?= htmlspecialchars($e) ?></li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<form method="post" action="<?= $form_action ?>">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

  <div>
    <label for="name">Nom <abbr title="obligatoire">*</abbr></label>
    <input type="text" id="name" name="name" required
           value="<?= htmlspecialchars($name_val) ?>">
  </div>

  <div>
    <label for="type">Type</label>
    <select id="type" name="type">
      <option value="genre" <?= $type_val === 'genre' ? 'selected' : '' ?>>Genre</option>
      <option value="tag"   <?= $type_val === 'tag'   ? 'selected' : '' ?>>Tag</option>
    </select>
  </div>

  <button type="submit"><?= $is_edit ? 'Enregistrer' : 'Ajouter' ?></button>
  <a href="/admin/genres">Annuler</a>
</form>
