-- Adminer 4.8.1 MySQL 10.4.32-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `characters`;
CREATE TABLE `characters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `character_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `level` int(11) DEFAULT 1,
  `hp` int(11) DEFAULT 100,
  `xp` int(11) DEFAULT 0,
  `attack` int(11) DEFAULT 5,
  `user_id` int(11) NOT NULL,
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `character_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `level` int(11) DEFAULT 1,
  `hp` int(11) DEFAULT 100,
  `xp` int(11) DEFAULT 0,
  `attack` int(11) DEFAULT 5,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_character_inventory_user` (`user_id`),
  KEY `fk_character_inventory_character` (`character_id`),
  CONSTRAINT `fk_character_inventory_character` FOREIGN KEY (`character_id`) REFERENCES `characters` (`character_id`),
  CONSTRAINT `fk_character_inventory_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `characters_inventory` (`id`, `user_id`, `character_id`, `name`, `level`, `hp`, `xp`, `attack`, `updated_at`) VALUES
(1,	3,	0,	'みんけ',	1,	100,	0,	5,	'2025-01-10 06:01:24'),
(3,	3,	1,	'戦士',	1,	100,	0,	5,	'2025-01-09 21:01:24'),
(4,	3,	2,	'弓使い',	1,	80,	0,	4,	'2025-01-09 21:01:24'),
(5,	3,	3,	'魔道士',	1,	60,	0,	3,	'2025-01-09 21:01:24'),
(6,	3,	4,	'僧侶',	1,	70,	0,	3,	'2025-01-09 21:01:24');

DROP TABLE IF EXISTS `dungeons`;
CREATE TABLE `dungeons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `required_level` int(11) NOT NULL,
  `floor` int(11) NOT NULL,
  `max_enemies` int(11) DEFAULT NULL,
  `reward_xp` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `dungeons` (`id`, `name`, `required_level`, `floor`, `max_enemies`, `reward_xp`, `created_at`, `updated_at`) VALUES
(1,	'森の迷宮',	1,	5,	3,	100,	'2025-01-19 21:14:29',	'2025-01-19 21:38:39'),
(2,	'火山の洞窟',	2,	5,	3,	100,	'2025-01-19 21:14:29',	'2025-01-19 21:38:39'),
(3,	'氷の城',	3,	5,	3,	100,	'2025-01-19 21:14:29',	'2025-01-19 21:38:39');

DROP TABLE IF EXISTS `dungeon_enemies`;
CREATE TABLE `dungeon_enemies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dungeon_id` int(11) NOT NULL,
  `enemy_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `dungeon_id` (`dungeon_id`),
  KEY `enemy_id` (`enemy_id`),
  CONSTRAINT `dungeon_enemies_ibfk_1` FOREIGN KEY (`dungeon_id`) REFERENCES `dungeons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dungeon_enemies_ibfk_2` FOREIGN KEY (`enemy_id`) REFERENCES `enemies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `dungeon_enemies` (`id`, `dungeon_id`, `enemy_id`, `created_at`, `updated_at`) VALUES
(1,	1,	1,	'2025-01-19 21:42:05',	'2025-01-19 21:42:05'),
(2,	1,	2,	'2025-01-19 21:42:05',	'2025-01-19 21:42:05'),
(3,	2,	2,	'2025-01-19 21:42:05',	'2025-01-19 21:42:05'),
(4,	2,	3,	'2025-01-19 21:42:05',	'2025-01-19 21:42:05'),
(5,	3,	1,	'2025-01-19 21:42:05',	'2025-01-19 21:42:05'),
(6,	3,	3,	'2025-01-19 21:42:05',	'2025-01-19 21:42:05');

DROP TABLE IF EXISTS `enemies`;
CREATE TABLE `enemies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enemy_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `hp` int(11) NOT NULL,
  `attack` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `enemy_id` (`enemy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `enemies` (`id`, `enemy_id`, `name`, `hp`, `attack`) VALUES
