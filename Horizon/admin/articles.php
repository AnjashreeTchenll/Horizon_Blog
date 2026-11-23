<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
exiger_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer'])) {
    csrf_verifier();
    $stmt = $pdo->prepare('DELETE FROM articles WHERE id = ?');
    $stmt->execute([(int) $_POST['supprimer']]);
    header('Location: articles.php?ok=supprime');
    exit;
}

$categories = ['Éducation', 'Carrière', 'Technologie'];
$classes = ['Éducation' => 'pill-education', 'Carrière' => 'pill-carriere', 'Technologie' => 'pill-technologie'];

$q   = trim($_GET['q'] ?? '');
$cat = $_GET['cat'] ?? '';
$tri = ($_GET['tri'] ?? 'recent') === 'ancien' ? 'ASC' : 'DESC';

$sql = 'SELECT id, titre, categorie, date_pub FROM articles WHERE 1=1';
$params = [];
if ($q !== '') {
    $sql .= ' AND titre LIKE ?';
    $params[] = '%' . $q . '%';
}
if (in_array($cat, $categories, true)) {
    $sql .= ' AND categorie = ?';
    $params[] = $cat;
}
$sql .= " ORDER BY date_pub $tri";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();
$total = (int) $pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn();

$titrePage = 'Articles';
require 'header.php';
?>
<div class="container py-5">
  <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
      <h2 class="m-0">Articles</h2>
      <a href="article_form.php" class="btn btn-horizon">+ Nouvel article</a>
    </div>
    <div class="glass stat s-cyan py-2 px-3">
      <span class="legende">Total articles</span>
      <strong class="ms-2"><?= $total ?></strong>
    </div>
  </div>

  <?php if (isset($_GET['ok'])): ?>
    <div class="alert alert-success py-2">
      <?= $_GET['ok'] === 'supprime' ? 'Article supprimé.' : 'Article enregistré.' ?>
    </div>
  <?php endif; ?>

  <div class="glass">
    <form method="get" class="row g-2 mb-3">
      <div class="col-md-5">
        <input type="text" name="q" class="form-control" placeholder="Rechercher..." value="<?= htmlspecialchars($q) ?>">
      </div>
      <div class="col-6 col-md-3">
        <select name="cat" class="form-select" onchange="this.form.submit()">
          <option value="">Toutes catégories</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c ?>" <?= $cat === $c ? 'selected' : '' ?>><?= $c ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-6 col-md-3">
        <select name="tri" class="form-select" onchange="this.form.submit()">
          <option value="recent" <?= $tri === 'DESC' ? 'selected' : '' ?>>Plus récents</option>
          <option value="ancien" <?= $tri === 'ASC' ? 'selected' : '' ?>>Plus anciens</option>
        </select>
      </div>
      <div class="col-md-1 d-grid">
        <button class="btn btn-horizon px-0">OK</button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table-admin">
        <thead>
          <tr><th>Titre</th><th>Catégorie</th><th>Date</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
          <?php if (!$articles): ?>
            <tr><td colspan="4" class="text-center text-secondary">Aucun article trouvé.</td></tr>
          <?php endif; ?>
          <?php foreach ($articles as $a): ?>
            <tr>
              <td><?= htmlspecialchars($a['titre']) ?></td>
              <td><span class="pill <?= $classes[$a['categorie']] ?? '' ?>"><?= htmlspecialchars($a['categorie']) ?></span></td>
              <td class="text-secondary"><?= date('d/m/Y', strtotime($a['date_pub'])) ?></td>
              <td class="text-end text-nowrap">
                <a href="../article.php?id=<?= (int) $a['id'] ?>" target="_blank" class="btn-act btn-voir">Voir</a>
                <a href="article_form.php?id=<?= (int) $a['id'] ?>" class="btn-act btn-modif">Modifier</a>
                <form method="post" class="d-inline" onsubmit="return confirm('Supprimer cet article ?');">
                  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                  <button name="supprimer" value="<?= (int) $a['id'] ?>" class="btn-act btn-suppr">Supprimer</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require 'footer.php'; ?>