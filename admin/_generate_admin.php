<?php
// Start der Session
session_start();

// Verbindungsaufbau zur Datenbank
$pdo = new PDO('mysql:host=localhost;dbname=mini_blog', 'root', '');

// Prüfen, ob der Administrator bereits existiert
$query = "SELECT * FROM users WHERE username = :username LIMIT 1";
$stmt = $pdo->prepare($query);
$username = 'admin'; // Hier definieren wir eine Variable für den Admin-Namen
$stmt->bindParam(':username', $username);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Admin existiert noch nicht, also erstellen wir ihn

    // Passwort für den Admin (mit password_hash sichern)
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $role = 'admin'; // Definiere die Rolle als 'admin'

    // Einfügen des Admin-Benutzers in die Tabelle
    $insertQuery = "INSERT INTO users (username, password, role, created_at) VALUES (:username, :password, :role, NOW())";
    $insertStmt = $pdo->prepare($insertQuery);

    // Hier verwenden wir die Variablen, die an bindParam gebunden werden
    $insertStmt->bindParam(':username', $username);
    $insertStmt->bindParam(':password', $password);
    $insertStmt->bindParam(':role', $role);

    $insertStmt->execute();

    echo "Admin wurde erfolgreich hinzugefügt.";
} else {
    // Admin existiert bereits
    echo "Der Administrator existiert bereits in der Datenbank.";
}
?>
