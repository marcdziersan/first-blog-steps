-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Erstellungszeit: 02. Dez 2024 um 05:03
-- Server-Version: 9.1.0
-- PHP-Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `mini_blog`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pages`
--

DROP TABLE IF EXISTS `pages`;
CREATE TABLE IF NOT EXISTS `pages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

--
-- Daten für Tabelle `pages`
--

INSERT INTO `pages` (`id`, `title`, `content`) VALUES
(1, 'Über uns', 'Willkommen bei Mini-Blog!\r\n\r\nMini-Blog ist dein Programmier-Lernprojekt, in dem wir Schritt für Schritt gemeinsam einen Blog erstellen. Von den ersten Zeilen Code bis hin zu einer funktionierenden Plattform – hier lernst du nicht nur die Grundlagen des Webentwickelns, sondern auch, wie du eigene Projekte umsetzen kannst.\r\n\r\nBegleite uns auf dieser spannenden Reise und entdecke die Welt der Programmierung. Egal, ob du gerade erst anfängst oder bereits Erfahrung hast – Mini-Blog ist für alle, die neugierig sind und dazulernen möchten.\r\n\r\nLasst uns zusammen lernen, teilen und wachsen!');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;

--
-- Daten für Tabelle `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `created_at`, `user_id`) VALUES
(1, 'Herzlich Willkommen', 'Erster Eintrag', '2024-11-30 23:48:29', 1),
(2, 'Erster Beitrag', 'Dies ist der Inhalt des ersten Beitrags.', '2024-12-01 00:45:33', 1),
(3, 'Zweiter Beitrag', 'Hier ist der Inhalt des zweiten Beitrags.', '2024-12-01 00:45:33', 1),
(4, 'Dritter Beitrag', 'Inhalt des dritten Beitrags geht hier.', '2024-12-01 00:45:33', 1),
(5, 'Vierter Beitrag', 'Der vierte Beitrag erklärt viele Dinge.', '2024-12-01 00:45:33', 1),
(6, 'Fünfter Beitrag', 'Dies ist der Inhalt des fünften Beitrags.', '2024-12-01 00:45:33', 1),
(7, 'Sechster Beitrag', 'Der sechste Beitrag ist hier.', '2024-12-01 00:45:33', 1),
(8, 'Siebter Beitrag', 'Dies ist der Inhalt des siebten Beitrags.', '2024-12-01 00:45:33', 1),
(9, 'Achter Beitrag', 'Der Inhalt des achten Beitrags.', '2024-12-01 00:45:33', 1),
(10, 'Neunter Beitrag', 'Neunter Beitrag, weitere Informationen hier.', '2024-12-01 00:45:33', 1),
(11, 'Zehnter Beitrag', 'Der zehnte Beitrag ist hier zu finden.', '2024-12-01 00:45:33', 1),
(12, 'Dieter', 'das war dieter...', '2024-12-01 21:49:47', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$3rp31arAdbd4z.L/foHkWeCsCqBvQRX0PIB4M98XkK6sZptocGptG', 'admin', '2024-12-01 00:13:14');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
