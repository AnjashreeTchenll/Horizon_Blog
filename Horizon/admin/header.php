<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
exiger_admin();
$titrePage = $titrePage ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($titrePage) ?> · HORIZON</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
  <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body class="admin">
  <nav class="admin-nav py-3">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="index.php" class="brand">HORIZON</a>
      <div>
  <a href="index.php" class="lien">Tableau de bord</a>
  <a href="articles.php" class="lien">Articles</a>
  <a href="messages.php" class="lien">Messages</a>
  <a href="../index.php" class="lien" target="_blank">Voir le site</a>
  <a href="logout.php" class="lien">Déconnexion</a>
</div>
    </div>
  </nav>