<?php
require __DIR__ . "/../config.php";
require_login("admin");

$id = (int)($_GET["id"] ?? 0);
$q = $pdo->prepare("SELECT c.*, u.name AS user_name, u.email AS user_email FROM complaints c JOIN users u ON u.id=c.user_id WHERE c.id=?");
$q->execute([$id]);
$c = $q->fetch();

if (!$c) {
    http_response_code(404);
    require __DIR__ . "/../includes/header.php";
    echo '<div class="card narrow" style="text-align: center;"><h2>Ticket Not Found</h2><p>This complaint does not exist.</p><a class="btn" href="' . BASE_URL . '/admin/dashboard.php">&larr; Back to Admin Dashboard</a></div>';
    require __DIR__ . "/../includes/footer.php";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $status = $_POST["status"] ?? "";
    $remark = trim($_POST["admin_remark"] ?? "");
    if (in_array($status, STATUSES, true)) {
        $pdo->prepare("UPDATE complaints SET status=?, admin_remark=? WHERE id=?")
            ->execute([$status, $remark, $id]);
        header("Location: " . BASE_URL . "/admin/dashboard.php?updated=1");
        exit;
    }
}
require __DIR__ . "/../includes/header.php";
?>
<div class="card narrow">
  <div class="row-between" style="border-bottom: 1px solid var(--border); padding-bottom: 14px; margin-bottom: 18px;">
    <div>
      <span class="category-tag"><?= e($c["category"]) ?></span>
      <h2 style="margin: 8px 0 0;">Manage Complaint #<?= $c["id"] ?></h2>
    </div>
    <div><?= badge($c["status"]) ?></div>
  </div>

  <div class="meta-grid">
    <div class="meta-item">
      <span class="meta-label">Submitted By</span>
      <span class="meta-val"><?= e($c["user_name"]) ?></span>
      <div class="subtext"><?= e($c["user_email"] ?? "") ?></div>
    </div>
    <div class="meta-item">
      <span class="meta-label">Submitted On</span>
      <span class="meta-val"><?= e(date("d M Y, h:i A", strtotime($c["created_at"]))) ?></span>
    </div>
    <div class="meta-item">
      <span class="meta-label">Current Status</span>
      <span class="meta-val"><?= e($c["status"]) ?></span>
    </div>
  </div>

  <div class="detail-block">
    <h4>Ticket Subject</h4>
    <p style="font-weight: 600; font-size: 16px; margin: 0 0 10px;"><?= e($c["title"]) ?></p>
    <h4>Grievance Description</h4>
    <div class="description-box"><?= nl2br(e($c["description"])) ?></div>
  </div>

  <div class="detail-block" style="margin-top: 24px; border-top: 1px solid var(--border); padding-top: 20px;">
    <h4>Update Status & Remark</h4>
    <form method="post">
      <label>Change Status</label>
      <select name="status">
        <?php foreach (STATUSES as $s): ?>
          <option value="<?= e($s) ?>" <?= $s === $c["status"] ? "selected" : "" ?>><?= e($s) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Official Remark (Visible to Student)</label>
      <textarea name="admin_remark" rows="4" placeholder="Enter resolution details, assigned technician, or reason for decision..."><?= e($c["admin_remark"]) ?></textarea>

      <div style="display: flex; gap: 12px; margin-top: 18px;">
        <button class="btn" type="submit" style="flex: 2;">Save & Update Ticket</button>
        <a class="btn secondary" href="<?= BASE_URL ?>/admin/dashboard.php" style="flex: 1; text-align: center;">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
