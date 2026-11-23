<?php
require 'includes/db.php';

$categories = ['Éducation', 'Carrière', 'Technologie'];
$q    = trim($_GET['q'] ?? '');
$cat  = $_GET['cat'] ?? '';
$page = max(1, (int) ($_GET['page'] ?? 1));
$parPage = 6;

$where = ' WHERE 1=1';
$params = [];
if ($q !== '') {
    $where .= ' AND (titre LIKE ? OR resume LIKE ?)';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
}
if (in_array($cat, $categories, true)) {
    $where .= ' AND categorie = ?';
    $params[] = $cat;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM articles$where");
$stmt->execute($params);
$total = (int) $stmt->fetchColumn();

$pages  = max(1, (int) ceil($total / $parPage));
$page   = min($page, $pages);
$offset = ($page - 1) * $parPage;

$stmt = $pdo->prepare("SELECT id, titre, categorie, resume FROM articles$where ORDER BY date_pub DESC LIMIT $parPage OFFSET $offset");
$stmt->execute($params);
$articles = $stmt->fetchAll();

function lien_blog(array $modif): string {
    $p = array_merge(['q' => $_GET['q'] ?? '', 'cat' => $_GET['cat'] ?? '', 'page' => 1], $modif);
    $p = array_filter($p, fn($v) => $v !== '' && $v !== null && $v !== 1);
    return 'blog.php' . ($p ? '?' . http_build_query($p) : '');
}

include 'includes/header.php';
?>

<section class="blog-page">
  <div class="container">
    <span class="etiquette">BLOG</span>
    <h1>Tous les articles</h1>

    <form method="get" class="recherche">
      <?php if ($cat !== ''): ?>
        <input type="hidden" name="cat" value="<?= htmlspecialchars($cat) ?>">
      <?php endif; ?>
      <input type="text" name="q" placeholder="Rechercher un article..." value="<?= htmlspecialchars($q) ?>">
      <button class="btn btn-horizon">Chercher</button>
    </form>

    <div class="filtres">
      <a href="<?= lien_blog(['cat' => '', 'page' => 1]) ?>" class="filtre <?= $cat === '' ? 'actif' : '' ?>">Tous</a>
      <?php foreach ($categories as $c): ?>
        <a href="<?= lien_blog(['cat' => $c, 'page' => 1]) ?>" class="filtre <?= $cat === $c ? 'actif' : '' ?>"><?= $c ?></a>
      <?php endforeach; ?>
    </div>

    <p class="text-secondary"><?= $total ?> article(s) trouvé(s)</p>

    <div class="row g-4 mt-1">
      <?php if (!$articles): ?>
        <p>Aucun article ne correspond à ta recherche.</p>
      <?php endif; ?>
      <?php foreach ($articles as $a): ?>
        <div class="col-md-4">
          <div class="article">
            <span class="tag"><?= htmlspecialchars($a['categorie']) ?></span>
            <h5><?= htmlspecialchars($a['titre']) ?></h5>
            <p><?= htmlspecialchars($a['resume']) ?></p>
            <a href="article.php?id=<?= (int) $a['id'] ?>">Lire l'article →</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if ($pages > 1): ?>
      <nav class="pagination-horizon">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <a href="<?= lien_blog(['page' => $i]) ?>" class="<?= $i === $page ? 'actif' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
      </nav>
    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>