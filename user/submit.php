<?php
require __DIR__ . "/../config.php";
require_login("user");

$errors = [];
$title = $description = "";
$category = CATEGORIES[0];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $category = $_POST["category"] ?? "";
    $description = trim($_POST["description"] ?? "");

    if ($title === "") $errors[] = "Complaint title is required.";
    if (!in_array($category, CATEGORIES, true)) $errors[] = "Please select a valid category.";
    if (strlen($description) < 10) $errors[] = "Description must be at least 10 characters long.";

    if (!$errors) {
        $pdo->prepare("INSERT INTO complaints (user_id,title,category,description) VALUES (?,?,?,?)")
            ->execute([$_SESSION["user"]["id"], $title, $category, $description]);
        header("Location: " . BASE_URL . "/user/dashboard.php?submitted=1");
        exit;
    }
}
require __DIR__ . "/../includes/header.php";
?>
<div class="card narrow">
  <h2>File a New Complaint</h2>
  <p class="subtitle">Please provide clear details to help us resolve the issue swiftly</p>

  <?php foreach ($errors as $err): ?>
    <div class="alert error"><?= e($err) ?></div>
  <?php endforeach; ?>

  <form method="post">
    <label>Complaint Title</label>
    <input type="text" name="title" value="<?= e($title) ?>" placeholder="e.g. Lab projector display flickering during lectures" required>

    <label>Department / Category</label>
    <select name="category">
      <?php foreach (CATEGORIES as $c): ?>
        <option value="<?= e($c) ?>" <?= $c === $category ? "selected" : "" ?>><?= e($c) ?></option>
      <?php endforeach; ?>
    </select>

    <label>Detailed Description</label>
    <textarea name="description" rows="5" placeholder="Describe where and when the issue occurs and any steps already taken..." required><?= e($description) ?></textarea>

    <div style="display: flex; gap: 12px; margin-top: 18px;">
      <button class="btn" type="submit" style="flex: 2;">Submit Complaint</button>
      <a class="btn secondary" href="<?= BASE_URL ?>/user/dashboard.php" style="flex: 1; text-align: center;">Cancel</a>
    </div>
  </form>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
