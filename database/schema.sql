-- Assuming `datapharmacist` table already exists in the `pharmcare` database.
-- It should have `id`, `hospital_name`, `role_id`, `username`, `fullname` etc.

DROP TABLE IF EXISTS `pharmalink_logs`;
DROP TABLE IF EXISTS `pharmalink_inquiries`;

-- 1. Table for storing the caller information and patient search context
CREATE TABLE `pharmalink_inquiries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `inquirer_name` VARCHAR(255) NOT NULL,
  `hospital` VARCHAR(255) NOT NULL,
  `province` VARCHAR(100) NOT NULL,
  `position` VARCHAR(150) NOT NULL,
  `license_number` VARCHAR(100),
  `phone_number` VARCHAR(50) NOT NULL,
  `inquiry_reason` TEXT NOT NULL, -- วัตถุประสงค์ (HA/PDPA)
  `patient_hn` VARCHAR(50), 
  `recorded_by` INT NOT NULL, 
  `recorded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pharmalink_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL, 
  `action` VARCHAR(100) NOT NULL, -- e.g., 'LOGIN', 'LOGOUT', 'SEARCH_PATIENT', 'VIEW_HISTORY'
  `details` TEXT, -- Additional context (e.g., HN searched)
  `ip_address` VARCHAR(50),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
