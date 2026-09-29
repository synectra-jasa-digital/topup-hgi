-- ============================================================
-- Bot Telegram Ayong Store - 4 Tabel Baru
-- Jalankan di phpMyAdmin > database topup_hgi > tab SQL
-- ============================================================

-- 1. Sesi login bot
CREATE TABLE `telegram_sessions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `telegram_user_id` bigint(20) UNSIGNED NOT NULL,
  `chat_id` bigint(20) NOT NULL,
  `admin_id` int(10) UNSIGNED NOT NULL,
  `notify` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `last_active_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `telegram_user_id` (`telegram_user_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Batas percobaan login
CREATE TABLE `telegram_login_attempts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `telegram_user_id` bigint(20) UNSIGNED NOT NULL,
  `attempted_at` datetime NOT NULL,
  `success` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `telegram_user_id_attempted_at` (`telegram_user_id`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Antrean notifikasi Telegram (outbox + retry)
CREATE TABLE `telegram_outbox` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `chat_id` bigint(20) NOT NULL,
  `order_id` int(10) UNSIGNED DEFAULT NULL,
  `type` varchar(32) NOT NULL,
  `payload` text NOT NULL,
  `attempts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `sent_message_id` bigint(20) DEFAULT NULL,
  `status` enum('pending','sent','failed') NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Idempotensi update Telegram (cegah proses ganda)
CREATE TABLE `telegram_updates` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `update_id` bigint(20) UNSIGNED NOT NULL,
  `received_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `update_id` (`update_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
