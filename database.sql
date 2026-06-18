-- CREATE AND INITIALIZE THE TARGET DATABASE CONTAINER
CREATE DATABASE IF NOT EXISTS `uclan_shop`;
USE `uclan_shop`;

-- phpMyAdmin SQL Dump
-- version 5.2.2deb1+deb13u1
-- https://www.phpmyadmin.net/

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_offers`
--

CREATE TABLE IF NOT EXISTS `tbl_offers` (
  `offer_id` int(11) NOT NULL,
  `offer_title` varchar(255) NOT NULL,
  `offer_desc` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_offers`
--

INSERT INTO `tbl_offers` (`offer_id`, `offer_title`, `offer_desc`) VALUES
(1, 'Back in Stock', 'Back by popular demand. Blue T-Shirts are now back-in-stock, and priced accordingly.'),
(2, 'Colours on Sale', 'Cyan, Magenta, and Yellow T-Shirts are now half-price. Are these primary or secondary colours? Who cares...they are now on sale!'),
(3, 'Grad Promo Code', 'Graduating this year? Receive 25% off your total spend. Use discount code \'GRAD25\' to apply the offer to your basket (hopefully).')
ON DUPLICATE KEY UPDATE `offer_id`=`offer_id`;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_orders`
--

CREATE TABLE IF NOT EXISTS `tbl_orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_ids` varchar(255) NOT NULL,
  `order_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_products`
--

CREATE TABLE IF NOT EXISTS `tbl_products` (
  `product_id` int(11) NOT NULL,
  `product_title` varchar(255) NOT NULL,
  `product_price` decimal(6,2) NOT NULL,
  `product_stock` enum('good-stock','low-stock','out-of-stock','') NOT NULL,
  `product_src` varchar(255) NOT NULL,
  `product_desc` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_products`
--

INSERT INTO `tbl_products` (`product_id`, `product_title`, `product_price`, `product_stock`, `product_src`, `product_desc`) VALUES
(1, 'Red T-Shirt', 7.99, 'out-of-stock', 'images/tshirts/tshirt1.jpg', 'Unfortunately we are sold out of this legacy item. Keep all eyes out for any future stock.'),
(2, 'Green T-Shirt', 7.99, 'low-stock', 'images/tshirts/tshirt2.jpg', 'Limited stock. Grab one of these nostalgic items before they make their way onto eBay.'),
(3, 'Blue T-Shirt', 7.99, 'good-stock', 'images/tshirts/tshirt3.jpg', 'Perfect for those graduating this year. Get a bargain whilst we still have (too much) stock.'),
(4, 'Cyan T-Shirt', 7.99, 'out-of-stock', 'images/tshirts/tshirt4.jpg', 'Unfortunately we are sold out of this legacy item. Keep all eyes out for any future stock.'),
(5, 'Magenta T-Shirt', 7.99, 'low-stock', 'images/tshirts/tshirt5.jpg', 'Limited stock. Grab one of these nostalgic items before they make their way onto eBay.'),
(6, 'Yellow T-Shirt', 7.99, 'good-stock', 'images/tshirts/tshirt6.jpg', 'Perfect for those graduating this year. Get a bargain whilst we still have (too much) stock.'),
(7, 'Black T-Shirt', 7.99, 'out-of-stock', 'images/tshirts/tshirt7.jpg', 'Unfortunately we are sold out of this legacy item. Keep all eyes out for any future stock.'),
(8, 'Grey T-Shirt', 7.99, 'low-stock', 'images/tshirts/tshirt8.jpg', 'Limited stock. Grab one of these nostalgic items before they make their way onto eBay.'),
(9, 'Burgundy T-Shirt', 7.99, 'good-stock', 'images/tshirts/tshirt9.jpg', 'Perfect for those graduating this year. Get a bargain whilst we still have (too much) stock.')
ON DUPLICATE KEY UPDATE `product_id`=`product_id`;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_reviews`
--

CREATE TABLE IF NOT EXISTS `tbl_reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `review_title` varchar(255) NOT NULL,
  `review_desc` mediumtext NOT NULL,
  `review_rating` enum('1','2','3','4','5') NOT NULL,
  `review_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_reviews`
--

INSERT INTO `tbl_reviews` (`review_id`, `user_id`, `product_id`, `review_title`, `review_desc`, `review_rating`, `review_timestamp`) VALUES
(1, 1, 1, 'Five star bargain!', 'Cotton authentic character and practicality are duly combined in this comfortable attire. Perfect for when venturing outdoors in summer.', '5', '2026-01-01 09:00:00')
ON DUPLICATE KEY UPDATE `review_id`=`review_id`;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE IF NOT EXISTS `tbl_users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_pass` varchar(255) NOT NULL,
  `user_address` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`user_id`, `user_name`, `user_email`, `user_pass`, `user_address`) VALUES
(1, 'Matthew', 'mbates5@lancashire.ac.uk', '$2y$10$2Ww0EECu2EbCNAJPxeIycO2nEzZANDI3RdhA6Tb8NZHdfQpgNZCnW', 'University of Lancashire, Preston.')
ON DUPLICATE KEY UPDATE `user_id`=`user_id`;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

ALTER TABLE `tbl_offers` ADD PRIMARY KEY (`offer_id`);
ALTER TABLE `tbl_orders` ADD PRIMARY KEY (`order_id`);
ALTER TABLE `tbl_products` ADD PRIMARY KEY (`product_id`);
ALTER TABLE `tbl_reviews` ADD PRIMARY KEY (`review_id`);
ALTER TABLE `tbl_users` ADD PRIMARY KEY (`user_id`);
ALTER TABLE `password_resets` ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `tbl_offers` MODIFY `offer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `tbl_orders` MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tbl_products` MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
ALTER TABLE `tbl_reviews` MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `tbl_users` MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `password_resets` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;