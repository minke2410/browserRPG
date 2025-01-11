-- Adminer 4.8.1 MySQL 8.0.40-0ubuntu0.24.04.1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `characters`;
CREATE TABLE `characters` (
  `id` int NOT NULL AUTO_INCREMENT,
  `character_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `level` int DEFAULT '1',
  `hp` int DEFAULT '100',
  `xp` int DEFAULT '0',
  `attack` int DEFAULT '5',
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `character_id` (`character_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `characters` (`id`, `character_id`, `name`, `level`, `hp`, `xp`, `attack`, `user_id`) VALUES
(7,	0,	'主人公',	1,	100,	0,	5,	0),
(8,	1,	'戦士',	1,	100,	0,	5,	0),
(9,	2,	'弓使い',	1,	80,	0,	4,	0),
(10,	3,	'魔道士',	1,	60,	0,	3,	0),
(11,	4,	'僧侶',	1,	70,	0,	3,	0);

DROP TABLE IF EXISTS `characters_inventory`;
CREATE TABLE `characters_inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `character_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `level` int DEFAULT '1',
  `hp` int DEFAULT '100',
  `xp` int DEFAULT '0',
  `attack` int DEFAULT '5',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_character_inventory_user` (`user_id`),
  KEY `fk_character_inventory_character` (`character_id`),
  CONSTRAINT `fk_character_inventory_character` FOREIGN KEY (`character_id`) REFERENCES `characters` (`character_id`),
  CONSTRAINT `fk_character_inventory_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `characters_inventory` (`id`, `user_id`, `character_id`, `name`, `level`, `hp`, `xp`, `attack`, `updated_at`) VALUES
(1,	3,	0,	'みんけ',	1,	100,	0,	5,	'2025-01-10 06:01:24'),
(2,	3,	0,	'主人公',	1,	100,	0,	5,	'2025-01-11 10:32:18'),
(3,	3,	1,	'戦士',	1,	100,	0,	5,	'2025-01-11 10:32:18'),
(4,	3,	2,	'弓使い',	1,	80,	0,	4,	'2025-01-11 10:32:18'),
(5,	3,	3,	'魔道士',	1,	60,	0,	3,	'2025-01-11 10:32:18'),
(6,	3,	4,	'僧侶',	1,	70,	0,	3,	'2025-01-11 10:32:18');

DROP TABLE IF EXISTS `enemies`;
CREATE TABLE `enemies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `level` int DEFAULT '1',
  `hp` int DEFAULT '100',
  `attack` int DEFAULT '10',
  `defense` int DEFAULT '5',
  `xp_reward` int DEFAULT '50',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `inventory`;
CREATE TABLE `inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `item_id` int DEFAULT NULL,
  `quantity` int DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `item_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `item_text` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `item_effect` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `items` (`id`, `item_name`, `item_text`, `item_effect`) VALUES
(1,	'回復薬',	'HPを50回復する',	50);

DROP TABLE IF EXISTS `items_inventory`;
CREATE TABLE `items_inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `item_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_inventory_user` (`user_id`),
  KEY `fk_inventory_item` (`item_id`),
  CONSTRAINT `fk_inventory_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `fk_inventory_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `items_inventory` (`id`, `user_id`, `item_id`, `quantity`) VALUES
(1,	3,	1,	1);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `gold` int NOT NULL DEFAULT '0',
  `last_save` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `username`, `password`, `gold`, `last_save`) VALUES
(3,	'minke',	'$2y$10$eXBnh9rnZuvnB/u/fmIYLO5fgrSAkmJ5.PcO2Z3dvY2y0zBYP4Lh2',	0,	'2025-01-09 05:27:02');

-- 2025-01-11 11:00:47
