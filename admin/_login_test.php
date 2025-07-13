<?php

include_once('_generate_admin.php');

// Überprüfen, ob der Benutzer bereits eingeloggt ist
if (isset($_SESSION['user_id'])) {
    header('Location: _admin_dashboard.php'); // Wenn bereits eingeloggt, weiter zur Admin-Seite
    exit;
}

// Verbindungsaufbau zur Datenbank
$pdo = new PDO('mysql:host=localhost;dbname=mini_blog', 'root', '');

// CSRF Token für das Formular (Schutz vor Cross-Site Request Forgery)
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fehlermeldung für ungültige Anmeldedaten
$error_message = '';

// Formulardaten verarbeiten
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF-Token-Überprüfung
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF Token mismatch!');
    }

    // Eingabewerte aus dem Formular sicher verarbeiten (XSS-Schutz)
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // SQL-Abfrage, um den Benutzer aus der Datenbank abzurufen
    $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Überprüfen, ob Benutzer existiert und Passwort korrekt ist
    if ($user && password_verify($password, $user['password'])) {
        // Login erfolgreich, Benutzer ist Administrator
        if ($user['role'] === 'admin') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: _admin_dashboard.php');
            exit;
        } else {
            $error_message = "Du bist kein Administrator.";
        }
    } else {
        $error_message = "Falscher Benutzername oder Passwort.";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { width: 100%; max-width: 400px; margin: 50px auto; background-color: #fff; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-radius: 8px; }
        h1 { text-align: center; }
        label { display: block; margin: 10px 0 5px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #45a049; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>

<div class="container">
    <h1>Login</h1>
    <?php if ($error_message): ?>
        <p class="error"><?= $error_message ?></p>
    <?php endif; ?>
    <form action="_login_test.php" method="POST">
        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <label for="username">Benutzername:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Passwort:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
