DROP TABLE IF EXISTS `#__shopmigrator_databases`;
CREATE TABLE IF NOT EXISTS `#__shopmigrator_databases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `driver` varchar(255) DEFAULT NULL,
  `host` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `database` varchar(255) DEFAULT NULL,
  `prefix` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__shopmigrator_shops`;
CREATE TABLE IF NOT EXISTS `#__shopmigrator_shops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `db_id` int(11) NOT NULL,
  `shop_system_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__shopmigrator_shopsystems`;
CREATE TABLE IF NOT EXISTS `#__shopmigrator_shopsystems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `#__shopmigrator_shopsystems` (`id`, `title`, `version`, `class`) VALUES
(1, 'OpenCart', '1.0', 'opencart'),
(2, 'ModX', '1.0', 'modx'),
(3, 'Flygtekniska', '1.0', 'flygtekn');

DROP TABLE IF EXISTS `#__shopmigrator_shopsystems_tasks`;
CREATE TABLE IF NOT EXISTS `#__shopmigrator_shopsystems_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shopsystems_id` int(11) DEFAULT NULL,
  `task` varchar(255) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `#__shopmigrator_shopsystems_tasks` (`id`, `shopsystems_id`, `task`, `ordering`) VALUES
(1, 1, 'categories.hasNoConflict', 1),
(2, 1, 'categories.migrateCategories', 2),
(3, 1, 'categories.migrateCategoryCategories', 3),
(4, 1, 'categories.migrateImages', 4),
(5, 1, 'manufacturer.hasNoConflict', 5),
(6, 1, 'manufacturer.migrateManufacturers', 6),
(7, 1, 'manufacturer.migrateImages', 7),
(8, 1, 'products.hasNoConflict', 8),
(9, 1, 'products.migrateProducts', 9),
(10, 1, 'products.migrateImages', 10),
(11, 1, 'products.migrateProdCategories', 11),
(12, 1, 'products.migrateRelated', 12),
(13, 1, 'reviews.migrateReviews', 13),
(14, 1, 'users.hasNoConflict', 14),
(15, 1, 'users.migrateUsers', 15),
(16, 2, 'categories.migrateCategories', 1),
(17, 2, 'articles.migrateArticles', 2),
(18, 3, 'members.migrateMembers', 1);
