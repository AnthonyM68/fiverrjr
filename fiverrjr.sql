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
  `category_id` int NOT NULL,
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

-- Listage de la structure de table fiverrjr. evaluation
CREATE TABLE IF NOT EXISTS `evaluation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_id` int NOT NULL,
  `order_id` int NOT NULL,
  `note` smallint NOT NULL,
  `comment` longtext COLLATE utf8mb4_unicode_ci,
  `date_evaluation` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table fiverrjr.evaluation : ~0 rows (environ)

-- Listage de la structure de table fiverrjr. invoice
CREATE TABLE IF NOT EXISTS `invoice` (
  `id` int NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,2) NOT NULL,
  `tva` decimal(5,2) NOT NULL,
  `date_create` datetime NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_traceability` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_traceability` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdf_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_relation_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9065174429E4EEDD` (`order_relation_id`),
  CONSTRAINT `FK_9065174429E4EEDD` FOREIGN KEY (`order_relation_id`) REFERENCES `order` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table fiverrjr.invoice : ~1 rows (environ)
INSERT INTO `invoice` (`id`, `amount`, `tva`, `date_create`, `status`, `client_traceability`, `order_traceability`, `pdf_path`, `order_relation_id`) VALUES
	(24, 0.00, 20.00, '2024-08-29 02:12:36', 'pennding', '{"invoice_id":24,"user":{"firstName":"Client","lastName":"Un","city":"Marseille"}}', '{"order":{"dateOrder":"2024-08-29T02:12:36+00:00","dateDelivery":"2024-09-05T02:12:36+00:00"},"payment":{"amount":"1700.00","datePayment":"2024-08-29T02:12:36+00:00"}}', 'C:\\Users\\Anthony\\Documents\\fiverrjr/public/uploads/invoices/invoice_49.pdf', 49);

-- Listage de la structure de table fiverrjr. message
CREATE TABLE IF NOT EXISTS `message` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sent_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_sent` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table fiverrjr.message : ~0 rows (environ)

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
  `user_id` int DEFAULT NULL,
  `date_order` datetime NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_delivery` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F5299398A76ED395` (`user_id`),
  CONSTRAINT `FK_F5299398A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table fiverrjr.order : ~2 rows (environ)
INSERT INTO `order` (`id`, `user_id`, `date_order`, `status`, `date_delivery`) VALUES
	(48, NULL, '2024-08-28 15:45:35', 'paid', '2024-09-04 15:45:35'),
	(49, NULL, '2024-08-29 02:12:36', 'paid', '2024-09-05 02:12:36');

-- Listage de la structure de table fiverrjr. payment
CREATE TABLE IF NOT EXISTS `payment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `amount` decimal(7,2) NOT NULL,
  `date_payment` datetime NOT NULL,
  `order_relation_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_6D28840D29E4EEDD` (`order_relation_id`),
  CONSTRAINT `FK_6D28840D29E4EEDD` FOREIGN KEY (`order_relation_id`) REFERENCES `order` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table fiverrjr.payment : ~1 rows (environ)
