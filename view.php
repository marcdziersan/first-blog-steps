<?php

// Klasse für die Datenbankverbindung
class Database {
    private $host = 'localhost';
    private $dbname = 'mini_blog';
    private $username = 'root';
    private $password = '';
    private $pdo;

    public function connect() {
        if ($this->pdo === null) {
            $this->pdo = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $this->pdo;
    }
}

// Klasse für Beiträge
class Blog {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getPostById($id) {
        $query = "
            SELECT posts.*, users.username AS author
            FROM posts
            INNER JOIN users ON posts.user_id = users.id
            WHERE posts.id = :postId
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':postId', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLatestPosts($limit = 5) {
        $query = "SELECT id, title FROM posts ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Initialisierung
$db = new Database();
$pdo = $db->connect();
$blog = new Blog($pdo);

// Beitrag anhand der ID abrufen
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = $blog->getPostById($postId);

// Falls kein Beitrag gefunden wurde
if (!$post) {
    echo "Beitrag nicht gefunden!";
    exit;
}
// aktuelle Beiträge abrufen
$latestPosts = $blog->getLatestPosts();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - <?= htmlspecialchars($post['title']) ?></title>
    <link rel="stylesheet" href="/test/Aufgabe_2/css/index.css">
</head>
<body>
    <!-- Navigation -->
    <div class="navbar">
        <a href="index.php" class="text-brand">Mini-Blog</a>
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="#" class="login">Login</a>
    </div>

    <!-- Hauptcontainer -->
    <div class="container">
        <!-- Hauptinhalt -->
        <div class="main-content">
            <h1><?= htmlspecialchars($post['title']) ?></h1>
            <div class="post">
                <p><strong>Autor:</strong> <?= htmlspecialchars($post['author']) ?></p>
                <p><strong>Veröffentlicht am:</strong> <?= date('d.m.Y H:i', strtotime($post['created_at'])) ?></p>
                <div><?= nl2br(htmlspecialchars($post['content'])) ?></div>

                <div class="post-footer">
                    <a href="index.php">Zurück zur Übersicht</a>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="box">
                <h4>Aktuelles</h4>
                <ul>
                    <?php foreach ($latestPosts as $post): ?>
                        <li><a href="view.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-left">
                <p>&copy; <?= date('Y') ?> Mini-Blog. Alle Rechte vorbehalten.</p>
            </div>
            <div class="footer-right">
                <a href="about.php">Über uns</a>
                <a href="contact.php">Kontakt</a>
                <a href="index.php">Home</a>
            </div>
        </div>
    </footer>
</body>
</html>