(1,	1,	'スライム',	50,	5),
(2,	2,	'ゴブリン',	60,	6),
(3,	3,	'オーク',	70,	8);

DROP TABLE IF EXISTS `inventory`;
CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(255) NOT NULL,
  `item_text` varchar(255) NOT NULL,
  `item_effect` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `items` (`id`, `item_name`, `item_text`, `item_effect`) VALUES
(1,	'回復薬',	'HPを50回復する',	50);

DROP TABLE IF EXISTS `items_inventory`;
CREATE TABLE `items_inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_inventory_user` (`user_id`),
  KEY `fk_inventory_item` (`item_id`),
  CONSTRAINT `fk_inventory_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `fk_inventory_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `items_inventory` (`id`, `user_id`, `item_id`, `quantity`) VALUES
(1,	3,	1,	1);

DROP TABLE IF EXISTS `parties`;
CREATE TABLE `parties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `party_name` varchar(255) DEFAULT NULL,
  `member1` int(11) DEFAULT NULL,
  `member2` int(11) DEFAULT NULL,
  `member3` int(11) DEFAULT NULL,
  `member4` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `member1` (`member1`),
  KEY `member2` (`member2`),
  KEY `member3` (`member3`),
  KEY `member4` (`member4`),
  CONSTRAINT `parties_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `parties_ibfk_6` FOREIGN KEY (`member1`) REFERENCES `characters_inventory` (`id`) ON DELETE SET NULL,
  CONSTRAINT `parties_ibfk_7` FOREIGN KEY (`member2`) REFERENCES `characters_inventory` (`id`) ON DELETE SET NULL,
  CONSTRAINT `parties_ibfk_8` FOREIGN KEY (`member3`) REFERENCES `characters_inventory` (`id`) ON DELETE SET NULL,
  CONSTRAINT `parties_ibfk_9` FOREIGN KEY (`member4`) REFERENCES `characters_inventory` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `parties` (`id`, `user_id`, `party_name`, `member1`, `member2`, `member3`, `member4`, `is_active`, `uploaded_at`) VALUES
(1,	3,	'お試し',	1,	3,	4,	5,	1,	'2025-01-15 00:31:01'),
(3,	3,	'新しいパーティー',	1,	3,	4,	6,	0,	'2025-01-15 06:45:39'),
(4,	3,	'編成1',	1,	4,	5,	6,	0,	'2025-01-21 05:32:34'),
(5,	3,	'編成2',	3,	4,	5,	6,	0,	'2025-01-21 05:34:11');

DROP TABLE IF EXISTS `progress_dungeons`;
CREATE TABLE `progress_dungeons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `dungeon_id` int(11) NOT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `current_floor` int(11) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `dungeon_id` (`dungeon_id`),
  CONSTRAINT `progress_dungeons_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `progress_dungeons_ibfk_2` FOREIGN KEY (`dungeon_id`) REFERENCES `dungeons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `progress_dungeons` (`id`, `user_id`, `dungeon_id`, `is_completed`, `is_active`, `current_floor`, `created_at`, `updated_at`) VALUES
(1,	3,	1,	0,	1,	1,	'2025-01-19 21:59:58',	'2025-01-19 21:59:58'),
(2,	3,	2,	0,	1,	1,	'2025-01-19 21:59:58',	'2025-01-20 10:33:30'),
(3,	3,	3,	0,	0,	1,	'2025-01-19 21:59:58',	'2025-01-19 21:59:58');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gold` int(11) NOT NULL DEFAULT 0,
  `level` int(11) NOT NULL DEFAULT 1,
  `xp` int(11) NOT NULL DEFAULT 0,
  `last_save` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `username`, `password`, `gold`, `level`, `xp`, `last_save`) VALUES
(3,	'minke',	'$2y$10$eXBnh9rnZuvnB/u/fmIYLO5fgrSAkmJ5.PcO2Z3dvY2y0zBYP4Lh2',	0,	1,	0,	'2025-01-09 05:27:02');

-- 2025-01-21 07:30:13