INSERT INTO `payment` (`id`, `amount`, `date_payment`, `order_relation_id`) VALUES
	(34, 1700.00, '2024-08-29 02:12:36', 49);

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
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `duration` int NOT NULL,
  `create_date` datetime NOT NULL,
  `picture` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D15891F2591CC992` (`course_id`),
  KEY `IDX_D15891F2A76ED395` (`user_id`),
  KEY `IDX_D15891F28D9F6D38` (`order_id`),
  CONSTRAINT `FK_D15891F2591CC992` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`),
  CONSTRAINT `FK_D15891F28D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`),
  CONSTRAINT `FK_D15891F2A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table fiverrjr.service_item : ~11 rows (environ)
INSERT INTO `service_item` (`id`, `course_id`, `user_id`, `title`, `description`, `price`, `duration`, `create_date`, `picture`, `order_id`) VALUES
	(1, 1, 22, 'Création de site vitrine professionnel', 'Nous développons des sites vitrines professionnels pour mettre en avant votre entreprise et vos services.', 1700, 30, '2024-07-10 19:54:03', 'group.webp', NULL),
	(2, 2, 22, 'Développement de blog personnalisé', 'Nous offrons des services de développement de blogs personnalisés avec des fonctionnalités avancées.', 1420, 25, '2024-07-10 19:54:03', 'group.webp', NULL),
	(4, 4, 22, 'Boutique en ligne avec Shopify', 'Nous développons des boutiques en ligne performantes et sécurisées avec Shopify.', 4500, 40, '2024-07-10 19:54:03', 'group.webp', NULL),
	(5, 5, 22, 'Développement de boutiques WooCommerce', 'Nous développons des boutiques en ligne performantes avec WooCommerce, adaptées à vos besoins.', 2000, 35, '2024-07-10 20:00:05', 'group.webp', NULL),
	(6, 6, 22, 'Intégration de systèmes de paiement', 'Nous intégrons des systèmes de paiement sécurisés comme Stripe, PayPal, etc., pour votre site web.', 500, 10, '2024-07-10 20:00:05', 'group.webp', NULL),
	(7, 7, 22, 'Développement HTML/CSS/JavaScript', 'Nous offrons des services de développement front-end en HTML, CSS et JavaScript pour des sites web interactifs.', 1000, 20, '2024-07-10 20:00:05', 'group.webp', NULL),
	(8, 8, 22, 'Utilisation de frameworks front-end', 'Nous utilisons des frameworks front-end comme React, Angular et Vue.js pour créer des applications web modernes.', 1500, 25, '2024-07-10 20:00:05', 'group.webp', NULL),
	(10, 10, 22, 'Développement avec Node.js', 'Nous développons des applications back-end robustes et évolutives avec Node.js.', 1800, 30, '2024-07-10 20:00:05', 'group.webp', NULL),
	(13, 13, 22, 'Projets MERN', 'Nous réalisons des projets MERN (MongoDB, Express, React, Node.js) pour des applications web complètes et performantes.', 2200, 40, '2024-07-10 20:00:05', 'group.webp', NULL),
	(15, 15, 22, 'Projets LAMP', 'Nous proposons des services de développement LAMP (Linux, Apache, MySQL, PHP) pour des solutions web robustes.', 1800, 30, '2024-07-10 20:00:05', 'group.webp', NULL),
	(17, 17, 22, 'Développement avec Joomla et Drupal', 'Nous offrons des services de développement avec Joomla et Drupal pour des sites web performants et sécurisés.', 1500, 30, '2024-07-10 20:00:05', 'group.webp', NULL);

-- Listage de la structure de table fiverrjr. theme
CREATE TABLE IF NOT EXISTS `theme` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name_theme` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table fiverrjr.theme : ~10 rows (environ)
INSERT INTO `theme` (`id`, `name_theme`) VALUES
	(1, 'Développement Web'),
	(2, 'Développement Mobile'),
	(3, 'Développement de Logiciels'),
	(4, 'Bases de Données'),
	(5, 'DevOps et Administration Système'),
	(6, 'Intelligence Artificielle et Data Science'),
	(8, 'Test Theme 1'),
	(9, 'Test Theme 2'),
	(10, 'Test Theme 3'),
	(11, 'Test Theme 4');

-- Listage de la structure de table fiverrjr. user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table fiverrjr.user : ~1 rows (environ)
INSERT INTO `user` (`id`, `email`, `roles`, `password`, `first_name`, `last_name`, `phone_number`, `date_register`, `picture`, `city`, `portfolio`, `bio`, `is_verified`, `username`) VALUES
	(22, 'developer@gmail.com', '["ROLE_DEVELOPER", "ROLE_ADMIN"]', '$2y$13$O/tVrl4JcD3CY3pmNdhXieKSQA6Uv6suzc2N.6FXSyOVfNrxAPYrq', 'Anthony', 'Montmirail', '0987654330', '2024-08-29 10:17:34', 'moi.jpg', 'Lyon', 'http://portfolio10.example.com', 'Gérant chez Logic-68ConsolesSystem', 1, 'Jad67tony');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
