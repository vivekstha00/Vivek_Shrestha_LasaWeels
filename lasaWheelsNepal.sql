-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 03, 2026 at 11:12 AM
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
-- Database: `lasaWheelsNepal`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` varchar(300) DEFAULT NULL,
  `content` longtext NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `driver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `service` enum('self','driver') NOT NULL DEFAULT 'self',
  `pickup_location` varchar(255) NOT NULL,
  `drop_location` varchar(255) NOT NULL,
  `pickup_datetime` datetime NOT NULL,
  `drop_datetime` datetime NOT NULL,
  `special_request` text DEFAULT NULL,
  `status` enum('pending','confirmed','active','completed','cancel_requested','cancelled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid',
  `original_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_type` enum('none','loyalty','code') NOT NULL DEFAULT 'none',
  `discount_code` varchar(255) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `loyalty_points_earned` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `loyalty_points_redeemed` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `loyalty_discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `security_deposit` decimal(10,2) DEFAULT NULL,
  `reminder_sent_at` timestamp NULL DEFAULT NULL,
  `loyalty_processed_at` timestamp NULL DEFAULT NULL,
  `cancellation_requested_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `vehicle_id`, `user_id`, `driver_id`, `service`, `pickup_location`, `drop_location`, `pickup_datetime`, `drop_datetime`, `special_request`, `status`, `payment_status`, `original_price`, `discount_amount`, `discount_type`, `discount_code`, `total_price`, `loyalty_points_earned`, `loyalty_points_redeemed`, `loyalty_discount_amount`, `security_deposit`, `reminder_sent_at`, `loyalty_processed_at`, `cancellation_requested_at`, `cancelled_at`, `cancelled_by`, `cancellation_reason`, `created_at`, `updated_at`) VALUES
