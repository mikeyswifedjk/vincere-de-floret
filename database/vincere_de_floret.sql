-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 26, 2025 at 04:16 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vincere_de_floret`
--

-- --------------------------------------------------------

--
-- Table structure for table `addons`
--

CREATE TABLE `addons` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Available',
  `total_sold` int(11) DEFAULT 0,
  `available_stocks` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addons`
--

INSERT INTO `addons` (`id`, `name`, `image`, `category`, `qty`, `price`, `category_id`, `status`, `total_sold`, `available_stocks`) VALUES
(211, 'Mugs', '68294e2c4cfe7.jpg', 'Add-Ons', 10, '150', 77, 'Available', 0, 0),
(212, 'Casio Watch', '68294e3c0110d.jpeg', 'Add-Ons', 10, '15499', 77, 'Available', 0, 0),
(213, 'Couple Shirt', '68294e4f40be7.jpg', 'Add-Ons', 10, '799', 77, 'Available', 0, 0),
(214, 'Versace Eros', '68294e7706b57.jpg', 'Add-Ons', 10, '79499', 77, 'Available', 0, 0),
(215, 'Cherry Cheesecake', '68294e9358c6e.jpg', 'Add-Ons', 10, '2459', 77, 'Available', 0, 0),
(216, 'White Teddy', '68294ea7ed2ff.jpg', 'Add-Ons', 10, '1599', 77, 'Available', 0, 0),
(217, 'Brown Teddy', '68294ebae4486.jpg', 'Add-Ons', 10, '1599', 77, 'Available', 0, 0),
(218, 'Don Papa Rum', '68294ed4ebba7.jpg', 'Add-Ons', 10, '75899', 77, 'Available', 0, 0),
(219, 'Irish Whiskey', '68294ef52e733.jpg', 'Add-Ons', 10, '69799', 77, 'Available', 0, 0),
(220, 'Lilo Blue', '68294f09a249f.jpg', 'Add-Ons', 10, '1399', 77, 'Available', 0, 0),
(221, 'Lilo Pink', '68294f2017b44.jpg', 'Add-Ons', 10, '1399', 77, 'Available', 0, 0),
(222, 'Mango Cheesecake', '68294f3905ee0.jpg', 'Add-Ons', 10, '2099', 77, 'Available', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(11) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`email`, `password`, `phone_number`, `address`, `image`, `username`, `fullname`) VALUES
('admin@gmail.com', '$2y$10$eoUMFupkmJMdjWHqfDDuAOa/13rUt9gVI7IiX.vSmplCKuvScnH8C', '09225049004', 'Baliwag, Bulacan', '../img/logo1.jpg', 'admin', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `product_name`, `product_image`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
(351, 77, 213, 'Bundle 3', '682940c8c0505.jpg', 1, '799', '2025-05-26 01:35:37', '2025-05-26 01:35:37');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `product_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `category`, `product_count`) VALUES
(70, 'Funeral Flowers', 12),
(71, 'Combo Bundle', 13),
(72, 'Fruits Basket', 12),
(73, 'Hamper Basket', 6),
(74, 'Money Bouquet', 6),
(75, 'Plants ', 6);

-- --------------------------------------------------------

--
-- Table structure for table `design_settings`
--

CREATE TABLE `design_settings` (
  `id` int(11) NOT NULL,
  `background_color` varchar(255) DEFAULT NULL,
  `font_color` varchar(255) DEFAULT NULL,
  `shop_name` varchar(255) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `image_one_path` varchar(255) DEFAULT NULL,
  `image_two_path` varchar(255) DEFAULT NULL,
  `image_three_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `design_settings`
--

INSERT INTO `design_settings` (`id`, `background_color`, `font_color`, `shop_name`, `logo_path`, `image_one_path`, `image_two_path`, `image_three_path`) VALUES
(1, '#f5f0e1', '#713e24', 'Vincere De Floret', '../img/logo1.png', '../img/gcash.webp', '../img/bdo.jpg', '../img/cod.png');

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` int(11) NOT NULL,
  `code` varchar(8) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `qty` int(11) NOT NULL,
  `status` enum('active','fully_redeemed') DEFAULT 'active',
  `user_id` int(11) DEFAULT NULL,
  `usage_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`id`, `code`, `amount`, `qty`, `status`, `user_id`, `usage_count`) VALUES
