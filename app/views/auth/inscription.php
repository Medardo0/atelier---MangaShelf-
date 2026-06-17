<div class="auth-page">
<h1>Créer un compte</h1>
<p>Rejoignez MangaShelf pour gérer votre collection, vos favoris et votre wishlist.</p>

<?php if (!empty($errors)): ?>
<div role="alert">
  <ul>
    <?php foreach ($errors as $error): ?>
    <li><?= htmlspecialchars($error) ?></li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<form method="post" action="/auth/inscription" novalidate>
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

  <div>
    <label for="username">Pseudo <abbr title="obligatoire">*</abbr></label>
    <input type="text" id="username" name="username" required
           minlength="3" maxlength="30" autocomplete="username"
           placeholder="Votre pseudo public"
           value="<?= htmlspecialchars($old['username'] ?? '') ?>">
    <p>Entre 3 et 30 caractères.</p>
  </div>

  <div>
    <label for="email">Adresse e-mail <abbr title="obligatoire">*</abbr></label>
    <input type="email" id="email" name="email" required
           autocomplete="email" placeholder="votre@email.com"
           value="<?= htmlspecialchars($old['email'] ?? '') ?>">
  </div>

  <div>
    <label for="password">Mot de passe <abbr title="obligatoire">*</abbr></label>
    <input type="password" id="password" name="password" required
           minlength="8" autocomplete="new-password">
    <p>Minimum 8 caractères.</p>
  </div>

  <div>
    <label for="password_confirm">Confirmer le mot de passe <abbr title="obligatoire">*</abbr></label>
    <input type="password" id="password_confirm" name="password_confirm" required
           autocomplete="new-password">
  </div>

  <button type="submit">Créer mon compte</button>
</form>

<p>Déjà inscrit ? <a href="/auth/connexion">Se connecter</a></p>
</div>
