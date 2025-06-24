-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 18, 2025 at 12:53 PM
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
-- Database: `fishing_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `email`, `phone`, `password`, `created_at`) VALUES
(1, 'admin', 'admin@gmail.com', '123456789', 'admin', '2025-06-18 12:14:11');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `user_id`, `message`, `submitted_at`) VALUES
(1, 1, 'good', '2025-06-16 22:23:45'),
(2, 2, 'Great variety of fish from Mangalore!', '2025-06-16 23:10:00'),
(3, 3, 'Love the Mangalore fishing magazines', '2025-06-16 23:11:00'),
(4, 4, 'Fast delivery in Mangalore', '2025-06-16 23:12:00'),
(5, NULL, 'Rescue response in Ullal was quick', '2025-06-16 23:13:00'),
(6, 6, 'App is perfect for Mangalore fishers', '2025-06-16 23:14:00'),
(7, NULL, 'hello', '2025-06-18 15:09:50');

-- --------------------------------------------------------

--
-- Table structure for table `fish`
--

CREATE TABLE `fish` (
  `fish_id` int(11) NOT NULL,
  `fisher_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `available_quantity` int(11) DEFAULT 0,
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fish`
--

INSERT INTO `fish` (`fish_id`, `fisher_id`, `name`, `description`, `image_url`, `price`, `available_quantity`, `added_at`) VALUES
(1, 1, 'Bangda (Mackerel)', 'Fresh Indian Mackerel from Mangalore coast', 'fish/bangda_1_1623898000.jpg', 8.50, 98, '2025-06-16 22:55:00'),
(2, 2, 'Seer Fish (Anjal)', 'Premium Kingfish caught near Ullal', 'fish/seer_2_1623898100.jpg', 25.00, 35, '2025-06-16 22:56:00'),
(3, 3, 'Pomfret (Maap)', 'Silver Pomfret, popular in Mangalore', 'fish/pomfret_3_1623898200.jpg', 18.75, 60, '2025-06-16 22:57:00'),
(4, 4, 'Sardine (Tarli)', 'Fresh Sardines from Panambur waters', 'fish/sardine_4_1623898300.jpg', 6.25, 150, '2025-06-16 22:58:00');

-- --------------------------------------------------------

--
-- Table structure for table `fisher`
--

CREATE TABLE `fisher` (
  `fisher_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `registered_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fisher`
--

INSERT INTO `fisher` (`fisher_id`, `name`, `email`, `phone`, `location`, `registered_date`) VALUES
(1, 'Ramesh Shetty', 'ramesh.shetty@gmail.com', '9876543210', 'Mangalore Port', '2025-06-16 22:45:00'),
(2, 'Sunil D’Souza', 'sunil.dsouza@gmail.com', '8765432109', 'Ullal Beach', '2025-06-16 22:46:00'),
(3, 'Vijay Kumar', 'vijay.kumar@gmail.com', '7654321098', 'Surathkal', '2025-06-16 22:47:00'),
(4, 'Anitha Rao', 'anitha.rao@gmail.com', '6543210987', 'Panambur Beach', '2025-06-16 22:48:00'),
(5, 'Mohammed Ali', 'mohammed.ali@gmail.com', '5432109876', 'Tannirbhavi Beach', '2025-06-18 16:21:02');

-- --------------------------------------------------------

--
-- Table structure for table `magazine`
--

