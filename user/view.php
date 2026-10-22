<?php
require __DIR__ . "/../config.php";
require_login("user");

$id = (int)($_GET["id"] ?? 0);
$q = $pdo->prepare("SELECT * FROM complaints WHERE id = ? AND user_id = ?");
$q->execute([$id, $_SESSION["user"]["id"]]);
$c = $q->fetch();

if (!$c) {
    http_response_code(404);
    require __DIR__ . "/../includes/header.php";
    echo '<div class="card narrow" style="text-align: center;"><h2>Complaint Not Found</h2><p>This complaint does not exist or you do not have permission to view it.</p><a class="btn" href="' . BASE_URL . '/user/dashboard.php">&larr; Back to Dashboard</a></div>';
    require __DIR__ . "/../includes/footer.php";
    exit;
}

require __DIR__ . "/../includes/header.php";
?>
<div class="card narrow">
  <div class="row-between" style="border-bottom: 1px solid var(--border); padding-bottom: 14px; margin-bottom: 18px;">
    <div>
      <span class="category-tag"><?= e($c["category"]) ?></span>
      <h2 style="margin: 8px 0 0;"><?= e($c["title"]) ?></h2>
    </div>
    <div><?= badge($c["status"]) ?></div>
  </div>

  <div class="meta-grid">
    <div class="meta-item">
      <span class="meta-label">Ticket ID</span>
      <span class="meta-val">#<?= $c["id"] ?></span>
    </div>
    <div class="meta-item">
      <span class="meta-label">Submitted On</span>
      <span class="meta-val"><?= e(date("d M Y, h:i A", strtotime($c["created_at"]))) ?></span>
    </div>
    <div class="meta-item">
      <span class="meta-label">Last Updated</span>
      <span class="meta-val"><?= e(date("d M Y, h:i A", strtotime($c["updated_at"]))) ?></span>
    </div>
  </div>

  <div class="detail-block">
    <h4>Complaint Description</h4>
    <div class="description-box"><?= nl2br(e($c["description"])) ?></div>
  </div>

  <div class="detail-block">
    <h4>Official Administrative Remark</h4>
    <?php if (!empty($c["admin_remark"])): ?>
      <div class="remark-box active">
        <div class="remark-badge">Official Response</div>
        <?= nl2br(e($c["admin_remark"])) ?>
      </div>
    <?php else: ?>
      <div class="remark-box pending">
        <em>Our administrative team is reviewing your ticket. Updates and remarks will appear here.</em>
      </div>
    <?php endif; ?>
  </div>

  <div style="margin-top: 24px;">
    <a class="btn secondary" href="<?= BASE_URL ?>/user/dashboard.php">&larr; Back to Dashboard</a>
  </div>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