(8, 6, 14, NULL, 'self', 'pokhara', 'Kathmandu, Bagmati Province, Nepal', '2026-04-04 08:25:00', '2026-04-05 09:25:00', NULL, 'cancelled', 'paid', 15000.00, 2000.00, 'code', 'WEEKEND2000', 13000.00, 0, 0, 0.00, 0.00, NULL, NULL, '2026-03-31 02:13:18', '2026-03-31 02:13:43', 11, 'gf broke up', '2026-03-31 02:11:28', '2026-03-31 02:13:43'),
(9, 7, 10, NULL, 'self', 'Butwal, Lumbini Province, Nepal', 'Palpa, Lumbini Province, Nepal', '2026-04-01 22:47:00', '2026-04-02 23:47:00', NULL, 'confirmed', 'paid', 27000.00, 3000.00, 'code', 'NEWYEAR26', 24000.00, 0, 0, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-31 16:33:35', '2026-03-31 16:35:05');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_requests`
--

CREATE TABLE `contact_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `booking_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `reply_message` text DEFAULT NULL,
  `status` enum('pending','assigned','replied','closed') NOT NULL DEFAULT 'pending',
  `replied_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_requests`
--

INSERT INTO `contact_requests` (`id`, `user_id`, `email`, `phone`, `vendor_id`, `booking_id`, `subject`, `message`, `reply_message`, `status`, `replied_at`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, NULL, NULL, NULL, 'hoe to book the vehicle', 'I am getting to choose vehicle', 'Ok we will send you the details', 'replied', '2026-03-27 20:42:22', '2026-03-27 19:36:50', '2026-03-27 20:42:22'),
(2, NULL, 'abc@gmail.coom', '98765432234', NULL, NULL, 'Retnal process', 'can you helpmeet to figure it out t', 'ok sure', 'replied', '2026-03-31 17:05:29', '2026-03-29 13:21:13', '2026-03-31 17:05:29'),
(3, 10, 'bs2288175@gmail.com', '9846483653', NULL, NULL, 'how to find', 'wcevv', 'wvw', 'replied', '2026-03-31 17:06:38', '2026-03-31 17:06:24', '2026-03-31 17:06:38');

-- --------------------------------------------------------

--
-- Table structure for table `discount_codes`
--

CREATE TABLE `discount_codes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `type` enum('fixed','percentage') NOT NULL DEFAULT 'percentage',
  `value` decimal(10,2) NOT NULL,
  `max_discount_amount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(10) UNSIGNED DEFAULT NULL,
  `used_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `valid_from` timestamp NULL DEFAULT NULL,
  `valid_until` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `discount_codes`
--

INSERT INTO `discount_codes` (`id`, `title`, `code`, `type`, `value`, `max_discount_amount`, `usage_limit`, `used_count`, `valid_from`, `valid_until`, `is_active`, `description`, `created_at`, `updated_at`) VALUES
(1, 'New Year Special 2026', 'NEWYEAR26', 'percentage', 15.00, 3000.00, 50, 2, '2026-03-25 20:54:00', '2026-04-25 20:54:00', 1, 'New year promotional offer for first set of users.', '2026-03-25 20:54:33', '2026-03-31 16:35:05'),
(2, 'Weekend Flat Rs 2000 Off', 'WEEKEND2000', 'fixed', 2000.00, NULL, 30, 2, '2026-03-25 20:55:00', '2026-04-02 20:55:00', 1, 'Flat discount for weekend bookings above minimum rental value.', '2026-03-25 20:55:19', '2026-03-31 17:21:47'),
(3, 'wevfw', 'WEVWEV33', 'fixed', 3223.00, 3223.00, 3, 0, '2026-03-31 17:04:00', '2026-04-01 17:04:00', 1, 'fd', '2026-03-31 17:04:36', '2026-03-31 17:04:36'),
(5, 'vgd c', 'WEEKEND200034', 'percentage', 1223.00, 122.00, 2123, 0, '2026-03-01 17:23:00', '2026-03-03 17:23:00', 1, 'dfghjhg', '2026-03-31 17:23:39', '2026-03-31 17:23:39');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `purpose` varchar(255) NOT NULL DEFAULT 'user_verification',
  `type` varchar(255) NOT NULL,
  `document_number` varchar(255) DEFAULT NULL,
  `issued_at` date DEFAULT NULL,
  `expires_at` date DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `user_id`, `purpose`, `type`, `document_number`, `issued_at`, `expires_at`, `file_path`, `original_name`, `file_type`, `file_size`, `status`, `reviewed_by`, `reviewed_at`, `remarks`, `created_at`, `updated_at`) VALUES
(19, 13, 'vendor_verification', 'national_id', NULL, NULL, NULL, 'vendor-documents/gO2RUmWNzJRvBZlXt0fsDF9TpVSmobLSudh4YGBE.jpg', NULL, 'image/png', NULL, 'approved', NULL, NULL, NULL, '2026-03-30 17:55:00', '2026-03-30 17:55:11'),
(20, 13, 'vendor_verification', 'business_license', NULL, NULL, NULL, 'vendor-documents/r7TBLOBCEMWs7Pgb38Rp26K2VyO6v9gBDV3hwa3R.jpg', NULL, 'image/png', NULL, 'approved', NULL, NULL, NULL, '2026-03-30 17:55:00', '2026-03-30 17:55:11'),
(21, 13, 'vendor_verification', 'tax_certificate', NULL, NULL, NULL, 'vendor-documents/lWw3ixny7W8XhAEPoNRQvRwmPPu3SwXGbIBzqoqe.jpg', NULL, 'image/png', NULL, 'approved', NULL, NULL, NULL, '2026-03-30 17:55:00', '2026-03-30 17:55:11'),
(22, 13, 'vendor_verification', 'proof_of_address', NULL, NULL, NULL, 'vendor-documents/ZKZtSOGnJPSk7OwIuOGeChw2kLJn3RjChpjO29HC.jpg', NULL, 'image/png', NULL, 'approved', NULL, NULL, NULL, '2026-03-30 17:55:00', '2026-03-30 17:55:11'),
(23, 14, 'user_verification', 'license', '76543', '2026-03-01', '2026-05-03', 'documents/14/tLZjVoM2j7ukri1QjcX4I2q9cCyQcgu5DQbwvAFt.jpg', NULL, NULL, NULL, 'approved', 11, '2026-03-31 02:09:58', NULL, '2026-03-31 02:09:19', '2026-03-31 02:09:58'),
(24, 14, 'user_verification', 'citizenship', '76543', NULL, NULL, 'documents/14/xpJR4ZfJ2p42toeNsdBqNfoVfOmI2hhyDkHiSPMm.jpg', NULL, NULL, NULL, 'approved', 11, '2026-03-31 02:09:56', NULL, '2026-03-31 02:09:26', '2026-03-31 02:09:56'),
(25, 10, 'user_verification', 'license', '54345', '2026-03-01', '2026-05-01', 'documents/10/iDp55swWQ7TeW1I4vkDP9EjE1ZDTg7JxFbzRP05n.jpg', NULL, NULL, NULL, 'approved', 11, '2026-03-31 16:12:25', NULL, '2026-03-31 09:23:31', '2026-03-31 16:12:25'),
(26, 10, 'user_verification', 'citizenship', '65432', NULL, NULL, 'documents/10/Zs8OUKDRC2sVoIppHJrdYKSdA7GNd4Hj91AhAAeL.jpg', NULL, NULL, NULL, 'approved', 11, '2026-03-31 16:12:23', NULL, '2026-03-31 09:23:39', '2026-03-31 16:12:23'),
(27, 15, 'vendor_verification', 'national_id', NULL, NULL, NULL, 'vendor-documents/8IzudSgQ0M9CxRmKrsu5l0mYMy0YL6VtroBwOMmE.jpg', NULL, 'image/jpeg', NULL, 'approved', NULL, NULL, NULL, '2026-03-31 18:21:09', '2026-03-31 18:21:48'),
(28, 15, 'vendor_verification', 'business_license', NULL, NULL, NULL, 'vendor-documents/ievClVvzX8EiHt1ubuVdlCs5F4NmUO7ire6y8dpq.jpg', NULL, 'image/jpeg', NULL, 'approved', NULL, NULL, NULL, '2026-03-31 18:21:09', '2026-03-31 18:21:48'),
(29, 15, 'vendor_verification', 'tax_certificate', NULL, NULL, NULL, 'vendor-documents/Fg6nRjbzlDkLl1k0KuVlCgskV3nC8qopJauZzVpk.jpg', NULL, 'image/jpeg', NULL, 'approved', NULL, NULL, NULL, '2026-03-31 18:21:09', '2026-03-31 18:21:48'),
(30, 15, 'vendor_verification', 'proof_of_address', NULL, NULL, NULL, 'vendor-documents/uLsqNmmeE8wDzzGKq1mLJNIaxtw1I1OV0uXOiDKf.jpg', NULL, 'image/jpeg', NULL, 'approved', NULL, NULL, NULL, '2026-03-31 18:21:09', '2026-03-31 18:21:48'),
(31, 17, 'vendor_verification', 'national_id', NULL, NULL, NULL, 'vendor-documents/jjAuNzpWUe0lYsdvPTAoOlBAfEph4YYDPaCtogAa.jpg', NULL, 'image/jpeg', NULL, 'pending', NULL, NULL, NULL, '2026-04-01 05:31:42', '2026-04-01 05:31:42'),
(32, 17, 'vendor_verification', 'business_license', NULL, NULL, NULL, 'vendor-documents/zg1hlPWT5PQtadsmduvaSZakEtfoVeuFMAokp3er.jpg', NULL, 'image/jpeg', NULL, 'pending', NULL, NULL, NULL, '2026-04-01 05:31:42', '2026-04-01 05:31:42'),
(33, 17, 'vendor_verification', 'tax_certificate', NULL, NULL, NULL, 'vendor-documents/IAYOfYJOPRVJz82HFJXItLF2AaSEtyPf0Zs2vKFd.jpg', NULL, 'image/jpeg', NULL, 'pending', NULL, NULL, NULL, '2026-04-01 05:31:42', '2026-04-01 05:31:42'),
(34, 17, 'vendor_verification', 'proof_of_address', NULL, NULL, NULL, 'vendor-documents/vyRZ2rW3UcV9Q80lw457VTooPUiGp5a73mdJrhUR.jpg', NULL, 'image/jpeg', NULL, 'pending', NULL, NULL, NULL, '2026-04-01 05:31:42', '2026-04-01 05:31:42');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `license_number` varchar(255) NOT NULL,
  `availability_status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  `rating` decimal(2,1) DEFAULT NULL,
  `status` enum('approved','removed') NOT NULL DEFAULT 'approved',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `vendor_id`, `name`, `phone`, `license_number`, `availability_status`, `rating`, `status`, `image`, `created_at`, `updated_at`) VALUES
