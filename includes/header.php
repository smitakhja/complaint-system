<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Complaint Management System</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
</head>
<body>
<nav class="nav">
  <a class="brand" href="<?= BASE_URL ?>/index.php">Complaint Management</a>
  <div>
  <?php if (!empty($_SESSION["user"])): ?>
    <?php if ($_SESSION["user"]["role"] === "admin"): ?>
      <a href="<?= BASE_URL ?>/admin/dashboard.php">All Complaints</a>
    <?php else: ?>
      <a href="<?= BASE_URL ?>/user/dashboard.php">My Complaints</a>
      <a href="<?= BASE_URL ?>/user/submit.php">New Complaint</a>
    <?php endif; ?>
    <a href="<?= BASE_URL ?>/logout.php">Logout (<?= e($_SESSION["user"]["name"]) ?>)</a>
  <?php else: ?>
    <a href="<?= BASE_URL ?>/login.php">Login</a>
    <a href="<?= BASE_URL ?>/register.php">Register</a>
  <?php endif; ?>
  </div>
</nav>
<main class="container">
