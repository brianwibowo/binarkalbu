-- -------------------------------------------------------------
-- TablePlus 6.7.1(636)
--
-- https://tableplus.com/
--
-- Database: binarkalbu
-- Generation Time: 2025-10-13 13:19:21.5760
-- -------------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `client_sessions`;
CREATE TABLE `client_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL COMMENT 'ID Psikolog',
  `session_description` text COLLATE utf8mb4_unicode_ci,
  `session_date` date NOT NULL,
  `session_start_time` time NOT NULL,
  `session_end_time` time NOT NULL,
  `transfer_date` date DEFAULT NULL,
  `payment_status` enum('dp','lunas') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dp',
  `payment_amount` int unsigned NOT NULL DEFAULT '0',
  `session_status` enum('terpakai','belum_terpakai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum_terpakai',
  `medical_record_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_sessions_client_id_foreign` (`client_id`),
  KEY `client_sessions_user_id_foreign` (`user_id`),
  CONSTRAINT `client_sessions_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `client_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `whatsapp_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `initial_diagnosis` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `daily_recaps`;
CREATE TABLE `daily_recaps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `recap_date` date NOT NULL,
  `session_count` int NOT NULL DEFAULT '0',
  `new_chats` int NOT NULL DEFAULT '0',
  `new_client_goals` int NOT NULL DEFAULT '0',
  `gmap_reviews` int NOT NULL DEFAULT '0',
  `source_tiktok` int NOT NULL DEFAULT '0',
  `source_google` int NOT NULL DEFAULT '0',
  `source_instagram` int NOT NULL DEFAULT '0',
  `source_friend` int NOT NULL DEFAULT '0',
  `jam_gandeng` int NOT NULL DEFAULT '0',
  `extra_notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_recaps_recap_date_unique` (`recap_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_photo_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','psikolog') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'psikolog',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-77de68daecd823babbb58edb1c8e14d7106e83bb', 'i:1;', 1760079919),
('laravel-cache-77de68daecd823babbb58edb1c8e14d7106e83bb:timer', 'i:1760079919;', 1760079919),
('laravel-cache-livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3', 'i:1;', 1760253650),
('laravel-cache-livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3:timer', 'i:1760253650;', 1760253650);

INSERT INTO `client_sessions` (`id`, `client_id`, `user_id`, `session_description`, `session_date`, `session_start_time`, `session_end_time`, `transfer_date`, `payment_status`, `payment_amount`, `session_status`, `medical_record_path`, `created_at`, `updated_at`) VALUES
(2, 4, 3, 'a piece of you', '2025-10-11', '12:30:00', '16:30:00', '2025-10-07', 'lunas', 100000, 'terpakai', 'medical-records/01K76545D8FS6KK2V93NSDKB2Z.png', '2025-10-10 03:31:57', '2025-10-10 11:12:35');

INSERT INTO `clients` (`id`, `name`, `date_of_birth`, `whatsapp_number`, `address`, `initial_diagnosis`, `created_at`, `updated_at`) VALUES
(2, 'Jefria', '2025-10-08', '081227874561', 'Semarang', 'Gun roses', '2025-10-09 16:12:20', '2025-10-10 11:02:03'),
(4, 'Abuld', '2025-10-13', '081226667654', 'sakjsbd', 'Sakit', '2025-10-10 03:31:24', '2025-10-10 03:31:24');

INSERT INTO `daily_recaps` (`id`, `recap_date`, `session_count`, `new_chats`, `new_client_goals`, `gmap_reviews`, `source_tiktok`, `source_google`, `source_instagram`, `source_friend`, `jam_gandeng`, `extra_notes`, `created_at`, `updated_at`) VALUES
(1, '2025-10-10', 10, 10, 10, 10, 10, 10, 10, 10, 0, 'Martabakl', '2025-10-09 16:22:49', '2025-10-09 16:22:49'),
(2, '2025-10-08', 10, 10, 10, 10, 10, 10, 10, 10, 0, '10', '2025-10-09 16:25:34', '2025-10-09 16:25:34');

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_10_09_153212_create_clients_table', 2),
(5, '2025_10_09_153216_create_client_sessions_table', 2),
(6, '2025_10_09_153219_create_daily_recaps_table', 2),
(7, '2025_10_09_153222_add_role_to_users_table', 2),
(8, '2025_10_10_020428_add_profile_photo_path_to_users_table', 3),
(9, '2025_10_10_020921_modify_daily_recaps_table_for_jam_gandeng', 4);

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('NTfJXzyIruJ7sJVQi2w6ifnVJ7JVDq3uHKJadydv', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiM0pUb1dVWGFORVd0SGFZT1hiWHpFNlhyeWNpbkREUklLY0pRajJxYyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjI6Imh0dHA6Ly9iaW5hcmthbGJ1LnRlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YToxOntzOjg6ImludGVuZGVkIjtzOjI4OiJodHRwOi8vYmluYXJrYWxidS50ZXN0L2FkbWluIjt9fQ==', 1760333262);

INSERT INTO `users` (`id`, `name`, `email`, `profile_photo_path`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Adminn', 'admin@binarkalbu.com', 'avatars/01K75AGAM2KNMNDEP4YHJ7H6Q7.png', NULL, '$2y$12$p.FN40fuE6shR5dFeg6YF.PQXie2OxDj1ocB7bWnRPAG2Ag3yHUHy', 'admin', NULL, '2025-10-09 15:40:30', '2025-10-10 03:27:22'),
(2, 'David da Silva ', 'yareu@binarkalbu.com', NULL, NULL, '$2y$12$LjDYKvMLHIrf.nRCEjFLP.NROToCizdM7bxNxxJjhp2PTJTatPiBu', 'psikolog', NULL, '2025-10-09 16:31:59', '2025-10-09 17:32:08'),
(3, 'Yoru', 'yoru@example.com', 'avatars/01K76EYNS2EWHS5R1YMZPAWY0Z.png', NULL, '$2y$12$Q0l.OpgRiWg/5lcvBA22e.nXK1PO28Orr9.1jUvFJRJCvpb7TyGZS', 'psikolog', NULL, '2025-10-09 16:32:38', '2025-10-10 14:04:21'),
(4, 'Jiwa', 'jiwa@example.com', NULL, NULL, '$2y$12$nCm.z4FWWgGMtz.KvayIwOwCz1t8mdIZPtD0G7nSOFptFMkYUQKgu', 'psikolog', NULL, '2025-10-09 17:31:54', '2025-10-09 17:31:54'),
(5, 'Lagiii', 'lagi@example.com', NULL, NULL, '$2y$12$zul79QqPFejOA6IO3GnauOw1fLXaLi0oS0kYkd65JILduTFMR/zf2', 'psikolog', NULL, '2025-10-10 00:35:29', '2025-10-10 00:35:29');



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;