(3, 13, 'Ramesh Gurung', '9876543223', '123-134-35456', 'available', 4.0, 'approved', 'drivers/IeBokj5roMdNxkK1PAU6vzut4EWsDlXweBqDUaRl.jpg', '2026-03-31 17:02:03', '2026-03-31 17:02:03'),
(4, 15, 'Journal Management', '+1234567890', '123-324-3523', 'available', 4.0, 'approved', 'drivers/yeNJoMy28soI78KcSM4LoSbqD58GljTlc9dNDHSg.jpg', '2026-03-31 18:32:41', '2026-03-31 18:32:41');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_accounts`
--

CREATE TABLE `loyalty_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `available_points` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `lifetime_earned_points` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `lifetime_redeemed_points` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `tier` enum('bronze','silver','gold') NOT NULL DEFAULT 'bronze',
  `completed_bookings_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `yearly_spend` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_transactions`
--

CREATE TABLE `loyalty_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED DEFAULT NULL,
  `event_key` varchar(255) NOT NULL,
  `type` enum('earn_booking','earn_first_booking_bonus','earn_review_bonus','redeem','restore_redemption','manual_adjustment') NOT NULL,
  `points` int(11) NOT NULL,
  `amount_npr` decimal(10,2) DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_12_29_083447_create_documents_table', 1),
(5, '2026_01_31_181724_create_vendor_profiles_table', 1),
(6, '2026_02_04_152124_create_vehicles_table', 1),
(7, '2026_02_05_075217_create_vehicle_services_table', 1),
(8, '2026_02_16_094547_create_vehicle_images_table', 1),
(9, '2026_02_23_094220_create_blog_posts_table', 1),
(10, '2026_02_28_073226_create_driver_table', 1),
(11, '2026_02_28_133548_create_bookings_table', 1),
(12, '2026_02_28_190829_create_contact_requests_table', 1),
(13, '2026_03_03_145227_create_payments_table', 1),
(14, '2026_03_10_101019_create_vehicle_service_items_table', 1),
(15, '2026_03_13_164527_create_reviews_table', 1),
(16, '2026_03_20_152032_create_loyalty_accounts_table', 1),
(17, '2026_03_20_152038_create_loyalty_transactions_table', 1),
(18, '2026_03_22_124834_create_discount_codes_table', 1),
(19, '2026_03_22_234525_create_subscription_plans_table', 1),
(20, '2026_03_23_000747_create_vendor_subscriptions_table', 1),
(21, '2026_03_23_225436_create_subscription_payments_table', 1),
(22, '2026_03_26_095236_add_location_fields_to_vendor_profiles_table', 2),
(23, '2026_03_29_000001_update_contact_requests_for_guest_support', 3);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(30) NOT NULL,
  `payment_type` varchar(30) NOT NULL DEFAULT 'full_online',
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remaining_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deposit_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `deposit_status` varchar(30) DEFAULT NULL,
  `settlement_status` varchar(50) DEFAULT NULL,
  `platform_commission` decimal(10,2) NOT NULL DEFAULT 0.00,
  `vendor_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payout_status` varchar(30) NOT NULL DEFAULT 'unpaid',
  `gateway_reference` varchar(255) DEFAULT NULL,
  `gateway_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_payload`)),
  `paid_at` timestamp NULL DEFAULT NULL,
  `refund_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `refund_status` enum('none','pending','refunded','rejected') NOT NULL DEFAULT 'none',
  `refund_requested_at` timestamp NULL DEFAULT NULL,
  `refund_processed_at` timestamp NULL DEFAULT NULL,
  `refund_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `vendor_id`, `booking_id`, `amount`, `method`, `payment_type`, `paid_amount`, `remaining_amount`, `deposit_amount`, `status`, `deposit_status`, `settlement_status`, `platform_commission`, `vendor_amount`, `payout_status`, `gateway_reference`, `gateway_payload`, `paid_at`, `refund_amount`, `refund_status`, `refund_requested_at`, `refund_processed_at`, `refund_note`, `created_at`, `updated_at`) VALUES
(8, 14, 13, 8, 13000.00, 'khalti', 'full_online', 13000.00, 0.00, 13000.00, 'refunded', 'paid', 'refunded', 1500.00, 13500.00, 'hold', 'nFz7HcTHsVmWc26PJt5zUo', '{\"pidx\":\"nFz7HcTHsVmWc26PJt5zUo\",\"total_amount\":1300000,\"status\":\"Completed\",\"transaction_id\":\"HmfDsmY8TeZ7vgHFmyoCzg\",\"fee\":0,\"refunded\":false}', '2026-03-31 02:12:12', 13000.00, 'refunded', '2026-03-31 02:13:18', '2026-03-31 02:13:43', 'gf broke up', '2026-03-31 02:11:51', '2026-03-31 02:13:43'),
(9, 10, 13, 9, 24000.00, 'khalti', 'full_online', 24000.00, 0.00, 24000.00, 'completed', 'paid', 'paid_to_vendor', 2700.00, 24300.00, 'paid', 'u25UbseLuTZqcCsxuWJbfA', '{\"pidx\":\"u25UbseLuTZqcCsxuWJbfA\",\"total_amount\":2400000,\"status\":\"Completed\",\"transaction_id\":\"aC4TN3F8yiaZsjMwsaRJZe\",\"fee\":0,\"refunded\":false}', '2026-03-31 16:35:05', 0.00, 'none', NULL, NULL, NULL, '2026-03-31 16:34:14', '2026-03-31 17:07:53');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `driver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `overall_rating` tinyint(3) UNSIGNED NOT NULL,
  `overall_review` text DEFAULT NULL,
  `vehicle_rating` tinyint(3) UNSIGNED NOT NULL,
  `vehicle_review` text DEFAULT NULL,
  `driver_rating` tinyint(3) UNSIGNED DEFAULT NULL,
  `driver_review` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1lbPIkTNJG0ounMbb3KS8tnjt2PqwayFKDj6C9rQ', 11, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3.1 Safari/605.1.15', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWDNQYnZTWU0yOEVVMVdCUlpKenJHdThsRjdKVDExbTBxNURUcXdqQyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjExO30=', 1775145091),
