<?php
require __DIR__ . "/../config.php";
require_login("admin");

$counts = array_fill_keys(STATUSES, 0);
$total_count = 0;
foreach ($pdo->query("SELECT status, COUNT(*) n FROM complaints GROUP BY status") as $r) {
    if (isset($counts[$r["status"]])) {
        $counts[$r["status"]] = (int)$r["n"];
        $total_count += (int)$r["n"];
    }
}

$filter = $_GET["status"] ?? "";
if (in_array($filter, STATUSES, true)) {
    $q = $pdo->prepare("SELECT c.*, u.name AS user_name, u.email AS user_email FROM complaints c JOIN users u ON u.id=c.user_id WHERE c.status=? ORDER BY c.created_at DESC");
    $q->execute([$filter]);
} else {
    $q = $pdo->query("SELECT c.*, u.name AS user_name, u.email AS user_email FROM complaints c JOIN users u ON u.id=c.user_id ORDER BY c.created_at DESC");
}
$complaints = $q->fetchAll();

require __DIR__ . "/../includes/header.php";
?>
<div class="page-header">
  <div>
    <h2>Administrative Control Center</h2>
    <p class="subtitle">Review, assign, and update submitted student & staff grievances</p>
  </div>
  <div class="header-badges">
    <span class="live-indicator"><span class="pulse-dot"></span> Live Portal</span>
  </div>
</div>

<?php if (isset($_GET["updated"])): ?>
  <div class="alert success">Complaint ticket was successfully updated with your remark.</div>
<?php endif; ?>

<div class="stats">
  <a class="stat <?= $filter === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/dashboard.php">
    <div class="stat-inner">
      <span class="stat-number"><?= $total_count ?></span>
      <span class="stat-label">Total Tickets</span>
    </div>
  </a>
  <?php foreach ($counts as $s => $n): ?>
    <?php $cls = strtolower(str_replace(' ', '-', $s)); ?>
    <a class="stat stat-<?= $cls ?> <?= $filter === $s ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/dashboard.php?status=<?= urlencode($s) ?>">
      <div class="stat-inner">
        <span class="stat-number"><?= $n ?></span>
        <span class="stat-label"><?= e($s) ?></span>
      </div>
    </a>
  <?php endforeach; ?>
</div>

<div class="card">
  <div class="card-header-bar">
    <h3 style="margin: 0;">
      <?= $filter ? 'Filtered: ' . e($filter) . ' (' . count($complaints) . ')' : 'All Complaints (' . count($complaints) . ')' ?>
    </h3>
    <?php if ($filter): ?>
      <a class="btn-clear" href="<?= BASE_URL ?>/admin/dashboard.php">&times; Clear Filter</a>
    <?php endif; ?>
  </div>

  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Student / User</th>
          <th>Subject & Details</th>
          <th>Department</th>
          <th>Status</th>
          <th>Date Filed</th>
          <th style="text-align: right;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($complaints as $c): ?>
        <tr>
          <td><strong>#<?= $c["id"] ?></strong></td>
          <td>
            <strong><?= e($c["user_name"]) ?></strong>
            <div class="subtext"><?= e($c["user_email"] ?? "") ?></div>
          </td>
          <td>
            <div style="font-weight: 600;"><?= e($c["title"]) ?></div>
            <div class="subtext" style="max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
              <?= e(substr($c["description"], 0, 70)) ?>...
            </div>
          </td>
          <td><span class="category-tag"><?= e($c["category"]) ?></span></td>
          <td><?= badge($c["status"]) ?></td>
          <td><?= e(date("d M Y", strtotime($c["created_at"]))) ?></td>
          <td style="text-align: right;">
            <a class="btn-sm" href="<?= BASE_URL ?>/admin/update.php?id=<?= $c["id"] ?>">Manage &rarr;</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$complaints): ?>
        <tr>
          <td colspan="7" style="text-align: center; padding: 40px;">
            <div class="empty-icon" style="font-size: 32px; margin-bottom: 8px;">📋</div>
            <p style="color: var(--text-muted); margin: 0;">No complaints matching this filter.</p>
          </td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
