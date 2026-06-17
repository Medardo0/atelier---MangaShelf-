<div class="auth-page">
<h1>Connexion</h1>
<p>Accédez à votre collection personnelle de mangas.</p>

<?php if (!empty($errors)): ?>
<div role="alert">
  <ul>
    <?php foreach ($errors as $error): ?>
    <li><?= htmlspecialchars($error) ?></li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<form method="post" action="/auth/connexion" novalidate>
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

  <div>
    <label for="email">Adresse e-mail <abbr title="obligatoire">*</abbr></label>
    <input type="email" id="email" name="email" required
           autocomplete="username" placeholder="votre@email.com">
  </div>

  <div>
    <label for="password">Mot de passe <abbr title="obligatoire">*</abbr></label>
    <input type="password" id="password" name="password" required
           autocomplete="current-password">
  </div>

  <button type="submit">Se connecter</button>
</form>

<p>Pas encore de compte ? <a href="/auth/inscription">Créer un compte gratuitement</a></p>
</div>