('QrHD409O2ksHb7T0ZSx824KhEP3cBafZEvDUOaKj', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUXNBMEhCcWM4cUdLT0lEbkFWUjFndHZNSXR0OXRpQ3NTUUZtblZsUiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fX0=', 1775144444);

-- --------------------------------------------------------

--
-- Table structure for table `subscription_payments`
--

CREATE TABLE `subscription_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_plan_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_gateway` varchar(30) NOT NULL DEFAULT 'khalti',
  `purchase_order_id` varchar(255) DEFAULT NULL,
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
  `gateway_reference` varchar(255) DEFAULT NULL,
  `gateway_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_payload`)),
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_payments`
--

INSERT INTO `subscription_payments` (`id`, `vendor_id`, `subscription_plan_id`, `amount`, `payment_gateway`, `purchase_order_id`, `status`, `gateway_reference`, `gateway_payload`, `paid_at`, `created_at`, `updated_at`) VALUES
(2, 13, 2, 4999.00, 'khalti', 'SUB-2-1774922287', 'pending', NULL, NULL, NULL, '2026-03-31 01:58:07', '2026-03-31 01:58:07'),
(3, 13, 2, 4999.00, 'khalti', 'SUB-3-1774943577', 'completed', 'RKhXfa73qJVHfNK59XxtgC', '{\"pidx\":\"RKhXfa73qJVHfNK59XxtgC\",\"total_amount\":499900,\"status\":\"Completed\",\"transaction_id\":\"KWs9TwWwuzGfcYfTM2ykuG\",\"fee\":0,\"refunded\":false}', '2026-03-31 07:53:21', '2026-03-31 07:52:57', '2026-03-31 07:53:21'),
(4, 13, 2, 4999.00, 'khalti', 'SUB-4-1774943621', 'pending', NULL, NULL, NULL, '2026-03-31 07:53:41', '2026-03-31 07:53:41'),
(5, 15, 3, 49999.00, 'khalti', 'SUB-5-1774981843', 'completed', 'HgTzXVFZrWLu7WLXeTgEfB', '{\"pidx\":\"HgTzXVFZrWLu7WLXeTgEfB\",\"total_amount\":4999900,\"status\":\"Completed\",\"transaction_id\":\"uEEzJjRxsB8WU24T4hdtFK\",\"fee\":0,\"refunded\":false}', '2026-03-31 18:31:00', '2026-03-31 18:30:43', '2026-03-31 18:31:00');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `billing_cycle` enum('free','monthly','yearly') NOT NULL DEFAULT 'free',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_vehicles` int(10) UNSIGNED NOT NULL DEFAULT 2,
  `max_drivers` int(10) UNSIGNED NOT NULL DEFAULT 2,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_plans`
--

INSERT INTO `subscription_plans` (`id`, `name`, `slug`, `billing_cycle`, `price`, `max_vehicles`, `max_drivers`, `is_active`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Free Plan', 'free-plan', 'free', 0.00, 2, 2, 1, 'Starter plan for new vendors. Allows up to 2 vehicles and 2 drivers', '2026-03-25 20:35:01', '2026-03-25 20:35:01'),
(2, 'Basic Monthly', 'basic-monthly', 'monthly', 4999.00, 10, 8, 1, 'Best for small rental vendors needing more fleet and driver capacity.', '2026-03-25 20:35:41', '2026-03-25 20:35:41'),
(3, 'Premium Yearly', 'premium-yearly', 'yearly', 49999.00, 50, 40, 1, 'Full-feature yearly plan for growing rental businesses with larger fleet operations.\r\nActive now: checked', '2026-03-25 20:36:27', '2026-03-25 20:36:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `vendor_status` varchar(255) DEFAULT NULL,
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `verification_note` text DEFAULT NULL,
  `loyalty_points` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `loyalty_tier` varchar(255) NOT NULL DEFAULT 'bronze',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `profile_image`, `email_verified_at`, `password`, `remember_token`, `role`, `status`, `vendor_status`, `verified_by`, `verified_at`, `verification_note`, `loyalty_points`, `loyalty_tier`, `created_at`, `updated_at`) VALUES
(10, 'Vivek Shrestha', 'bs2288175@gmail.com', '9846483653', 'Pokhara', NULL, NULL, '$2y$12$VNtGd.FRiwbI/YENHP6eqOr6lF.r5sTu6EZs0UQUGSJ3o5EX1vEoG', NULL, 'user', 'approved', NULL, NULL, NULL, NULL, 0, 'bronze', '2026-03-30 16:06:35', '2026-03-31 16:13:42'),
(11, 'Bibek Shrestha', 'vivekstha00@gmail.com', '9800009988', NULL, NULL, NULL, '$2y$12$Yz3tfZ5EZWQ2BnFFKxOAX.2tqeDI/77YlTIigB9OJX6ebz4VxdHS6', NULL, 'admin', 'approved', NULL, NULL, NULL, NULL, 0, 'bronze', '2026-03-30 16:08:39', '2026-03-30 16:08:39'),
(13, 'Yam Kala', 'yamkala1979@gmail.com', '9812871276', NULL, NULL, NULL, '$2y$12$YOgY/F1.2PURY4lfOTj.TOi841Lgcv8iVF.hd1.bykRloxU7dzSX2', NULL, 'vendor', 'active', 'approved', NULL, NULL, NULL, 0, 'bronze', '2026-03-30 17:53:09', '2026-03-30 17:55:11'),
(14, 'Rohit giri', 'rohitgeree727@gmail.com', '9819135346', NULL, NULL, NULL, '$2y$12$dT1aq/01HKo.wy92I7chiOYECa14c.AF.IbsJ1swZlXwCwllh9lgq', NULL, 'user', 'approved', NULL, NULL, NULL, NULL, 0, 'bronze', '2026-03-31 02:02:33', '2026-03-31 02:02:33'),
(15, 'Ram Thapa', 'vivek.shrestha.a23@icp.edu.np', '987656733', NULL, NULL, NULL, '$2y$12$7wfjoGI/s8zE8a7U46GVF.mVt1WNiJ2X/l.X3A7eIHIvFDusuJaHe', NULL, 'vendor', 'active', 'approved', NULL, NULL, NULL, 0, 'bronze', '2026-03-31 18:15:11', '2026-03-31 18:21:48'),
(17, 'Ramesh Gurung', 'ab@gmail.com', '9876543211', NULL, NULL, NULL, '$2y$12$xNvp4w0jvNlPc5cGGK9Q/eDmIzHqirNfGaE0aY8ZIMP3zL/r37.bq', NULL, 'vendor', 'active', 'pending', NULL, NULL, NULL, 0, 'bronze', '2026-04-01 05:25:50', '2026-04-01 05:31:45'),
(18, 'Vivek Shrestha', 'vivek00@gmail.com', '9846464646', NULL, NULL, NULL, '$2y$12$lFryvLaD8olwra/RiNWn/eNrPqmZPQmpieVdrY2Yt/5m4mNEaFne.', NULL, 'user', 'approved', NULL, NULL, NULL, NULL, 0, 'bronze', '2026-04-01 10:41:18', '2026-04-01 10:41:18'),
(19, 'abcd', 'aaa@gmail.com', '9876543212', NULL, NULL, NULL, '$2y$12$jZQsDN8bjiKNKnStsTIRZOGPDYU/e7R0qFOtpAKAKo1bj6d24qR9C', NULL, 'vendor', 'active', 'draft', NULL, NULL, NULL, 0, 'bronze', '2026-04-02 02:47:59', '2026-04-02 02:48:08');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `wheel_type` varchar(255) DEFAULT NULL,
  `vehicle_type` varchar(255) NOT NULL,
  `brand` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `variant` varchar(255) DEFAULT NULL,
  `registration_no` varchar(255) NOT NULL,
  `manufacture_year` year(4) DEFAULT NULL,
  `fuel_type` varchar(255) NOT NULL,
  `transmission` varchar(255) NOT NULL,
  `seating_capacity` tinyint(3) UNSIGNED NOT NULL DEFAULT 4,
  `mileage_per_litre` decimal(5,2) DEFAULT NULL,
  `fuel_tank_capacity` decimal(5,2) DEFAULT NULL,
  `battery_capacity` decimal(6,2) DEFAULT NULL,
  `range_per_charge` decimal(6,2) DEFAULT NULL,
  `charging_time` decimal(5,2) DEFAULT NULL,
  `charger_type` varchar(255) DEFAULT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'NPR',
  `price_per_day` decimal(10,2) NOT NULL,
  `with_driver_price_per_day` decimal(10,2) DEFAULT NULL,
  `security_deposit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_15_days` decimal(5,2) NOT NULL DEFAULT 0.00,
  `discount_30_days` decimal(5,2) NOT NULL DEFAULT 0.00,
  `discount_60_days` decimal(5,2) NOT NULL DEFAULT 0.00,
  `location_city` varchar(255) NOT NULL,
  `location_area` varchar(255) DEFAULT NULL,
  `pickup_address` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `reject_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vendor_id`, `title`, `wheel_type`, `vehicle_type`, `brand`, `model`, `variant`, `registration_no`, `manufacture_year`, `fuel_type`, `transmission`, `seating_capacity`, `mileage_per_litre`, `fuel_tank_capacity`, `battery_capacity`, `range_per_charge`, `charging_time`, `charger_type`, `currency`, `price_per_day`, `with_driver_price_per_day`, `security_deposit`, `discount_15_days`, `discount_30_days`, `discount_60_days`, `location_city`, `location_area`, `pickup_address`, `image_url`, `description`, `status`, `is_active`, `approved_by`, `approved_at`, `reject_reason`, `created_at`, `updated_at`) VALUES
(6, 13, 'KTM ADV 250 (BIKE)', '2_wheeler', 'bike', 'KTM', 'ADV 250', NULL, 'BA 18 CHA 7744', '2025', 'petrol', 'manual', 2, 12.00, 14.00, NULL, NULL, NULL, NULL, 'NPR', 7500.00, NULL, 0.00, 4.00, 8.00, 12.00, 'pokhara', NULL, NULL, 'vehicles/sBD00tq1HNGNA3FjpaqNrNblofgdwkbkQMo1Djv7.jpg', 'afbqwbfqwbf', 'available', 1, NULL, NULL, NULL, '2026-03-31 02:01:41', '2026-03-31 02:01:41'),
(7, 13, 'Deepal SO7 (CAR)', '4_wheeler', 'car', 'Deepal', 'SO7', NULL, 'BA 1234 111', '2025', 'electric', 'automatic', 5, NULL, NULL, 60.00, 500.00, 2.00, 'CCS2 Fast Charging / Type 2', 'NPR', 13500.00, 15000.00, 0.00, 4.00, 8.00, 12.00, 'Pokhara', NULL, NULL, 'vehicles/dPnmjCuxuCZMoFfzWkav20whNiUTmUV9xnesnJLJ.jpg', NULL, 'available', 1, NULL, NULL, NULL, '2026-03-31 16:30:16', '2026-03-31 16:30:16'),
(8, 15, 'BYD Atto 3 (EV)', '4_wheeler', 'ev', 'BYD', 'Atto 3', NULL, 'GA-6-CHA-111', '2025', 'electric', 'automatic', 5, NULL, NULL, 97.00, 467.00, 2.50, 'CCS Fast Charging', 'NPR', 12000.00, 15000.00, 0.00, 4.00, 8.00, 12.00, 'KTM', NULL, NULL, 'vehicles/xTezO7SWIV25YhszKeVF7PJQ3E1Cy5DllaXBx9pf.jpg', 'this is the mid range ev section with comfort', 'available', 1, NULL, NULL, NULL, '2026-03-31 18:24:00', '2026-03-31 18:24:00'),
(9, 15, 'TVS iQube (SCOOTER)', '2_wheeler', 'scooter', 'TVS', 'iQube', NULL, 'GA-4-CHA-1122', '2025', 'electric', 'automatic', 2, NULL, NULL, 3.40, 100.00, 5.00, 'Type 2', 'NPR', 3000.00, NULL, 0.00, 4.00, 8.00, 12.00, 'Pokhara', NULL, NULL, 'vehicles/MriTEctXCAbm0csTMdBXqsX9eb1V3oCeHyWk7S0U.jpg', 'Smart electric scooter ideal for city commuting with smooth ride and low running cost.', 'approved', 1, 11, '2026-04-02 14:01:21', NULL, '2026-04-01 20:30:13', '2026-04-02 14:01:21'),
(10, 15, 'Aprilia SR 150 (SCOOTER)', '2_wheeler', 'scooter', 'Aprilia', 'SR 150', NULL, 'GA-9-CHA-150', '2020', 'petrol', 'manual', 2, 21.00, 6.00, NULL, NULL, NULL, NULL, 'NPR', 2999.00, NULL, 0.00, 3.00, 6.00, 9.00, 'Pokhara', NULL, NULL, 'vehicles/3lRxlPqd5tIyptigm3roEkfvaQVAp5gD1TuVDouM.jpg', 'Sporty performance scooter with strong pickup and stylish design, perfect for urban rides.', 'approved', 1, 11, '2026-04-02 14:01:19', NULL, '2026-04-01 20:35:06', '2026-04-02 14:01:19');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_images`
--

