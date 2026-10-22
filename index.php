<?php
require __DIR__ . '/config.php';

if (!empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . ($_SESSION['user']['role'] === 'admin' ? '/admin/dashboard.php' : '/user/dashboard.php'));
    exit;
}
require __DIR__ . '/includes/header.php';
?>
<div class="hero-section">
  <div class="badge-hero">⚡ Semester-5 Project &bull; Online Portal</div>
  <h1 class="hero-title">Complaint Management System</h1>
  <p class="hero-subtitle">
    A streamlined grievance redressing platform. Submit issues across Technical, Service, and Billing categories and track real-time resolution updates.
  </p>
  <div class="hero-actions">
    <a class="btn btn-lg" href="<?= BASE_URL ?>/register.php">Get Started &rarr;</a>
    <a class="btn btn-lg secondary" href="<?= BASE_URL ?>/login.php">Sign In</a>
  </div>
</div>

<div class="features-grid">
  <div class="feature-card">
    <div class="feature-icon">📝</div>
    <h3>Easy Submission</h3>
    <p>Submit detailed grievances with categorized issues and real-time validation.</p>
  </div>
  <div class="feature-card">
    <div class="feature-icon">🔍</div>
    <h3>Live Tracking</h3>
    <p>Track progress from <em>Pending</em> to <em>In Progress</em> and <em>Resolved</em> with admin remarks.</p>
  </div>
  <div class="feature-card">
    <div class="feature-icon">🛡️</div>
    <h3>Admin Dashboard</h3>
    <p>Dedicated administrative controls with status breakdown analytics and quick updates.</p>
  </div>
</div>

<div class="card demo-banner">
  <div class="demo-banner-content">
    <div>
      <h4>Quick Live Demo Access</h4>
      <p>Log in right away using pre-configured test credentials for Admin or Student.</p>
    </div>
    <a class="btn" href="<?= BASE_URL ?>/login.php">Go to Login &rarr;</a>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
