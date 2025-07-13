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

// Klasse für die Logik der Blogbeiträge
class Blog {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getPosts($searchQuery = "", $start = 0, $limit = 2) {
        $query = "SELECT posts.*, users.username AS author 
                  FROM posts 
                  INNER JOIN users ON posts.user_id = users.id 
                  WHERE posts.title LIKE :search OR posts.content LIKE :search 
                  ORDER BY posts.created_at DESC 
                  LIMIT :start, :limit";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':search', '%' . $searchQuery . '%', PDO::PARAM_STR);
        $stmt->bindParam(':start', $start, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalPosts($searchQuery = "") {
        $query = "SELECT COUNT(*) AS total 
                  FROM posts 
                  WHERE title LIKE :search OR content LIKE :search";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':search', '%' . $searchQuery . '%', PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getLatestPosts($limit = 5) {
        $query = "SELECT id, title FROM posts ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Hilfsklasse zur Behandlung von Pagination
class Pagination {
    private $totalItems;
    private $itemsPerPage;

    public function __construct($totalItems, $itemsPerPage) {
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
    }

    public function getTotalPages() {
        return ceil($this->totalItems / $this->itemsPerPage);
    }

    public function getCurrentPage() {
        return isset($_GET['page']) ? (int)$_GET['page'] : 1;
    }

    public function getStart() {
        return ($this->getCurrentPage() - 1) * $this->itemsPerPage;
    }
}

// Initialisierung
$db = new Database();
$pdo = $db->connect();
$blog = new Blog($pdo);

// Suchabfrage
$searchQuery = isset($_POST['search']) ? $_POST['search'] : "";

// Pagination konfigurieren
$totalPosts = $blog->getTotalPosts($searchQuery);
$pagination = new Pagination($totalPosts, 2);
$currentPage = $pagination->getCurrentPage();
$start = $pagination->getStart();

// Beiträge und aktuelle Beiträge abrufen
$paginatedPosts = $blog->getPosts($searchQuery, $start, 2);
$latestPosts = $blog->getLatestPosts();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Home</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <!-- Navigation -->
    <div class="navbar">
        <a href="index.php" class="text-brand">Mini-Blog</a>
        <a href="index.php" class="active">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="#" class="login">Login</a>
    </div>

    <!-- Hauptcontainer -->
    <div class="container">
        <!-- Hauptinhalt -->
        <div class="main-content">
            <h1>Blog Übersicht</h1>

            <!-- Beiträge anzeigen -->
            <?php foreach ($paginatedPosts as $post): ?>
                <div class="post">
                    <h2><a href="/test/Aufgabe_2/<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                    <p><strong>Autor:</strong> <?= htmlspecialchars($post['author']) ?></p>
                    <p><strong>Veröffentlicht am:</strong> <?= date('d.m.Y H:i', strtotime($post['created_at'])) ?></p>
                    <p><?= substr(htmlspecialchars($post['content']), 0, 100) ?>...</p>
                    <div class="post-footer">
                        <a href="view.php?id=<?= $post['id'] ?>">Weiterlesen</a>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Pagination -->
            <div class="pagination">
                <?php 
                    // Standardwert für Seite setzen, wenn nicht gesetzt
                    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    for ($page = 1; $page <= $pagination->getTotalPages(); $page++): 
                        // Prüfen, ob die aktuelle Seite gleich der aktuellen Seitenzahl ist
                        $isActive = ($page == $currentPage) ? 'active' : '';
                ?>
                <a href="index.php?page=<?= $page ?>&search=<?= urlencode($searchQuery) ?>" class="<?= $isActive ?>"><?= $page ?></a>
                    <?php endfor; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="box">
                <h4>Suche</h4>
                <form action="index.php" method="POST" class="search-box">
                    <input type="text" name="search" placeholder="Suche nach Beiträgen" value="<?= htmlspecialchars($searchQuery) ?>" />
                    <button type="submit">Suchen</button>
                </form>
            </div>
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
