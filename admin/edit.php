<?php
require 'Post.php';

$blog = new Post('localhost', 'mini_blog', 'root', '');

$id = $_GET['id'] ?? null;
$post = $blog->get($id);

if (!$post) {
    die("Beitrag nicht gefunden.");
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Beitrag bearbeiten</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { width: 80%; margin: 0 auto; max-width: 1000px; }
        h1 { text-align: center; color: #333; }
        .form-container { padding: 20px; background-color: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); max-width: 600px; margin: 20px auto; }
        input[type="text"], textarea { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; }
        button { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        button:hover { background-color: #45a049; }
        a { color: #007BFF; text-decoration: none; padding: 5px 10px; border: 1px solid #007BFF; border-radius: 4px; }
        a:hover { background-color: #007BFF; color: white; }
    </style>
</head>
<body>

<div class="container">
    <h1>Beitrag bearbeiten</h1>

    <!-- Formular zum Bearbeiten des Beitrags -->
    <div class="form-container">
        <form method="POST" action="index.php">
            <input type="hidden" name="id" value="<?= $post['id'] ?>">
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
            <textarea name="content" required><?= htmlspecialchars($post['content']) ?></textarea>
            <button type="submit" name="update">Speichern</button>
        </form>
    </div>

    <a href="index.php">Abbrechen</a>
</div>

</body>
</html>
