<?php
class Post
{
    private $pdo;

    public function __construct($host, $db, $user, $pass)
    {
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
        }
    }

    // Beitrag erstellen
    public function create($title, $content)
    {
        $stmt = $this->pdo->prepare("INSERT INTO posts (title, content) VALUES (:title, :content)");
        return $stmt->execute(['title' => $title, 'content' => $content]);
    }

    // Alle Beiträge lesen
    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Einzelnen Beitrag lesen
    public function get($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Beitrag aktualisieren
    public function update($id, $title, $content)
    {
        $stmt = $this->pdo->prepare("UPDATE posts SET title = :title, content = :content WHERE id = :id");
        return $stmt->execute(['id' => $id, 'title' => $title, 'content' => $content]);
    }

    // Beitrag löschen
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
?>