CREATE TABLE `magazine` (
  `magazine_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `content` text DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `file_url` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `magazine`
--

INSERT INTO `magazine` (`magazine_id`, `title`, `content`, `publish_date`, `file_url`, `image_url`) VALUES
(1, 'Mangalore Fishing Times', 'Updates on Mangalore’s fishing industry', '2025-06-02', 'magazine/mangalore_times.pdf', 'magazine/times_1_1623898500.jpg'),
(2, 'Coastal Karnataka Angler', 'Guide to fishing spots in Mangalore', '2025-06-04', 'magazine/coastal_angler.pdf', 'magazine/angler_2_1623898600.jpg'),
(3, 'Tulu Nadu Fisherman', 'Stories of Mangalore’s fishing community', '2025-06-06', 'magazine/tulu_fisherman.pdf', 'magazine/fisherman_3_1623898700.jpg'),
(4, 'Mangalore Marine News', 'Marine conservation in Mangalore', '2025-06-08', 'magazine/marine_news.pdf', 'magazine/news_4_1623898800.jpg'),
(5, 'Ullal Fishing Chronicles', 'History of fishing in Ullal', '2025-06-10', 'magazine/ullal_chronicles.pdf', 'magazine/chronicles_5_1623898900.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fish_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `fish_id`, `quantity`, `order_date`, `status`) VALUES
(1, 2, 1, 10, '2025-06-16 23:00:00', 'Pending'),
(2, 3, 2, 2, '2025-06-16 23:01:00', 'Confirmed'),
(3, 4, 3, 5, '2025-06-16 23:02:00', 'Pending'),
(7, 7, 1, 2, '2025-06-17 00:32:08', 'Pending'),
(8, 7, 2, 4, '2025-06-17 00:39:14', 'Pending'),
(9, 7, 2, 1, '2025-06-18 15:10:08', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `rescue`
--

CREATE TABLE `rescue` (
  `rescue_id` int(11) NOT NULL,
  `fisher_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `reported_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rescue`
--

INSERT INTO `rescue` (`rescue_id`, `fisher_id`, `description`, `location`, `status`, `reported_at`) VALUES
(1, 1, 'Stranded fishing boat near Mangalore Port', 'Mangalore Port', 'Pending', '2025-06-16 23:05:00'),
(2, 2, 'Injured turtle spotted off Ullal Beach', 'Ullal Beach', 'In Progress', '2025-06-16 23:06:00'),
(3, 3, 'Lost fishing net near Surathkal', 'Surathkal', 'Resolved', '2025-06-16 23:07:00'),
(4, 4, 'Oil spill reported at Panambur Beach', 'Panambur Beach', 'Pending', '2025-06-16 23:08:00'),
(5, NULL, 'Distressed dolphin near Tannirbhavi', 'Tannirbhavi Beach', 'In Progress', '2025-06-16 23:09:00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `email`, `phone`, `password`, `created_at`) VALUES
(1, 'ashik', 'ashik@gmail.com', '123456781', '$2y$10$LfK8uyqtW3uokJKljEJSE.2NFwViEAoCyqvhkbnjvbNyXblexvRfK', '2025-06-16 22:23:26'),
(2, 'Prakash Naik', 'prakash.naik@gmail.com', '9988776655', '$2y$10$Q8z3l5j6k8m2n4p6r8t9u.vW7xY9zA1b2c3d4e5f6g7h8i9j0k', '2025-06-16 22:50:00'),
(3, 'Divya Pinto', 'divya.pinto@gmail.com', '8877665543', '$2y$10$Q8z3l5j6k8m2n4p6r8t9u.vW7xY9zA1b2c3d4e5f6g7h8i9j0k', '2025-06-16 22:51:00'),
(4, 'Suresh Hegde', 'suresh.hegde@gmail.com', '7766554433', '$2y$10$Q8z3l5j6k8m2n4p6r8t9u.vW7xY9zA1b2c3d4e5f6g7h8i9j0k', '2025-06-16 22:52:00'),
(6, 'Arun Fernandes', 'arun.fernandes@gmail.com', '5544332211', '$2y$10$Q8z3l5j6k8m2n4p6r8t9u.vW7xY9zA1b2c3d4e5f6g7h8i9j0k', '2025-06-16 22:54:00'),
(7, 'ajay', 'ajya@gmail.com', '9876540987', '$2y$10$.VGzbr0nrgmm1qYoCjKcL.wdVz041ItsgvnNQ/v.bqr1OzleYo8ne', '2025-06-17 00:17:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `fish`
--
ALTER TABLE `fish`
  ADD PRIMARY KEY (`fish_id`),
  ADD KEY `fisher_id` (`fisher_id`);

--
-- Indexes for table `fisher`
--
ALTER TABLE `fisher`
  ADD PRIMARY KEY (`fisher_id`);

--
-- Indexes for table `magazine`
--
ALTER TABLE `magazine`
  ADD PRIMARY KEY (`magazine_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fish_id` (`fish_id`);

--
-- Indexes for table `rescue`
--
ALTER TABLE `rescue`
  ADD PRIMARY KEY (`rescue_id`),
  ADD KEY `fisher_id` (`fisher_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `fish`
--
ALTER TABLE `fish`
  MODIFY `fish_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `fisher`
--
ALTER TABLE `fisher`
  MODIFY `fisher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `magazine`
--
ALTER TABLE `magazine`
  MODIFY `magazine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `rescue`
--
ALTER TABLE `rescue`
  MODIFY `rescue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `fish`
--
ALTER TABLE `fish`
  ADD CONSTRAINT `fish_ibfk_1` FOREIGN KEY (`fisher_id`) REFERENCES `fisher` (`fisher_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`fish_id`) REFERENCES `fish` (`fish_id`) ON DELETE CASCADE;

--
-- Constraints for table `rescue`
--
ALTER TABLE `rescue`
  ADD CONSTRAINT `rescue_ibfk_1` FOREIGN KEY (`fisher_id`) REFERENCES `fisher` (`fisher_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
