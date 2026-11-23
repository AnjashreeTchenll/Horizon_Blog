<?php
require 'includes/auth.php';
require 'includes/db.php';

$erreurs = [];
$envoye = false;
$f = ['nom' => '', 'email' => '', 'sujet' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verifier();

    // Champ piège : un humain ne le remplit jamais
    if (!empty($_POST['site_web'])) {
        exit;
    }

    foreach ($f as $k => $v) {
        $f[$k] = trim($_POST[$k] ?? '');
    }

    if ($f['nom'] === '' || mb_strlen($f['nom']) > 100) {
        $erreurs[] = 'Le nom est obligatoire (100 caractères maximum).';
    }
    if (!filter_var($f['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($f['email']) > 150) {
        $erreurs[] = "L'adresse email n'est pas valide.";
    }
    if ($f['sujet'] === '' || mb_strlen($f['sujet']) > 150) {
        $erreurs[] = 'Le sujet est obligatoire (150 caractères maximum).';
    }
    if (mb_strlen($f['message']) < 10 || mb_strlen($f['message']) > 3000) {
        $erreurs[] = 'Le message doit faire entre 10 et 3000 caractères.';
    }

    // Anti-spam simple : un message toutes les 30 secondes par visiteur
    if (!$erreurs && time() - ($_SESSION['dernier_msg'] ?? 0) < 30) {
        $erreurs[] = 'Merci de patienter quelques secondes avant un nouvel envoi.';
    }

    if (!$erreurs) {
        $stmt = $pdo->prepare('INSERT INTO messages (nom, email, sujet, message) VALUES (?, ?, ?, ?)');
        $stmt->execute([$f['nom'], $f['email'], $f['sujet'], $f['message']]);
        $_SESSION['dernier_msg'] = time();
        $envoye = true;
        $f = ['nom' => '', 'email' => '', 'sujet' => '', 'message' => ''];
    }
}

include 'includes/header.php';
?>

<section class="contact-page">
  <div class="container">
    <span class="etiquette">CONTACT</span>
    <h1>Écris-nous</h1>
    <p class="intro">Une question, une suggestion ou un article à proposer ? Envoie-nous un message, nous lisons tout.</p>

    <?php if ($envoye): ?>
      <div class="alert alert-success">Merci ! Ton message a bien été envoyé.</div>
    <?php endif; ?>
    <?php foreach ($erreurs as $e): ?>
      <div class="alert alert-danger py-2"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>

    <form method="post" class="form-contact">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="text" name="site_web" class="piege" tabindex="-1" autocomplete="off">

      <div class="row g-3">
        <div class="col-md-6">
          <label>Nom</label>
          <input type="text" name="nom" maxlength="100" value="<?= htmlspecialchars($f['nom']) ?>" required>
        </div>
        <div class="col-md-6">
          <label>Email</label>
          <input type="email" name="email" maxlength="150" value="<?= htmlspecialchars($f['email']) ?>" required>
        </div>
        <div class="col-12">
          <label>Sujet</label>
          <input type="text" name="sujet" maxlength="150" value="<?= htmlspecialchars($f['sujet']) ?>" required>
        </div>
        <div class="col-12">
          <label>Message</label>
          <textarea name="message" rows="7" maxlength="3000" required><?= htmlspecialchars($f['message']) ?></textarea>
        </div>
        <div class="col-12">
          <button class="btn btn-horizon">Envoyer le message</button>
        </div>
      </div>
    </form>
  </div>
</section>

<?php include 'includes/footer.php'; ?>