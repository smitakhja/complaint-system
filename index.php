<?php
require __DIR__ . '/config.php';
if (!empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . ($_SESSION['user']['role'] === 'admin' ? '/admin/dashboard.php' : '/user/dashboard.php'));
    exit;
}
require __DIR__ . '/includes/header.php';
?>
<div class="card hero">
  <h1>Complaint Management System</h1>
  <p>Submit your complaints online and track their status until they are resolved.</p>
  <a class="btn" href="register.php">Get Started</a>
  <a class="btn secondary" href="login.php">Login</a>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
