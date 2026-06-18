<h1>Dashboard</h1>

<section class="admin-stats">
  <h2>Indicateurs</h2>
  <ul>
    <li><strong><?= $stats['mangas_published'] ?></strong><span>Mangas publies</span></li>
    <li><strong><?= $stats['messages_unread'] ?></strong><span>Messages non lus</span></li>
    <li><strong><?= $stats['genres_count'] ?></strong><span>Genres et tags</span></li>
  </ul>
</section>

<section class="admin-panel">
  <h2>Derniers mangas</h2>
  <div class="admin-table">
    <table>
      <caption>Mangas recents</caption>
      <thead>
        <tr>
          <th>Titre</th>
          <th>Genre</th>
          <th>Statut</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recent_mangas as $m): ?>
        <tr>
          <td><?= htmlspecialchars($m['title']) ?></td>
          <td><?= htmlspecialchars($m['genre']) ?></td>
          <td><?= htmlspecialchars($m['status']) ?></td>
          <td><?= htmlspecialchars($m['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