CREATE TABLE `vehicle_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_images`
--

INSERT INTO `vehicle_images` (`id`, `vehicle_id`, `path`, `is_primary`, `created_at`, `updated_at`) VALUES
(12, 6, 'vehicles/sBD00tq1HNGNA3FjpaqNrNblofgdwkbkQMo1Djv7.jpg', 1, '2026-03-31 02:01:41', '2026-03-31 02:01:41'),
(13, 6, 'vehicles/3Eoztzw01uZupaJkIZ2ClZidOobQ5lz2SLqAxzmx.jpg', 0, '2026-03-31 02:01:41', '2026-03-31 02:01:41'),
(14, 7, 'vehicles/dPnmjCuxuCZMoFfzWkav20whNiUTmUV9xnesnJLJ.jpg', 1, '2026-03-31 16:30:16', '2026-03-31 16:30:16'),
(15, 7, 'vehicles/PheXm0zW9qFyAlNH1dmLaUtSEr4y7fHoBP3M2jLu.jpg', 0, '2026-03-31 16:30:16', '2026-03-31 16:30:16'),
(16, 8, 'vehicles/xTezO7SWIV25YhszKeVF7PJQ3E1Cy5DllaXBx9pf.jpg', 1, '2026-03-31 18:24:00', '2026-03-31 18:24:00'),
(17, 8, 'vehicles/VZ0JoAxu749NDzUyQjrvbwH6Vv8ndkDugV1d2wiZ.jpg', 0, '2026-03-31 18:24:00', '2026-03-31 18:24:00'),
(18, 8, 'vehicles/YMCASlJhZSuSnz6WkkCmYS3LeMlpQsdST9pNei3i.jpg', 0, '2026-03-31 18:24:00', '2026-03-31 18:24:00'),
(19, 9, 'vehicles/MriTEctXCAbm0csTMdBXqsX9eb1V3oCeHyWk7S0U.jpg', 1, '2026-04-01 21:36:45', '2026-04-01 21:36:45'),
(20, 9, 'vehicles/pxs0wF10U3kDY0dalC97vkEvI4UdsFwonl8JcQhY.jpg', 0, '2026-04-01 21:36:45', '2026-04-01 21:36:45'),
(21, 9, 'vehicles/xe3vbs1ZCJPj5M1hJpHp1d0zylnz4IzP8OIFdxXO.jpg', 0, '2026-04-01 21:36:45', '2026-04-01 21:36:45'),
(22, 9, 'vehicles/Gqw3ZHktdkZenVrQLn16a4P6uS03IEbNGEjs2OED.jpg', 0, '2026-04-01 21:36:45', '2026-04-01 21:36:45'),
(23, 10, 'vehicles/3lRxlPqd5tIyptigm3roEkfvaQVAp5gD1TuVDouM.jpg', 1, '2026-04-01 21:37:08', '2026-04-01 21:37:08'),
(24, 10, 'vehicles/s9xaFau9m5LnrBGdT10XKT3QMmpItEJR5TtTV2uA.jpg', 0, '2026-04-01 21:37:08', '2026-04-01 21:37:08'),
(25, 10, 'vehicles/qXJPNPSM50KkGv5CHxS9vji4bdBE8DnbE5aLUse7.jpg', 0, '2026-04-01 21:37:08', '2026-04-01 21:37:08');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_services`
--

CREATE TABLE `vehicle_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `service_date` date NOT NULL,
  `service_type` varchar(255) NOT NULL,
  `odometer_km` int(10) UNSIGNED DEFAULT NULL,
  `cost` int(10) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `next_service_due_date` date DEFAULT NULL,
  `next_service_due_km` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_services`
--

INSERT INTO `vehicle_services` (`id`, `vehicle_id`, `service_date`, `service_type`, `odometer_km`, `cost`, `notes`, `next_service_due_date`, `next_service_due_km`, `created_at`, `updated_at`) VALUES
(1, 7, '2026-03-22', 'battery_health_check, battery_cooling_service, motor_inspection, charging_port_check, regenerative_braking_check, electrical_wiring_inspection, brake_service', 12000, 32000, 'there is no major issue all good', '2026-05-30', 20000, '2026-03-31 16:32:18', '2026-03-31 16:32:18'),
(2, 8, '2026-03-01', 'battery_health_check, battery_cooling_service, motor_inspection, controller_diagnostics, charging_port_check, electrical_wiring_inspection, suspension_check', 11378, 5500, 'All the necessary check up was done', '2026-06-15', 21000, '2026-04-01 20:14:59', '2026-04-01 20:14:59');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_service_items`
--

