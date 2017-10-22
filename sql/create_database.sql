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

CREATE TABLE `posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL,
  `abstract` text COLLATE utf8_czech_ci NOT NULL,
  `file` mediumblob,
  `file_name` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `published` timestamp NULL DEFAULT NULL,
  `published_by_id` int(10) UNSIGNED DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Klíče pro tabulku `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_SLUG` (`slug`),
  ADD KEY `FK_POST_AUTHOR` (`author_id`),
  ADD KEY `FK_POST_PUBLISHER` (`published_by_id`);

--
-- AUTO_INCREMENT pro tabulku `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Omezení pro tabulku `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `FK_POST_AUTHOR` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `FK_POST_PUBLISHER` FOREIGN KEY (`published_by_id`) REFERENCES `users` (`id`);




--
-- Struktura tabulky `scores`
--

CREATE TABLE `scores` (
  `post_id` int(10) UNSIGNED NOT NULL,
  `reviewer_id` int(10) UNSIGNED NOT NULL,
  `rating_originality` tinyint(4) NOT NULL DEFAULT '0',
  `rating_language` tinyint(4) NOT NULL DEFAULT '0',
  `rating_quality` tinyint(4) NOT NULL DEFAULT '0',
  `score` decimal(10,0) NOT NULL DEFAULT '0',
  `note` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Klíče pro tabulku `scores`
--
ALTER TABLE `scores`
  ADD UNIQUE KEY `UK_RATING` (`post_id`,`reviewer_id`) USING BTREE,
  ADD KEY `IX_SCORE_REVIEWER` (`reviewer_id`) USING BTREE;

--
-- Omezení pro tabulku `scores`
--
ALTER TABLE `scores`
  ADD CONSTRAINT `FK_SCORE_POST` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  ADD CONSTRAINT `FK_SCORE_REVIEWER` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`);




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
