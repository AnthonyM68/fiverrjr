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

-- Listage de la structure de table fiverrjr. doctrine_migration_versions
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Listage des données de la table fiverrjr.doctrine_migration_versions : ~1 rows (environ)
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

-- Listage de la structure de table fiverrjr. reset_password_request
CREATE TABLE IF NOT EXISTS `reset_password_request` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `selector` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashed_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requested_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_7CE748AA76ED395` (`user_id`),
  CONSTRAINT `FK_7CE748AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table fiverrjr.reset_password_request : ~0 rows (environ)

-- Listage de la structure de table fiverrjr. service_item
CREATE TABLE IF NOT EXISTS `service_item` (
  `id` int NOT NULL AUTO_INCREMENT,
  `course_id` int NOT NULL,
  `user_id` int NOT NULL,
  `order_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `duration` int NOT NULL,
  `create_date` datetime NOT NULL,
  `picture` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D15891F2591CC992` (`course_id`),
  KEY `IDX_D15891F2A76ED395` (`user_id`),
  KEY `IDX_D15891F28D9F6D38` (`order_id`),
  CONSTRAINT `FK_D15891F2591CC992` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`),
  CONSTRAINT `FK_D15891F28D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`),
  CONSTRAINT `FK_D15891F2A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table fiverrjr.service_item : ~19 rows (environ)
INSERT INTO `service_item` (`id`, `course_id`, `user_id`, `order_id`, `title`, `description`, `price`, `duration`, `create_date`, `picture`) VALUES
	(1, 1, 1, NULL, 'Création de site vitrine professionnel', 'Nous développons des sites vitrines professionnels pour mettre en avant votre entreprise et vos services.', 1500, 30, '2024-07-10 19:54:03', 'vitrine.jpg'),
	(2, 2, 1, NULL, 'Développement de blog personnalisé', 'Nous offrons des services de développement de blogs personnalisés avec des fonctionnalités avancées.', 1200, 25, '2024-07-10 19:54:03', 'blog.jpg'),
	(3, 3, 1, NULL, 'Création de portfolio en ligne', 'Nous créons des portfolios en ligne élégants pour présenter vos travaux et compétences.', 800, 20, '2024-07-10 19:54:03', 'portfolio.jpg'),
	(4, 4, 2, NULL, 'Boutique en ligne avec Shopify', 'Nous développons des boutiques en ligne performantes et sécurisées avec Shopify.', 2500, 40, '2024-07-10 19:54:03', 'shopify.jpg'),
	(5, 5, 1, NULL, 'Développement de boutiques WooCommerce', 'Nous développons des boutiques en ligne performantes avec WooCommerce, adaptées à vos besoins.', 2000, 35, '2024-07-10 20:00:05', 'woocommerce.jpg'),
	(6, 6, 1, NULL, 'Intégration de systèmes de paiement', 'Nous intégrons des systèmes de paiement sécurisés comme Stripe, PayPal, etc., pour votre site web.', 500, 10, '2024-07-10 20:00:05', 'payment.jpg'),
	(7, 7, 1, NULL, 'Développement HTML/CSS/JavaScript', 'Nous offrons des services de développement front-end en HTML, CSS et JavaScript pour des sites web interactifs.', 1000, 20, '2024-07-10 20:00:05', 'html_css_js.jpg'),
	(8, 8, 1, NULL, 'Utilisation de frameworks front-end', 'Nous utilisons des frameworks front-end comme React, Angular et Vue.js pour créer des applications web modernes.', 1500, 25, '2024-07-10 20:00:05', 'frontend_frameworks.jpg'),
	(9, 9, 1, NULL, 'Optimisation des performances front-end', 'Nous optimisons les performances front-end de votre site pour garantir une expérience utilisateur fluide et rapide.', 800, 15, '2024-07-10 20:00:05', 'performance_optimization.jpg'),
	(10, 10, 1, NULL, 'Développement avec Node.js', 'Nous développons des applications back-end robustes et évolutives avec Node.js.', 1800, 30, '2024-07-10 20:00:05', 'nodejs.jpg'),
	(11, 11, 1, NULL, 'Développement avec Python/Django', 'Nous offrons des services de développement avec Python et Django pour des applications web performantes.', 2000, 35, '2024-07-10 20:00:05', 'python_django.jpg'),
	(12, 12, 1, NULL, 'Utilisation de PHP et frameworks', 'Nous utilisons PHP et des frameworks comme Laravel et Symfony pour créer des applications web puissantes.', 1700, 30, '2024-07-10 20:00:05', 'php_frameworks.jpg'),
	(13, 13, 1, NULL, 'Projets MERN', 'Nous réalisons des projets MERN (MongoDB, Express, React, Node.js) pour des applications web complètes et performantes.', 2200, 40, '2024-07-10 20:00:05', 'mern.jpg'),
	(14, 14, 1, NULL, 'Projets MEAN', 'Nous développons des projets MEAN (MongoDB, Express, Angular, Node.js) pour des applications web complètes et performantes.', 2200, 40, '2024-07-10 20:00:05', 'mean.jpg'),
	(15, 15, 1, NULL, 'Projets LAMP', 'Nous proposons des services de développement LAMP (Linux, Apache, MySQL, PHP) pour des solutions web robustes.', 1800, 30, '2024-07-10 20:00:05', 'lamp.jpg'),
	(16, 16, 1, NULL, 'Développement de thèmes et plugins WordPress', 'Nous développons des thèmes et plugins WordPress personnalisés pour répondre à vos besoins spécifiques.', 1200, 25, '2024-07-10 20:00:05', 'wordpress_themes_plugins.jpg'),
	(17, 17, 1, NULL, 'Développement avec Joomla et Drupal', 'Nous offrons des services de développement avec Joomla et Drupal pour des sites web performants et sécurisés.', 1500, 30, '2024-07-10 20:00:05', 'joomla_drupal.jpg'),
	(18, 18, 1, NULL, 'Développement d\'API RESTful', 'Nous développons des API RESTful pour faciliter la communication entre vos différentes applications.', 1000, 20, '2024-07-10 20:00:05', 'api_restful.jpg'),
	(19, 19, 1, NULL, 'Intégration de services tiers', 'Nous intégrons des services tiers comme Stripe, PayPal, etc., pour enrichir les fonctionnalités de votre site web.', 700, 15, '2024-07-10 20:00:05', './img/services/service.png');

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
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
	(1, 'enterprise@gmail.com', '["ROLE_ENTERPRISE"]', '$2y$13$030uvowwY4st0yG1THvBuuC5vjemI9k4kUMluwi.IBH32YCV.uVl2', 'Elan', 'Formation', '330760000000', '2024-06-20 06:48:38', './img/enterprises/enterprise.webp', 'Thann', NULL, NULL, 0, 'jad67tony'),
	(2, 'developer@gmail.com', '["ROLE_DEVELOPER"]', '$2y$13$O/tVrl4JcD3CY3pmNdhXieKSQA6Uv6suzc2N.6FXSyOVfNrxAPYrq', 'Anthony', 'Montmirail', '330760000000', '2024-06-27 00:00:00', './img/developers/668e00e3b6f2c.png', 'Thann', NULL, 'Test update', 0, 'Anthony');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
