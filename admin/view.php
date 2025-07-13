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
    <title>Beitrag ansehen</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { width: 80%; margin: 0 auto; max-width: 1000px; }
        h1 { text-align: center; color: #333; }
        .post-container { padding: 20px; background-color: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: 20px auto; max-width: 800px; }
        h2 { color: #333; }
        p { font-size: 1rem; color: #666; line-height: 1.5; }
        .action-buttons { margin-top: 20px; }
        button { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        button:hover { background-color: #45a049; }
        a { color: #007BFF; text-decoration: none; padding: 5px 10px; border: 1px solid #007BFF; border-radius: 4px; }
        a:hover { background-color: #007BFF; color: white; }
    </style>
</head>
<body>

<div class="container">
    <h1>Beitrag ansehen</h1>

    <!-- Beitrag anzeigen -->
    <div class="post-container">
        <h2><?= htmlspecialchars($post['title']) ?></h2>
        <p><strong>Erstellt am:</strong> <?= $post['created_at'] ?></p>
        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>

        <div class="action-buttons">
            <a href="edit.php?id=<?= $post['id'] ?>">Bearbeiten</a>
            <form method="POST" action="index.php" style="display:inline;">
                <input type="hidden" name="id" value="<?= $post['id'] ?>">
                <button type="submit" name="delete">Löschen</button>
            </form>
        </div>
    </div>

    <div class="action-buttons">
        <a href="index.php">Zurück zur Übersicht</a>
    </div>
</div>

</body>
</html>
