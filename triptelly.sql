-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 14, 2025 at 04:01 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `triptelly`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adminID` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `adminName` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `adminFname` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `adminEmail` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `adminPassword` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminID`, `adminName`, `adminFname`, `adminEmail`, `adminPassword`) VALUES
('187f865e92c96233', 'Erfa', 'Epa', 'erfa@gmail.com', '$2y$10$JeaHwp4rpKyE71gTjKWGSOCaMQsMRjWILjFUyTC2PCaB3jCtS4LyC'),
('524c84117063255f', 'Tiger Bray', 'Carol Huber', 'xonyfakaja@mailinator.com', '$2y$10$kSNjZolbgIUEUeO5lgEp5uPY7nwmnZPDzGkgRi.lcXUM346xpn/P6'),
('6a6400c0f678e99e', 'Ian Key', 'Yael Cooke', 'lemoba@mailinator.com', '$2y$10$0W7aql/I3iQLeQWpsISOe.UKK64cUVO3gv0hH.7ChRMqEU3H/mtOG'),
('6a7e4e764408f52f', 'Sam', 'Usamah', 'usamahsamah@gmail.com', '$2y$10$2G7pE40XTea/awPyeY5ztOKL6/EDrg8qaX0eyKWhkxwN5kEXHG8Yq'),
('877ca852fbbab4dc', 'Alea Webb', 'Callum Acosta', 'wovyd@mailinator.com', '$2y$10$FJdVBGBWFyy.y3G3jPOHFe.Ofsd3eFvoWhdcfwulm7Vld1gLleC36'),
('a8b69264b4fdb5cc', 'Ina Aguirre', 'Mannix Larsen', 'zesosybeb@mailinator.com', '$2y$10$eVwKhC3.51DVPmS4wHBLSO792PDsBfj1h.KOu0fNVNpvdCI7BNGEO'),
('a9e1da2425ad4d46', 'Lacota Hyde', 'Rae Mann', 'xigicice@mailinator.com', '$2y$10$qOk2bMRHtcZXj7pvsNRrTuJs.458h3sseweGmRgQI9q.i3og7HbTq'),
('c5bcd6582ba55069', 'Quynn Sykes', 'Jessamine Ellison', 'pycu@mailinator.com', '$2y$10$zz0ZzP2FYMF5kmy2aFTbt.sLnLty5tvvTQrFpKtSwSJceBRo.xNXS'),
('e1dbace618f25c7a', 'Malik Barrera', 'Ishmael Bates', 'puly@mailinator.com', '$2y$10$dca1ev65eBBzGBM9sawrgebXN0lgUsx6Tr1eBkeAuBXFVO06D6Zfi');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cartID` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `userID` varchar(225) COLLATE utf8mb4_general_ci NOT NULL,
  `fromLocation` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `destinationLocation` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `departureDate` date NOT NULL,
  `returnDate` date NOT NULL,
  `member` int NOT NULL,
  `max_budget` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_attractions`
--

