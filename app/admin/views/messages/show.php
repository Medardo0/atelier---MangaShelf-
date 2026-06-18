<nav aria-label="Retour">
  <a href="/admin/messages">← Retour aux messages</a>
</nav>

<article>
  <h1><?= htmlspecialchars($message['subject']) ?></h1>
  <dl>
    <dt>Expediteur</dt>
    <dd><?= htmlspecialchars($message['sender_name']) ?>
        &lt;<a href="mailto:<?= htmlspecialchars($message['sender_email']) ?>">
          <?= htmlspecialchars($message['sender_email']) ?>
        </a>&gt;
    </dd>
    <dt>Date</dt>
    <dd><?= htmlspecialchars($message['created_at']) ?></dd>
  </dl>
  <section>
    <h2>Message</h2>
    <p><?= nl2br(htmlspecialchars($message['body'])) ?></p>
  </section>
</article>

<form method="post" action="/admin/message_delete/<?= $message['id'] ?>">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
  <button type="submit" onclick="return confirm('Supprimer ce message ?')">Supprimer</button>
</form>
