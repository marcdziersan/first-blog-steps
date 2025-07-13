<?php
session_start();

// Überprüfen, ob der Benutzer eingeloggt ist und ob der Benutzer ein Administrator ist
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); // Falls der Benutzer nicht eingeloggt ist oder kein Admin ist, wird er zur öffentlichen Seite umgeleitet.
    exit;
}

// Wenn der Benutzer auf Logout klickt, löschen wir die Session
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php'); // Nach dem Logout zur öffentlichen Seite umleiten
    exit;
}

// Verbindungsaufbau zur Datenbank
$pdo = new PDO('mysql:host=localhost;dbname=mini_blog', 'root', '');

// SQL-Abfragen, um die Anzahl der Benutzer und Beiträge zu erhalten
$userCountQuery = "SELECT COUNT(*) FROM users";
$postCountQuery = "SELECT COUNT(*) FROM posts";
$postsQuery = "SELECT * FROM posts ORDER BY created_at DESC"; // Alle Posts abrufen, sortiert nach Erstellungsdatum

// Benutzer- und Post-Anzahl abfragen
$userCountStmt = $pdo->prepare($userCountQuery);
$userCountStmt->execute();
$userCount = $userCountStmt->fetchColumn();

$postCountStmt = $pdo->prepare($postCountQuery);
$postCountStmt->execute();
$postCount = $postCountStmt->fetchColumn();

// Alle Posts abrufen
$postsStmt = $pdo->prepare($postsQuery);
$postsStmt->execute();
$posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { width: 80%; margin: 0 auto; max-width: 1000px; }
        h1 { text-align: center; color: #333; }
        .welcome { padding: 20px; background-color: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: 20px auto; max-width: 800px; text-align: center; }
        .logout-button { background-color: #f44336; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        .logout-button:hover { background-color: #e53935; }
        .admin-actions { text-align: center; margin-top: 30px; }
        a { color: #007BFF; text-decoration: none; font-size: 1rem; }
        a:hover { color: #0056b3; }
        .statistics { margin-top: 20px; padding: 10px; background-color: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; }
        .posts-list { margin-top: 20px; padding: 10px; background-color: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .post-item { border-bottom: 1px solid #ddd; padding: 10px 0; }
        .post-item h4 { margin: 0; font-size: 1.2rem; }
        .post-item p { margin: 5px 0; }
    </style>
</head>
<body>

<div class="container">
    <h1>Willkommen im Admin Dashboard</h1>

    <div class="welcome">
        <h2>Hallo, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
        <p>Du bist als Administrator eingeloggt.</p>
        <div class="admin-actions">
            <a href="index.php">Zur öffentlichen Blog-Übersicht</a>
        </div>
    </div>

    <!-- Anzeige der Statistiken -->
    <div class="statistics">
        <h3>Systemstatistiken</h3>
        <p>Benutzeranzahl: <?= $userCount ?></p>
        <p>Beitragsanzahl: <?= $postCount ?></p>
    </div>

    <!-- Anzeige der Blog-Beiträge -->
    <div class="posts-list">
        <h3>Alle Beiträge:</h3>
        <?php foreach ($posts as $post): ?>
            <div class="post-item">
                <h4><?= htmlspecialchars($post['title']) ?></h4>
                <p><?= substr(htmlspecialchars($post['content']), 0, 150) ?>...</p>
                <a href="view.php?id=<?= $post['id'] ?>">Weiterlesen</a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="admin-actions">
        <a href="?logout=true" class="logout-button">Logout</a>
    </div>
</div>

</body>
</html>
