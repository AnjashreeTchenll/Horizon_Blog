<?php
require 'includes/db.php';

$articles = $pdo
    ->query('SELECT id, titre, categorie, resume FROM articles ORDER BY date_pub DESC LIMIT 3')
    ->fetchAll();

include 'includes/header.php';
?>

<!-- HERO -->
<section class="hero">
  <div class="container">
    <h1>Découvre le savoir.<br>Trouve ta voie.</h1>
    <p>Des informations utiles, des ressources et des opportunités pour avancer.</p>
    <a href="blog.php" class="btn btn-horizon">Explorer les ressources</a>
  </div>
</section>

<!-- CARTES -->
<section class="container cartes" id="ressources">
  <div class="row g-4">
    <div class="col-md-4">
      <div class="carte">
        <a class="carte" href="blog.php?cat=%C3%89ducation">
  <h5>Éducation</h5>
  <p>Apprendre, se former et découvrir des ressources utiles.</p>
</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="carte">
        <a class="carte" href="blog.php?cat=Carri%C3%A8re">
        <h5>Carrière</h5>
        <p>CV, entretiens, stages, emplois et bourses d'études.</p>
        </a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="carte">
        <a class="carte" href="blog.php?cat=Technologie">
        <h5>Technologie</h5>
        <p>Informatique, programmation, IA et sécurité numérique.</p>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- À PROPOS -->
<section class="apropos" id="apropos">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <span class="etiquette">À PROPOS</span>
        <h2>Le savoir, sans frontières.</h2>
        <p>HORIZON rend l'information, les connaissances et les opportunités accessibles à tous : des guides pratiques, des ressources utiles et des offres vérifiées, toujours avec leurs sources.</p>
        <a href="#actus" class="btn btn-horizon">En savoir plus</a>
      </div>
      <div class="col-lg-6">
        <img src="assets/img/about.jpg.jpg" alt="Diplômée lançant son chapeau de remise de diplôme">
      </div>
    </div>
  </div>
</section>

<!-- DERNIERS ARTICLES (depuis la base de données) -->
<section class="actus" id="actus">
  <div class="container">
    <span class="etiquette">ACTUALITÉS</span>
    <h2>Derniers articles</h2>

    <div class="row g-4 mt-3">
      <?php if (empty($articles)): ?>
        <p>Aucun article pour le moment.</p>
      <?php else: ?>
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
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>