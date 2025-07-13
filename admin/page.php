<?php
// Verbindung zur Datenbank herstellen
$pdo = new PDO('mysql:host=localhost;dbname=mini_blog', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Standardwerte für Aktionen und Nachrichten
$action = $_GET['action'] ?? 'list';
$message = "";

// CREATE
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);

    if (!empty($title) && !empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO pages (title, content) VALUES (:title, :content)");
        $stmt->execute([':title' => $title, ':content' => $content]);
        $message = "Seite erfolgreich erstellt!";
        $action = 'list';
    } else {
        $message = "Titel und Inhalt dürfen nicht leer sein.";
    }
}

// UPDATE
if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);

    if (!empty($title) && !empty($content)) {
        $stmt = $pdo->prepare("UPDATE pages SET title = :title, content = :content WHERE id = :id");
        $stmt->execute([':title' => $title, ':content' => $content, ':id' => $id]);
        $message = "Seite erfolgreich aktualisiert!";
        $action = 'list';
    } else {
        $message = "Titel und Inhalt dürfen nicht leer sein.";
    }
}

// DELETE
if ($action === 'delete') {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM pages WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $message = "Seite erfolgreich gelöscht!";
    $action = 'list';
}

// Daten für die Bearbeitung oder Liste abrufen
if ($action === 'edit') {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $page = $stmt->fetch(PDO::FETCH_ASSOC);
} elseif ($action === 'list') {
    $stmt = $pdo->query("SELECT * FROM pages");
    $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pages CRUD</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table th { background-color: #f4f4f4; }
        .message { color: green; margin-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group textarea { width: 100%; padding: 8px; }
        .form-group textarea { height: 100px; }
        .actions a { margin-right: 10px; }
    </style>
</head>
<body>
    <h1>Pages CRUD Verwaltung</h1>

    <?php if (!empty($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($action === 'list'): ?>
        <a href="?action=create">Neue Seite erstellen</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titel</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pages as $page): ?>
                    <tr>
                        <td><?= htmlspecialchars($page['id']) ?></td>
                        <td><?= htmlspecialchars($page['title']) ?></td>
                        <td class="actions">
                            <a href="?action=edit&id=<?= $page['id'] ?>">Bearbeiten</a>
                            <a href="?action=delete&id=<?= $page['id'] ?>" onclick="return confirm('Möchten Sie diese Seite wirklich löschen?')">Löschen</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($action === 'create'): ?>
        <h2>Neue Seite erstellen</h2>
        <form method="POST" action="?action=create">
            <div class="form-group">
                <label for="title">Titel</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="content">Inhalt</label>
                <textarea id="content" name="content" required></textarea>
            </div>
            <button type="submit">Speichern</button>
        </form>
    <?php elseif ($action === 'edit'): ?>
        <h2>Seite bearbeiten</h2>
        <form method="POST" action="?action=edit">
            <input type="hidden" name="id" value="<?= $page['id'] ?>">
            <div class="form-group">
                <label for="title">Titel</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($page['title']) ?>" required>
            </div>
            <div class="form-group">
                <label for="content">Inhalt</label>
                <textarea id="content" name="content" required><?= htmlspecialchars($page['content']) ?></textarea>
            </div>
            <button type="submit">Speichern</button>
        </form>
    <?php endif; ?>
</body>
</html>
