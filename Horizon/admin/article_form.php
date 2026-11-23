<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
exiger_admin();

$id = (int) ($_GET['id'] ?? 0);
$categories = ['Éducation', 'Carrière', 'Technologie'];
$article = ['titre' => '', 'categorie' => 'Éducation', 'resume' => '', 'contenu' => ''];
$erreurs = [];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM articles WHERE id = ?');
    $stmt->execute([$id]);
    $trouve = $stmt->fetch();
    if (!$trouve) {
        header('Location: articles.php');
        exit;
    }
    $article = $trouve;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verifier();

    $article['titre']     = trim($_POST['titre'] ?? '');
    $article['categorie'] = $_POST['categorie'] ?? '';
    $article['resume']    = trim($_POST['resume'] ?? '');
    $article['contenu']   = trim($_POST['contenu'] ?? '');

    if ($article['titre'] === '' || mb_strlen($article['titre']) > 200) {
        $erreurs[] = 'Le titre est obligatoire (200 caractères maximum).';
    }
    if (!in_array($article['categorie'], $categories, true)) {
        $erreurs[] = 'Catégorie invalide.';
    }
    if ($article['resume'] === '') {
        $erreurs[] = 'Le résumé est obligatoire.';
    }
    if ($article['contenu'] === '') {
        $erreurs[] = 'Le contenu est obligatoire.';
    }

    if (!$erreurs) {
        $valeurs = [$article['titre'], $article['categorie'], $article['resume'], $article['contenu']];
        if ($id) {
            $stmt = $pdo->prepare('UPDATE articles SET titre=?, categorie=?, resume=?, contenu=? WHERE id=?');
            $stmt->execute([...$valeurs, $id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO articles (titre, categorie, resume, contenu) VALUES (?, ?, ?, ?)');
            $stmt->execute($valeurs);
        }
        header('Location: articles.php?ok=1');
        exit;
    }
}

$titrePage = $id ? 'Modifier un article' : 'Nouvel article';
require 'header.php';
?>
<div class="container py-5" style="max-width:820px">
  <h2 class="mb-4"><?= $titrePage ?></h2>

  <?php foreach ($erreurs as $e): ?>
    <div class="alert alert-danger py-2"><?= htmlspecialchars($e) ?></div>
  <?php endforeach; ?>

  <form method="post" class="admin-carte">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

    <label class="form-label">Titre</label>
    <input type="text" name="titre" class="form-control mb-3" maxlength="200"
           value="<?= htmlspecialchars($article['titre']) ?>" required>

    <label class="form-label">Catégorie</label>
    <select name="categorie" class="form-select mb-3">
      <?php foreach ($categories as $c): ?>
        <option value="<?= $c ?>" <?= $article['categorie'] === $c ? 'selected' : '' ?>><?= $c ?></option>
      <?php endforeach; ?>
    </select>

    <label class="form-label">Résumé (affiché sur la carte)</label>
    <textarea name="resume" rows="3" class="form-control mb-3" required><?= htmlspecialchars($article['resume']) ?></textarea>

    <label class="form-label">Contenu de l'article</label>
    <textarea name="contenu" rows="14" class="form-control mb-4" required><?= htmlspecialchars($article['contenu']) ?></textarea>

    <button class="btn btn-horizon">Enregistrer</button>
    <a href="articles.php" class="btn btn-outline-light ms-2 rounded-pill">Annuler</a>
  </form>
</div>
<?php require 'footer.php'; ?>