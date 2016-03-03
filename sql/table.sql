-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.6.17 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for inventory
CREATE DATABASE IF NOT EXISTS `inventory` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `inventory`;


-- Dumping structure for table inventory.inventory
CREATE TABLE IF NOT EXISTS `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemName` varchar(50) NOT NULL DEFAULT '0',
  `manufacturer` varchar(50) NOT NULL DEFAULT '0',
  `description` text,
  `assetTag` varchar(50) NOT NULL DEFAULT '0',
  `serialCode` varchar(50) DEFAULT '0',
  `category` int(11) NOT NULL,
  `dateAdded` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `issued` enum('Y','N') NOT NULL DEFAULT 'N',
  `returned` enum('Y','N') NOT NULL DEFAULT 'N',
  `disposed` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='Main list of all inventory items';

-- Dumping data for table inventory.inventory: ~1 rows (approximately)
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
INSERT INTO `inventory` (`id`, `itemName`, `manufacturer`, `description`, `assetTag`, `serialCode`, `category`, `dateAdded`, `issued`, `returned`, `disposed`) VALUES
	(1, '5550', 'Dell', 'dsada', '1234', '12345', 0, '2015-08-11 01:19:21', 'N', 'N', 'N');
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;


-- Dumping structure for table inventory.inventory_movements
CREATE TABLE IF NOT EXISTS `inventory_movements` (
  `id` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reasonid` int(11) NOT NULL,
  `changedBy` int(11) NOT NULL,
  `owner` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_inventory_movements_inventory` (`itemid`),
  CONSTRAINT `FK_inventory_movements_inventory` FOREIGN KEY (`itemid`) REFERENCES `inventory` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table inventory.inventory_movements: ~0 rows (approximately)
/*!40000 ALTER TABLE `inventory_movements` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_movements` ENABLE KEYS */;


-- Dumping structure for table inventory.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `admin` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`,`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table inventory.users: ~0 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
inventory