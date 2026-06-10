<?php
$is_edit = $manga !== null;

$title_val       = $is_edit ? $manga['title']             : ($old['title'] ?? '');
$author_val      = $is_edit ? $manga['author']            : ($old['author'] ?? '');
$volumes_val     = $is_edit ? $manga['volumes']           : ($old['volumes'] ?? '0');
$status_s_val    = $is_edit ? $manga['series_status']     : ($old['series_status'] ?? 'ongoing');
$content_val     = $is_edit ? $manga['content']           : ($old['content'] ?? '');
$short_desc_val  = $is_edit ? $manga['short_description'] : ($old['short_description'] ?? '');
$status_val      = $is_edit ? $manga['status']            : ($old['status'] ?? 'published');
$selected_ids    = $is_edit
    ? array_map('intval', $manga['tag_ids'] ?? [])
    : array_map('intval', (array) ($old['tags'] ?? []));

$form_action = $is_edit
    ? '/mangashelf/public/admin/manga_edit/' . (int) $manga['id']
    : '/mangashelf/public/admin/manga_create';
?>

<h1><?= $is_edit ? 'Modifier ' . htmlspecialchars($manga['title']) : 'Ajouter un manga' ?></h1>

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
    <label for="title">Titre <abbr title="obligatoire">*</abbr></label>
    <input type="text" id="title" name="title" required
           value="<?= htmlspecialchars($title_val) ?>">
  </div>

  <div>
    <label for="author">Auteur <abbr title="obligatoire">*</abbr></label>
    <input type="text" id="author" name="author" required
           value="<?= htmlspecialchars($author_val) ?>">
  </div>

  <div>
    <label for="volumes">Nombre de tomes</label>
    <input type="number" id="volumes" name="volumes" min="0"
           value="<?= (int) $volumes_val ?>">
  </div>

  <div>
    <label for="series_status">Statut de la série</label>
    <select id="series_status" name="series_status">
      <option value="ongoing"   <?= $status_s_val === 'ongoing'   ? 'selected' : '' ?>>En cours</option>
      <option value="completed" <?= $status_s_val === 'completed' ? 'selected' : '' ?>>Terminé</option>
      <option value="on_hold"   <?= $status_s_val === 'on_hold'   ? 'selected' : '' ?>>En pause</option>
    </select>
  </div>

  <div>
    <label for="short_description">Résumé court</label>
    <input type="text" id="short_description" name="short_description" maxlength="255"
           value="<?= htmlspecialchars($short_desc_val) ?>">
  </div>

  <div>
    <label for="content">Synopsis</label>
    <textarea id="content" name="content" rows="6"><?= htmlspecialchars($content_val) ?></textarea>
  </div>

  <?php if (!empty($all_tags)): ?>
  <?php $genres_list = array_values(array_filter($all_tags, fn($t) => $t['type'] === 'genre')); ?>
  <?php $tags_list   = array_values(array_filter($all_tags, fn($t) => $t['type'] === 'tag')); ?>

  <?php if (!empty($genres_list)): ?>
  <fieldset>
    <legend>Genres</legend>
    <?php foreach ($genres_list as $tag): ?>
    <label>
      <input type="checkbox" name="tags[]" value="<?= (int) $tag['id'] ?>"
             <?= in_array((int) $tag['id'], $selected_ids, true) ? 'checked' : '' ?>>
      <?= htmlspecialchars($tag['name']) ?>
    </label>
    <?php endforeach; ?>
  </fieldset>
  <?php endif; ?>

  <?php if (!empty($tags_list)): ?>
  <fieldset>
    <legend>Tags</legend>
    <?php foreach ($tags_list as $tag): ?>
    <label>
      <input type="checkbox" name="tags[]" value="<?= (int) $tag['id'] ?>"
             <?= in_array((int) $tag['id'], $selected_ids, true) ? 'checked' : '' ?>>
      <?= htmlspecialchars($tag['name']) ?>
    </label>
    <?php endforeach; ?>
  </fieldset>
  <?php endif; ?>
  <?php endif; ?>

  <div>
    <label for="status">Visibilité</label>
    <select id="status" name="status">
      <option value="published" <?= $status_val === 'published' ? 'selected' : '' ?>>Publié</option>
      <option value="draft"     <?= $status_val === 'draft'     ? 'selected' : '' ?>>Brouillon</option>
    </select>
  </div>

  <button type="submit">
    <?= $is_edit ? 'Enregistrer les modifications' : 'Ajouter le manga' ?>
  </button>
  <a href="/mangashelf/public/admin/mangas">Annuler</a>
</form>
