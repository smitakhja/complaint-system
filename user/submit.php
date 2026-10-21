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

    if ($title === "") $errors[] = "Title is required.";
    if (!in_array($category, CATEGORIES, true)) $errors[] = "Select a valid category.";
    if (strlen($description) < 10) $errors[] = "Description must be at least 10 characters.";

    if (!$errors) {
        $pdo->prepare("INSERT INTO complaints (user_id,title,category,description) VALUES (?,?,?,?)")
            ->execute([$_SESSION["user"]["id"], $title, $category, $description]);
        header("Location: dashboard.php?submitted=1");
        exit;
    }
}
require __DIR__ . "/../includes/header.php";
?>
<div class="card narrow">
  <h2>Submit a Complaint</h2>
  <?php foreach ($errors as $err): ?><div class="alert error"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post">
    <label>Title</label>
    <input type="text" name="title" value="<?= e($title) ?>" required>
    <label>Category</label>
    <select name="category">
      <?php foreach (CATEGORIES as $c): ?>
        <option <?= $c === $category ? "selected" : "" ?>><?= e($c) ?></option>
      <?php endforeach; ?>
    </select>
    <label>Description</label>
    <textarea name="description" rows="6" required><?= e($description) ?></textarea>
    <button class="btn" type="submit">Submit Complaint</button>
  </form>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
