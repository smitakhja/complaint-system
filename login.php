<?php
require __DIR__ . '/config.php';

// Redirect if already logged in
if (!empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . ($_SESSION['user']['role'] === 'admin' ? '/admin/dashboard.php' : '/user/dashboard.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $q = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $q->execute([$email]);
    $user = $q->fetch();

    if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $user['id'], 'name' => $user['name'], 'role' => $user['role']];
        header('Location: ' . BASE_URL . ($user['role'] === 'admin' ? '/admin/dashboard.php' : '/user/dashboard.php'));
        exit;
    }
    $error = 'Invalid email or password.';
}
require __DIR__ . '/includes/header.php';
?>
<div class="card narrow">
  <h2>Welcome Back</h2>
  <p class="subtitle">Sign in to your Complaint Management account</p>

  <?php if (isset($_GET['registered'])): ?>
    <div class="alert success">Registration successful! You can now log in below.</div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert error"><?= e($error) ?></div>
  <?php endif; ?>

  <div class="demo-box">
    <div class="demo-header">
      <span class="demo-badge">Quick Demo Access</span>
      <span class="demo-hint">Click below to auto-fill</span>
    </div>
    <div class="demo-buttons">
      <button type="button" class="btn-demo" onclick="fillCreds('admin@example.com', 'admin123')">
        <span class="role-pill admin">Admin</span>
        <code>admin@example.com</code> / <code>admin123</code>
      </button>
      <button type="button" class="btn-demo" onclick="fillCreds('smit@example.com', 'admin123')">
        <span class="role-pill user">Student</span>
        <code>smit@example.com</code> / <code>admin123</code>
      </button>
    </div>
  </div>

  <form method="post" id="loginForm">
    <label>Email Address</label>
    <input type="email" name="email" id="emailInput" placeholder="name@example.com" required>

    <label>Password</label>
    <input type="password" name="password" id="passInput" placeholder="Enter password" required>

    <button class="btn" type="submit" style="width: 100%;">Sign In</button>
  </form>

  <p class="form-footer">
    Don't have an account? <a href="<?= BASE_URL ?>/register.php">Create one now</a>
  </p>
</div>

<script>
function fillCreds(email, pass) {
  document.getElementById('emailInput').value = email;
  document.getElementById('passInput').value = pass;
}
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
