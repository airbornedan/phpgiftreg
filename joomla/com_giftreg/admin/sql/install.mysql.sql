CREATE TABLE IF NOT EXISTS `#__giftreg_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__giftreg_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` decimal(9,2) DEFAULT NULL,
  `source` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(1024) NOT NULL DEFAULT '',
  `ranking` tinyint NOT NULL DEFAULT 3,
  `category_id` int DEFAULT NULL,
  `comment` text,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- One shopper per item (every item has quantity 1). bought = 0 reserved, 1 bought.
CREATE TABLE IF NOT EXISTS `#__giftreg_allocs` (
  `item_id` int NOT NULL,
  `user_id` int NOT NULL,
  `bought` tinyint NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__giftreg_events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `description` varchar(100) NOT NULL,
  `eventdate` date NOT NULL,
  `recurring` tinyint NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- In-app notices (the server sends no mail), e.g. "an item you reserved was deleted".
CREATE TABLE IF NOT EXISTS `#__giftreg_notices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `message` varchar(500) NOT NULL,
  `created` datetime NOT NULL,
  `is_read` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `#__giftreg_categories` (`id`, `title`) VALUES
(1,'Books'),(2,'Music'),(3,'Video Games'),(4,'Clothing'),(5,'Movies/DVD'),(6,'Gift Certificates'),
(7,'Hobbies'),(8,'Household'),(9,'Electronics'),(10,'Ornaments/Figurines'),(11,'Automotive'),
(12,'Toys'),(13,'Jewellery'),(14,'Computer'),(15,'Games'),(16,'Tools');
