-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2026 at 04:48 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eoim_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `alerts`
--

CREATE TABLE `alerts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `device_id` int(10) UNSIGNED NOT NULL,
  `alert_type` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `severity` enum('low','medium','high','critical') NOT NULL DEFAULT 'low',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `alerts`
--

INSERT INTO `alerts` (`id`, `device_id`, `alert_type`, `message`, `severity`, `created_at`) VALUES
(1, 1, 'high_power_usage', 'อุปกรณ์ Air Conditioner ใช้กำลังไฟสูงผิดปกติ', 'high', '2026-04-01 05:30:12'),
(2, 2, 'low_power_factor', 'ค่า Power Factor ต่ำผิดปกติที่อุปกรณ์ Refrigerator', 'medium', '2026-04-01 05:30:12'),
(3, 3, 'voltage_abnormal', 'แรงดันไฟฟ้าผิดปกติที่อุปกรณ์ Washing Machine', 'critical', '2026-04-01 05:30:12');

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `id` int(10) UNSIGNED NOT NULL,
  `device_name` varchar(150) NOT NULL,
  `device_type` varchar(100) NOT NULL,
  `location` varchar(150) DEFAULT NULL,
  `status` enum('active','inactive','maintenance') NOT NULL DEFAULT 'active',
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `device_secret` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`id`, `device_name`, `device_type`, `location`, `status`, `user_id`, `ip_address`, `device_secret`, `created_at`, `updated_at`) VALUES
(1, 'Air Conditioner', 'air_conditioner', 'Living Room', 'active', NULL, NULL, NULL, '2026-04-01 04:34:52', '2026-04-01 04:34:52'),
(2, 'Refrigerator', 'refrigerator', 'Kitchen', 'active', NULL, NULL, NULL, '2026-04-01 04:34:52', '2026-04-01 04:34:52'),
(3, 'Washing Machine', 'washing_machine', 'Laundry Area', 'active', NULL, NULL, NULL, '2026-04-01 04:34:52', '2026-04-01 04:34:52');

-- --------------------------------------------------------

--
-- Table structure for table `smartmeter_readings`
--

CREATE TABLE `smartmeter_readings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `device_id` int(10) UNSIGNED NOT NULL,
  `voltage` decimal(10,2) NOT NULL DEFAULT 0.00,
  `current` decimal(10,2) NOT NULL DEFAULT 0.00,
  `power` decimal(10,2) NOT NULL DEFAULT 0.00,
  `energy` decimal(10,3) NOT NULL DEFAULT 0.000,
  `frequency` decimal(10,2) NOT NULL DEFAULT 0.00,
  `power_factor` decimal(5,2) NOT NULL DEFAULT 0.00,
  `recorded_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smartmeter_readings`
--

INSERT INTO `smartmeter_readings` (`id`, `device_id`, `voltage`, `current`, `power`, `energy`, `frequency`, `power_factor`, `recorded_at`, `created_at`) VALUES
(1, 1, 220.50, 1.25, 275.40, 1.520, 50.00, 0.92, '2026-04-01 11:35:04', '2026-04-01 04:35:04'),
(2, 1, 220.50, 1.20, 264.60, 1.250, 50.00, 0.95, '2026-04-01 12:29:49', '2026-04-01 05:29:49'),
(3, 2, 221.00, 0.80, 176.80, 0.920, 50.00, 0.90, '2026-04-01 12:29:49', '2026-04-01 05:29:49'),
(4, 3, 219.80, 2.50, 549.50, 2.400, 50.00, 0.88, '2026-04-01 12:29:49', '2026-04-01 05:29:49'),
(5, 1, 220.20, 1.30, 286.26, 1.380, 50.00, 0.96, '2026-04-01 12:29:49', '2026-04-01 05:29:49'),
(6, 2, 220.90, 0.75, 165.68, 0.850, 50.00, 0.89, '2026-04-01 12:29:49', '2026-04-01 05:29:49'),
(7, 1, 221.10, 1.10, 243.21, 1.100, 50.00, 0.94, '2026-04-01 07:54:09', '2026-04-01 05:54:09'),
(8, 1, 219.90, 1.40, 307.86, 1.420, 50.00, 0.96, '2026-04-01 08:54:09', '2026-04-01 05:54:09'),
(9, 1, 220.40, 1.35, 297.54, 1.390, 50.00, 0.95, '2026-04-01 09:54:09', '2026-04-01 05:54:09'),
(10, 1, 222.00, 1.25, 277.50, 1.280, 50.00, 0.93, '2026-04-01 10:54:09', '2026-04-01 05:54:09'),
(11, 1, 220.60, 1.30, 286.78, 1.330, 50.00, 0.97, '2026-04-01 11:54:09', '2026-04-01 05:54:09');

-- --------------------------------------------------------

--
-- Table structure for table `device_api_logs`
--

CREATE TABLE `device_api_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `device_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `endpoint` varchar(255) NOT NULL DEFAULT '/api/smartmeter/store',
  `request_method` varchar(10) NOT NULL DEFAULT 'POST',
  `request_path` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `payload` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','viewer') NOT NULL DEFAULT 'viewer',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@eoim.local', '$2y$10$ssC.AipD.rOhq7A1l55QzupTIXfvTdQbC07f.AAQBUtHr95WU0J/q', 'admin', '2026-04-01 04:19:42', '2026-04-01 04:19:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alerts`
--
ALTER TABLE `alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_alert_device` (`device_id`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_devices_user_id` (`user_id`);

--
-- Indexes for table `smartmeter_readings`
--
ALTER TABLE `smartmeter_readings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_smartmeter_device` (`device_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `device_api_logs`
--
ALTER TABLE `device_api_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_device_api_logs_device_id` (`device_id`),
  ADD KEY `idx_device_api_logs_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alerts`
--
ALTER TABLE `alerts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `smartmeter_readings`
--
ALTER TABLE `smartmeter_readings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `device_api_logs`
--
ALTER TABLE `device_api_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alerts`
--
ALTER TABLE `alerts`
  ADD CONSTRAINT `fk_alert_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `smartmeter_readings`
--
ALTER TABLE `smartmeter_readings`
  ADD CONSTRAINT `fk_smartmeter_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `device_api_logs`
--
ALTER TABLE `device_api_logs`
  ADD CONSTRAINT `fk_device_api_logs_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_device_api_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `fk_device_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
