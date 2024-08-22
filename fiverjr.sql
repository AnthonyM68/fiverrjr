-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.0.30 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour fiverrjr
CREATE DATABASE IF NOT EXISTS `fiverrjr` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `fiverrjr`;

-- Listage de la structure de table fiverrjr. category
CREATE TABLE IF NOT EXISTS `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name_category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `theme_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_64C19C159027487` (`theme_id`),
  CONSTRAINT `FK_64C19C159027487` FOREIGN KEY (`theme_id`) REFERENCES `theme` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table fiverrjr.category : ~12 rows (environ)
INSERT INTO `category` (`id`, `name_category`, `theme_id`) VALUES
	(1, 'Création de Sites Web', 1),
	(2, 'Sites E-commerce', 1),
	(3, 'Développement Front-end', 1),
	(4, 'Développement Back-end', 1),
	(5, 'Développement Full-stack', 1),
	(6, 'CMS et Frameworks', 1),
	(7, 'API et Intégrations', 1),
	(8, 'Applications iOS', 2),
	(9, 'Applications Android', 2),
	(10, 'Applications Hybrides', 2),
	(11, 'Applications Android', 2),
	(13, 'Test Category 1', 1);

-- Listage de la structure de table fiverrjr. course
CREATE TABLE IF NOT EXISTS `course` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `name_course` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_169E6FB912469DE2` (`category_id`),
  CONSTRAINT `FK_169E6FB912469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table fiverrjr.course : ~20 rows (environ)
INSERT INTO `course` (`id`, `category_id`, `name_course`) VALUES
	(1, 1, 'Développement de sites vitrines'),
	(2, 1, 'Développement de blogs'),
	(3, 1, 'Création de portfolios en ligne'),
	(4, 2, 'Développement de boutiques en ligne avec Shopify'),
	(5, 2, 'Développement de boutiques WooCommerce'),
	(6, 2, 'Intégration de systèmes de paiement'),
	(7, 3, 'HTML/CSS/JavaScript'),
	(8, 3, 'Utilisation de frameworks front-end (React, Angular, Vue.js)'),
	(9, 3, 'Optimisation des performances front-end'),
	(10, 4, 'Développement avec Node.js'),
	(11, 4, 'Développement avec Python/Django'),
	(12, 4, 'Utilisation de PHP et frameworks (Laravel, Symfony)'),
	(13, 5, 'Projets MERN (MongoDB, Express, React, Node.js)'),
	(14, 5, 'Projets MEAN (MongoDB, Express, Angular, Node.js)'),
	(15, 5, 'Projets LAMP (Linux, Apache, MySQL, PHP)'),
	(16, 6, 'Développement de thèmes et plugins WordPress'),
	(17, 6, 'Développement avec Joomla et Drupal'),
	(18, 7, 'Développement d\'API RESTful'),
	(19, 7, 'Intégration de services tiers (Stripe, PayPal, etc.)'),
	(21, 1, 'Test Course 1');

-- Listage de la structure de table fiverrjr. messenger_messages
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table fiverrjr.messenger_messages : ~0 rows (environ)

-- Listage de la structure de table fiverrjr. order
CREATE TABLE IF NOT EXISTS `order` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `service_id` int NOT NULL,
  `date_order` datetime NOT NULL,
  `status` json NOT NULL,
  `date_delivery` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F5299398A76ED395` (`user_id`),
  CONSTRAINT `FK_F5299398A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table fiverrjr.order : ~0 rows (environ)

-- Listage de la structure de table fiverrjr. service
CREATE TABLE IF NOT EXISTS `service` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `order_id` int DEFAULT NULL,
  `course_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `duration` int NOT NULL,
  `create_date` datetime NOT NULL,
  `picture` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E19D9AD2A76ED395` (`user_id`),
  KEY `IDX_E19D9AD2591CC992` (`course_id`),
  KEY `IDX_E19D9AD28D9F6D38` (`order_id`),
  CONSTRAINT `FK_E19D9AD2591CC992` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`),
  CONSTRAINT `FK_E19D9AD28D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`),
  CONSTRAINT `FK_E19D9AD2A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table fiverrjr.service : ~3 rows (environ)
INSERT INTO `service` (`id`, `user_id`, `order_id`, `course_id`, `title`, `description`, `price`, `duration`, `create_date`, `picture`) VALUES
	(1, 1, NULL, 1, 'Titre service 1 ', 'Proposition de service de l\'utilisateur Admin', 10, 60, '2024-06-26 17:32:18', 'service.jpg'),
	(2, 2, NULL, 2, 'Titre service 2', 'Proposition de service de l\'utilisateur: user', 50, 300, '2024-06-28 06:15:16', 'service.jpg'),
	(5, 1, NULL, 1, 'Titre service 3', 'test picture', 20, 10, '2024-07-03 13:42:53', 'service.jpg');

-- Listage de la structure de table fiverrjr. theme
CREATE TABLE IF NOT EXISTS `theme` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name_theme` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table fiverrjr.theme : ~7 rows (environ)
INSERT INTO `theme` (`id`, `name_theme`) VALUES
	(1, 'Développement Web'),
	(2, 'Développement Mobile'),
	(3, 'Développement de Logiciels'),
	(4, 'Bases de Données'),
	(5, 'DevOps et Administration Système'),
	(6, 'Intelligence Artificielle et Data Science'),
	(8, 'Test Theme 1');

-- Listage de la structure de table fiverrjr. user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_register` datetime NOT NULL,
  `picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `portfolio` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_verified` tinyint(1) NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table fiverrjr.user : ~2 rows (environ)
INSERT INTO `user` (`id`, `email`, `roles`, `password`, `first_name`, `last_name`, `phone_number`, `date_register`, `picture`, `city`, `portfolio`, `bio`, `is_verified`, `username`) VALUES
	(1, 'admin@gmail.com', '["ROLE_ADMIN"]', '$2y$13$030uvowwY4st0yG1THvBuuC5vjemI9k4kUMluwi.IBH32YCV.uVl2', NULL, NULL, NULL, '2024-06-20 06:48:38', '/public/img/Service/service.jpg', NULL, NULL, NULL, 0, 'jad67tony'),
	(2, 'user@gmail.com', '[]', '$2y$13$DSnyXKGD9ZXNx6CzHbDow.wm3d.HK4gjuHviyVNW/m2WK4.FcWlrO', NULL, NULL, NULL, '2024-06-27 05:50:34', 'public/img/Service/service.jpg', NULL, NULL, NULL, 0, 'Anthony');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
