<?php
require 'includes/db.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM articles WHERE id = :id');
$stmt->execute(['id' => $id]);
$article = $stmt->fetch();

if (!$article) {
    http_response_code(404);
}

include 'includes/header.php';
?>

<section class="page-article">
  <div class="container">
    <?php if (!$article): ?>
      <h1>Article introuvable</h1>
      <p>Cet article n'existe pas ou a été supprimé.</p>
      <a href="index.php" class="btn btn-horizon">← Retour à l'accueil</a>
    <?php else: ?>
      <a href="index.php" class="retour">← Retour</a>
      <span class="tag"><?= htmlspecialchars($article['categorie']) ?></span>
      <h1><?= htmlspecialchars($article['titre']) ?></h1>
      <p class="date">Publié le <?= date('d/m/Y', strtotime($article['date_pub'])) ?></p>
      <p class="chapeau"><?= htmlspecialchars($article['resume']) ?></p>
      <div class="contenu">
        <?= nl2br(htmlspecialchars($article['contenu'])) ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>