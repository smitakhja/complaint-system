<?php
require __DIR__ . "/../config.php";
require_login("admin");

$counts = array_fill_keys(STATUSES, 0);
foreach ($pdo->query("SELECT status, COUNT(*) n FROM complaints GROUP BY status") as $r) {
    $counts[$r["status"]] = $r["n"];
}

$filter = $_GET["status"] ?? "";
if (in_array($filter, STATUSES, true)) {
    $q = $pdo->prepare("SELECT c.*, u.name AS user_name FROM complaints c JOIN users u ON u.id=c.user_id WHERE c.status=? ORDER BY c.created_at DESC");
    $q->execute([$filter]);
} else {
    $q = $pdo->query("SELECT c.*, u.name AS user_name FROM complaints c JOIN users u ON u.id=c.user_id ORDER BY c.created_at DESC");
}
$complaints = $q->fetchAll();

require __DIR__ . "/../includes/header.php";
?>
<h2>Admin Dashboard</h2>
<?php if (isset($_GET["updated"])): ?><div class="alert success">Complaint updated.</div><?php endif; ?>
<div class="stats">
  <?php foreach ($counts as $s => $n): ?>
    <a class="stat" href="?status=<?= urlencode($s) ?>"><span><?= $n ?></span><?= e($s) ?></a>
  <?php endforeach; ?>
  <a class="stat" href="dashboard.php"><span>&#8734;</span>Show All</a>
</div>
<div class="card">
  <table>
    <tr><th>#</th><th>User</th><th>Title</th><th>Category</th><th>Status</th><th>Date</th><th></th></tr>
    <?php foreach ($complaints as $c): ?>
    <tr>
      <td><?= $c["id"] ?></td>
      <td><?= e($c["user_name"]) ?></td>
      <td><?= e($c["title"]) ?></td>
      <td><?= e($c["category"]) ?></td>
      <td><?= badge($c["status"]) ?></td>
      <td><?= e(date("d M Y", strtotime($c["created_at"]))) ?></td>
      <td><a href="update.php?id=<?= $c["id"] ?>">Update</a></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$complaints): ?><tr><td colspan="7">No complaints found.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
