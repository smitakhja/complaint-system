<?php
require __DIR__ . '/config.php';
$errors = [];
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '') $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';

    if (!$errors) {
        $q = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $q->execute([$email]);
        if ($q->fetch()) {
            $errors[] = 'Email already registered.';
        } else {
            $pdo->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)')
                ->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
require __DIR__ . '/includes/header.php';
?>
<div class="card narrow">
  <h2>Create Account</h2>
  <?php foreach ($errors as $err): ?><div class="alert error"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post">
    <label>Full Name</label>
    <input type="text" name="name" value="<?= e($name) ?>" required>
    <label>Email</label>
    <input type="email" name="email" value="<?= e($email) ?>" required>
    <label>Password</label>
    <input type="password" name="password" required>
    <button class="btn" type="submit">Register</button>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
