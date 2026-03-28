-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Jan 05, 2026 at 02:49 PM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bike_showroom`
--

-- --------------------------------------------------------

--
-- Table structure for table `bikes`
--

DROP TABLE IF EXISTS `bikes`;
CREATE TABLE IF NOT EXISTS `bikes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `model_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reg_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reg_number` (`reg_number`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bikes`
--

INSERT INTO `bikes` (`id`, `model_name`, `reg_number`, `customer_id`) VALUES
(1, 'Honda  Activa', 'WB-01-1234', 1),
(2, 'Jawa 42', 'WB-01-3400', 2),
(3, 'TVS APACHE', 'WB-29-3456', 4),
(4, 'Honda  Activa', 'WB-30-3456', 1),
(5, 'Honda  Activa', 'WB-29-3453', 1),
(6, 'Honda Shine 125', 'NEW-1000', 1),
(7, 'Honda SP 125', 'NEW-A1016', 6),
(8, 'Honda Shine 125', 'NEW-A1011', 6),
(9, 'Honda Shine 125', 'NEW-1001', 7),
(10, 'Honda Shine 125', 'NEW-1002', 6),
(11, 'Honda  Activa', 'WB-01-1299', 1),
(12, 'Honda Shine 125', 'NEW-10003', 8),
(13, 'Honda SP 125', 'NEW-A1023', 8),
(14, 'Honda SP 125', 'NEW-A1017', 9),
(15, 'Honda SP 125', 'NEW-A1025', 2);

-- --------------------------------------------------------

--
-- Table structure for table `bike_sales`
--

DROP TABLE IF EXISTS `bike_sales`;
CREATE TABLE IF NOT EXISTS `bike_sales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int DEFAULT NULL,
  `stock_id` int DEFAULT NULL,
  `sale_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `final_price` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bike_sales`
--

INSERT INTO `bike_sales` (`id`, `customer_id`, `stock_id`, `sale_date`, `final_price`, `payment_method`) VALUES
(1, 1, 1, '2025-12-31 19:43:48', 73000.00, 'Cash/Finance'),
(2, 6, 17, '2025-12-31 20:57:41', 81000.00, 'Cash'),
(3, 6, 12, '2025-12-31 21:04:24', 76000.00, 'Cash'),
(4, 7, 2, '2025-12-31 21:07:34', 75000.00, 'Cash'),
(5, 6, 3, '2025-12-31 21:13:31', 75000.00, 'Cash'),
(6, 8, 4, '2025-12-31 22:58:25', 74000.00, 'Cash'),
(7, 8, 24, '2026-01-01 15:02:42', 81000.00, 'Cash'),
(8, 9, 18, '2026-01-01 15:05:55', 81000.00, 'Cash'),
(9, 2, 26, '2026-01-01 15:23:25', 82000.00, 'Finance');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `address`) VALUES
(1, 'Sohan Paul', '8972594800', 'panskura'),
(2, 'Sukhendu Paul', '7733598700', 'khanyadihi'),
(3, 'Sukhendu Paul', '7733598700', 'panskura'),
(4, 'Arnab', '99557766009', 'keshapat'),
(5, 'SOHAN PAUL', '08372052798', 'vill - Purba Pitpur , po - Keshapat ,ps - Panskura'),
(6, 'Rohan Sen', '123456789', 'khanyadihi'),
(7, 'Sukhendu Paul', '123456784', 'khanyadihi'),
(8, 'Prasanta Samanta', '8989776655', 'DEULIA'),
(9, 'Prasanta Samanta', '8989776656', 'khanyadihi');

-- --------------------------------------------------------

--
-- Table structure for table `parts`
--

DROP TABLE IF EXISTS `parts`;
CREATE TABLE IF NOT EXISTS `parts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `part_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int DEFAULT NULL,
  `min_alert` int DEFAULT '5',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parts`
--

INSERT INTO `parts` (`id`, `part_name`, `price`, `stock`, `min_alert`) VALUES
(1, 'Brake oil', 500.00, 14, 5),
(2, 'MOTUL  ENGINE OIL ', 1050.00, 12, 5),
(3, 'BRAKE PAD', 830.00, 18, 5),
(5, 'Gear oil', 750.00, 15, 5);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bike_id` int DEFAULT NULL,
  `service_date` date DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `total_cost` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `gst_amount` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `bike_id`, `service_date`, `details`, `total_cost`, `status`, `gst_amount`) VALUES
(1, 1, '2025-12-31', 'break pad', 1062.00, 'Paid', 162.00),
(2, 1, '2025-12-31', 'hgfh', 1298.00, 'Paid', 198.00),
(5, 2, '2026-01-05', 'Engine Noise , Break pad ', 2065.00, 'Paid', 315.00),
(3, 1, '2025-12-31', 'assa', 708.00, 'Paid', 108.00),
(4, 1, '2025-12-31', 'xyz', 2714.00, 'Pending', 414.00);

-- --------------------------------------------------------

--
-- Table structure for table `service_parts`
--

