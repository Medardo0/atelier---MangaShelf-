<h1>Messages</h1>

<?php if (empty($messages)): ?>
<p>Aucun message.</p>
<?php else: ?>
<table>
  <caption>Messages recus</caption>
  <thead>
    <tr><th>Expediteur</th><th>Sujet</th><th>Date</th><th>Lu</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($messages as $msg): ?>
    <tr <?= !$msg['is_read'] ? 'style="font-weight:bold"' : '' ?>>
      <td><?= htmlspecialchars($msg['sender_name']) ?></td>
      <td>
        <a href="/mangashelf/public/admin/message_show/<?= $msg['id'] ?>">
          <?= htmlspecialchars($msg['subject']) ?>
        </a>
      </td>
      <td><?= htmlspecialchars($msg['created_at']) ?></td>
      <td><?= $msg['is_read'] ? 'Oui' : 'Non' ?></td>
      <td>
        <form method="post" action="/mangashelf/public/admin/message_delete/<?= $msg['id'] ?>" style="display:inline">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
          <button type="submit" onclick="return confirm('Supprimer ce message ?')">
            Supprimer
          </button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
