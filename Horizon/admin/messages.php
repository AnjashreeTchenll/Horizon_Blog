<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
exiger_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer'])) {
    csrf_verifier();
    $stmt = $pdo->prepare('DELETE FROM messages WHERE id = ?');
    $stmt->execute([(int) $_POST['supprimer']]);
    header('Location: messages.php');
    exit;
}

$messages = $pdo->query('SELECT * FROM messages ORDER BY date_envoi DESC')->fetchAll();

$titrePage = 'Messages';
require 'header.php';
?>
<div class="container py-5">
  <h2 class="mb-4">Messages reçus</h2>

  <?php if (!$messages): ?>
    <div class="glass text-secondary">Aucun message pour le moment.</div>
  <?php endif; ?>

  <?php foreach ($messages as $m): ?>
  <div class="glass msg mb-3">
    <div class="d-flex justify-content-between flex-wrap gap-2">
      <strong class="msg-sujet"><?= htmlspecialchars($m['sujet']) ?></strong>
      <span class="msg-date"><?= date('d/m/Y H:i', strtotime($m['date_envoi'])) ?></span>
    </div>
    <div class="msg-from mb-2">
      <?= htmlspecialchars($m['nom']) ?> ·
      <a href="mailto:<?= htmlspecialchars($m['email']) ?>"><?= htmlspecialchars($m['email']) ?></a>
    </div>
    <p class="mb-3"><?= nl2br(htmlspecialchars($m['message'])) ?></p>

    <div class="d-flex gap-2">
      <a class="btn-act btn-repondre"
         href="mailto:<?= htmlspecialchars($m['email']) ?>?subject=<?= rawurlencode('Re: ' . $m['sujet']) ?>">Répondre</a>
      <form method="post" class="d-inline" onsubmit="return confirm('Supprimer ce message ?');">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <button name="supprimer" value="<?= (int) $m['id'] ?>" class="btn-act btn-suppr">Supprimer</button>
      </form>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php require 'footer.php'; ?>