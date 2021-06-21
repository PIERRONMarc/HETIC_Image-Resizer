CREATE DATABASE IF NOT EXISTS `resizer` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `resizer`;

DROP TABLE IF EXISTS `Avatar`;
CREATE TABLE `Avatar` (
  `Id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Name` varchar(50) NOT NULL,
  `Path` varchar(100) NOT NULL,
  `Created_at` date NOT NULL,
  `Updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


DROP TABLE IF EXISTS `User`;
CREATE TABLE `User` (
  `Id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Name` varchar(50) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Created_at` date NOT NULL,
  `Updated_at` date NOT NULL,
  `IdAvatar` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

ALTER TABLE `User`
  ADD CONSTRAINT `fk_id_avatar` FOREIGN KEY (`IdAvatar`) REFERENCES `Avatar` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;
