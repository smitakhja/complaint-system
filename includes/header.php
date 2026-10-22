<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Complaint Management System</title>
<meta name="description" content="Online grievance redressal and tracking system for students and administration.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
</head>
<body>
<nav class="nav">
  <div class="nav-inner">
    <a class="brand" href="<?= BASE_URL ?>/index.php">
      <span class="brand-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
          <path d="M9 12l2 2 4-4"/>
        </svg>
      </span>
      <span>Complaint Desk</span>
    </a>
    <div class="nav-links">
    <?php if (!empty($_SESSION["user"])): ?>
      <?php if ($_SESSION["user"]["role"] === "admin"): ?>
        <a class="nav-item" href="<?= BASE_URL ?>/admin/dashboard.php">All Complaints</a>
      <?php else: ?>
        <a class="nav-item" href="<?= BASE_URL ?>/user/dashboard.php">My Complaints</a>
        <a class="nav-item" href="<?= BASE_URL ?>/user/submit.php">+ New Complaint</a>
      <?php endif; ?>
      <span class="user-pill">
        <span class="user-role-tag <?= e($_SESSION["user"]["role"]) ?>"><?= ucfirst(e($_SESSION["user"]["role"])) ?></span>
        <span class="user-name"><?= e($_SESSION["user"]["name"]) ?></span>
      </span>
      <a class="nav-item nav-logout" href="<?= BASE_URL ?>/logout.php">Logout</a>
    <?php else: ?>
      <a class="nav-item" href="<?= BASE_URL ?>/login.php">Sign In</a>
      <a class="nav-item nav-btn" href="<?= BASE_URL ?>/register.php">Register</a>
    <?php endif; ?>
    </div>
  </div>
</nav>
<main class="container">
