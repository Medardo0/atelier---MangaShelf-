<div class="contact-page">
<h1>Contact</h1>

<?php if ($sent): ?>
<div class="contact-success">
  <p>Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.</p>
</div>
<?php else: ?>

<?php if (!empty($errors)): ?>
<div role="alert">
  <ul>
    <?php foreach ($errors as $e): ?>
    <li><?= htmlspecialchars($e) ?></li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<form method="post" action="/contact" novalidate>
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

  <div>
    <label for="name">Nom <abbr title="obligatoire">*</abbr></label>
    <input type="text" id="name" name="name" required
           autocomplete="name"
           value="<?= htmlspecialchars($old['name'] ?? '') ?>">
  </div>

  <div>
    <label for="email">Adresse e-mail <abbr title="obligatoire">*</abbr></label>
    <input type="email" id="email" name="email" required
           autocomplete="email" placeholder="votre@email.com"
           value="<?= htmlspecialchars($old['email'] ?? '') ?>">
  </div>

  <div>
    <label for="subject">Sujet <abbr title="obligatoire">*</abbr></label>
    <input type="text" id="subject" name="subject" required maxlength="150"
           value="<?= htmlspecialchars($old['subject'] ?? '') ?>">
  </div>

  <div>
    <label for="body">Message <abbr title="obligatoire">*</abbr></label>
    <textarea id="body" name="body" required rows="6"><?= htmlspecialchars($old['body'] ?? '') ?></textarea>
  </div>

  <button type="submit">Envoyer</button>
</form>

<?php endif; ?>
</div>
