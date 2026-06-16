-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 26, 2026 at 11:20 AM
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
-- Database: `hostel_fees`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `name`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$R/CBxw6pRkbrw4LphpObW.XHCeqy2PToBLdyipd82DSUydEDaOXwO', 'Administrator', 'admin@hostel.com', '2026-01-19 15:04:48');

-- --------------------------------------------------------

--
-- Table structure for table `admission`
--

CREATE TABLE `admission` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `reg_no` varchar(100) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `date_of_birth` date NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `residential_address` text NOT NULL,
  `course` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `year_of_study` varchar(50) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `mess_status` varchar(50) NOT NULL,
  `date_of_joining` date NOT NULL,
  `duration_of_stay` varchar(50) NOT NULL,
  `is_confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admission`
--

INSERT INTO `admission` (`id`, `user_id`, `student_name`, `reg_no`, `gender`, `date_of_birth`, `phone`, `email`, `residential_address`, `course`, `department`, `year_of_study`, `room_type`, `room_number`, `mess_status`, `date_of_joining`, `duration_of_stay`, `is_confirmed`, `created_at`) VALUES
(1, 1, 'muthupandik', '24mca510', 'Male', '2026-01-01', '9943312014', 'Muthupandik715@gmail.com', '1/454 MIDDLE STREET - Kulathur (North) K SUBRAMANIYAPURAM Thoothukkudi - Tamil Nadu 628903', 'mca', 'computer aplication', '3year', 'Single', 'RM-650', 'Yes', '2026-01-24', '3year', 1, '2026-01-21 21:03:45'),
(2, 2, 'muthupandik', '24mca510', 'Male', '2026-01-01', '9943312014', 'Muthupandik715@gmail.com', '1/454 MIDDLE STREET - Kulathur (North) K SUBRAMANIYAPURAM Thoothukkudi - Tamil Nadu 628903', 'bca', 'computer aplication', '3year', 'Single', 'RM-830', 'Yes', '2026-01-23', '3year', 1, '2026-01-21 21:40:58'),
(3, 3, 'muthupandik', '24mca510', 'Male', '2026-01-09', '9943312914', 'Muthupandik715@gmail.com', '1/454 MIDDLE STREET - Kulathur (North) K SUBRAMANIYAPURAM Thoothukkudi - Tamil Nadu 628903', 'bca', 'computer aplication', '3year', 'Single', 'RM-980', 'Yes', '2026-01-23', '3year', 0, '2026-01-22 14:14:08'),
(4, 4, 'muthupandik', '24mca510', 'Male', '2026-01-08', '9943312014', 'Muthupandik715@gmail.com', '1/454 MIDDLE STREET - Kulathur (North) K SUBRAMANIYAPURAM Thoothukkudi - Tamil Nadu 628903', 'mca', 'computer aplication', '3year', 'Single', 'RM-275', 'Yes', '2026-01-23', '3year', 0, '2026-01-22 15:53:21'),
(5, 5, 'muthupandik', '24mca510', 'Male', '2026-01-20', '9943312914', 'Muthupandik715@gmail.com', '1/454 MIDDLE STREET - Kulathur (North) K SUBRAMANIYAPURAM Thoothukkudi - Tamil Nadu 628903', 'mca', 'computer aplication', '3year', 'Single', 'RM-730', 'Yes', '2026-01-16', '3year', 0, '2026-01-22 21:17:10'),
(6, 6, 'manickam', '562435', 'Male', '2026-01-12', '9943312014', 'manickam@gmail.com', '1/454 MIDDLE STREET - Kulathur (North) K SUBRAMANIYAPURAM Thoothukkudi - Tamil Nadu 628903', 'mca', 'computer aplication', '3year', 'Single', 'RM-530', 'Yes', '2026-01-23', '3year', 1, '2026-01-22 22:21:48'),
(7, 7, 'muthupandik', '24mca510', 'Male', '2026-01-10', '9943312014', 'Muthupandik715@gmail.com', '1/454 MIDDLE STREET - Kulathur (North) K SUBRAMANIYAPURAM Thoothukkudi - Tamil Nadu 628903', 'mca', 'computer aplication', '3year', 'Single', 'RM-337', 'Yes', '2026-01-31', '3year', 1, '2026-01-23 15:15:16'),
(8, 8, 'alex', '54356', 'Male', '2026-01-29', '9943312014', 'alex15@gmail.com', '1/454 MIDDLE STREET - Kulathur (North) K SUBRAMANIYAPURAM Thoothukkudi - Tamil Nadu 628903', 'bca', 'computer aplication', '3year', 'Single', 'RM-147', 'Yes', '2026-01-24', '3year', 1, '2026-01-23 17:10:55'),
(9, 9, 'arun', '24mca508', 'Male', '2026-01-03', '9943312214', 'Muthupandik715@gmail.com', '1/454 MIDDLE STREET - Kulathur (North) K SUBRAMANIYAPURAM Thoothukkudi - Tamil Nadu 628903', 'bca', 'computer aplication', '3year', 'Single', 'RM-547', 'Yes', '2026-01-24', '3year', 1, '2026-01-23 17:23:34'),
(10, 10, 'muthupandik', '24mca510', 'Male', '2026-01-10', '9943312014', 'Muthupandik715@gmail.com', '1/454 MIDDLE STREET - Kulathur (North) K SUBRAMANIYAPURAM Thoothukkudi - Tamil Nadu 628903', 'bca', 'computer aplication', '2', 'Single', 'RM-184', 'Yes', '2026-01-30', '3year', 1, '2026-01-23 17:48:34'),
(11, 11, 'Muthu', '12345', 'Male', '2026-01-09', '9943312914', 'Muthupandik715@gmail.com', '1/454 MIDDLE STREET - Kulathur (North) K SUBRAMANIYAPURAM Thoothukkudi - Tamil Nadu 628903', 'bca', 'Information Technology', '3year', 'Single', 'RM-967', 'Yes', '2026-01-10', '3year', 1, '2026-01-23 23:00:27'),
(12, 12, 'aac', '24mca510', 'Male', '2026-01-01', '7896543210', 'aac@gmail.com', '1/454 MIDDLE STREET - Kulathur (North) K SUBRAMANIYAPURAM Thoothukkudi - Tamil Nadu 628903', 'mca', 'computer aplication', '3year', 'Single', 'RM-197', 'Yes', '2026-01-30', '3year', 0, '2026-01-26 11:13:48'),
(13, 13, 'aachostel', 'aac123', 'Female', '2026-01-28', '9943312914', 'aachostel@gmail.com', '1/454 MIDDLE STREET - Kulathur (North) K SUBRAMANIYAPURAM Thoothukkudi - Tamil Nadu 628903', 'mca', 'computer aplication', '3year', 'Triple', 'RM-508', 'Yes', '2026-01-10', '3year', 0, '2026-01-26 14:32:51');

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admission_id` int(11) DEFAULT NULL,
  `term_number` int(11) NOT NULL DEFAULT 0,
  `payment_type` varchar(50) NOT NULL,
  `amount` int(11) NOT NULL,
  `razorpay_order_id` varchar(255) DEFAULT NULL,
  `razorpay_payment_id` varchar(255) DEFAULT NULL,
  `razorpay_signature` varchar(255) DEFAULT NULL,
  `status` enum('pending','success','failed') DEFAULT 'pending',
  `paid_on` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `receipt_pdf_path` varchar(500) DEFAULT NULL,
  `combined_terms` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `new_admission`
--

CREATE TABLE `new_admission` (
  `id` int(11) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `year` varchar(20) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `admission_year` year(4) NOT NULL,
  `amount` int(11) NOT NULL DEFAULT 100,
  `payment_status` enum('PENDING','PAID') DEFAULT 'PENDING',
  `razorpay_order_id` varchar(100) DEFAULT NULL,
  `razorpay_payment_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `new_admission`