CREATE TABLE `vehicle_service_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_service_id` bigint(20) UNSIGNED NOT NULL,
  `service_item` varchar(255) NOT NULL,
  `is_custom` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_service_items`
--

INSERT INTO `vehicle_service_items` (`id`, `vehicle_service_id`, `service_item`, `is_custom`, `created_at`, `updated_at`) VALUES
(1, 1, 'battery_health_check', 0, '2026-03-31 16:32:18', '2026-03-31 16:32:18'),
(2, 1, 'battery_cooling_service', 0, '2026-03-31 16:32:18', '2026-03-31 16:32:18'),
(3, 1, 'motor_inspection', 0, '2026-03-31 16:32:18', '2026-03-31 16:32:18'),
(4, 1, 'charging_port_check', 0, '2026-03-31 16:32:18', '2026-03-31 16:32:18'),
(5, 1, 'regenerative_braking_check', 0, '2026-03-31 16:32:18', '2026-03-31 16:32:18'),
(6, 1, 'electrical_wiring_inspection', 0, '2026-03-31 16:32:18', '2026-03-31 16:32:18'),
(7, 1, 'brake_service', 0, '2026-03-31 16:32:18', '2026-03-31 16:32:18'),
(8, 2, 'battery_health_check', 0, '2026-04-01 20:14:59', '2026-04-01 20:14:59'),
(9, 2, 'battery_cooling_service', 0, '2026-04-01 20:14:59', '2026-04-01 20:14:59'),
(10, 2, 'motor_inspection', 0, '2026-04-01 20:14:59', '2026-04-01 20:14:59'),
(11, 2, 'controller_diagnostics', 0, '2026-04-01 20:14:59', '2026-04-01 20:14:59'),
(12, 2, 'charging_port_check', 0, '2026-04-01 20:14:59', '2026-04-01 20:14:59'),
(13, 2, 'electrical_wiring_inspection', 0, '2026-04-01 20:14:59', '2026-04-01 20:14:59'),
(14, 2, 'suspension_check', 0, '2026-04-01 20:14:59', '2026-04-01 20:14:59');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_profiles`
--

