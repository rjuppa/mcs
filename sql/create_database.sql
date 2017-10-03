-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Počítač: localhost
-- Vytvořeno: Úte 03. říj 2017, 01:10
-- Verze serveru: 10.1.26-MariaDB
-- Verze PHP: 7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `mcs`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `password_hash` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `is_active` bit(1) NOT NULL DEFAULT b'0',
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `deleted` bit(1) NOT NULL DEFAULT b'0',
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password_hash`, `is_active`, `type`, `deleted`, `created`) VALUES
(3, 'Jan', 'Admin', 'admin@email.cz', NULL, b'0', 1, b'0', '2017-10-02 21:32:42'),
(10, 'Petr', 'Builder', 'pb@email.cz', NULL, b'0', 1, b'0', '2017-10-02 21:52:54'),
(11, 'Oma', 'Ole', 'pb2@email.cz', NULL, b'0', 1, b'0', '2017-10-02 21:55:30'),
(12, 'Martin', 'Duna', 'mmmm@email.cz', NULL, b'0', 1, b'0', '2017-10-02 21:56:22'),
(13, 'David', 'Bowie', 'david@google.cz', NULL, b'0', 1, b'0', '2017-10-02 21:59:36'),
(14, 'Josef', 'Dvorak', 'pepa@dvorak.cz', NULL, b'0', 1, b'0', '2017-10-02 22:11:18');

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_email` (`email`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