(8, 'FTLX6803', 80.00, 5, 'active', NULL, 0),
(9, 'AQYL3290', 100.00, 10, 'active', NULL, 0),
(10, 'WKUZ9376', 120.00, 20, 'active', NULL, 0),
(11, 'GHTI5607', 1000.00, 3, 'active', NULL, 0),
(12, 'AGJN1506', 500.00, 10, 'active', NULL, 0),
(13, 'XLOV3271', 1000.00, 10, 'active', NULL, 0),
(14, 'KHTO7498', 50.00, 10, 'active', NULL, 0),
(15, 'RHLU0546', 20.00, 12, 'active', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `flower`
--

CREATE TABLE `flower` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Available',
  `total_sold` int(11) DEFAULT 0,
  `available_stocks` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flower`
--

INSERT INTO `flower` (`id`, `name`, `image`, `category`, `qty`, `price`, `category_id`, `status`, `total_sold`, `available_stocks`) VALUES
(211, 'Sunflower & Blue Rose', '68294b29f3405.jpg', 'Flowers', 10, '10499', 71, 'Available', 0, 0),
(212, 'Pink Bouquet Flowers', '68294b52d9f40.jpg', 'Flowers', 10, '10599', 71, 'Available', 0, 0),
(213, 'Red Roses', '68294bb0d6e55.jpeg', 'Flowers', 10, '17455', 76, 'Available', 0, 0),
(214, 'White Dried Flowers', '68294c3557987.jpg', 'Flowers', 10, '3249', 76, 'Available', 0, 0),
(215, 'Pink Dried Flowers', '68294c58cef6e.jpg', 'Flowers', 10, '6499', 76, 'Available', 0, 0),
(216, 'Violet Dried Flowers', '68294c7d5ca6b.jpg', 'Flowers', 10, '3999', 76, 'Available', 0, 0),
(217, 'Violet & Blue / White Bouquet', '68294ca798ee6.jpg', 'Flowers', 10, '5999', 76, 'Available', 0, 0),
(218, 'Sweet Heart ', '68294cd4ac4fc.jpg', 'Flowers', 10, '8499', 76, 'Available', 0, 0),
(219, 'Minimalist Bouquet', '68294ce98c1e1.jpg', 'Flowers', 10, '9499', 76, 'Available', 0, 0),
(220, 'Dandelion Color Bouquet', '68294d0dcc06b.jpg', 'Flowers', 10, '6455', 76, 'Available', 0, 0),
(221, 'Red & Pink Roses', '68294d2c7280c.jpg', 'Flowers', 10, '7499', 76, 'Available', 0, 0),
(222, 'Lily Bouquet', '68294d563fa3b.jpg', 'Flowers', 10, '4699', 76, 'Available', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `sender_name` varchar(255) NOT NULL,
  `sender_phone` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(255) DEFAULT 'Under Review',
  `custom_letter` text DEFAULT NULL,
  `region_id` int(11) NOT NULL,
  `discount_code` varchar(255) NOT NULL,
  `receiver_name` varchar(255) NOT NULL,
  `receiver_phone` int(11) NOT NULL,
  `shipping_status` varchar(255) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_name`, `sender_name`, `sender_phone`, `address`, `payment_method`, `total_amount`, `order_date`, `status`, `custom_letter`, `region_id`, `discount_code`, `receiver_name`, `receiver_phone`, `shipping_status`) VALUES