CREATE TABLE `cart_attractions` (
  `cartAttID` int NOT NULL,
  `cartID` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `attID` varchar(225) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'google places id',
  `attName` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `attLocation` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `attPrice` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_hotel`
--

CREATE TABLE `cart_hotel` (
  `cartHotelID` int NOT NULL,
  `cartID` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `hotelID` varchar(255) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'google places ID',
  `hotelName` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `hotelLocation` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `hotelPrice` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

CREATE TABLE `password_reset` (
  `id` int NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `paymentID` int NOT NULL,
  `cartID` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `userID` varchar(225) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hotelData` longtext COLLATE utf8mb4_general_ci,
  `placeData` longtext COLLATE utf8mb4_general_ci,
  `totalPrice` double(10,2) DEFAULT NULL,
  `fromLocation` varchar(225) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `destinationLocation` varchar(225) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `departureDate` date DEFAULT NULL,
  `returnDate` date DEFAULT NULL,
  `person` decimal(10,0) DEFAULT NULL,
  `max_budget` decimal(10,2) DEFAULT NULL,
  `paymentDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`paymentID`, `cartID`, `userID`, `hotelData`, `placeData`, `totalPrice`, `fromLocation`, `destinationLocation`, `departureDate`, `returnDate`, `person`, `max_budget`, `paymentDate`) VALUES
(12, '3-KVVoxCo81', '69e6a1311ecaca54', '{\"cartID\": \"3-KVVoxCo81\", \"hotelID\": \"ChIJJ2V7SClPzDER779w0YaYOXU\", \"hotelName\": \"Royale Chulan Damansara\", \"hotelPrice\": \"103.00\", \"cartHotelID\": 19, \"hotelLocation\": \"2A, Jalan PJU 7/3, Mutiara Damansara, Petaling Jaya\"}', '[{\"attID\": \"ChIJvy1SAMZPzDERS6HN5ms9Xwo\", \"cartID\": \"3-KVVoxCo81\", \"attName\": \"Shawnalyzer Studio\", \"attPrice\": \"61.00\", \"cartAttID\": 56, \"attLocation\": \"15, Jalan Zuhrah U5/151, Taman Subang Murni, Shah Alam\"}, {\"attID\": \"ChIJJSAtCNNJzDERETbGF9iz7CI\", \"cartID\": \"3-KVVoxCo81\", \"attName\": \"Muzium Telekom\", \"attPrice\": \"79.00\", \"cartAttID\": 57, \"attLocation\": \"Jalan Raja Chulan, Kuala Lumpur\"}, {\"attID\": \"ChIJx2_KuMZJzDERboiFiS2RDyU\", \"cartID\": \"3-KVVoxCo81\", \"attName\": \"Muzium Etnologi Dunia Melayu\", \"attPrice\": \"79.00\", \"cartAttID\": 58, \"attLocation\": \"Jalan Damansara, Kuala Lumpur\"}, {\"attID\": \"ChIJwZUFgFs2zDERvM72M2ukZ1U\", \"cartID\": \"3-KVVoxCo81\", \"attName\": \"Art House Gallery Museum of Ethnic Arts\", \"attPrice\": \"78.00\", \"cartAttID\": 59, \"attLocation\": \"Lot 3.04 & 3.05, Level 2, Annexe Building, Central Market, 10,, Jalan Hang Kasturi, Kuala Lumpur\"}, {\"attID\": \"ChIJLTz0ccxJzDER0lOupg5YSmc\", \"cartID\": \"3-KVVoxCo81\", \"attName\": \"Galeri Ketua Polis Negara\", \"attPrice\": \"59.00\", \"cartAttID\": 61, \"attLocation\": \"Jalan Bukit Aman, Tasik Perdana, Kuala Lumpur\"}, {\"attID\": \"ChIJn7ct6mxIzDERAfOvaaTX5DM\", \"cartID\": \"3-KVVoxCo81\", \"attName\": \"National Art Gallery\", \"attPrice\": \"48.00\", \"cartAttID\": 62, \"attLocation\": \"Lembaga Pembangunan Seni Visual Negara, 2, Jalan Temerloh, off, Jalan Tun Razak, Kuala Lumpur\"}]', 507.00, 'kelantan', 'kuala lumpur', '2024-11-29', '2024-12-02', '1', '605.00', '2024-11-27 19:17:35'),
(13, 'l5JPqy3rJ9j', '69e6a1311ecaca54', '{\"cartID\": \"l5JPqy3rJ9j\", \"hotelID\": \"ChIJ7Z19qqnyyjERjI16PdKY99M\", \"hotelName\": \"Sunway Lost World Hotel\", \"hotelPrice\": \"125.00\", \"cartHotelID\": 11, \"hotelLocation\": \"1, Persiaran Lagun Sunway, Sunway City, Ipoh\"}', '[{\"attID\": \"ChIJ7ZsMJ4atyjERivUghzb_TYE\", \"cartID\": \"l5JPqy3rJ9j\", \"attName\": \"Matang Museum\", \"attPrice\": \"92.00\", \"cartAttID\": 18, \"attLocation\": \"Kampung Pekan Matang, Matang\"}, {\"attID\": \"ChIJyTplPviuyjERVvnvFM5JNp4\", \"cartID\": \"l5JPqy3rJ9j\", \"attName\": \"5D Art Paradise Taiping\", \"attPrice\": \"58.00\", \"cartAttID\": 19, \"attLocation\": \"25, Jalan Maharajalela, Taiping\"}, {\"attID\": \"ChIJRXTNZwi9yjERv6RpqHfW_SQ\", \"cartID\": \"l5JPqy3rJ9j\", \"attName\": \"Galeri Sultan Azlan Shah\", \"attPrice\": \"111.00\", \"cartAttID\": 20, \"attLocation\": \"Jalan Istana, Bukit Chandan, Kuala Kangsar\"}, {\"attID\": \"ChIJgaROuH3syjERv8PsY2-k-go\", \"cartID\": \"l5JPqy3rJ9j\", \"attName\": \"22 Hale Street Heritage Gallery\", \"attPrice\": \"147.00\", \"cartAttID\": 21, \"attLocation\": \"22, Jalan Tun Sambanthan, Ipoh\"}, {\"attID\": \"ChIJWyheJuNntTERMmFrMJWf5xU\", \"cartID\": \"l5JPqy3rJ9j\", \"attName\": \"Lenggong Archaeological Museum\", \"attPrice\": \"34.00\", \"cartAttID\": 22, \"attLocation\": \"Jabatan Warisan Negara Zon Tengah, Kota Tampan, Lenggong\"}]', 567.00, 'kedah', 'perak', '2024-11-21', '2024-11-23', '1', '500.00', '2024-11-27 19:17:43');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `reviewID` int NOT NULL,
  `placeID` varchar(255) NOT NULL,
  `userID` varchar(255) NOT NULL,
  `reviewText` text NOT NULL,
  `reviewTimestamp` timestamp NOT NULL,
  `reviewRating` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`reviewID`, `placeID`, `userID`, `reviewText`, `reviewTimestamp`, `reviewRating`) VALUES
(1, 'ChIJZ4-lbwO9yjERN913Ga5gI-Y', '8c515ad5bc828c08', 'Test review', '2024-12-01 08:20:28', 4);

-- --------------------------------------------------------

--
-- Table structure for table `review_delete`
--

CREATE TABLE `review_delete` (
  `deleteID` int NOT NULL,
  `reviewURL` varchar(500) NOT NULL,
  `deleteTimestamp` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `review_delete`
--

INSERT INTO `review_delete` (`deleteID`, `reviewURL`, `deleteTimestamp`) VALUES
(1, 'https://www.google.com/maps/contrib/104674682823074932894/reviews', '2024-12-13 17:39:23'),
(2, 'https://www.google.com/maps/contrib/102235486029481211886/reviews', '2024-12-13 17:40:55'),
(3, 'https://www.google.com/maps/contrib/102235486029481211886/reviews', '2024-12-13 17:41:19'),
(4, 'https://www.google.com/maps/contrib/102235486029481211886/reviews', '2024-12-13 17:41:22'),
(5, 'https://www.google.com/maps/contrib/102235486029481211886/reviews', '2024-12-13 17:41:24'),
(6, 'https://www.google.com/maps/contrib/102235486029481211886/reviews', '2024-12-13 17:41:26'),
(7, 'https://www.google.com/maps/contrib/102235486029481211886/reviews', '2024-12-13 17:41:54'),
(8, 'https://www.google.com/maps/contrib/102235486029481211886/reviews', '2024-12-13 17:41:55'),
(9, 'https://www.google.com/maps/contrib/102235486029481211886/reviews', '2024-12-13 17:41:57'),
(10, 'https://www.google.com/maps/contrib/102235486029481211886/reviews', '2024-12-13 17:42:01'),
(11, 'https://www.google.com/maps/contrib/102235486029481211886/reviews', '2024-12-13 17:42:04'),
(12, 'https://www.google.com/maps/contrib/115249869185842145530/reviews', '2024-12-13 17:42:15'),
(13, 'https://www.google.com/maps/contrib/104805868192139690595/reviews', '2024-12-13 17:42:33'),
(14, 'https://www.google.com/maps/contrib/104805868192139690595/reviews', '2024-12-13 17:43:45'),
(15, 'https://www.google.com/maps/contrib/115249869185842145530/reviews', '2024-12-13 17:43:56'),
(16, 'https://www.google.com/maps/contrib/115249869185842145530/reviews', '2024-12-13 17:46:00'),
(17, 'https://www.google.com/maps/contrib/115249869185842145530/reviews', '2024-12-13 17:46:13'),
(18, 'https://www.google.com/maps/contrib/115108268971975126561/reviews', '2024-12-13 17:46:46'),
(19, 'https://www.google.com/maps/contrib/115108268971975126561/reviews', '2024-12-13 17:46:53'),
(20, 'https://www.google.com/maps/contrib/115108268971975126561/reviews', '2024-12-13 17:46:55'),
(21, 'https://www.google.com/maps/contrib/115108268971975126561/reviews', '2024-12-13 17:46:57'),
(22, 'https://www.google.com/maps/contrib/115108268971975126561/reviews', '2024-12-13 17:50:17'),
(23, 'https://www.google.com/maps/contrib/113446358179386502048/reviews', '2024-12-13 17:51:18'),
(24, 'https://www.google.com/maps/contrib/100516470621537673026/reviews', '2024-12-13 17:53:26'),
(25, 'https://www.google.com/maps/contrib/115249869185842145530/reviews', '2024-12-13 17:55:02'),
(26, 'https://www.google.com/maps/contrib/115249869185842145530/reviews', '2024-12-13 17:55:39'),
(27, 'https://www.google.com/maps/contrib/115249869185842145530/reviews', '2024-12-13 17:55:56'),
(28, 'https://www.google.com/maps/contrib/115249869185842145530/reviews', '2024-12-13 17:55:57'),
(29, 'https://www.google.com/maps/contrib/115249869185842145530/reviews', '2024-12-13 17:56:02'),
(30, 'https://www.google.com/maps/contrib/115249869185842145530/reviews', '2024-12-13 17:56:04'),
(31, 'https://www.google.com/maps/contrib/115249869185842145530/reviews', '2024-12-13 17:56:18'),
(32, 'https://www.google.com/maps/contrib/115249869185842145530/reviews', '2024-12-13 17:56:19'),
(33, 'https://www.google.com/maps/contrib/115249869185842145530/reviews', '2024-12-13 17:56:21'),
(34, 'https://www.google.com/maps/contrib/115249869185842145530/reviews', '2024-12-13 17:56:22'),
(35, 'https://www.google.com/maps/contrib/115249869185842145530/reviews', '2024-12-13 17:56:42'),
(36, 'https://www.google.com/maps/contrib/115249869185842145530/reviews', '2024-12-13 17:57:11'),
(37, 'https://www.google.com/maps/contrib/115249869185842145530/reviews', '2024-12-13 17:57:12'),
(38, 'https://www.google.com/maps/contrib/103362961242662567584/reviews', '2024-12-13 17:57:56'),
(39, 'https://www.google.com/maps/contrib/115249869185842145530/reviews', '2024-12-13 17:58:54'),
(40, 'https://www.google.com/maps/contrib/114786077949981923871/reviews', '2024-12-13 17:59:06'),
(41, '1', '2024-12-14 05:39:04'),
(42, '1', '2024-12-14 05:41:00'),
(43, 'https://www.google.com/maps/contrib/116610863751457112728/reviews', '2024-12-14 05:57:28'),
(44, 'https://www.google.com/maps/contrib/112948489749322374239/reviews', '2024-12-14 05:57:36'),
(45, 'https://www.google.com/maps/contrib/105319771393540377621/reviews', '2024-12-14 05:57:42');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userID` varchar(225) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `userFname` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `userEmail` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `userPassword` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `username`, `userFname`, `userEmail`, `userPassword`) VALUES
('292d32c9dacb6f07', 'bixetujijo', 'Alexa Bass', 'lamyqego@mailinator.com', '$2y$10$91BSFS.V4a1AcyVe5.SLPuBtRo/e883gC4q7gjunFg4K8miRz/OEu'),
('3d143f5194b6292a', 'xuhigupor', 'Tatyana Meadows', 'lixuv@mailinator.com', '$2y$10$n.UgyBKZLqmog2KYHXfiGegr9llMunc5LO3FPAVPZPDll9rs.LLTu'),
('5af4645a36c1777a', 'focusot', 'Wylie Villarreal', 'vuvelar@mailinator.com', '$2y$10$rgDEptOne5Kzs7z3sikDseuN9aKykIFGWShsPMJKAD5OJKNt8Z3/O'),
('606a031bcf9b9189', 'zyfese', 'Kimberley David', 'qixyqa@mailinator.com', '$2y$10$p.pyV11u48Zo8Rb.7c6utOYibN./qLcDP0SErTImtCCfXGKaAX2Fq'),
('6083ef8352251f8e', 'gyzezox', 'Macaulay Richard', 'xuwilu@mailinator.com', '$2y$10$emBgYmeXrIf48WI8.GmtmunNYxbVpqcFJwjFqCK4aWIHJEm62Iffu'),
('632ba882362fd70a', 'vimep', 'Vance Lopez', 'wedip@mailinator.com', '$2y$10$whP2RfF/mzgmTVLdB9Hx6uwzykfnnnunwPnxydR3AT1fMY21kwby2'),
('69e6a1311ecaca54', 'Usamah', 'Mohamad Usamah Thani Che Arif', 'mohamadusamahthani@gmail.com', '$2y$10$GmJzTs.fVK94EKS8BfCifOX787DUMKvy0kykxrsqel7uNb.6FgSSu'),
('6c0f1834877c342c', 'bometewe', 'Caleb Rowland', 'bevowo@mailinator.com', '$2y$10$xzTY9zoBjr3SLlGcSyHZrOdEyoj9x5udtJMFJ8TX4sm0evpP5ulOi'),
('8c515ad5bc828c08', 'Sam', 'UsamahThani', 'usamah@gmail.com', '$2y$10$JJudpbh3gtNiL0tcBKI5D.HMzndUW1e7PltoK/J8vjB3.VfUlFfmS'),
('b419549b1ea2e9e0', 'vyqureqa', 'Camille Morgan', 'xycidaheh@mailinator.com', '$2y$10$xPkZMbUdHwetXD7rglJ8jOehbWdJGEDfr6sZV84.7LTQ0UaiErKue'),
('cf8170e907a85071', 'Erfa', 'Erfa Customer', 'erfa@gmail.com', '$2y$10$Hwx/7FnI/1V9/9ZFzLZdaO4aPaf7Ap8imjKiU5Lxdt/ybxYtDpDza'),
('d7718da3d2a8dd18', 'vywerenore', 'Anika Harmon', 'hofonuqaz@mailinator.com', '$2y$10$H8nMS1uKGiRiZit7WtzWXOza9N/xDpYdwtaDqYOZ0OrPV16OFT90a'),
('db786e6917cda17c', 'Mah', 'Samah', 'vulpinefgo@gmail.com', '$2y$10$GLwqbvdq.BwjJbVobgsOE.O3FwcQB4rd5z4l5zZkzv0KOtGWbpyzS'),
('f103f3b68ee23036', 'qubovyr', 'Hector Ross', 'bevek@mailinator.com', '$2y$10$A5woI/fo6.ox61HWnTI2ru9O2hVhtDgy3sprO8sRJrqJdxD4Mo8JS'),
('f45db1fffc728f51', 'gyfese', 'Channing Mcgowan', 'xeqeqi@mailinator.com', '$2y$10$qHylofeoAtQqDYxBVbPLMeTQpXoMNdeIzmPpY5R35cAzSUUUNuDPe');

