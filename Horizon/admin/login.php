<?php
require '../includes/db.php';
session_start();

if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE email = ?');
    $stmt->execute([trim($_POST['email'] ?? '')]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($_POST['mot_de_passe'] ?? '', $admin['mot_de_passe'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: index.php');
        exit;
    }
    $erreur = 'Email ou mot de passe incorrect.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connexion · HORIZON</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
  <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body class="admin admin-login">
  <form method="post" class="login-box">
    <h1>Espace admin</h1>
    <?php if ($erreur): ?>
      <div class="alert alert-danger py-2"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>
    <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
    <input type="password" name="mot_de_passe" class="form-control mb-3" placeholder="Mot de passe" required>
    <button class="btn btn-horizon w-100 py-2">Se connecter</button>
  </form>
</body>
</html>