CREATE TABLE `vendor_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `national_id_number` varchar(255) NOT NULL,
  `residential_address` text NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `business_type` varchar(255) NOT NULL,
  `business_registration_number` varchar(255) DEFAULT NULL,
  `tax_id_number` varchar(255) DEFAULT NULL,
  `business_address` text NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `current_step` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `is_submitted` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_profiles`
--

INSERT INTO `vendor_profiles` (`id`, `user_id`, `full_name`, `phone`, `national_id_number`, `residential_address`, `business_name`, `business_type`, `business_registration_number`, `tax_id_number`, `business_address`, `latitude`, `longitude`, `current_step`, `is_submitted`, `status`, `reviewed_by`, `reviewed_at`, `remarks`, `created_at`, `updated_at`) VALUES
(8, 13, 'Yam Kala', '9812871276', '2323', 'Pokhara', 'Sajilo Yatra', 'individual', '2343212', '1234323', 'Infomatics Collage Pokhara, Hospital Marg, Bhrikuti Tol, Pokhara-12, Pokhara, Kaski, Gandaki Province, 33709, Nepal', 28.2141322, 84.0021329, 5, 1, 'approved', 11, '2026-03-30 17:55:11', NULL, '2026-03-30 17:53:09', '2026-03-30 17:55:11'),
(9, 15, 'Ram Thapa', '987656733', '3432', 'Jhapa', 'Riders', 'partnership', '5353626', '421415', 'Purple Haze Vape Shop, Lakeside 6, Lakeside Marg, Baidam, Pokhara-06, Pokhara, Kaski, Gandaki Province, 00799, Nepal', 28.2106436, 83.9571200, 5, 1, 'approved', 11, '2026-03-31 18:21:48', NULL, '2026-03-31 18:15:11', '2026-03-31 18:21:48');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_subscriptions`
--

CREATE TABLE `vendor_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_plan_id` bigint(20) UNSIGNED NOT NULL,
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','expired','cancelled') NOT NULL DEFAULT 'active',
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_subscriptions`
--

