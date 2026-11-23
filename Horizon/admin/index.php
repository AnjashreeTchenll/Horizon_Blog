<?php
$titrePage = 'Tableau de bord';
require 'header.php';

$nb = (int) $pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn();
$nbCat = (int) $pdo->query('SELECT COUNT(DISTINCT categorie) FROM articles')->fetchColumn();
$derniere = $pdo->query('SELECT MAX(date_pub) FROM articles')->fetchColumn();
$derniers = $pdo->query('SELECT id, titre, categorie, date_pub FROM articles ORDER BY date_pub DESC LIMIT 5')->fetchAll();

$classes = ['Éducation' => 'pill-education', 'Carrière' => 'pill-carriere', 'Technologie' => 'pill-technologie'];
?>
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="m-0">Tableau de bord</h2>
    <a href="article_form.php" class="btn btn-horizon">+ Nouvel article</a>
  </div>

  <div class="row g-4 mb-5">
    <div class="col-md-4">
      <div class="glass stat s-bleu">
        <div class="nombre"><?= $nb ?></div>
        <div class="legende">Article(s) publié(s)</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="glass stat s-cyan">
        <div class="nombre"><?= $nbCat ?></div>
        <div class="legende">Catégories actives</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="glass stat s-violet">
        <div class="nombre" style="font-size:1.8rem"><?= $derniere ? date('d/m/Y', strtotime($derniere)) : '—' ?></div>
        <div class="legende">Dernière publication</div>
      </div>
    </div>
  </div>

  <h5 class="mb-3">Derniers articles</h5>
  <div class="glass">
    <?php if (!$derniers): ?>
      <p class="text-secondary m-0">Aucun article pour le moment.</p>
    <?php endif; ?>
    <?php foreach ($derniers as $a): ?>
      <div class="ligne-article">
        <span class="titre"><?= htmlspecialchars($a['titre']) ?></span>
        <span class="pill <?= $classes[$a['categorie']] ?? '' ?>"><?= htmlspecialchars($a['categorie']) ?></span>
        <span class="date"><?= date('d/m/Y', strtotime($a['date_pub'])) ?></span>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php require 'footer.php'; ?>