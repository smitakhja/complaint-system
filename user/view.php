<?php
require __DIR__ . "/../config.php";
require_login("user");

$q = $pdo->prepare("SELECT * FROM complaints WHERE id = ? AND user_id = ?");
$q->execute([(int)($_GET["id"] ?? 0), $_SESSION["user"]["id"]]);
$c = $q->fetch();
if (!$c) { http_response_code(404); die("Complaint not found."); }

require __DIR__ . "/../includes/header.php";
?>
<div class="card narrow">
  <h2>Complaint #<?= $c["id"] ?>: <?= e($c["title"]) ?></h2>
  <p><strong>Status:</strong> <?= badge($c["status"]) ?></p>
  <p><strong>Category:</strong> <?= e($c["category"]) ?></p>
  <p><strong>Submitted:</strong> <?= e($c["created_at"]) ?></p>
  <p><strong>Last updated:</strong> <?= e($c["updated_at"]) ?></p>
  <h3>Description</h3>
  <p><?= nl2br(e($c["description"])) ?></p>
  <h3>Admin Remark</h3>
  <p><?= $c["admin_remark"] ? nl2br(e($c["admin_remark"])) : "<em>No remark yet.</em>" ?></p>
  <a class="btn secondary" href="dashboard.php">Back</a>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
