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

// Datenbankverbindung herstellen
$db = new Database();
$pdo = $db->connect();

// Abfrage für die "Über uns"-Seite
$query = "SELECT title, content FROM pages WHERE title = :title LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute([':title' => 'Über uns']);
$page = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - <?= htmlspecialchars($page['title']) ?></title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <!-- Navigation -->
    <div class="navbar">
        <a href="index.php" class="text-brand">Mini-Blog</a>
        <a href="index.php">Home</a>
        <a href="about.php" class="active">About</a>
        <a href="contact.php">Contact</a>
        <a href="#" class="login">Login</a>
    </div>

    <!-- Hauptcontainer -->
    <div class="container">
        <div class="main-content">
            <h1><?= htmlspecialchars($page['title']) ?></h1>
            <div class="post">
                <p>
                    <?= nl2br(htmlspecialchars($page['content'])) ?>
                </p>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="box">
                <h4>Unser Team</h4>
                <ul>
                    <li><strong>Marcus Dziersan</strong>: Gründer und Chefentwickler</li>
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
