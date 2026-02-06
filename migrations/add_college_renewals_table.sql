-- Migration: create college_renewals table
-- Run this SQL on your database (e.g., via phpMyAdmin or mysql CLI)

CREATE TABLE IF NOT EXISTS `college_renewals` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `applicant_id` INT UNSIGNED NOT NULL,
  `renewal_reason` TEXT NULL,
  `additional_info` TEXT NULL,
  `submitted_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `admin_note` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX (`applicant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional: create a documents table to store uploaded file records (if not already created)
-- CREATE TABLE IF NOT EXISTS `college_renewal_documents` (
--   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
--   `renewal_id` INT UNSIGNED NOT NULL,
--   `file_name` VARCHAR(255) NOT NULL,
--   `file_path` VARCHAR(512) NOT NULL,
--   `uploaded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   INDEX (`renewal_id`),
--   FOREIGN KEY (`renewal_id`) REFERENCES `college_renewals`(`id`) ON DELETE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Create college_renewals table for tracking renewal requests
CREATE TABLE IF NOT EXISTS `college_renewals` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `applicant_id` INT NOT NULL,
  `renewal_reason` LONGTEXT NOT NULL,
  `additional_info` LONGTEXT,
  `submitted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(50) DEFAULT 'pending' COMMENT 'pending, approved, rejected',
  `admin_notes` LONGTEXT,
  `reviewed_at` DATETIME,
  `reviewed_by` INT,
  FOREIGN KEY (`applicant_id`) REFERENCES `college_account`(`applicant_id`) ON DELETE CASCADE,
  INDEX (`applicant_id`),
  INDEX (`status`),
  INDEX (`submitted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
