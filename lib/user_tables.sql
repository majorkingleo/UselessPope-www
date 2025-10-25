-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+deb12u1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Erstellungszeit: 25. Okt 2025 um 09:54
-- Server-Version: 10.11.14-MariaDB-0+deb12u2
-- PHP-Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `nowa`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `USERPROPERTIES`
--

CREATE TABLE `USERPROPERTIES` (
  `idx` int(11) NOT NULL,
  `user_idx` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `USERRIGHTS`
--

CREATE TABLE `USERRIGHTS` (
  `idx` int(11) NOT NULL,
  `user_idx` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `USERS`
--

CREATE TABLE `USERS` (
  `idx` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `creation_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modification_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `locked` tinyint(1) NOT NULL,
  `failed_count` int(11) NOT NULL,
  `last_failed_login` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_login` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `USERPROPERTIES`
--
ALTER TABLE `USERPROPERTIES`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `user_idx` (`user_idx`);

--
-- Indizes für die Tabelle `USERRIGHTS`
--
ALTER TABLE `USERRIGHTS`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `user_idx` (`user_idx`);

--
-- Indizes für die Tabelle `USERS`
--
ALTER TABLE `USERS`
  ADD PRIMARY KEY (`idx`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `USERPROPERTIES`
--
ALTER TABLE `USERPROPERTIES`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `USERRIGHTS`
--
ALTER TABLE `USERRIGHTS`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `USERS`
--
ALTER TABLE `USERS`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `USERPROPERTIES`
--
ALTER TABLE `USERPROPERTIES`
  ADD CONSTRAINT `USERPROPERTIES_ibfk_1` FOREIGN KEY (`user_idx`) REFERENCES `USERS` (`idx`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `USERRIGHTS`
--
ALTER TABLE `USERRIGHTS`
  ADD CONSTRAINT `USERRIGHTS_ibfk_1` FOREIGN KEY (`user_idx`) REFERENCES `USERS` (`idx`) ON DELETE CASCADE,
  ADD CONSTRAINT `USERRIGHTS_ibfk_2` FOREIGN KEY (`user_idx`) REFERENCES `USERS` (`idx`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
