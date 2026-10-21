<?php
require __DIR__ . "/../config.php";
require_login("user");

$q = $pdo->prepare("SELECT * FROM complaints WHERE user_id = ? ORDER BY created_at DESC");
$q->execute([$_SESSION["user"]["id"]]);
$complaints = $q->fetchAll();

require __DIR__ . "/../includes/header.php";
?>
<div class="row-between">
  <h2>My Complaints</h2>
  <a class="btn" href="submit.php">+ New Complaint</a>
</div>
<?php if (isset($_GET["submitted"])): ?><div class="alert success">Complaint submitted successfully.</div><?php endif; ?>
<div class="card">
<?php if (!$complaints): ?>
  <p>You have not submitted any complaints yet.</p>
<?php else: ?>
  <table>
    <tr><th>#</th><th>Title</th><th>Category</th><th>Status</th><th>Submitted</th><th></th></tr>
    <?php foreach ($complaints as $c): ?>
    <tr>
      <td><?= $c["id"] ?></td>
      <td><?= e($c["title"]) ?></td>
      <td><?= e($c["category"]) ?></td>
      <td><?= badge($c["status"]) ?></td>
      <td><?= e(date("d M Y", strtotime($c["created_at"]))) ?></td>
      <td><a href="view.php?id=<?= $c["id"] ?>">View</a></td>
    </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
