<?php
require __DIR__ . '/config.php';

// Redirect if already logged in
if (!empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . ($_SESSION['user']['role'] === 'admin' ? '/admin/dashboard.php' : '/user/dashboard.php'));
    exit;
}

$errors = [];
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '') $errors[] = 'Full name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';

    if (!$errors) {
        $q = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $q->execute([$email]);
        if ($q->fetch()) {
            $errors[] = 'An account with this email already exists.';
        } else {
            $pdo->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)')
                ->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            header('Location: ' . BASE_URL . '/login.php?registered=1');
            exit;
        }
    }
}
require __DIR__ . '/includes/header.php';
?>
<div class="card narrow">
  <h2>Create Student / User Account</h2>
  <p class="subtitle">Register to submit grievances and track resolutions</p>

  <?php foreach ($errors as $err): ?>
    <div class="alert error"><?= e($err) ?></div>
  <?php endforeach; ?>

  <form method="post">
    <label>Full Name</label>
    <input type="text" name="name" value="<?= e($name) ?>" placeholder="e.g. Smit Akhja" required>

    <label>Email Address</label>
    <input type="email" name="email" value="<?= e($email) ?>" placeholder="student@example.com" required>

    <label>Password (min. 6 characters)</label>
    <input type="password" name="password" placeholder="Create a secure password" required minlength="6">

    <button class="btn" type="submit" style="width: 100%;">Create Account</button>
  </form>

  <p class="form-footer">
    Already registered? <a href="<?= BASE_URL ?>/login.php">Sign in here</a>
  </p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
