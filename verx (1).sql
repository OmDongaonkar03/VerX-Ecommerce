-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 04, 2025 at 10:13 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `verx`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin details`
--

CREATE TABLE `admin details` (
  `no.` int(255) NOT NULL,
  `admin_password` varchar(255) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `privileges` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin details`
--

INSERT INTO `admin details` (`no.`, `admin_password`, `admin_email`, `privileges`) VALUES
(6, 'om123', 'omdongaonkar2006@gmail.com', 'read,edit,notification,add_editProduct,create_admin,all');

-- --------------------------------------------------------

--
-- Table structure for table `admin_notification`
--

CREATE TABLE `admin_notification` (
  `no.` int(11) NOT NULL,
  `admin_title` varchar(255) NOT NULL,
  `admin_detail` varchar(255) NOT NULL,
  `admin_email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_notification`
--

INSERT INTO `admin_notification` (`no.`, `admin_title`, `admin_detail`, `admin_email`) VALUES
(1, 'Om Amit Dongaonkar added VerX Flat-Front Straight Trousers in Cart', 'Om Amit Dongaonkar added VerX Flat-Front Straight Trousers in Cart price: 29 × 1', 'omdongaonkar2006@gmail.com'),
(2, 'New user Access Request', 'Om Dongaonkar has sent a request to access the website', 'omdongaonkar2006@gmail.com'),
(3, 'Om Amit Dongaonkar added Machine Denim Cargo Pants Royal Enfield x VerX in Cart', 'Om Amit Dongaonkar added Machine Denim Cargo Pants Royal Enfield x VerX in Cart price: 29 × 3', 'omdongaonkar2006@gmail.com'),
(4, 'Om Amit Dongaonkar added Gunner_Royal Enfield x VerX in Cart', 'Om Amit Dongaonkar added Gunner_Royal Enfield x VerX in Cart price: 19 × 2', 'omdongaonkar2006@gmail.com'),
(5, 'Om Amit Dongaonkar added Denim Cargo Pants Royal Enfield x VerX in Cart', 'Om Amit Dongaonkar added Denim Cargo Pants Royal Enfield x VerX in Cart price: 29 × 2', 'omdongaonkar2006@gmail.com'),
(6, 'Om Amit Dongaonkar added Cargo Pants VerX in Cart', 'Om Amit Dongaonkar added Cargo Pants VerX in Cart price: 29 × 1', 'omdongaonkar2006@gmail.com'),
(7, 'Om Amit Dongaonkar added Made Like A Gun Cropped Tee Royal Enfield x Urban Monkey in Cart', 'Om Amit Dongaonkar added Made Like A Gun Cropped Tee Royal Enfield x Urban Monkey in Cart price: 29 × 1', 'omdongaonkar2006@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `atcproduct`
--

CREATE TABLE `atcproduct` (
  `productID` varchar(255) NOT NULL,
  `productName` varchar(255) NOT NULL,
  `productCategory` varchar(255) NOT NULL,
  `productPrice` varchar(255) NOT NULL,
  `productColor` varchar(255) NOT NULL,
  `productQuantity` int(11) NOT NULL DEFAULT 1,
  `productImage` varchar(255) NOT NULL,
  `userID` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `connect request`
--

CREATE TABLE `connect request` (
  `userEmail` varchar(255) NOT NULL,
  `userMSG` varchar(255) NOT NULL,
  `no.` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `connect request`
--

INSERT INTO `connect request` (`userEmail`, `userMSG`, `no.`) VALUES
('omdongaonkar2006@gmail.com', 'test', 1),
('omdongaonkar2006@gmail.com', 'trial 1: message check contac Us', 2);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `no.` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `detail` varchar(255) NOT NULL,
  `timestamp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`no.`, `title`, `detail`, `timestamp`) VALUES
(1, 'New user Access Request', 'Om Dongaonkar has sent a request to access the website', '2025/03/07 22:36'),
(2, 'Om Amit Dongaonkar added Machine Denim Cargo Pants Royal Enfield x VerX in Cart', 'Om Amit Dongaonkar added Machine Denim Cargo Pants Royal Enfield x VerX in Cart price: 29 × 3', '2025/03/09 22:39'),
(3, 'Om Amit Dongaonkar added Gunner_Royal Enfield x VerX in Cart', 'Om Amit Dongaonkar added Gunner_Royal Enfield x VerX in Cart price: 19 × 2', '2025/03/20 15:16'),
(4, 'Om Amit Dongaonkar added Gunner_Royal Enfield x VerX in Cart', 'Om Amit Dongaonkar added Gunner_Royal Enfield x VerX in Cart price: 19 × 2', '2025/03/20 15:18'),
(5, 'Om Amit Dongaonkar added Gunner_Royal Enfield x VerX in Cart', 'Om Amit Dongaonkar added Gunner_Royal Enfield x VerX in Cart price: 19 × 1', '2025/03/20 15:19'),
(6, 'Om Amit Dongaonkar added Machine Denim Cargo Pants Royal Enfield x VerX in Cart', 'Om Amit Dongaonkar added Machine Denim Cargo Pants Royal Enfield x VerX in Cart price: 29 × 3', '2025/03/20 15:20'),
(7, 'Om Amit Dongaonkar added Gunner_Royal Enfield x VerX in Cart', 'Om Amit Dongaonkar added Gunner_Royal Enfield x VerX in Cart price: 19 × 2', '2025/03/20 16:12'),
(8, 'Om Amit Dongaonkar added Machine Denim Cargo Pants Royal Enfield x VerX in Cart', 'Om Amit Dongaonkar added Machine Denim Cargo Pants Royal Enfield x VerX in Cart price: 29 × 2', '2025/03/20 16:15'),
(9, 'Om Amit Dongaonkar added Gunner_Royal Enfield x VerX in Cart', 'Om Amit Dongaonkar added Gunner_Royal Enfield x VerX in Cart price: 19 × 2', '2025/03/20 16:39'),
(10, 'Om Amit Dongaonkar added Machine Denim Cargo Pants Royal Enfield x VerX in Cart', 'Om Amit Dongaonkar added Machine Denim Cargo Pants Royal Enfield x VerX in Cart price: 29 × 1', '2025/03/20 16:39'),
(11, 'Om Amit Dongaonkar added Gunner_Royal Enfield x VerX in Cart', 'Om Amit Dongaonkar added Gunner_Royal Enfield x VerX in Cart price: 19 × 3', '2025/03/20 16:50'),
(12, 'Om Amit Dongaonkar added Machine Denim Cargo Pants Royal Enfield x VerX in Cart', 'Om Amit Dongaonkar added Machine Denim Cargo Pants Royal Enfield x VerX in Cart price: 29 × 5', '2025/03/20 16:51'),
(13, 'Om Amit Dongaonkar added Machine Denim Cargo Pants Royal Enfield x VerX in Cart', 'Om Amit Dongaonkar added Machine Denim Cargo Pants Royal Enfield x VerX in Cart price: 29 × 2', '2025/03/20 16:56'),
(14, 'Om Amit Dongaonkar added VerX Flat-Front Straight Trousers in Cart', 'Om Amit Dongaonkar added VerX Flat-Front Straight Trousers in Cart price: 29 × 2', '2025/03/22 22:13'),
(15, 'Om Amit Dongaonkar added Denim Cargo Pants Royal Enfield x VerX in Cart', 'Om Amit Dongaonkar added Denim Cargo Pants Royal Enfield x VerX in Cart price: 29 × 2', '2025/03/22 22:28'),
(16, 'Om Amit Dongaonkar added Cargo Pants VerX in Cart', 'Om Amit Dongaonkar added Cargo Pants VerX in Cart price: 29 × 1', '2025/03/22 22:32'),
(17, 'Om Amit Dongaonkar added Made Like A Gun Cropped Tee Royal Enfield x Urban Monkey in Cart', 'Om Amit Dongaonkar added Made Like A Gun Cropped Tee Royal Enfield x Urban Monkey in Cart price: 29 × 1', '2025/03/22 22:59'),
(18, 'Om Amit Dongaonkar added VerX Flat-Front Straight Trousers in Cart', 'Om Amit Dongaonkar added VerX Flat-Front Straight Trousers in Cart price: 29 × 1', '2025/03/23 00:36'),
(19, 'Om Amit Dongaonkar added oversized tee in Cart', 'Om Amit Dongaonkar added oversized tee in Cart price: 19 × 1', '2025/03/23 01:18'),
(20, 'Om Dongaonkar added oversized tee in Cart', 'Om Dongaonkar added oversized tee in Cart price: 19 × 2', '2025/03/23 02:46'),
(21, 'Om Dongaonkar added oversized tee in Cart', 'Om Dongaonkar added oversized tee in Cart price: 19 × 2', '2025/03/31 18:55'),
(22, 'New user Access Request', 'Test Admin has sent a request to access the website', '2025/04/01 00:12');

-- --------------------------------------------------------

--
-- Table structure for table `ordered products`
--

CREATE TABLE `ordered products` (
  `no` int(128) NOT NULL,
  `userID` varchar(255) NOT NULL,
  `userName` varchar(255) NOT NULL,
  `userEmail` varchar(255) NOT NULL,
  `userContact` varchar(255) NOT NULL,
  `productID` varchar(255) NOT NULL,
  `productName` varchar(255) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `totalPrice` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `pinCode` varchar(255) NOT NULL,
  `orderMonth` varchar(255) NOT NULL,
  `orderDate` varchar(255) NOT NULL,
  `orderTime` varchar(255) NOT NULL,
  `Status` varchar(255) NOT NULL DEFAULT 'In Queue'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ordered products`
--

INSERT INTO `ordered products` (`no`, `userID`, `userName`, `userEmail`, `userContact`, `productID`, `productName`, `quantity`, `color`, `price`, `totalPrice`, `address`, `city`, `state`, `pinCode`, `orderMonth`, `orderDate`, `orderTime`, `Status`) VALUES
(1, '86530', 'Om Amit Dongaonkar', 'omdongaonkar2006@gmail.com', '09529334149', '11693', 'Cargo Pants VerX', '1', 'blue', '29', '29', 'shree sham kamal,anguribagh, aurangpura aurangabad', 'Aurangabad', 'Maharastra', '431001', 'March', '2025-03-22', '2025-03-22 20:45:50', 'In Queue'),
(2, '86530', 'Om Amit Dongaonkar', 'omdongaonkar2006@gmail.com', '09529334149', '34436', 'Made Like A Gun Cropped Tee Royal Enfield x Urban Monkey', '2', 'White', '29', '58', 'shree sham kamal,anguribagh, aurangpura aurangabad', 'Aurangabad', 'Maharastra', '431001', 'March', '2025-03-22', '2025-03-22 20:45:50', 'In Queue'),
(3, '86530', 'Om Amit Dongaonkar', 'omdongaonkar2006@gmail.com', '09529334149', '19230', 'VerX Flat-Front Straight Trousers', '1', 'white', '29', '29', 'shree sham kamal,anguribagh, aurangpura aurangabad', 'Aurangabad', 'Maharastra', '431001', 'March', '2025-03-22', '2025-03-22 20:45:50', 'In Queue'),
(4, '86530', 'Om Amit Dongaonkar', 'omdongaonkar2006@gmail.com', '09529334149', '26239', 'oversized tee', '1', 'white', '19', '19', 'shree sham kamal,anguribagh,aurangabad', 'Aurangabad', 'Maharastra', '431001', 'March', '2025-03-22', '2025-03-22 20:47:49', 'In Queue'),
(5, '86530', 'Om Amit Dongaonkar', 'omdongaonkar2006@gmail.com', '09529334149', '26239', 'oversized tee', '4', 'white', '19', '76', '9th lane,woodland', 'NY', 'Maharastra', '128524', 'March', '2025-03-22', '2025-03-22 20:50:17', 'In Queue');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `no.` int(11) NOT NULL,
  `productID` varchar(255) NOT NULL,
  `productName` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `discount` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `product_Image` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `colors` varchar(255) NOT NULL,
  `product_image2` varchar(255) NOT NULL,
  `product_image3` varchar(255) NOT NULL,
  `product_image4` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `signup`
--

CREATE TABLE `signup` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `Profile photo` varchar(255) NOT NULL,
  `Status` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `signup`
--

INSERT INTO `signup` (`id`, `name`, `contact`, `email`, `password`, `Profile photo`, `Status`, `date`) VALUES
(43203, 'Om Dongaonkar', '9529334149', 'omdongaonkar2006@gmail.com', 'om2006@@', 'uploads/profile photo (square).png', 'Accepted', '07/03/2025'),
(86530, 'Om Amit Dongaonkar (Admin)', '09529334149', 'omdongaonkar@gmail.com', 'om123', 'uploads/profile photo (square).png', 'Accepted', '07/03/2025'),
(96660, 'Test Admin', '7875170449', 'testadmin@gmail.com', '$2y$10$81pOWM71sgcf7OsdEwLyFucmD3lTu7s1iwWH1FpvaEnMK0rVvM7dG', 'uploads/user.png', 'Pending', '01/04/2025');

-- --------------------------------------------------------

--
-- Table structure for table `user_notification`
--

CREATE TABLE `user_notification` (
  `id` int(11) NOT NULL,
  `notification_title` varchar(255) NOT NULL,
  `notification_detail` varchar(255) NOT NULL,
  `notification_link` varchar(255) NOT NULL,
  `time` date NOT NULL DEFAULT current_timestamp(),
  `timestamp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_notification`
--

INSERT INTO `user_notification` (`id`, `notification_title`, `notification_detail`, `notification_link`, `time`, `timestamp`) VALUES
(1, 'Important Update', 'We have some important updates about our services.', 'https://example.com/updates', '2025-03-03', '2025/03/03 13:46'),
(2, 'Test Notification', 'This is a testing notification', 'https://chatgpt.com/c/67df0835-6a04-8006-aad0-144b158fddd8', '2025-03-23', '2025/03/23 00:42');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `userId` int(128) NOT NULL,
  `productID` int(128) NOT NULL,
  `productName` varchar(255) NOT NULL,
  `productPrice` varchar(255) NOT NULL,
  `productImage` varchar(255) NOT NULL,
  `productCategory` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin details`
--
ALTER TABLE `admin details`
  ADD PRIMARY KEY (`no.`);

--
-- Indexes for table `admin_notification`
--
ALTER TABLE `admin_notification`
  ADD PRIMARY KEY (`no.`);

--
-- Indexes for table `connect request`
--
ALTER TABLE `connect request`
  ADD PRIMARY KEY (`no.`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`no.`);

--
-- Indexes for table `ordered products`
--
ALTER TABLE `ordered products`
  ADD PRIMARY KEY (`no`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`no.`);

--
-- Indexes for table `signup`
--
ALTER TABLE `signup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_notification`
--
ALTER TABLE `user_notification`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin details`
--
ALTER TABLE `admin details`
  MODIFY `no.` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `admin_notification`
--
ALTER TABLE `admin_notification`
  MODIFY `no.` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `connect request`
--
ALTER TABLE `connect request`
  MODIFY `no.` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `no.` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `ordered products`
--
ALTER TABLE `ordered products`
  MODIFY `no` int(128) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `no.` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `signup`
--
ALTER TABLE `signup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96661;

--
-- AUTO_INCREMENT for table `user_notification`
--
ALTER TABLE `user_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
