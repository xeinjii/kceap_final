-- Create college_renewal_documents table to store uploaded files for renewals
CREATE TABLE IF NOT EXISTS `college_renewal_documents` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `renewal_id` INT NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `uploaded_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`renewal_id`) REFERENCES `college_renewals`(`id`) ON DELETE CASCADE,
  INDEX (`renewal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
