-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gazdă: localhost:3306
-- Timp de generare: ian. 03, 2024 la 08:46 AM
-- Versiune server: 8.0.30
-- Versiune PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Bază de date: `sales`
--

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `clients`
--

CREATE TABLE `clients` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cui` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iban` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `info` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Eliminarea datelor din tabel `clients`
--

INSERT INTO `clients` (`id`, `name`, `cui`, `rc`, `bank`, `iban`, `phone`, `email`, `state`, `city`, `address`, `person`, `position`, `info`, `created_at`, `updated_at`) VALUES
(1, 'MEDIA EVENTS S.R.L', '1255586', 'J19/14/2015', 'TRANSILVANIA', 'RO17BBCDEC65655656546545646', '0746489864', 'balo.madalin.marian@gmail.com', 'București', 'București', NULL, 'Cosmin ', 'DIRECTOR', NULL, '2023-12-23 17:29:34', '2023-12-23 17:29:34');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `collections`
--

CREATE TABLE `collections` (
  `id` bigint UNSIGNED NOT NULL,
  `start_at` date NOT NULL,
  `amount_received` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_id` bigint UNSIGNED NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Eliminarea datelor din tabel `collections`
--

INSERT INTO `collections` (`id`, `start_at`, `amount_received`, `invoice_id`, `payment_method`, `details`, `created_at`, `updated_at`) VALUES
(10, '2023-12-23', '23325', 3, 'cont', NULL, '2023-12-24 15:41:15', '2023-12-24 15:41:15');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `companies`
--

CREATE TABLE `companies` (
  `id` bigint UNSIGNED NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cui` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iban` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Eliminarea datelor din tabel `companies`
--

INSERT INTO `companies` (`id`, `logo`, `name`, `cui`, `rc`, `bank`, `iban`, `phone`, `email`, `state`, `city`, `address`, `person`, `position`, `created_at`, `updated_at`) VALUES
(1, 'IxHSJrWjJ2UfJAzpnVL3Kd3eGQ4xZD-metaV2lyZSBCT1guanBn-.jpg', 'Wirbox SRL', '37481868', 'J38/423/2017', 'TRANSILVANIA', NULL, '0746489864', 'office@wirbox.ro', 'VALCEA', 'GURA VAII (BUJORENI VL)', 'CALEA LUI TRAIAN, 209', 'BALO MADALIN', 'DIRECTOR', '2023-12-24 11:44:13', '2023-12-24 11:44:13');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `estimates`
--

CREATE TABLE `estimates` (
  `id` bigint UNSIGNED NOT NULL,
  `series` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'WBX-OF',
  `start_at` date DEFAULT NULL,
  `due_at` date DEFAULT NULL,
  `clients_id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Eliminarea datelor din tabel `estimates`
--

INSERT INTO `estimates` (`id`, `series`, `start_at`, `due_at`, `clients_id`, `project_id`, `created_at`, `updated_at`) VALUES
(2, 'WBX-Ofertă', '2023-12-22', '2023-12-24', 1, 1, '2023-12-23 17:32:07', '2023-12-23 17:32:07');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint UNSIGNED NOT NULL,
  `expended_at` date NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `quantity` int NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Eliminarea datelor din tabel `expenses`
--

INSERT INTO `expenses` (`id`, `expended_at`, `price`, `quantity`, `category`, `description`, `payment_method`, `created_at`, `updated_at`) VALUES
(1, '2023-12-24', 50.00, 1, 'good', NULL, 'cont', '2023-12-24 08:10:29', '2023-12-24 15:45:06');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `series` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'WBX',
  `start_at` date DEFAULT NULL,
  `due_at` date DEFAULT NULL,
  `clients_id` bigint UNSIGNED NOT NULL,
  `total` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `products_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Eliminarea datelor din tabel `invoices`
--

INSERT INTO `invoices` (`id`, `series`, `start_at`, `due_at`, `clients_id`, `total`, `products_id`, `created_at`, `updated_at`) VALUES
(3, 'WBX', '2023-11-02', '2023-12-23', 1, '23325', NULL, '2023-12-23 17:57:11', '2023-12-24 14:41:44');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Eliminarea datelor din tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2023_08_22_141844_create_clients_table', 1),
(6, '2023_08_24_040548_create_expenses_table', 1),
(7, '2023_08_25_122638_create_projects_table', 1),
(8, '2023_08_26_192313_create_estimates_table', 1),
(9, '2023_08_27_192643_create_invoices_table', 1),
(10, '2023_08_28_134626_create_positions_table', 1),
(11, '2023_09_01_181022_create_settings_table', 1),
(12, '2023_09_19_000001_modify_expenses_table', 1),
(13, '2023_12_22_133221_create_products_table', 1),
(14, '2023_12_22_134025_create_collections_table', 1),
(15, '2023_12_22_134244_create_companies_table', 1);

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `positions`
--

CREATE TABLE `positions` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_id` bigint UNSIGNED NOT NULL,
  `start_at` datetime NOT NULL,
  `due_at` datetime NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `remote` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `name_product` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_product` text COLLATE utf8mb4_unicode_ci,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tva` decimal(4,2) DEFAULT NULL,
  `quantity` decimal(8,2) NOT NULL,
  `unit_price` decimal(8,2) NOT NULL,
  `product_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_id` bigint UNSIGNED DEFAULT NULL,
  `estimate_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Eliminarea datelor din tabel `products`
--

INSERT INTO `products` (`id`, `name_product`, `description_product`, `unit`, `tva`, `quantity`, `unit_price`, `product_value`, `discount`, `invoice_id`, `estimate_id`, `created_at`, `updated_at`) VALUES
(1, 'mentenanta', 'test', 'luni', 0.00, 1.00, 15.00, '0', NULL, NULL, 2, '2023-12-23 17:32:07', '2023-12-23 17:32:07'),
(2, 'mentenatna', 'tateywdu', 'bax', 9.00, 1.00, 2000.00, '0', '0', 1, NULL, '2023-12-23 17:57:11', '2023-12-24 09:05:22'),
(3, 'kweghvufwg', 'wkejvj', 'bac', 0.00, 15.00, 1555.00, '0', '0', 3, NULL, '2023-12-24 09:07:10', '2023-12-24 09:07:10');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `projects`
--

CREATE TABLE `projects` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `clients_id` bigint UNSIGNED NOT NULL,
  `start_at` date DEFAULT NULL,
  `due_at` date DEFAULT NULL,
  `pricing_unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aborted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Eliminarea datelor din tabel `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `clients_id`, `start_at`, `due_at`, `pricing_unit`, `total`, `aborted`, `created_at`, `updated_at`) VALUES
(1, 'Gazuire ssd 6gb', NULL, 1, '2023-12-21', '2023-12-25', 'p', '500', 0, '2023-12-24 10:14:42', '2023-12-24 10:14:42');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `settings`
--

CREATE TABLE `settings` (
  `field` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attributes` json DEFAULT NULL,
  `weight` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Eliminarea datelor din tabel `settings`
--

INSERT INTO `settings` (`field`, `value`, `type`, `attributes`, `weight`, `created_at`, `updated_at`) VALUES
('accountHolder', NULL, 'text', NULL, 100, '2023-12-23 17:23:42', '2023-12-23 17:23:42'),
('bank', NULL, 'text', NULL, 90, '2023-12-23 17:23:42', '2023-12-23 17:23:42'),
('bic', NULL, 'text', NULL, 80, '2023-12-23 17:23:42', '2023-12-23 17:23:42'),
('city', 'GURA VĂII', 'text', NULL, 30, '2023-12-23 17:23:42', '2023-12-30 10:56:14'),
('company', 'WIRBOX SRL', 'text', NULL, 20, '2023-12-23 17:23:42', '2023-12-30 10:55:53'),
('country', 'ROMÂNIA', 'text', NULL, 30, '2023-12-23 17:23:42', '2023-12-30 10:56:28'),
('email', NULL, 'email', NULL, 40, '2023-12-23 17:23:42', '2023-12-23 17:23:42'),
('iban', NULL, 'text', NULL, 70, '2023-12-23 17:23:42', '2023-12-23 17:23:42'),
('logo', NULL, 'textarea', NULL, 130, '2023-12-23 17:23:42', '2023-12-23 17:23:42'),
('name', 'BALO MADALIN', 'text', NULL, 10, '2023-12-23 17:23:42', '2023-12-30 10:55:38'),
('phone', NULL, 'tel', NULL, 50, '2023-12-23 17:23:42', '2023-12-23 17:23:42'),
('signature', NULL, 'textarea', NULL, 140, '2023-12-23 17:23:42', '2023-12-23 17:23:42'),
('street', '209', 'text', NULL, 30, '2023-12-23 17:23:42', '2023-12-30 10:56:43'),
('taxOffice', '37481868', 'text', NULL, 110, '2023-12-23 17:23:42', '2023-12-30 10:57:07'),
('vatId', NULL, 'text', NULL, 120, '2023-12-23 17:23:42', '2023-12-23 17:23:42'),
('vatRate', NULL, 'number', NULL, 130, '2023-12-23 17:23:42', '2023-12-23 17:23:42'),
('website', NULL, 'url', NULL, 60, '2023-12-23 17:23:42', '2023-12-23 17:23:42'),
('zip', NULL, 'text', NULL, 30, '2023-12-23 17:23:42', '2023-12-23 17:23:42');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Eliminarea datelor din tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@example.com', '2023-12-23 17:23:44', '$2y$10$iKVDDbpmmOwb.PX2aS7W2OVxg6zUtsUy3mqruaOfp6G7SOMj2NbEu', 'tZBKLayXHZwgDR3n3DIcaSBZusEzXCkWBH9zMJ7VSpknODhttSbVbLUGoitv', '2023-12-23 17:23:44', '2023-12-23 17:23:44');

--
-- Indexuri pentru tabele eliminate
--

--
-- Indexuri pentru tabele `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `collections`
--
ALTER TABLE `collections`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `estimates`
--
ALTER TABLE `estimates`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexuri pentru tabele `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexuri pentru tabele `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexuri pentru tabele `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `positions_invoice_id_foreign` (`invoice_id`);

--
-- Indexuri pentru tabele `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexuri pentru tabele `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`field`);

--
-- Indexuri pentru tabele `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT pentru tabele eliminate
--

--
-- AUTO_INCREMENT pentru tabele `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pentru tabele `collections`
--
ALTER TABLE `collections`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pentru tabele `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pentru tabele `estimates`
--
ALTER TABLE `estimates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pentru tabele `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pentru tabele `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pentru tabele `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pentru tabele `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `positions`
--
ALTER TABLE `positions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pentru tabele `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pentru tabele `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pentru tabele `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constrângeri pentru tabele eliminate
--

--
-- Constrângeri pentru tabele `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `positions_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
