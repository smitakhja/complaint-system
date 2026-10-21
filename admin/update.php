<?php
require __DIR__ . "/../config.php";
require_login("admin");

$id = (int)($_GET["id"] ?? 0);
$q = $pdo->prepare("SELECT c.*, u.name AS user_name, u.email FROM complaints c JOIN users u ON u.id=c.user_id WHERE c.id=?");
$q->execute([$id]);
$c = $q->fetch();
if (!$c) { http_response_code(404); die("Complaint not found."); }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $status = $_POST["status"] ?? "";
    $remark = trim($_POST["admin_remark"] ?? "");
    if (in_array($status, STATUSES, true)) {
        $pdo->prepare("UPDATE complaints SET status=?, admin_remark=? WHERE id=?")
            ->execute([$status, $remark, $id]);
        header("Location: dashboard.php?updated=1");
        exit;
    }
}
require __DIR__ . "/../includes/header.php";
?>
<div class="card narrow">
  <h2>Complaint #<?= $c["id"] ?>: <?= e($c["title"]) ?></h2>
  <p><strong>By:</strong> <?= e($c["user_name"]) ?> (<?= e($c["email"]) ?>)</p>
  <p><strong>Category:</strong> <?= e($c["category"]) ?> &nbsp; <?= badge($c["status"]) ?></p>
  <p><?= nl2br(e($c["description"])) ?></p>
  <hr>
  <form method="post">
    <label>Update Status</label>
    <select name="status">
      <?php foreach (STATUSES as $s): ?>
        <option <?= $s === $c["status"] ? "selected" : "" ?>><?= e($s) ?></option>
      <?php endforeach; ?>
    </select>
    <label>Remark for User</label>
    <textarea name="admin_remark" rows="4"><?= e($c["admin_remark"]) ?></textarea>
    <button class="btn" type="submit">Save</button>
    <a class="btn secondary" href="dashboard.php">Cancel</a>
  </form>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