-- --------------------------------------------------------

--
-- Table structure for table `user_engagement`
--

CREATE TABLE `user_engagement` (
  `id` int NOT NULL,
  `event_type` enum('visit','login') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `userId` varchar(255) DEFAULT NULL,
  `event_time` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_engagement`
--

INSERT INTO `user_engagement` (`id`, `event_type`, `userId`, `event_time`) VALUES
(1, 'visit', NULL, '2024-11-30 13:55:18'),
(2, 'login', '69e6a1311ecaca54', '2024-11-30 13:58:06'),
(3, 'visit', NULL, '2024-11-30 14:08:43'),
(4, 'login', '69e6a1311ecaca54', '2024-11-30 14:08:49'),
(5, 'visit', NULL, '2024-11-30 14:11:36'),
(6, 'login', '69e6a1311ecaca54', '2024-11-30 14:11:46'),
(7, 'visit', NULL, '2024-12-01 11:09:40'),
(8, 'visit', NULL, NULL),
(9, 'visit', NULL, NULL),
(10, 'visit', NULL, NULL),
(11, 'login', '69e6a1311ecaca54', NULL),
(12, 'visit', NULL, NULL),
(13, 'visit', NULL, NULL),
(14, 'visit', NULL, NULL),
(15, 'visit', NULL, NULL),
(16, 'login', '69e6a1311ecaca54', NULL),
(17, 'login', '69e6a1311ecaca54', NULL),
(18, 'login', '69e6a1311ecaca54', NULL),
(19, 'visit', NULL, NULL),
(20, 'login', '69e6a1311ecaca54', NULL),
(21, 'login', '69e6a1311ecaca54', NULL),
(22, 'visit', NULL, NULL),
(23, 'login', '69e6a1311ecaca54', NULL),
(24, 'visit', NULL, NULL),
(25, 'login', '69e6a1311ecaca54', NULL),
(26, 'visit', NULL, NULL),
(27, 'visit', NULL, NULL),
(28, 'login', '69e6a1311ecaca54', NULL),
(29, 'visit', NULL, NULL),
(30, 'visit', NULL, NULL),
(31, 'visit', NULL, NULL),
(32, 'visit', NULL, NULL),
(33, 'visit', NULL, NULL),
(34, 'login', '69e6a1311ecaca54', NULL),
(35, 'visit', NULL, NULL),
(36, 'visit', NULL, NULL),
(37, 'login', '69e6a1311ecaca54', NULL),
(38, 'visit', NULL, NULL),
(39, 'login', '69e6a1311ecaca54', NULL),
(40, 'visit', NULL, NULL),
(41, 'login', 'db786e6917cda17c', NULL),
(42, 'visit', NULL, NULL),
(43, 'login', 'db786e6917cda17c', NULL),
(44, 'login', 'db786e6917cda17c', NULL),
(45, 'login', 'db786e6917cda17c', NULL),
(46, 'login', 'db786e6917cda17c', NULL),
(47, 'login', 'db786e6917cda17c', NULL),
(48, 'login', 'db786e6917cda17c', NULL),
(49, 'login', 'db786e6917cda17c', NULL),
(50, 'login', 'db786e6917cda17c', NULL),
(51, 'login', 'db786e6917cda17c', NULL),
(52, 'login', 'db786e6917cda17c', NULL),
(53, 'login', 'db786e6917cda17c', NULL),
(54, 'login', 'db786e6917cda17c', NULL),
(55, 'login', 'db786e6917cda17c', NULL),
(56, 'login', 'db786e6917cda17c', NULL),
(57, 'login', 'db786e6917cda17c', NULL),
(58, 'login', 'db786e6917cda17c', NULL),
(59, 'login', 'db786e6917cda17c', NULL),
(60, 'login', 'db786e6917cda17c', NULL),
(61, 'login', 'db786e6917cda17c', NULL),
(62, 'login', 'db786e6917cda17c', NULL),
(63, 'login', 'db786e6917cda17c', NULL),
(64, 'login', 'db786e6917cda17c', NULL),
(65, 'login', 'db786e6917cda17c', NULL),
(66, 'login', 'db786e6917cda17c', NULL),
(67, 'login', 'db786e6917cda17c', NULL),
(68, 'login', 'db786e6917cda17c', NULL),
(69, 'login', 'db786e6917cda17c', NULL),
(70, 'visit', NULL, NULL),
(71, 'visit', NULL, NULL),
(72, 'login', '69e6a1311ecaca54', NULL),
(73, 'login', '69e6a1311ecaca54', NULL),
(74, 'login', '69e6a1311ecaca54', NULL),
(75, 'login', '69e6a1311ecaca54', NULL),
(76, 'login', '69e6a1311ecaca54', NULL),
(77, 'visit', NULL, NULL),
(78, 'login', '69e6a1311ecaca54', NULL),
(79, 'login', '69e6a1311ecaca54', NULL),
(80, 'visit', NULL, NULL),
(81, 'visit', NULL, NULL),
(82, 'login', '69e6a1311ecaca54', NULL),
(83, 'login', '69e6a1311ecaca54', NULL),
(84, 'login', '69e6a1311ecaca54', NULL),
(85, 'login', '69e6a1311ecaca54', NULL),
(86, 'login', '69e6a1311ecaca54', NULL),
(87, 'login', '69e6a1311ecaca54', NULL),
(88, 'visit', NULL, NULL),
(89, 'login', '69e6a1311ecaca54', NULL),
(90, 'visit', NULL, NULL),
(91, 'visit', NULL, NULL),
(92, 'login', '69e6a1311ecaca54', NULL),
(93, 'login', '69e6a1311ecaca54', NULL),
(94, 'login', '69e6a1311ecaca54', NULL),
(95, 'visit', NULL, NULL),
(96, 'login', '69e6a1311ecaca54', NULL),
(97, 'login', '69e6a1311ecaca54', NULL),
(98, 'login', '69e6a1311ecaca54', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cartID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `cart_attractions`
--
ALTER TABLE `cart_attractions`
  ADD PRIMARY KEY (`cartAttID`) USING BTREE,
  ADD KEY `cartID` (`cartID`);

--
-- Indexes for table `cart_hotel`
--
ALTER TABLE `cart_hotel`
  ADD PRIMARY KEY (`cartHotelID`) USING BTREE,
  ADD KEY `cartID` (`cartID`);

--
-- Indexes for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`paymentID`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`reviewID`);

--
-- Indexes for table `review_delete`
--
ALTER TABLE `review_delete`
  ADD PRIMARY KEY (`deleteID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `userEmail` (`userEmail`);

--
-- Indexes for table `user_engagement`
--
ALTER TABLE `user_engagement`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_attractions`
--
ALTER TABLE `cart_attractions`
  MODIFY `cartAttID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `cart_hotel`
--
ALTER TABLE `cart_hotel`
  MODIFY `cartHotelID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `paymentID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `reviewID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `review_delete`
--
ALTER TABLE `review_delete`
  MODIFY `deleteID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `user_engagement`
--
ALTER TABLE `user_engagement`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