(82, 'test1', 'test', '09123456789', 'Testing St.', 'BDO', 16903.00, '2025-05-21 07:34:12', 'Approved', 'letters/letter_1747812833_2357.pdf', 1, 'AQYL3290', 'receiver', 78945632, 'Out for Delivery'),
(83, 'test1', 'Testing', '09123456789', 'Testing St.', 'GCash', 25005.00, '2025-05-21 07:37:10', 'Under Review\r\n', NULL, 1, 'FTLX6803', 'receiver', 978456321, 'Pending'),
(84, 'test1', 'test', '09123456789', 'Testing St.', 'COD', 384.00, '2025-05-21 07:38:26', 'Under Review\r\n', NULL, 1, 'AGJN1506', 'receiver', 912345678, 'Pending'),
(85, 'test1', 'Testing', '09123456789', 'Testing St.', 'COD', 24085.00, '2025-05-21 10:18:59', 'Under Review', '', 1, 'XLOV3271', 'receiver', 912345678, 'Pending'),
(86, 'test1', 'Testing', '09123456789', 'Testing St.', 'GCash', 17054.00, '2025-05-25 15:49:31', 'Approved\r\n', 'letters/letter_1748188150_1844.pdf', 2, 'AGJN1506', 'receiver', 978945623, 'Processing'),
(87, 'test1', 'sender', '03123456789', 'Testing St.', 'GCash', 784.00, '2025-05-26 01:31:50', 'Under Review\r\n', NULL, 4, 'WKUZ9376', 'receiver', 987654321, 'Pending'),
(88, 'test1', 'sender', '09123456789', 'Testing St.', 'BDO', 14334.00, '2025-05-26 01:37:30', 'Under Review\r\n', NULL, 1, 'GHTI5607', 'Receiver', 945621389, 'Pending'),
(89, 'test1', 'sender', '098745632', 'Testing St. Test', 'COD', 8424.00, '2025-05-26 01:55:51', 'Under Review', NULL, 1, 'WKUZ9376', 'receiver', 912345678, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_name`, `quantity`, `price`, `total_price`, `product_image`) VALUES
(83, 82, 'Fruit Basket 3', 2, 8459.00, 16918.00, '682941dc1b6b8.jpg'),
(84, 83, 'Money Bouquet 5', 1, 25000.00, 25000.00, '68294a3ca4412.jpg'),
(85, 84, 'Bundle 3', 1, 799.00, 799.00, '682940c8c0505.jpg'),
(86, 85, 'Money Bouquet 5', 1, 25000.00, 25000.00, '68294a3ca4412.jpg'),
(87, 86, 'Money Bouquet 6', 1, 17459.00, 17459.00, '68294a4fc9789.jpg'),
(88, 87, 'Bundle 3', 1, 799.00, 799.00, '682940c8c0505.jpg'),
(89, 88, 'Bundle 10', 1, 15249.00, 15249.00, '6829418771d64.jpg'),
(90, 89, 'Fruit Basket 3', 1, 8459.00, 8459.00, '682941dc1b6b8.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pos_orders`
--

CREATE TABLE `pos_orders` (
  `id` int(11) NOT NULL,
  `cashier_name` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pos_orders`
--

INSERT INTO `pos_orders` (`id`, `cashier_name`, `total_amount`, `payment_method`, `order_date`) VALUES
(5, 'Admin', 3799.00, 'Cash', '2025-05-21 01:53:08');

-- --------------------------------------------------------

--
-- Table structure for table `pos_order_items`
--

CREATE TABLE `pos_order_items` (
  `id` int(11) NOT NULL,
  `pos_order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pos_order_items`
--

INSERT INTO `pos_order_items` (`id`, `pos_order_id`, `product_id`, `product_name`, `quantity`, `price`, `total_price`) VALUES
(4, 5, 211, 'Bundle 1', 1, 3200.00, 3200.00),
(5, 5, 212, 'Bundle 2', 1, 599.00, 599.00);

-- --------------------------------------------------------

--
-- Table structure for table `pots`
--

CREATE TABLE `pots` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Available',
  `total_sold` int(11) DEFAULT 0,
  `available_stocks` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pots`
--

INSERT INTO `pots` (`id`, `name`, `image`, `category`, `qty`, `price`, `category_id`, `status`, `total_sold`, `available_stocks`) VALUES
(211, 'Pot Design 1', '6829525c7ce93.jpg', 'Pots', 10, '459', 78, 'Available', 0, 0),
(212, 'Pot Design 2', '6829526db6470.jpg', 'Pots', 10, '499', 78, 'Available', 0, 0),
(213, 'Pot Design 3', '68295278eaf6c.jpg', 'Pots', 10, '799', 78, 'Available', 0, 0),
(214, 'Pot Design 4', '6829528dcf91f.jpg', 'Pots', 10, '699', 78, 'Available', 0, 0),
(215, 'Pot Design 5', '6829529c3f310.jpg', 'Pots', 10, '1299', 78, 'Available', 0, 0),
(216, 'Pot Design 6', '682952bbbf371.jpg', 'Pots', 10, '1599', 78, 'Available', 0, 0),
(217, 'Pot Design 7', '682952cace90f.jpg', 'Pots', 10, '1399', 78, 'Available', 0, 0),
(218, 'Pot Design 8', '682952e388f77.jpg', 'Pots', 10, '1100', 78, 'Available', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Available',
  `total_sold` int(11) DEFAULT 0,
  `available_stocks` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `name`, `image`, `category`, `qty`, `price`, `category_id`, `status`, `total_sold`, `available_stocks`) VALUES
(211, 'Bundle 1', '682940a1e9718.jpg', 'Combo Bundle', 10, '3200', 71, 'Available', 0, 10),
(212, 'Bundle 2', '682940b76ef86.jpg', 'Combo Bundle', 10, '599', 71, 'Available', 0, 10),
(213, 'Bundle 3', '682940c8c0505.jpg', 'Combo Bundle', 10, '799', 71, 'Available', 1, 9),
(214, 'Bundle 4', '682940db9704b.jpg', 'Combo Bundle', 10, '699', 71, 'Available', 0, 10),
(215, 'Bundle 5', '682940f3ad3f4.jpg', 'Combo Bundle', 10, '5249', 71, 'Available', 0, 10),
(216, 'Bundle 6', '68294116acebe.jpg', 'Combo Bundle', 10, '12499', 71, 'Available', 0, 10),
(217, 'Bundle 7', '6829412e8706c.jpg', 'Combo Bundle', 10, '7459', 71, 'Available', 0, 10),
(218, 'Bundle 8', '6829414d29613.jpg', 'Combo Bundle', 10, '15299', 71, 'Available', 0, 10),
(219, 'Bundle 9', '682941758f2e6.jpg', 'Combo Bundle', 10, '12599', 71, 'Available', 0, 10),
(220, 'Bundle 10', '6829418771d64.jpg', 'Combo Bundle', 10, '15249', 71, 'Available', 0, 10),
(221, 'Bundle 11', '682941990bb4d.jpg', 'Combo Bundle', 10, '7499', 71, 'Available', 0, 10),
(222, 'Fruit Basket 1', '682941b3a1b7c.jpg', 'Fruits Basket', 10, '7499', 72, 'Available', 0, 10),
(223, 'Fruit Basket 2', '682941c5e3b9a.jpg', 'Fruits Basket', 10, '4599', 72, 'Available', 0, 10),
(224, 'Fruit Basket 3', '682941dc1b6b8.jpg', 'Fruits Basket', 10, '8459', 72, 'Available', 2, 8),
(225, 'Fruit Basket 4', '682941efadcd4.jpg', 'Fruits Basket', 10, '12459', 72, 'Available', 0, 10),
(226, 'Fruit Basket 5', '68294209b1e27.jpg', 'Fruits Basket', 10, '10455', 72, 'Available', 0, 10),
(227, 'Fruit Basket 6', '682944671c6c8.jpg', 'Fruits Basket', 10, '11299', 72, 'Available', 0, 10),
(228, 'Fruit Basket 7', '682944fb06b44.jpg', 'Fruits Basket', 10, '6799', 72, 'Available', 0, 10),
(229, 'Fruit Basket 8', '682945193fd93.jpg', 'Fruits Basket', 10, '8990', 72, 'Available', 0, 10),
(230, 'Fruit Basket 9', '6829456bc3447.jpg', 'Fruits Basket', 10, '15799', 72, 'Available', 0, 10),
(231, 'Fruit Basket 10', '68294581da9e8.jpg', 'Fruits Basket', 10, '13499', 72, 'Available', 0, 10),
(232, 'Fruit Basket 11', '6829459415f16.jpg', 'Fruits Basket', 10, '17999', 72, 'Available', 0, 10),
(233, 'Fruit Basket 12', '682945b239889.jpg', 'Fruits Basket', 10, '9899', 72, 'Available', 0, 10),
(234, 'Funeral Package 1', '6829483545106.jpg', 'Funeral Flowers', 10, '25000', 70, 'Available', 0, 10),
(235, 'Funeral Package 2', '68294847c113b.jpg', 'Funeral Flowers', 10, '7455', 70, 'Available', 0, 10),
(236, 'Funeral Package 3', '6829485a8b68e.jpg', 'Funeral Flowers', 10, '28155', 70, 'Available', 0, 10),
(237, 'Funeral Package 4', '6829486b9614a.jpg', 'Funeral Flowers', 10, '7999', 70, 'Available', 0, 10),
(238, 'Funeral Package 5', '6829487e3e9a7.jpg', 'Funeral Flowers', 10, '4999', 70, 'Available', 0, 10),
(239, 'Funeral Package 6', '68294895039e2.jpg', 'Funeral Flowers', 10, '10544', 70, 'Available', 0, 10),
(240, 'Funeral Package 7', '682948ad9f394.jpg', 'Funeral Flowers', 10, '10299', 70, 'Available', 0, 10),
(241, 'Funeral Package 8', '682948c43e4f2.jpg', 'Funeral Flowers', 10, '11599', 70, 'Available', 0, 10),
(242, 'Funeral Package 9', '682948d8358c2.jpg', 'Funeral Flowers', 10, '10599', 70, 'Available', 0, 10),
(243, 'Funeral Package 10', '682948e504368.jpg', 'Funeral Flowers', 10, '7499', 70, 'Available', 0, 10),
(244, 'Funeral Package 11', '682949105e97d.jpg', 'Funeral Flowers', 10, '12599', 70, 'Available', 0, 10),
(245, 'Funeral Package 12', '682949234e658.jpg', 'Funeral Flowers', 10, '21999', 70, 'Available', 0, 10),
(246, 'Hamper Basket 1', '6829495e74844.jpg', 'Hamper Basket', 10, '17499', 73, 'Available', 0, 10),
(247, 'Hamper Basket 2', '6829497285881.jpg', 'Hamper Basket', 10, '12599', 73, 'Available', 0, 10),
(248, 'Hamper Basket 3', '6829498d45264.jpg', 'Hamper Basket', 10, '7499', 73, 'Available', 0, 10),
(249, 'Hamper Basket 4', '6829499d77af9.jpg', 'Hamper Basket', 10, '5499', 73, 'Available', 0, 10),
(250, 'Hamper Basket 5', '682949bc64325.jpg', 'Hamper Basket', 10, '8779', 73, 'Available', 0, 10),
(251, 'Hamper Basket 6', '682949ced102c.jpg', 'Hamper Basket', 10, '10599', 73, 'Available', 0, 10),
(252, 'Money Bouquet 1', '682949ea4a4fc.jpg', 'Money Bouquet', 10, '15000', 74, 'Available', 0, 10),
(253, 'Money Bouquet 2', '682949fb614ff.jpg', 'Money Bouquet', 10, '10000', 74, 'Available', 0, 10),
(254, 'Money Bouquet 3', '68294a1a7b9d4.jpg', 'Money Bouquet', 10, '15499', 74, 'Available', 0, 10),
(255, 'Money Bouquet 4', '68294a2daae2b.jpg', 'Money Bouquet', 10, '7999', 74, 'Available', 0, 10),
(256, 'Money Bouquet 5', '68294a3ca4412.jpg', 'Money Bouquet', 10, '25000', 74, 'Available', 1, 9),
(257, 'Money Bouquet 6', '68294a4fc9789.jpg', 'Money Bouquet', 10, '17459', 74, 'Available', 0, 10),
(258, 'Plant 1', '68294a90a4bd3.jpg', 'Plants ', 10, '4596', 75, 'Available', 0, 10),
(259, 'Plant 2', '68294a9d7d2ec.jpg', 'Plants ', 10, '7459', 75, 'Available', 0, 10),
(260, 'Plant 3', '68294aaa15043.jpg', 'Plants ', 10, '4597', 75, 'Available', 0, 10),
(261, 'Plant 4', '68294ab7e9c72.jpg', 'Plants ', 10, '4599', 75, 'Available', 0, 10),
(262, 'Plant 5', '68294acc02cc9.jpg', 'Plants ', 10, '10499', 75, 'Available', 0, 10),
(263, 'Plant 6', '68294ade8fb7d.jpg', 'Plants ', 10, '3459', 75, 'Available', 0, 10);

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

CREATE TABLE `shipping` (
  `id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `fee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping`
--

INSERT INTO `shipping` (`id`, `address`, `fee`) VALUES
(1, 'NCR', 85),
(2, 'North Luzon', 95),
(3, 'South Luzon', 100),
(4, 'Visayas', 105),
(5, 'Mindanao', 110),
(6, 'Island', 115),
(8, 'International', 250);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verification_code` int(6) NOT NULL,
  `email_verified_at` datetime(6) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiration` varchar(255) DEFAULT NULL,
  `last_attempt` timestamp NOT NULL DEFAULT current_timestamp(),
  `blocked` tinyint(1) NOT NULL DEFAULT 0,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `contact_number` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `verification_code`, `email_verified_at`, `reset_token`, `reset_token_expiration`, `last_attempt`, `blocked`, `attempts`, `contact_number`, `address`, `image_path`, `first_name`, `middle_name`, `last_name`) VALUES
(77, 'test1', 'test1@gmail.com', '$2y$10$RmBCWTOeijBRXqOF8uKbie3DSYXd3P2z5.nSwyIPKIkf865NhvIAi', 168148, '2025-05-17 22:16:54.000000', 'a32903518b7e7a0565fc0536f009c12e', '2025-05-25 19:35:18', '2025-05-17 14:16:41', 0, 0, '09123456789', 'Testing St.', '../img/68333de6cb087.jpg', 'Testing', 'T', 'Testing'),
(78, 'test2', 'test2@gmail.com', '$2y$10$RMxabFjN1GQu.F5R6J5l4OliJ.rU/rRebMTJrQ1mv3TbBT275fYWq', 569408, NULL, NULL, NULL, '2025-05-18 01:58:02', 1, 0, '09123456789', 'Testing St.', NULL, 'Testing', 'T', 'Test'),
(79, 'itsmy', 'ybiza2018@gmail.com', '$2y$10$WpgiPqf7dcXQHloR4TAscONmszHdNkvUGwPK4gXTn39EGhyLpj5ya', 252240, '2025-05-26 00:40:27.000000', '993df0d8b84cffc5847a333f98543b7b', '2025-05-25 19:41:44', '2025-05-25 16:40:13', 0, 0, '09496563656', 'Kalye Katorse', '', 'Maika', 'Ybiza O.', 'Simbulan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addons`
--
ALTER TABLE `addons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `design_settings`
--
ALTER TABLE `design_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `flower`
--
ALTER TABLE `flower`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `pos_orders`
--
ALTER TABLE `pos_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pos_order_items`
--
ALTER TABLE `pos_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pos_order_id` (`pos_order_id`);

--
-- Indexes for table `pots`
--
ALTER TABLE `pots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addons`
--
ALTER TABLE `addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=353;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `design_settings`
--
ALTER TABLE `design_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `flower`
--
ALTER TABLE `flower`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `pos_orders`
--
ALTER TABLE `pos_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pos_order_items`
--
ALTER TABLE `pos_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pots`
--
ALTER TABLE `pots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=264;

--
-- AUTO_INCREMENT for table `shipping`
--
ALTER TABLE `shipping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `discounts`
--
ALTER TABLE `discounts`
  ADD CONSTRAINT `discounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `pos_order_items`
--
ALTER TABLE `pos_order_items`
  ADD CONSTRAINT `pos_order_items_ibfk_1` FOREIGN KEY (`pos_order_id`) REFERENCES `pos_orders` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
