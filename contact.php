<?php

session_start();

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

// Klasse für das Kontaktformular
class ContactForm {
    private $pdo;
    private $errors = [];

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function generateCaptcha() {
        // Zufällige Zahlen für das mathematische Captcha generieren
        if (!isset($_SESSION['captcha'])) {
            $_SESSION['captcha'] = [
                'num1' => rand(1, 10),
                'num2' => rand(1, 10)
            ];
        }
    }

    public function validateForm($data) {
        $name = htmlspecialchars($data['name']);
        $email = htmlspecialchars($data['email']);
        $subject = htmlspecialchars($data['subject']);
        $message = htmlspecialchars($data['message']);
        $captcha_result = (int)$data['captcha_result'];

        // Validierung
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            $this->errors[] = 'Alle Felder müssen ausgefüllt werden.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'Die E-Mail-Adresse ist ungültig.';
        }
        if ($captcha_result !== ($_SESSION['captcha']['num1'] + $_SESSION['captcha']['num2'])) {
            $this->errors[] = 'Das Captcha ist falsch.';
        }
        return $this->errors;
    }

    public function saveMessage($data) {
        $name = htmlspecialchars($data['name']);
        $email = htmlspecialchars($data['email']);
        $subject = htmlspecialchars($data['subject']);
        $message = htmlspecialchars($data['message']);

        // Nachricht in der Datenbank speichern
        $stmt = $this->pdo->prepare("
            INSERT INTO contact_messages (name, email, subject, message)
            VALUES (:name, :email, :subject, :message)
        ");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':subject' => $subject,
            ':message' => $message
        ]);
    }
}

// Initialisierung der Datenbankverbindung
$db = new Database();
$pdo = $db->connect();

// Initialisierung der Kontaktformular-Klasse
$contactForm = new ContactForm($pdo);

// Captcha generieren
$contactForm->generateCaptcha();

// Formularverarbeitung
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Formularvalidierung
    $errors = $contactForm->validateForm($_POST);

    // Wenn keine Fehler, Nachricht speichern
    if (empty($errors)) {
        $contactForm->saveMessage($_POST);
        $_SESSION['success'] = 'Ihre Nachricht wurde erfolgreich gesendet!';
        unset($_SESSION['captcha']); // Captcha zurücksetzen
        header('Location: contact.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Kontakt</title>
    <link rel="stylesheet" href="css/index.css">
    <style>
        /* Spezifische Styles für das Kontaktformular */
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group textarea {
            resize: vertical;
            height: 100px;
        }
        .btn {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <div class="navbar">
        <a href="index.php" class="text-brand">Mini-Blog</a>
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php" class="active">Contact</a>
        <a href="#" class="login">Login</a>
    </div>

    <!-- Kontaktformular -->
    <div class="container">
        <div class="main-content">
            <h1>Kontakt</h1>
            <div class="post">
                <?php if (!empty($errors)): ?>
                    <div class="error">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($_SESSION['success'])): ?>
                    <div class="success">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <form action="contact.php" method="POST">
                    <p>Wir freuen uns, von Ihnen zu hören! Haben Sie Fragen, Anregungen oder Feedback? Nutzen Sie einfach das untenstehende Formular, um mit uns in Kontakt zu treten. Wir melden uns so schnell wie möglich bei Ihnen!</p>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">E-Mail</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Betreff</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Nachricht</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="captcha">Was ergibt <?= $_SESSION['captcha']['num1'] ?> + <?= $_SESSION['captcha']['num2'] ?>?</label>
                        <input type="number" id="captcha" name="captcha_result" required>
                    </div>
                    <button type="submit" class="btn">Nachricht senden</button>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="box">
                <h4>Weitere Kontakt Wege</h4>
                <ul>
                    <li><strong>Post</strong>: <br>Marcus Dziersan<br> Ferdinandstraße 2 in 44536 Lünen</li>
                    <li><strong>E-Mail</strong>: <br>marcus.dziersan@vodafone.de</li>
                    <li><strong>Telefon</strong>: <br>Mo - Fr von 08:00 bis 16:00 Uhr <br>0162 / 8372760</li>
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
