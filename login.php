<?php
require __DIR__ . '/config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $q = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $q->execute([$email]);
    $user = $q->fetch();

    if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $user['id'], 'name' => $user['name'], 'role' => $user['role']];
        header('Location: index.php');
        exit;
    }
    $error = 'Invalid email or password.';
}
require __DIR__ . '/includes/header.php';
?>
<div class="card narrow">
  <h2>Login</h2>
  <?php if (isset($_GET['registered'])): ?><div class="alert success">Registration successful. Please login.</div><?php endif; ?>
  <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <label>Email</label>
    <input type="email" name="email" required>
    <label>Password</label>
    <input type="password" name="password" required>
    <button class="btn" type="submit">Login</button>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