INSERT INTO `vendor_subscriptions` (`id`, `vendor_id`, `subscription_plan_id`, `starts_at`, `ends_at`, `status`, `amount_paid`, `created_at`, `updated_at`) VALUES
(2, 13, 2, '2026-03-31 07:53:21', '2026-05-01 07:53:21', 'active', 4999.00, '2026-03-31 07:53:21', '2026-03-31 07:53:21'),
(3, 15, 3, '2026-03-31 18:31:00', '2027-03-31 18:31:00', 'active', 49999.00, '2026-03-31 18:31:00', '2026-03-31 18:31:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blog_posts_slug_unique` (`slug`),
  ADD KEY `blog_posts_user_id_foreign` (`user_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_user_id_foreign` (`user_id`),
  ADD KEY `bookings_driver_id_foreign` (`driver_id`),
  ADD KEY `bookings_cancelled_by_foreign` (`cancelled_by`),
  ADD KEY `bookings_vehicle_id_pickup_datetime_drop_datetime_index` (`vehicle_id`,`pickup_datetime`,`drop_datetime`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `contact_requests`
--
ALTER TABLE `contact_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contact_requests_vendor_id_foreign` (`vendor_id`),
  ADD KEY `contact_requests_booking_id_foreign` (`booking_id`),
  ADD KEY `contact_requests_user_id_foreign` (`user_id`);

--
-- Indexes for table `discount_codes`
--
ALTER TABLE `discount_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `discount_codes_code_unique` (`code`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `documents_user_id_purpose_type_unique` (`user_id`,`purpose`,`type`),
  ADD KEY `documents_reviewed_by_foreign` (`reviewed_by`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `drivers_license_number_unique` (`license_number`),
  ADD UNIQUE KEY `drivers_phone_unique` (`phone`),
  ADD KEY `drivers_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loyalty_accounts`
--
ALTER TABLE `loyalty_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loyalty_accounts_user_id_unique` (`user_id`);

--
-- Indexes for table `loyalty_transactions`
--
ALTER TABLE `loyalty_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loyalty_transactions_event_key_unique` (`event_key`),
  ADD KEY `loyalty_transactions_user_id_foreign` (`user_id`),
  ADD KEY `loyalty_transactions_booking_id_foreign` (`booking_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_user_id_foreign` (`user_id`),
  ADD KEY `payments_vendor_id_foreign` (`vendor_id`),
  ADD KEY `payments_booking_id_foreign` (`booking_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reviews_booking_id_unique` (`booking_id`),
  ADD KEY `reviews_user_id_foreign` (`user_id`),
  ADD KEY `reviews_vehicle_id_foreign` (`vehicle_id`),
  ADD KEY `reviews_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `subscription_payments`
--
ALTER TABLE `subscription_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscription_payments_purchase_order_id_unique` (`purchase_order_id`),
  ADD KEY `subscription_payments_subscription_plan_id_foreign` (`subscription_plan_id`),
  ADD KEY `subscription_payments_vendor_id_status_index` (`vendor_id`,`status`);

--
-- Indexes for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscription_plans_slug_unique` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`),
  ADD KEY `users_verified_by_foreign` (`verified_by`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicles_registration_no_unique` (`registration_no`),
  ADD KEY `vehicles_approved_by_foreign` (`approved_by`),
  ADD KEY `vehicles_vendor_id_status_index` (`vendor_id`,`status`),
  ADD KEY `vehicles_location_city_index` (`location_city`),
  ADD KEY `vehicles_vehicle_type_index` (`vehicle_type`);

--
-- Indexes for table `vehicle_images`
--
ALTER TABLE `vehicle_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_images_vehicle_id_is_primary_index` (`vehicle_id`,`is_primary`);

--
-- Indexes for table `vehicle_services`
--
ALTER TABLE `vehicle_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_services_vehicle_id_service_date_index` (`vehicle_id`,`service_date`);

--
-- Indexes for table `vehicle_service_items`
--
ALTER TABLE `vehicle_service_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_service_items_vehicle_service_id_foreign` (`vehicle_service_id`);

--
-- Indexes for table `vendor_profiles`
--
ALTER TABLE `vendor_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendor_profiles_user_id_unique` (`user_id`),
  ADD KEY `vendor_profiles_reviewed_by_foreign` (`reviewed_by`);

--
-- Indexes for table `vendor_subscriptions`
--
ALTER TABLE `vendor_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_subscriptions_subscription_plan_id_foreign` (`subscription_plan_id`),
  ADD KEY `vendor_subscriptions_vendor_id_status_index` (`vendor_id`,`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `contact_requests`
--
ALTER TABLE `contact_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `discount_codes`
--
ALTER TABLE `discount_codes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loyalty_accounts`
--
ALTER TABLE `loyalty_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `loyalty_transactions`
--
ALTER TABLE `loyalty_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subscription_payments`
--
ALTER TABLE `subscription_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `vehicle_images`
--
ALTER TABLE `vehicle_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `vehicle_services`
--
ALTER TABLE `vehicle_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vehicle_service_items`
--
ALTER TABLE `vehicle_service_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `vendor_profiles`
--
ALTER TABLE `vendor_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `vendor_subscriptions`
--
ALTER TABLE `vendor_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contact_requests`
--
ALTER TABLE `contact_requests`
  ADD CONSTRAINT `contact_requests_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contact_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contact_requests_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `drivers_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loyalty_accounts`
--
ALTER TABLE `loyalty_accounts`
  ADD CONSTRAINT `loyalty_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loyalty_transactions`
--
ALTER TABLE `loyalty_transactions`
  ADD CONSTRAINT `loyalty_transactions_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loyalty_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscription_payments`
--
ALTER TABLE `subscription_payments`
  ADD CONSTRAINT `subscription_payments_subscription_plan_id_foreign` FOREIGN KEY (`subscription_plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscription_payments_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vehicles_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_images`
--
ALTER TABLE `vehicle_images`
  ADD CONSTRAINT `vehicle_images_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_services`
--
ALTER TABLE `vehicle_services`
  ADD CONSTRAINT `vehicle_services_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_service_items`
--
ALTER TABLE `vehicle_service_items`
  ADD CONSTRAINT `vehicle_service_items_vehicle_service_id_foreign` FOREIGN KEY (`vehicle_service_id`) REFERENCES `vehicle_services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_profiles`
--
ALTER TABLE `vendor_profiles`
  ADD CONSTRAINT `vendor_profiles_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vendor_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_subscriptions`
--
ALTER TABLE `vendor_subscriptions`
  ADD CONSTRAINT `vendor_subscriptions_subscription_plan_id_foreign` FOREIGN KEY (`subscription_plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vendor_subscriptions_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