--

INSERT INTO `new_admission` (`id`, `student_name`, `department`, `year`, `phone`, `admission_year`, `amount`, `payment_status`, `razorpay_order_id`, `razorpay_payment_id`, `created_at`) VALUES
(1, 'rkmuthu', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S623MheILOBaMh', '2026-01-20 00:48:56'),
(2, 'rkmuthu', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PENDING', NULL, NULL, '2026-01-20 01:02:55'),
(3, 'rkmuthu', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PENDING', NULL, NULL, '2026-01-20 01:03:11'),
(4, 'rkmuthu', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PENDING', NULL, NULL, '2026-01-20 01:09:10'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-21 17:52:31'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-21 17:55:58'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-21 17:58:16'),
(0, 'rkmuthu', 'computer aplication', '2nd Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-21 18:00:24'),
(0, 'Muthu', 'computer aplications', '1st Year', '11111-11111', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 01:40:33'),
(0, 'Muthu', 'computer aplications', '1st Year', '11111-11111', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 01:49:05'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 01:51:30'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 07:03:38'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 07:16:04'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 07:32:06'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 07:35:57'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 07:46:42'),
(0, 'Muthu', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 08:35:10'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 08:45:55'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 08:51:56'),
(0, 'rkmuthu', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 08:57:47'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 09:23:05'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 09:56:23'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 10:01:36'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 10:11:26'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 10:19:28'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 15:36:40'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 15:42:22'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 16:24:37'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PAID', NULL, 'pay_S6zkeV8v4CPrFR', '2026-01-22 16:43:29'),
(0, 'muthupandik', 'computer aplication', '1st Year', '9943312914', '2026', 100, 'PENDING', NULL, NULL, '2026-01-22 16:46:24'),
(0, 'manickam', 'computer aplication', '3year', '9943312014', '2026', 10000, 'PAID', 'order_S705ATntqUon5t', 'pay_S705InaVDXbP7h', '2026-01-22 17:03:28'),
(0, 'muthupandik', 'computer aplication', '3year', '9943312014', '2026', 0, 'PAID', 'order_S7HBOMzUV0q2pf', 'pay_S7HBWc72hu4eiW', '2026-01-23 09:47:06'),
(0, 'alex', 'computer aplication', '3year', '9943312014', '2026', 0, 'PAID', 'order_S7JBXYUB6h69g1', 'pay_S7JBgGyo79qWYr', '2026-01-23 11:44:37'),
(0, 'arun', 'computer aplication', '3year', '9943312214', '2026', 0, 'PAID', 'order_S7JLZoSsYhSzj3', 'pay_S7JLhDP1qRwDfG', '2026-01-23 11:54:06'),
(0, 'muthupandik', 'computer aplication', '2', '9943312014', '2026', 10000, 'PAID', 'order_S7O2fRgbYYaoHk', 'pay_S7O2pC30e4htRu', '2026-01-23 16:29:45'),
(0, 'Muthu', 'Information Technology', '3year', '9943312914', '2026', 10000, 'PAID', 'order_S7P5Ks3nQOTLhi', 'pay_S7P5X1DAZs6yJI', '2026-01-23 17:30:58');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admission_id` int(11) DEFAULT NULL,
  `instalment_number` int(11) NOT NULL DEFAULT 0,
  `amount` int(11) NOT NULL,
  `status` enum('pending','success','failed','refunded') DEFAULT 'pending',
  `razorpay_order_id` varchar(255) DEFAULT NULL,
  `razorpay_payment_id` varchar(255) DEFAULT NULL,
  `razorpay_signature` varchar(255) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `admission_id`, `instalment_number`, `amount`, `status`, `razorpay_order_id`, `razorpay_payment_id`, `razorpay_signature`, `payment_method`, `payment_date`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0, 10000, 'success', 'order_S6a1r5NOYBphCy', 'pay_S6a2TPRJXBvWoH', 'fe81a3520b563606c65129957c87aa82a07e762a4f0f929f042146b9ee80bf7f', 'card', '2026-01-21 21:05:11', '2026-01-21 21:05:11', '2026-01-21 21:05:11'),
(2, 2, 2, 0, 10000, 'success', 'order_S6af6nfX2zWwoQ', 'pay_S6afuTZaMbSwnC', '19f5697186b368575c76924a6d6fded31bc02e7a553cde30bb2366838927854b', 'card', '2026-01-21 21:42:14', '2026-01-21 21:42:14', '2026-01-21 21:42:14'),
(3, 5, 5, 0, 10000, 'success', 'order_S6zSq8UKDWvuVZ', 'pay_S6zhxJJpqftJ6F', 'b75dad808503e041a95a1a7caf34f7b98c42b1d69c950286a2070dd853d559e5', 'netbanking', '2026-01-22 22:11:22', '2026-01-22 22:11:22', '2026-01-22 22:11:22'),
(4, 6, 6, 0, 10000, 'success', 'order_S705ATntqUon5t', 'pay_S705InaVDXbP7h', '8f9f7924bae60b6d114129b640e59cb32471c726e36558ef7aab7c1fde887f62', 'netbanking', '2026-01-22 22:33:28', '2026-01-22 22:33:28', '2026-01-22 22:33:28'),
(5, 6, 6, 1, 1500000, 'success', 'order_S7Fdn6zbQJ1PzK', 'pay_S7Fe7VYzomPr3s', '23ee51f370e887c84880ea9cfc76f1a2911f16a72e536bf796b0699e0d228b77', 'netbanking', '2026-01-23 13:46:48', '2026-01-23 13:46:48', '2026-01-23 13:46:48'),
(7, 6, 6, 1, 1500000, 'success', 'order_S7FsYfy1utv7Lw', 'pay_S7FsmKbM9K1HxW', '25d092cc9e195f52d4227371ab3da09c35dc9beecd2c18214eab43e59732339c', 'netbanking', '2026-01-23 14:00:41', '2026-01-23 14:00:41', '2026-01-23 14:00:41'),
(9, 6, 6, 1, 1500000, 'success', 'order_S7GNETAuIKdHHc', 'pay_S7GNMnLlvTD8z2', 'a407b595f2b8acab2c465ab8f7952d11f06a6ca31f703aca7d58bb0b71b5c3c6', 'netbanking', '2026-01-23 14:29:39', '2026-01-23 14:29:39', '2026-01-23 14:29:39'),
(11, 6, 6, 2, 0, 'success', 'order_S7GgfpsYwQkUQs', 'pay_S7GgqaQJMNXKQ0', 'a3483fb50057e0cb4216f13c855229d993f186ca7bd913926498afcbdcd0f83d', NULL, '2026-01-23 14:48:02', '2026-01-23 14:48:02', '2026-01-23 14:48:02'),
(12, 6, 6, 3, 0, 'success', 'order_S7GhKBm6jCB1Ay', 'pay_S7GhR6Z8aqXE4q', '0ddaba45fc51495d6299a8087d70694e4bc726cd956841d9b6c24be612d7ae7f', NULL, '2026-01-23 14:48:37', '2026-01-23 14:48:37', '2026-01-23 14:48:37'),
(13, 7, 7, 0, 0, 'success', 'order_S7HBOMzUV0q2pf', 'pay_S7HBWc72hu4eiW', '5c8e7f27328df1a4b0a2dde0387bdd09354a7d1cfb617c1f71221a26593f297e', NULL, '2026-01-23 15:17:06', '2026-01-23 15:17:06', '2026-01-23 15:17:06'),
(14, 7, 7, 1, 0, 'success', 'order_S7HgFwKDoczIWj', 'pay_S7HgO9SIlj2S6d', '6b8f8b2391cae24facb521da20fd9a16a55e6f0224e4c71c3ecc6b2ada87fb9a', NULL, '2026-01-23 15:46:19', '2026-01-23 15:46:19', '2026-01-23 15:46:19'),
(15, 7, 7, 1, 1500000, 'success', 'order_S7Isrri0r8x2Jh', 'pay_S7It07d1GLQBI0', '346456ce155d926880d42198429dea84e45062bc646195a467b0a767dcaa31ee', NULL, '2026-01-23 16:56:56', '2026-01-23 16:56:56', '2026-01-23 16:56:56'),
(17, 7, 7, 2, 1300000, 'success', 'order_S7J27xAOJXBmvT', 'pay_S7J2GJJJNZkbx8', '81594c8b390c4a499ab4121e4d380870c4879efc1ae8d48a1d8d77fd9deb6c20', NULL, '2026-01-23 17:05:43', '2026-01-23 17:05:43', '2026-01-23 17:05:43'),
(19, 8, 8, 0, 0, 'success', 'order_S7JBXYUB6h69g1', 'pay_S7JBgGyo79qWYr', 'a2cd3df7f57f0a320dc3da8596607b8a6dfe6e35994efded8ba99d5dd681cd70', NULL, '2026-01-23 17:14:37', '2026-01-23 17:14:37', '2026-01-23 17:14:37'),
(20, 8, 8, 1, 1500000, 'success', 'order_S7JCV07C1HQ0RK', 'pay_S7JCdGw7e2SVoP', '7f961031e3f0852005c11bed42bae732ad3b25901933a3b2d0efdc807aa045f5', NULL, '2026-01-23 17:15:33', '2026-01-23 17:15:33', '2026-01-23 17:15:33'),
(22, 8, 8, 2, 1300000, 'success', 'order_S7JGZMyX4m4I9C', 'pay_S7JGh2mFe6EHSj', '23f15ece51d8db0c0ebf239de155eb2ce366c56cdea4d8c8f381e2848a3abbcc', NULL, '2026-01-23 17:19:24', '2026-01-23 17:19:24', '2026-01-23 17:19:24'),
(24, 9, 9, 0, 0, 'success', 'order_S7JLZoSsYhSzj3', 'pay_S7JLhDP1qRwDfG', '9d27004ba3644ee46c6fd51ff51141182153cc27d4d89cf0ae8ed26eeba8343c', NULL, '2026-01-23 17:24:06', '2026-01-23 17:24:06', '2026-01-23 17:24:06'),
(25, 9, 9, 1, 1500000, 'success', 'order_S7JMDlDtJYE9J5', 'pay_S7JMJuXBL7r7YE', 'f87e2031154dbec06c27e29ed10718ac48003bbb69f87b7ba99efa255708b591', NULL, '2026-01-23 17:24:42', '2026-01-23 17:24:42', '2026-01-23 17:24:42'),
(27, 9, 9, 2, 1300000, 'success', 'order_S7JSl2dO7NVhqP', 'pay_S7JSrrwIj6Emqc', '82e1c597f80f469c33328560da4c8c0eec4509d58d59e0cec2e830d7bdd8d1a1', NULL, '2026-01-23 17:30:56', '2026-01-23 17:30:56', '2026-01-23 17:30:56'),
(29, 10, 10, 0, 10000, 'success', 'order_S7O2fRgbYYaoHk', 'pay_S7O2pC30e4htRu', '5fb24163cb3b81def54af6dc314d185607afde4776a5320e529de89cfc93d527', 'netbanking', '2026-01-23 21:59:45', '2026-01-23 21:59:45', '2026-01-23 21:59:45'),
(30, 10, 10, 1, 1500000, 'success', 'order_S7OmLJwD5uTrDy', 'pay_S7OmU5aJIz1do4', 'a5d2f57974d87b3252066515ffaac66ad4501e2dea91c802e0b9f267c49ad55b', 'netbanking', '2026-01-23 22:42:56', '2026-01-23 22:42:56', '2026-01-23 22:42:56'),
(31, 10, 10, 2, 1300000, 'success', 'order_S7P08DplehqeON', 'pay_S7P0FJWNrJGQUC', '37193fe18b3eff6bfa0eece2751b1fb4695b8f5df3d24fc7edd5953489f2b0db', 'netbanking', '2026-01-23 22:55:59', '2026-01-23 22:55:59', '2026-01-23 22:55:59'),
(32, 11, 11, 0, 10000, 'success', 'order_S7P5Ks3nQOTLhi', 'pay_S7P5X1DAZs6yJI', 'a86db0184784b8c60c8b7346a203a0dd98657ccb0438e16ea20cbf331dca0ff0', 'netbanking', '2026-01-23 23:00:58', '2026-01-23 23:00:58', '2026-01-23 23:00:58'),
(33, 11, 11, 1, 1500000, 'success', 'order_S7P632r0kY4Qye', 'pay_S7P6Czl43OtNwS', '11591183e23b592a8fce36ec7f3f79de9abc98906153c9832461b959de012447', 'netbanking', '2026-01-23 23:01:37', '2026-01-23 23:01:37', '2026-01-23 23:01:37'),
(34, 11, 11, 1, 1500000, 'success', 'order_S8NrdATKG9VdBl', 'pay_S8Ns7IGDpgxCIt', '76d60fe4ea682ff20439561f5ac7ac125fcb862518af40f6d196c62c46bb84dd', 'netbanking', '2026-01-26 10:28:40', '2026-01-26 10:28:40', '2026-01-26 10:28:40'),
(35, 12, 12, 0, 10000, 'success', 'order_S8Rbb2yKTeacZ1', 'pay_S8RbksS3EZNZcZ', '7f38a130bca04d1461e5f728f21593d2ad135eb4d34c25e9f9754f47fc815f21', 'netbanking', '2026-01-26 14:07:52', '2026-01-26 14:07:52', '2026-01-26 14:07:52'),
(36, 12, 12, 1, 1500000, 'success', 'order_S8RcNWq2sqHUYb', 'pay_S8RcZ2lbjJUF0e', 'd2d5ddb5591924b56d8b1e637fc1e3c025136fdb4fe48048db51f46523a889a3', 'netbanking', '2026-01-26 14:08:36', '2026-01-26 14:08:36', '2026-01-26 14:08:36'),
(37, 12, 12, 2, 1300000, 'success', 'order_S8RcNWq2sqHUYb', 'pay_S8RcZ2lbjJUF0e', 'd2d5ddb5591924b56d8b1e637fc1e3c025136fdb4fe48048db51f46523a889a3', 'netbanking', '2026-01-26 14:08:36', '2026-01-26 14:08:36', '2026-01-26 14:08:36'),
(38, 12, 12, 3, 1000000, 'success', 'order_S8RcNWq2sqHUYb', 'pay_S8RcZ2lbjJUF0e', 'd2d5ddb5591924b56d8b1e637fc1e3c025136fdb4fe48048db51f46523a889a3', 'netbanking', '2026-01-26 14:08:36', '2026-01-26 14:08:36', '2026-01-26 14:08:36'),
(39, 13, 13, 0, 10000, 'success', 'order_S8S2ahOvF0tFuC', 'pay_S8S2kCNRIXDoAy', 'd3c231f2ad10eb080f3c5788ebf6883622641289a76955b0727b9f3ad0faa32a', 'netbanking', '2026-01-26 14:33:23', '2026-01-26 14:33:23', '2026-01-26 14:33:23'),
(40, 13, 13, 1, 1500000, 'success', 'order_S8S3HSeK1vP2de', 'pay_S8S3TSqTIekdpS', '554cdd00c5cf0fb9f3b76593047cffff85a85f2681098c0a8b17f3a679224fb6', 'netbanking', '2026-01-26 14:34:04', '2026-01-26 14:34:04', '2026-01-26 14:34:04'),
(41, 13, 13, 2, 1300000, 'success', 'order_S8TITLCkbr7LyX', 'pay_S8TIePueP7X9Kw', '2ff355a7b23b670920d3f1b4e228b9601b8cd280e861cf7abafbcaa512730c7f', 'netbanking', '2026-01-26 15:47:09', '2026-01-26 15:47:09', '2026-01-26 15:47:09');

-- --------------------------------------------------------

--
-- Stand-in structure for view `payments_summary`
-- (See below for the actual view)
--
CREATE TABLE `payments_summary` (
`id` int(11)
,`user_id` int(11)
,`name` varchar(255)
,`term_number` int(11)
,`payment_type` varchar(50)
,`combined_terms` varchar(100)
,`amount` int(11)
,`status` enum('pending','success','failed')
,`paid_on` timestamp
,`receipt_pdf_path` varchar(500)
,`payment_description` varchar(14)
);

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admission_id` int(11) DEFAULT NULL,
  `receipt_number` varchar(100) NOT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `receipt_type` enum('SINGLE','ALL_TERMS') DEFAULT 'SINGLE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `receipts`
--

INSERT INTO `receipts` (`id`, `payment_id`, `user_id`, `admission_id`, `receipt_number`, `pdf_path`, `created_at`, `receipt_type`) VALUES
(1, 1, 1, 1, 'RCP-2026-000001', NULL, '2026-01-21 21:05:11', 'SINGLE'),
(2, 2, 2, 2, 'RCP-2026-000002', NULL, '2026-01-21 21:42:14', 'SINGLE'),
(3, 3, 5, 5, 'RCP-2026-000003', NULL, '2026-01-22 22:11:22', 'SINGLE'),
(4, 4, 6, 6, 'RCP-2026-000004', NULL, '2026-01-22 22:33:28', 'SINGLE'),
(5, 5, 6, 6, 'RCP-2026-000005', NULL, '2026-01-23 13:46:48', 'SINGLE'),
(6, 7, 6, 6, 'RCP-2026-000007', NULL, '2026-01-23 14:00:41', 'SINGLE'),
(7, 9, 6, 6, 'RCP-2026-000009', NULL, '2026-01-23 14:29:39', 'SINGLE'),
(8, 11, 6, 6, 'RCP-2026-000011', NULL, '2026-01-23 14:48:02', 'SINGLE'),
(9, 12, 6, 6, 'RCP-2026-000012', NULL, '2026-01-23 14:48:37', 'SINGLE'),
(10, 13, 7, 7, 'RCP-2026-000013', NULL, '2026-01-23 15:17:06', 'SINGLE'),
(11, 14, 7, 7, 'RCP-2026-000014', NULL, '2026-01-23 15:46:19', 'SINGLE'),
(12, 15, 7, 7, 'RCP-2026-000015', NULL, '2026-01-23 16:56:56', 'SINGLE'),
(13, 17, 7, 7, 'RCP-2026-000017', NULL, '2026-01-23 17:05:43', 'SINGLE'),
(14, 19, 8, 8, 'RCP-2026-000019', NULL, '2026-01-23 17:14:37', 'SINGLE'),
(15, 20, 8, 8, 'RCP-2026-000020', NULL, '2026-01-23 17:15:33', 'SINGLE'),
(16, 22, 8, 8, 'RCP-2026-000022', NULL, '2026-01-23 17:19:24', 'SINGLE'),
(17, 24, 9, 9, 'RCP-2026-000024', NULL, '2026-01-23 17:24:06', 'SINGLE'),
(18, 25, 9, 9, 'RCP-2026-000025', NULL, '2026-01-23 17:24:42', 'SINGLE'),
(19, 27, 9, 9, 'RCP-2026-000027', NULL, '2026-01-23 17:30:56', 'SINGLE'),
(20, 29, 10, 10, 'RCP-2026-000029', NULL, '2026-01-23 21:59:45', 'SINGLE'),
(21, 30, 10, 10, 'RCP-2026-000030', NULL, '2026-01-23 22:43:37', 'SINGLE'),
(22, 31, 10, 10, 'RCP-2026-000031', NULL, '2026-01-23 22:58:06', 'SINGLE'),
(23, 32, 11, 11, 'RCP-2026-000032', NULL, '2026-01-23 23:00:58', 'SINGLE'),
(25, 34, 11, 11, 'RCP-2026-000034', NULL, '2026-01-26 10:30:09', 'SINGLE'),
(26, 35, 12, 12, 'RCP-2026-000035', NULL, '2026-01-26 14:07:52', 'SINGLE'),
(27, 36, 12, 12, 'RCP-ALL-2026-000036', NULL, '2026-01-26 14:08:36', 'ALL_TERMS'),
(28, 37, 12, 12, 'RCP-2026-000037', NULL, '2026-01-26 14:09:16', 'SINGLE'),
(29, 38, 12, 12, 'RCP-2026-000038', NULL, '2026-01-26 14:09:23', 'SINGLE'),
(30, 39, 13, 13, 'RCP-2026-000039', NULL, '2026-01-26 14:33:23', 'SINGLE'),
(31, 40, 13, 13, 'RCP-2026-000040', NULL, '2026-01-26 14:34:04', 'SINGLE'),
(32, 41, 13, 13, 'RCP-2026-000041', NULL, '2026-01-26 15:47:09', 'SINGLE');

-- --------------------------------------------------------

--
-- Stand-in structure for view `student_payment_status`
-- (See below for the actual view)
--
CREATE TABLE `student_payment_status` (
`user_id` int(11)
,`student_name` varchar(255)
,`term_1_paid` bigint(1)
,`term_2_paid` bigint(1)
,`term_3_paid` bigint(1)
,`total_paid` decimal(22,0)
,`total_amount_paid` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `security_question` varchar(255) DEFAULT NULL,
  `security_answer` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `security_question`, `security_answer`, `created_at`) VALUES
(1, 'Muthupandi k', 'muthupandik715@gmail.com', '09943312014', '$2y$10$/zaGjhJg3nqVUAquMSDB.utn2HeQnJLBZXmSgNjqBLz22jLlWbNnG', 'muthu', 'hai', '2026-01-21 21:02:55'),
(2, 'Muthurk', 'muthurk@gmail.com', '9943312014', '$2y$10$2fKx8X9Ca5plOxOr/3doseG9gO5wvLcEf.CaR5cBSFMci78m2p3LW', 'muthu', 'oii', '2026-01-21 21:40:15'),
(3, 'rkrkrk', 'rkrkrk@gmail.com', '9943312914', '$2y$10$qQD2aFD8ubkQqBLSe75td.M7WyAjXrR1panENv1MQ1EgbxVzuLEhm', 'muthu', 'hai', '2026-01-22 14:13:22'),
(4, 'tttttt', 'ttttt@gmail.com', '9943312914', '$2y$10$Y2Y1NaomTILfNnA/5GWKT.IMem2PQh.67YhtJG74.gx8ARoReJLUK', 'rkmuthu', 'yes', '2026-01-22 15:52:36'),
(5, 'pandi', 'pandik715@gmail.com', '994331214', '$2y$10$bRpTJRx5GbwEUrzX7Atd6.lCc6PMpOjviMvE.9B9J1btUbqBn4fb.', 'muthu', 'good', '2026-01-22 21:15:22'),
(6, 'manickam', 'manickam@gmail.com', '9943312014', '$2y$10$6Zl.2O2mMbi3/dBP9pUMjuKBvFkawPjpKYU3qQstoJ.CgbjqWbdAe', 'manikam', 'manick', '2026-01-22 22:20:33'),
(7, 'rajan', 'rajan123@gmail.com', '1234567890', '$2y$10$rXxAhjsjDVbe9pN.bpFpT.eJqsKF/LeW8w5kyRNIGcEWgMLDGlfry', 'raj', '4ft', '2026-01-23 14:50:00'),
(8, 'alex', 'alex15@gmail.com', '9943312914', '$2y$10$ZHbRvS7ISKAw4jRN0Ys.du3cw18894elVSAdtOQhNcsWiLgQJVkBe', 'al', 'la', '2026-01-23 17:10:00'),
(9, 'arun', 'arun75@gmail.com', '9943312914', '$2y$10$3fSmdKZfxNB8cfVaD6qwleTCWVD4nEkQ72kbVplNAGLlj7Nsf5VUO', 'muthu', 'oii', '2026-01-23 17:22:42'),
(10, 'wwww', 'wwww75@gmail.com', '1236547890', '$2y$10$knWuhlzsQTSTylyqS9pHkusoAq4oMOsVJcwSZIoIzGYgWFL/37nui', 'muthu', 'yes', '2026-01-23 17:47:53'),
(11, 'Muthuk', 'muthuk@gmail.com', '09943312014', '$2y$10$oysG2m/wSJdrXmA3zv736eFENH0GE9Rj/fWwOoP4WbqZtCFTmRSlu', 'df', 'gh', '2026-01-23 22:59:36'),
(12, 'aac', 'aac@gmail.com', '9876543210', '$2y$10$sHUttjc97g85I..JcHk3LeO1EXx4yoZdwid2UMz9GYpJwaI0a6.Py', 'aach', 'aachostel', '2026-01-26 11:12:42'),
(13, 'aachostel', 'aachostel@gmail.com', '0321654987', '$2y$10$S3CKIBafNI6D75rC.YYxPOJjy9V5Oe89aqawwlnHeuJg2wGKK5OsO', 'hostel', 'aac', '2026-01-26 14:31:12');

-- --------------------------------------------------------

--
-- Structure for view `payments_summary`
--
DROP TABLE IF EXISTS `payments_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `payments_summary`  AS SELECT `f`.`id` AS `id`, `f`.`user_id` AS `user_id`, `u`.`name` AS `name`, `f`.`term_number` AS `term_number`, `f`.`payment_type` AS `payment_type`, `f`.`combined_terms` AS `combined_terms`, `f`.`amount` AS `amount`, `f`.`status` AS `status`, `f`.`paid_on` AS `paid_on`, `f`.`receipt_pdf_path` AS `receipt_pdf_path`, CASE WHEN `f`.`term_number` = 0 AND `f`.`combined_terms` = '2,3' THEN '2nd & 3rd Term' WHEN `f`.`term_number` = 0 AND `f`.`combined_terms` = '1,2,3' THEN 'All 3 Terms' WHEN `f`.`term_number` = 1 THEN '1st Term' WHEN `f`.`term_number` = 2 THEN '2nd Term' WHEN `f`.`term_number` = 3 THEN '3rd Term' ELSE 'Unknown' END AS `payment_description` FROM (`fees` `f` left join `users` `u` on(`f`.`user_id` = `u`.`id`)) ORDER BY `f`.`created_at` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `student_payment_status`
--
DROP TABLE IF EXISTS `student_payment_status`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `student_payment_status`  AS SELECT `u`.`id` AS `user_id`, `u`.`name` AS `student_name`, max(case when `f`.`term_number` = 1 and `f`.`status` = 'success' then 1 else 0 end) AS `term_1_paid`, max(case when `f`.`term_number` = 2 and `f`.`status` = 'success' then 1 else 0 end) AS `term_2_paid`, max(case when `f`.`term_number` = 3 and `f`.`status` = 'success' then 1 else 0 end) AS `term_3_paid`, sum(case when `f`.`status` = 'success' then 1 else 0 end) AS `total_paid`, sum(case when `f`.`status` = 'success' then `f`.`amount` else 0 end) AS `total_amount_paid` FROM (`users` `u` left join `fees` `f` on(`u`.`id` = `f`.`user_id`)) GROUP BY `u`.`id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `admission`
--
ALTER TABLE `admission`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `razorpay_payment_id` (`razorpay_payment_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_admission_id` (`admission_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_term_number` (`term_number`),
  ADD KEY `idx_order_id` (`razorpay_order_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_payment_instalment` (`razorpay_payment_id`,`instalment_number`),
  ADD KEY `fk_payment_user` (`user_id`),
  ADD KEY `fk_payment_admission` (`admission_id`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipt_number` (`receipt_number`),
  ADD KEY `fk_receipt_payment` (`payment_id`),
  ADD KEY `fk_receipt_user` (`user_id`),
  ADD KEY `fk_receipt_admission` (`admission_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admission`
--
ALTER TABLE `admission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admission`
--
ALTER TABLE `admission`
  ADD CONSTRAINT `fk_admission_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fees`
--
ALTER TABLE `fees`
  ADD CONSTRAINT `fees_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fees_ibfk_2` FOREIGN KEY (`admission_id`) REFERENCES `admission` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payment_admission` FOREIGN KEY (`admission_id`) REFERENCES `admission` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_payment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `fk_receipt_admission` FOREIGN KEY (`admission_id`) REFERENCES `admission` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_receipt_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_receipt_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
