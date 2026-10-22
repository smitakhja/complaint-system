<?php
require __DIR__ . "/../config.php";
require_login("user");

$q = $pdo->prepare("SELECT * FROM complaints WHERE user_id = ? ORDER BY created_at DESC");
$q->execute([$_SESSION["user"]["id"]]);
$complaints = $q->fetchAll();

require __DIR__ . "/../includes/header.php";
?>
<div class="page-header">
  <div>
    <h2>My Complaints</h2>
    <p class="subtitle">Track and review grievances you have submitted</p>
  </div>
  <a class="btn" href="<?= BASE_URL ?>/user/submit.php">+ Submit New Complaint</a>
</div>

<?php if (isset($_GET["submitted"])): ?>
  <div class="alert success">Your complaint has been submitted successfully and will be reviewed shortly.</div>
<?php endif; ?>

<div class="card">
<?php if (!$complaints): ?>
  <div class="empty-state">
    <div class="empty-icon">📂</div>
    <h3>No complaints submitted yet</h3>
    <p>If you are facing any service, technical or billing issues, file a complaint to get it resolved.</p>
    <a class="btn" href="<?= BASE_URL ?>/user/submit.php">File Your First Complaint</a>
  </div>
<?php else: ?>
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Title</th>
          <th>Category</th>
          <th>Status</th>
          <th>Submitted On</th>
          <th style="text-align: right;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($complaints as $c): ?>
        <tr>
          <td><strong>#<?= $c["id"] ?></strong></td>
          <td><?= e($c["title"]) ?></td>
          <td><span class="category-tag"><?= e($c["category"]) ?></span></td>
          <td><?= badge($c["status"]) ?></td>
          <td><?= e(date("d M Y, h:i A", strtotime($c["created_at"]))) ?></td>
          <td style="text-align: right;">
            <a class="btn-sm" href="<?= BASE_URL ?>/user/view.php?id=<?= $c["id"] ?>">View &rarr;</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