DROP TABLE IF EXISTS `service_parts`;
CREATE TABLE IF NOT EXISTS `service_parts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_id` int DEFAULT NULL,
  `part_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `part_id` (`part_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_parts`
--

INSERT INTO `service_parts` (`id`, `service_id`, `part_id`, `quantity`) VALUES
(1, 4, 1, 2),
(2, 4, 2, 1),
(3, 5, 2, 1),
(4, 5, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `showroom_stock`
--

DROP TABLE IF EXISTS `showroom_stock`;
CREATE TABLE IF NOT EXISTS `showroom_stock` (
  `id` int NOT NULL AUTO_INCREMENT,
  `model_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chassis_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engine_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_price` decimal(10,2) DEFAULT NULL,
  `showroom_price` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Available',
  PRIMARY KEY (`id`),
  UNIQUE KEY `chassis_no` (`chassis_no`),
  UNIQUE KEY `engine_no` (`engine_no`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `showroom_stock`
--

INSERT INTO `showroom_stock` (`id`, `model_name`, `color`, `chassis_no`, `engine_no`, `purchase_price`, `showroom_price`, `status`) VALUES
(1, 'Honda Shine 125', 'BLUE', '1000', '0000', 60000.00, 65000.00, 'Sold'),
(2, 'Honda Shine 125', 'BLUE', '1001', '0001', 60000.00, 65000.00, 'Sold'),
(3, 'Honda Shine 125', 'BLUE', '1002', '0002', 60000.00, 65000.00, 'Sold'),
(4, 'Honda Shine 125', 'BLUE', 'MBLHA10003', 'ENG3', 60000.00, 65000.00, 'Sold'),
(5, 'Honda Shine 125', 'BLUE', 'MBLHA10004', 'ENG4', 60000.00, 65000.00, 'Available'),
(6, 'Honda Shine 125', 'BLUE', 'MBLHA10005', 'ENG5', 60000.00, 65000.00, 'Available'),
(7, 'Honda Shine 125', 'BLUE', 'MBLHA10006', 'ENG6', 60000.00, 65000.00, 'Available'),
(8, 'Honda Shine 125', 'BLUE', 'MBLHA10007', 'ENG7', 60000.00, 65000.00, 'Available'),
(9, 'Honda Shine 125', 'BLUE', 'MBLHA10008', 'ENG8', 60000.00, 65000.00, 'Available'),
(10, 'Honda Shine 125', 'BLUE', 'MBLHA10009', 'ENG9', 60000.00, 65000.00, 'Available'),
(11, 'Honda Shine 125', 'BLUE', 'MBLHA10010', 'ENG10', 60000.00, 65000.00, 'Available'),
(12, 'Honda Shine 125', 'BLACK', 'MBLHA1011', 'ENG11', 61000.00, 66000.00, 'Sold'),
(13, 'Honda Shine 125', 'BLACK', 'MBLHA1012', 'ENG12', 61000.00, 66000.00, 'Available'),
(14, 'Honda Shine 125', 'BLACK', 'MBLHA1013', 'ENG13', 61000.00, 66000.00, 'Available'),
(15, 'Honda Shine 125', 'RED', 'MBLHA1014', 'ENG14', 60500.00, 65500.00, 'Available'),
(16, 'Honda Shine 125', 'RED', 'MBLHA1015', 'ENG15', 60500.00, 65500.00, 'Available'),
(17, 'Honda SP 125', 'RED', 'MBLHA1016', 'ENG16', 65000.00, 71000.00, 'Sold'),
(18, 'Honda SP 125', 'RED', 'MBLHA1017', 'ENG17', 65000.00, 71000.00, 'Sold'),
(19, 'Honda SP 125', 'RED', 'MBLHA1018', 'ENG18', 65000.00, 71000.00, 'Available'),
(20, 'Honda SP 125', 'RED', 'MBLHA1019', 'ENG19', 65000.00, 71000.00, 'Available'),
(21, 'Honda SP 125', 'RED', 'MBLHA1020', 'ENG20', 65000.00, 71000.00, 'Available'),
(22, 'Honda SP 125', 'RED', 'MBLHA1021', 'ENG21', 65000.00, 71000.00, 'Available'),
(23, 'Honda SP 125', 'RED', 'MBLHA1022', 'ENG22', 65000.00, 71000.00, 'Available'),
(24, 'Honda SP 125', 'BLACK', 'MBLHA1023', 'ENG23', 65000.00, 71000.00, 'Sold'),
(25, 'Honda SP 125', 'BLACK', 'MBLHA1024', 'ENG24', 65000.00, 71000.00, 'Available'),
(26, 'Honda SP 125', 'BLACK', 'MBLHA1025', 'ENG25', 65000.00, 71000.00, 'Sold'),
(27, 'Honda SP 125', 'BLACK', 'MBLHA1026', 'ENG26', 65000.00, 71000.00, 'Available'),
(28, 'Honda SP 125', 'BLACK', 'MBLHA1027', 'ENG27', 65000.00, 71000.00, 'Available'),
(29, 'Honda SP 125', 'BLACK', 'MBLHA1028', 'ENG28', 65000.00, 71000.00, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `stock_entries`
--

DROP TABLE IF EXISTS `stock_entries`;
CREATE TABLE IF NOT EXISTS `stock_entries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `part_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `entry_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_entries`
--

INSERT INTO `stock_entries` (`id`, `part_name`, `supplier_id`, `quantity`, `price`, `entry_date`) VALUES
(1, 'Brake oil', 2, 4, 500.00, '2025-12-31 15:31:50'),
(2, 'Brake oil', 1, 5, 500.00, '2025-12-31 15:32:30'),
(3, 'MOTUL  ENGINE OIL ', 1, 2, 1000.00, '2025-12-31 15:32:43'),
(4, 'BRAKE PAD', 2, 2, 850.00, '2025-12-31 15:59:14'),
(5, 'BRAKE PAD', 1, 2, 800.00, '2025-12-31 16:54:42'),
(6, 'BRAKE PAD', 3, 2, 830.00, '2025-12-31 16:55:35'),
(7, 'BRAKE PAD', 1, 1, 820.00, '2025-12-31 17:04:11');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact`) VALUES
(1, 'Rahul Spares', '8977664433'),
(2, 'Sohan Spares', '89877657841'),
(3, 'Rohan Spares', '9899675435');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '1234', 'admin');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
