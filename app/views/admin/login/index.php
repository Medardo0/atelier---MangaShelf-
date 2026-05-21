<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Login — Admin MangaShelf</title></head>
<body>
<h1>Connexion administrateur</h1>
<?php if (!empty($error)): ?>
  <div role="alert"><p>Identifiants incorrects.</p></div>
<?php endif; ?>
<form method="POST" action="/mangashelf/public/admin/login">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
  <label for="username">Nom d'utilisateur</label>
  <input type="text" id="username" name="username" required autocomplete="username">
  <label for="password">Mot de passe</label>
  <input type="password" id="password" name="password" required autocomplete="current-password">
  <button type="submit">Se connecter</button>
</form>
<p><a href="/mangashelf/public/">Retour au site</a></p>
</body>
</html>
