<?php
// Verbindungsaufbau zur Datenbank
$pdo = new PDO('mysql:host=localhost;dbname=mini_blog', 'root', '');

// SQL-Abfrage, um alle Beiträge abzurufen
$query = "SELECT * FROM posts ORDER BY created_at DESC"; // Alle Posts abrufen, sortiert nach Erstellungsdatum
$stmt = $pdo->prepare($query);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog-Übersicht</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { width: 80%; margin: 0 auto; max-width: 1000px; }
        h1 { text-align: center; color: #333; }
        .posts-list { margin-top: 20px; padding: 10px; background-color: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .post-item { border-bottom: 1px solid #ddd; padding: 10px 0; }
        .post-item h4 { margin: 0; font-size: 1.2rem; }
        .post-item p { margin: 5px 0; }
        a { color: #007BFF; text-decoration: none; font-size: 1rem; }
        a:hover { color: #0056b3; }
    </style>
</head>
<body>

<div class="container">
    <h1>Blog-Übersicht</h1>

    <div class="posts-list">
        <?php foreach ($posts as $post): ?>
            <div class="post-item">
                <h4><?= htmlspecialchars($post['title']) ?></h4>
                <p><?= substr(htmlspecialchars($post['content']), 0, 150) ?>...</p>
                <a href="view.php?id=<?= $post['id'] ?>">Weiterlesen</a>
            </div>
        <?php endforeach; ?>
    </div>

</div>

</body>
</html>
