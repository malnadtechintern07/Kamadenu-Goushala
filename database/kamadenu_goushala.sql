-- KAMADENU GOUSHALA DATABASE SCHEMA & SEED DATA
CREATE DATABASE IF NOT EXISTS `kamadenu_goushala` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `kamadenu_goushala`;

-- 1. ROLES & PERMISSIONS
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `display_name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `module` VARCHAR(50) NOT NULL,
  `description` VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` INT NOT NULL,
  `permission_id` INT NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. ADMINS & USERS
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role_id` INT NOT NULL,
  `phone` VARCHAR(20),
  `avatar` VARCHAR(255) DEFAULT 'assets/images/default-admin.jpg',
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20),
  `address` TEXT,
  `gouseva_points` INT DEFAULT 0,
  `avatar` VARCHAR(255) DEFAULT 'assets/images/default-avatar.jpg',
  `status` ENUM('active', 'suspended') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_id` INT NOT NULL,
  `role_id` INT NOT NULL,
  PRIMARY KEY (`user_id`, `role_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. COWS & HEALTH
CREATE TABLE IF NOT EXISTS `cows` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `cow_code` VARCHAR(20) NOT NULL UNIQUE, -- e.g., KG-001
  `name` VARCHAR(100) NOT NULL,
  `name_kn` VARCHAR(100),
  `name_hi` VARCHAR(100),
  `breed` VARCHAR(100) NOT NULL,
  `age_years` INT DEFAULT 0,
  `age_months` INT DEFAULT 0,
  `gender` ENUM('female', 'male') DEFAULT 'female',
  `color` VARCHAR(50),
  `weight_kg` DECIMAL(6,2),
  `rescue_date` DATE,
  `rescue_story` TEXT,
  `photo` VARCHAR(255) DEFAULT 'assets/images/cow-default.jpg',
  `health_status` ENUM('Excellent', 'Good', 'Under Treatment', 'Critical') DEFAULT 'Good',
  `adoption_status` ENUM('Available', 'Sponsored', 'Partially Sponsored') DEFAULT 'Available',
  `monthly_sponsorship_amount` DECIMAL(10,2) DEFAULT 2500.00,
  `is_featured` TINYINT(1) DEFAULT 0,
  `whatsapp_number` VARCHAR(20) DEFAULT NULL,
  `contact_method` VARCHAR(20) DEFAULT 'website',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `cow_health` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `cow_id` INT NOT NULL,
  `last_checkup_date` DATE NOT NULL,
  `health_status` ENUM('Excellent', 'Good', 'Under Treatment', 'Critical') NOT NULL,
  `weight_kg` DECIMAL(6,2),
  `pulse_rate` INT,
  `temperature_f` DECIMAL(4,1),
  `dietary_plan` TEXT,
  `medical_notes` TEXT,
  `next_checkup_date` DATE,
  `updated_by` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`cow_id`) REFERENCES `cows`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `medical_records` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `cow_id` INT NOT NULL,
  `condition_name` VARCHAR(150) NOT NULL,
  `diagnosis` TEXT,
  `treatment_plan` TEXT,
  `veterinarian_name` VARCHAR(100),
  `record_date` DATE NOT NULL,
  `status` ENUM('Ongoing', 'Recovered', 'Chronic') DEFAULT 'Ongoing',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`cow_id`) REFERENCES `cows`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `vaccinations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `cow_id` INT NOT NULL,
  `vaccine_name` VARCHAR(150) NOT NULL,
  `administered_date` DATE NOT NULL,
  `next_due_date` DATE,
  `administered_by` VARCHAR(100),
  `notes` TEXT,
  FOREIGN KEY (`cow_id`) REFERENCES `cows`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `cow_journey` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `cow_id` INT NOT NULL,
  `stage` VARCHAR(100) NOT NULL, -- e.g., Rescue, Arrival, Medical Check, Recovery, Current
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `image` VARCHAR(255),
  `milestone_date` DATE NOT NULL,
  `status` ENUM('Published', 'Draft') DEFAULT 'Published',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`cow_id`) REFERENCES `cows`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `cow_updates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `cow_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `update_text` TEXT NOT NULL,
  `photo` VARCHAR(255),
  `update_month` VARCHAR(20),
  `update_year` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`cow_id`) REFERENCES `cows`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. SPONSORS & SPONSORSHIPS
CREATE TABLE IF NOT EXISTS `sponsors` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(20),
  `pan_number` VARCHAR(20),
  `address` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `sponsorships` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sponsor_id` INT NOT NULL,
  `cow_id` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `duration_months` INT DEFAULT 1,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `renewal_date` DATE,
  `status` ENUM('Active', 'Expired', 'Cancelled', 'Pending Approval') DEFAULT 'Active',
  `payment_id` VARCHAR(100),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`sponsor_id`) REFERENCES `sponsors`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`cow_id`) REFERENCES `cows`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. DONATIONS & PAYMENTS
CREATE TABLE IF NOT EXISTS `donations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT,
  `donor_name` VARCHAR(100) NOT NULL,
  `donor_email` VARCHAR(150) NOT NULL,
  `donor_phone` VARCHAR(20),
  `pan_number` VARCHAR(20),
  `amount` DECIMAL(10,2) NOT NULL,
  `purpose` VARCHAR(100) DEFAULT 'General Gouseva',
  `is_anonymous` TINYINT(1) DEFAULT 0,
  `payment_id` VARCHAR(100),
  `status` ENUM('Completed', 'Pending', 'Failed', 'Pending Approval') DEFAULT 'Completed',
  `receipt_number` VARCHAR(50) UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` VARCHAR(100) NOT NULL,
  `payment_id` VARCHAR(100) NOT NULL UNIQUE,
  `signature` VARCHAR(255),
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(10) DEFAULT 'INR',
  `status` ENUM('Captured', 'Authorized', 'Failed', 'Refunded', 'Pending Approval') DEFAULT 'Captured',
  `payment_method` VARCHAR(50) DEFAULT 'Razorpay',
  `entity_type` ENUM('Donation', 'Sponsorship', 'Order', 'Seva') NOT NULL,
  `entity_id` INT NOT NULL,
  `raw_response` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `payment_webhooks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `event_type` VARCHAR(100) NOT NULL,
  `payload` TEXT NOT NULL,
  `processed` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `receipts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `donation_id` INT NOT NULL,
  `receipt_number` VARCHAR(50) NOT NULL UNIQUE,
  `pdf_path` VARCHAR(255),
  `generated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`donation_id`) REFERENCES `donations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. SEVA & LOGS
CREATE TABLE IF NOT EXISTS `seva` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `title_kn` VARCHAR(150),
  `title_hi` VARCHAR(150),
  `description` TEXT,
  `category` VARCHAR(50) DEFAULT 'Daily Care', -- Feeding, Watering, Cleaning, Grooming, Healthcare
  `suggested_amount` DECIMAL(10,2) DEFAULT 500.00,
  `icon` VARCHAR(50) DEFAULT 'fa-heart',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `seva_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `seva_id` INT NOT NULL,
  `user_id` INT,
  `sponsor_name` VARCHAR(100) NOT NULL,
  `cow_id` INT,
  `date_performed` DATE NOT NULL,
  `status` ENUM('Scheduled', 'Completed', 'In Progress', 'Pending Approval') DEFAULT 'Completed',
  `amount_paid` DECIMAL(10,2) DEFAULT 0.00,
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`seva_id`) REFERENCES `seva`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`cow_id`) REFERENCES `cows`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. VOLUNTEERS & VETERINARIANS
CREATE TABLE IF NOT EXISTS `volunteers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `skills` TEXT,
  `availability` VARCHAR(100),
  `interest_area` VARCHAR(100),
  `message` TEXT,
  `status` ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
  `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `veterinarians` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `qualification` VARCHAR(150),
  `phone` VARCHAR(20) NOT NULL,
  `email` VARCHAR(150),
  `clinic_address` TEXT,
  `specialization` VARCHAR(100),
  `status` ENUM('Active', 'Inactive') DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `veterinary_visits` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `veterinarian_id` INT NOT NULL,
  `cow_id` INT NOT NULL,
  `visit_date` DATE NOT NULL,
  `reason` VARCHAR(255),
  `observations` TEXT,
  `prescribed_meds` TEXT,
  `next_visit_date` DATE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`veterinarian_id`) REFERENCES `veterinarians`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`cow_id`) REFERENCES `cows`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. PRODUCTS & INVENTORY
CREATE TABLE IF NOT EXISTS `product_categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT,
  `image` VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `name_kn` VARCHAR(150),
  `name_hi` VARCHAR(150),
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `description` TEXT,
  `price` DECIMAL(10,2) NOT NULL,
  `stock_quantity` INT NOT NULL DEFAULT 0,
  `unit` VARCHAR(20) DEFAULT 'pack', -- kg, ltr, pack, bottle
  `image` VARCHAR(255) DEFAULT 'assets/images/product-default.jpg',
  `is_active` TINYINT(1) DEFAULT 1,
  `whatsapp_number` VARCHAR(20) DEFAULT NULL,
  `contact_method` VARCHAR(20) DEFAULT 'website',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `product_categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `inventory` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL UNIQUE,
  `current_stock` INT NOT NULL DEFAULT 0,
  `min_threshold` INT DEFAULT 10,
  `max_capacity` INT DEFAULT 500,
  `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `inventory_transactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `transaction_type` ENUM('purchase', 'sale', 'adjustment', 'return') NOT NULL,
  `quantity` INT NOT NULL,
  `reference_id` VARCHAR(100),
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. ORDERS & ITEMS
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT,
  `order_code` VARCHAR(50) NOT NULL UNIQUE, -- e.g., KGO-10023
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(150) NOT NULL,
  `customer_phone` VARCHAR(20) NOT NULL,
  `shipping_address` TEXT NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `payment_status` ENUM('Paid', 'Pending', 'Failed', 'Pending Approval') DEFAULT 'Paid',
  `order_status` ENUM('On Hold', 'Processing', 'Dispatched', 'Delivered', 'Cancelled') DEFAULT 'Processing',
  `payment_id` VARCHAR(100),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_name` VARCHAR(150) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `quantity` INT NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. EMERGENCY CAMPAIGNS & CONTENT
CREATE TABLE IF NOT EXISTS `emergency_campaigns` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `story` TEXT NOT NULL,
  `target_amount` DECIMAL(10,2) NOT NULL,
  `raised_amount` DECIMAL(10,2) DEFAULT 0.00,
  `photo` VARCHAR(255) DEFAULT 'assets/images/emergency-default.jpg',
  `status` ENUM('Active', 'Paused', 'Completed') DEFAULT 'Active',
  `urgency_level` ENUM('High', 'Critical', 'Normal') DEFAULT 'Critical',
  `start_date` DATE NOT NULL,
  `end_date` DATE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `stories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `title_kn` VARCHAR(200),
  `title_hi` VARCHAR(200),
  `slug` VARCHAR(200) NOT NULL UNIQUE,
  `summary` TEXT,
  `content` LONGTEXT NOT NULL,
  `image` VARCHAR(255),
  `author` VARCHAR(100) DEFAULT 'Kamadenu Team',
  `status` ENUM('Published', 'Draft') DEFAULT 'Published',
  `published_at` DATE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `gallery` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `category` VARCHAR(50) DEFAULT 'Goushala Life', -- Cows, Seva, Events, Rescue
  `image` VARCHAR(255) NOT NULL,
  `caption` TEXT,
  `is_featured` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `role` VARCHAR(100) DEFAULT 'Devotee / Donor',
  `location` VARCHAR(100),
  `quote` TEXT NOT NULL,
  `avatar` VARCHAR(255) DEFAULT 'assets/images/default-avatar.jpg',
  `rating` INT DEFAULT 5,
  `is_featured` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `quotes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `quote_en` TEXT NOT NULL,
  `quote_kn` TEXT NOT NULL,
  `quote_hi` TEXT NOT NULL,
  `source` VARCHAR(100) DEFAULT 'Vedic Scriptures',
  `date_active` DATE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. CERTIFICATES, POINTS, NOTIFICATIONS & AUDIT
CREATE TABLE IF NOT EXISTS `certificates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT,
  `cert_code` VARCHAR(50) NOT NULL UNIQUE, -- e.g., KGC-2026-9812
  `cert_type` ENUM('Sponsorship', 'Donation', 'Seva', 'Volunteer') NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `recipient_name` VARCHAR(100) NOT NULL,
  `issue_date` DATE NOT NULL,
  `pdf_path` VARCHAR(255),
  `qr_hash` VARCHAR(100),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT,
  `admin_id` INT,
  `title` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `link` VARCHAR(255),
  `is_read` TINYINT(1) DEFAULT 0,
  `type` VARCHAR(50) DEFAULT 'info', -- info, success, warning, danger
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `gouseva_points` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `activity_type` VARCHAR(100) NOT NULL, -- Donation, Adoption, Volunteer, Seva
  `points` INT NOT NULL,
  `description` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `badges` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `icon` VARCHAR(50) DEFAULT 'fa-award',
  `min_points` INT DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user_badges` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `badge_id` INT NOT NULL,
  `awarded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`badge_id`) REFERENCES `badges`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `admin_id` INT,
  `admin_name` VARCHAR(100),
  `action` VARCHAR(100) NOT NULL,
  `target_table` VARCHAR(50),
  `record_id` INT,
  `old_values` TEXT,
  `new_values` TEXT,
  `ip_address` VARCHAR(45),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `media` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `filename` VARCHAR(255) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_size` INT NOT NULL,
  `file_type` VARCHAR(50) NOT NULL,
  `uploaded_by` VARCHAR(100) DEFAULT 'Admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT,
  `setting_group` VARCHAR(50) DEFAULT 'general',
  `description` VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================
-- SEED DATA INSERTIONS
-- =================================================================

-- 1. Roles
INSERT INTO `roles` (`id`, `name`, `display_name`, `description`) VALUES
(1, 'super_admin', 'Super Admin', 'Full system access and role management'),
(2, 'goushala_manager', 'Goushala Manager', 'Manages cows, health, and daily operations'),
(3, 'finance_manager', 'Finance Manager', 'Manages donations, receipts, and sponsorships'),
(4, 'store_manager', 'Store Manager', 'Manages products, inventory, and order fulfillment'),
(5, 'veterinarian', 'Veterinarian', 'Access to medical records and cow health logs');

-- 2. Default Admins (Password: 1234 -> $2y$10$2PUrOWurJV/W4LJWfuf1z.2Cf9ki9vkR4ELFgpKOVtujiuRuo9bAC)
-- We will store password hash for 1234
INSERT INTO `admins` (`id`, `name`, `email`, `password`, `role_id`, `phone`) VALUES
(1, 'Sri Kamadenu Sevak', 'abc@123@gmail.com', '$2y$10$2PUrOWurJV/W4LJWfuf1z.2Cf9ki9vkR4ELFgpKOVtujiuRuo9bAC', 1, '+91 98800 12345'),
(2, 'Dr. Ramesh Kumar', 'vet@kamadenugoushala.org', '$2y$10$r94V8E1Gq8p5u12345678uF1N9GqZ5p5s6i8r9t0v1w2x3y4z5', 5, '+91 98450 67890');

-- 3. Default Demo User (Password: user123)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `gouseva_points`) VALUES
(1, 'Ananya Sharma', 'user@kamadenugoushala.org', '$2y$10$r94V8E1Gq8p5u12345678uF1N9GqZ5p5s6i8r9t0v1w2x3y4z5', '+91 99000 55443', '108 Temple Street, Malleshwaram, Bengaluru', 450);

-- 4. Sample Cows
INSERT INTO `cows` (`id`, `cow_code`, `name`, `name_kn`, `name_hi`, `breed`, `age_years`, `age_months`, `gender`, `color`, `weight_kg`, `rescue_date`, `rescue_story`, `health_status`, `adoption_status`, `monthly_sponsorship_amount`, `is_featured`) VALUES
(1, 'KG-001', 'Gauri', 'ಗೌರಿ', 'गौरी', 'Gir', 4, 2, 'female', 'Reddish Brown & White', 385.50, '2023-04-12', 'Rescued from an abandoned illegal slaughter transport near Highway 44. She was severely malnourished but has fully recovered into a playful and affection-loving Gir cow.', 'Excellent', 'Sponsored', 2500.00, 1),
(2, 'KG-002', 'Lakshmi', 'ಲಕ್ಷ್ಮಿ', 'लक्ष्मी', 'Sahiwal', 5, 8, 'female', 'Warm Reddish', 410.00, '2022-11-05', 'Rescued during a storm with an injured leg. Under continuous Gouseva and specialized herbal treatment, Lakshmi now leads the herd gracefully.', 'Good', 'Available', 3000.00, 1),
(3, 'KG-003', 'Nandi', 'ನಂದಿ', 'नंदी', 'Kankrej', 3, 6, 'male', 'Silver Grey', 520.00, '2023-08-20', 'A magnificent Kankrej bull rescued from a flooded village. He possesses strong majestic horns and a gentle, calm temperament.', 'Excellent', 'Available', 3500.00, 1),
(4, 'KG-004', 'Kamadenu', 'ಕಾಮಧೇನು', 'कामधेनु', 'Tharparkar', 6, 0, 'female', 'Pure White', 390.00, '2021-06-15', 'Found roaming dehydrated in drought conditions. She is the mother of the Goushala herd and brings divine peace to everyone who visits.', 'Excellent', 'Sponsored', 2500.00, 1),
(5, 'KG-005', 'Kapila', 'ಕಪಿಲಾ', 'कपिला', 'Vechur', 2, 4, 'female', 'Light Tan', 180.00, '2024-01-10', 'Rescued miniature indigenous Vechur cow. Highly energetic and loves fresh green fodder prepared during morning Seva.', 'Good', 'Available', 2000.00, 1),
(6, 'KG-006', 'Ganga', 'ಗಂಗಾ', 'गंगा', 'Hallikar', 4, 11, 'female', 'Greyish White', 365.00, '2023-09-01', 'Native Karnataka Hallikar breed rescued from severe heat exhaustion. Now healthy and enjoys being pampered by volunteers.', 'Under Treatment', 'Available', 2500.00, 0);

-- 5. Cow Health & Medical Logs
INSERT INTO `cow_health` (`cow_id`, `last_checkup_date`, `health_status`, `weight_kg`, `pulse_rate`, `temperature_f`, `dietary_plan`, `medical_notes`, `next_checkup_date`) VALUES
(1, '2026-08-01', 'Excellent', 385.50, 64, 101.5, 'Fresh Napier grass, Ayurvedic Jaggery mixture, minerals', 'All vital parameters optimal. Coat healthy and shiny.', '2026-09-01'),
(2, '2026-08-05', 'Good', 410.00, 62, 101.2, 'Dry fodder, green grass, sesame oil supplement', 'Leg strength improved. Daily gentle walking recommended.', '2026-09-05'),
(6, '2026-08-10', 'Under Treatment', 365.00, 68, 102.1, 'Soft mash, liver tonic, boosted hydration supplements', 'Recovering from minor skin irritation. Ointment applied daily.', '2026-08-25');

-- 6. Cow Journey Timeline
INSERT INTO `cow_journey` (`cow_id`, `stage`, `title`, `description`, `milestone_date`) VALUES
(1, 'Rescue', 'Rescued from Danger', 'Rescued from illegal transport lorry near the state border.', '2023-04-12'),
(1, 'Arrival', 'Welcome to Kamadenu', 'Safely arrived at Kamadenu Goushala and received initial warm bath and pooja.', '2023-04-13'),
(1, 'Medical Check', 'Comprehensive Health Assessment', 'Dr. Ramesh conducted blood tests and initiated deworming & vitamin course.', '2023-04-15'),
(1, 'Recovery', 'Full Health Restoration', 'Gained 45 kg over 3 months with nutritious organic fodder.', '2023-07-20'),
(1, 'Current', 'Living Happily & Sponsored', 'Regularly receives love, jaggery feeding, and daily morning pooja.', '2024-01-01');

-- 7. Cow Updates
INSERT INTO `cow_updates` (`cow_id`, `title`, `update_text`, `update_month`, `update_year`) VALUES
(1, 'Gauri Enjoys Fresh Monsoon Grass', 'Gauri has been enjoying the fresh green Napier grass harvested this monsoon. She gained 4kg and loves playing in the open paddock.', 'August', 2026),
(2, 'Lakshmi Medical Review Success', 'Lakshmis leg checkup went smoothly! She enjoyed special jaggery treats sponsored by our devotees.', 'August', 2026);

-- 8. Sponsors & Sponsorships
INSERT INTO `sponsors` (`id`, `user_id`, `name`, `email`, `phone`, `pan_number`, `address`) VALUES
(1, 1, 'Ananya Sharma', 'user@kamadenugoushala.org', '+91 99000 55443', 'ABCDE1234F', 'Malleshwaram, Bengaluru');

INSERT INTO `sponsorships` (`sponsor_id`, `cow_id`, `amount`, `duration_months`, `start_date`, `end_date`, `status`) VALUES
(1, 1, 2500.00, 12, '2026-01-01', '2026-12-31', 'Active');

-- 9. Seva Items
INSERT INTO `seva` (`id`, `title`, `title_kn`, `title_hi`, `description`, `category`, `suggested_amount`, `icon`) VALUES
(1, 'Daily Green Fodder Seva', 'ದೈನಂದಿನ ಹಸಿರು ಹುಲ್ಲು ಸೇವೆ', 'दैनिक हरा चारा सेवा', 'Sponsor nutritious green fodder & jaggery for 50+ cows for one day.', 'Feeding', 1000.00, 'fa-seedling'),
(2, 'Gou Pooja & Aarti Seva', 'ಗೋ ಪೂಜೆ ಮತ್ತು ಆರತಿ ಸೇವೆ', 'गौ पूजा एवं आरती सेवा', 'Sponsor traditional Veda chanting, garland decor, and Gou Aarti in your name.', 'Worship', 1500.00, 'fa-om'),
(3, 'Cow Healthcare & Medical Seva', 'ಹಸು ಆರೋಗ್ಯ ಮತ್ತು ವೈದ್ಯಕೀಯ ಸೇವೆ', 'गौ स्वास्थ्य एवं चिकित्सा सेवा', 'Fund essential Ayurvedic medicines, vaccinations, and veterinary care.', 'Healthcare', 2500.00, 'fa-heartpulse'),
(4, 'One Day Complete Goushala Seva', 'ಒಂದು ದಿನದ ಸಂಪೂರ್ಣ ಗೋಶಾಲೆ ಸೇವೆ', 'एक दिवसीय पूर्ण गौशाला सेवा', 'Sponsor food, water, maintenance, and care for all resident cows for an entire day.', 'Full Sponsorship', 5000.00, 'fa-sun');

-- 10. Seva Logs
INSERT INTO `seva_logs` (`seva_id`, `user_id`, `sponsor_name`, `cow_id`, `date_performed`, `status`, `amount_paid`) VALUES
(1, 1, 'Ananya Sharma', 1, '2026-08-18', 'Completed', 1000.00),
(2, NULL, 'Suresh Hegde', 2, '2026-08-19', 'Completed', 1500.00);

-- 11. Product Categories & Products
INSERT INTO `product_categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Pure Ghee & Dairy', 'pure-ghee-dairy', 'A2 Indigenous Gir Cow Ghee prepared using traditional Bilona method.'),
(2, 'Panchagavya Products', 'panchagavya', 'Ayurvedic soaps, dhoop batti, and wellness preparations.'),
(3, 'Organic Agriculture & Bio-inputs', 'organic-agri', 'Natural organic fertilizers and bio-pesticides from Gou-Krupamrutum.');

INSERT INTO `products` (`id`, `category_id`, `name`, `name_kn`, `name_hi`, `slug`, `description`, `price`, `stock_quantity`, `unit`) VALUES
(1, 1, 'A2 Vedic Bilona Gir Cow Ghee (500ml)', 'A2 ವೈದಿಕ ಬಿಲೋನಾ ಗಿರ್ ಹಸುವಿನ ತುಪ್ಪ', 'A2 वैदिक बिलोना गिर गाय घी', 'a2-gir-cow-ghee-500ml', 'Hand-churned A2 Ghee made from grass-fed Gir cows using traditional earthen pots.', 1250.00, 45, 'bottle'),
(2, 1, 'A2 Vedic Bilona Gir Cow Ghee (1 Litre)', 'A2 ವೈದಿಕ ಬಿಲೋನಾ ಗಿರ್ ಹಸುವಿನ ತುಪ್ಪ (1 ಲೀಟರ್)', 'A2 वैदिक बिलोना गिर गाय घी (1 लीटर)', 'a2-gir-cow-ghee-1L', 'Pure 1 Litre A2 Gir Cow Bilona Ghee filled with natural aroma and medicinal goodness.', 2400.00, 30, 'bottle'),
(3, 2, 'Panchagavya Herbal Bathing Soap', 'ಪಂಚಗವ್ಯ ಮೂಲಿಕೆ ಸ್ನಾನದ ಸೋಪು', 'पंचगव्य हर्बल स्नान साबुन', 'panchagavya-soap', 'Chemical-free skin rejuvenating herbal soap enriched with Gou-Ghee, Neem & Tulsi.', 120.00, 120, 'pack'),
(4, 2, 'Organic Gou-Dhoop Sticks (Box of 30)', 'ಸಾವಯವ ಗೋ-ಧೂಪ ಹೂಬತ್ತಿ (30 ಬಾಕ್ಸ್)', 'जैविक गौ-धूप बत्ती (30 का बॉक्स)', 'organic-gou-dhoop', 'Natural purifying aromatic dhoop sticks made with Gomaya, Camphor, and Desi Ghee.', 180.00, 85, 'pack'),
(5, 2, 'Distilled Gomutra Arka (500ml)', 'ಸಂಸ್ಕರಿಸಿದ ಗೋಮೂತ್ರ ಅರ್ಕ', 'अर्क गोमूत्र (500ml)', 'gomutra-arka-500ml', 'Pure distilled Gou-Arka prepared under scientific and traditional Ayurvedic supervision.', 150.00, 60, 'bottle');

-- Initialize Inventory records
INSERT INTO `inventory` (`product_id`, `current_stock`, `min_threshold`, `max_capacity`) VALUES
(1, 45, 10, 200),
(2, 30, 10, 150),
(3, 120, 20, 500),
(4, 85, 15, 300),
(5, 60, 15, 200);

-- 12. Emergency Campaigns
INSERT INTO `emergency_campaigns` (`id`, `title`, `story`, `target_amount`, `raised_amount`, `status`, `urgency_level`, `start_date`) VALUES
(1, 'Urgent Rescue & ICU Fodder Supply for 25 Flood-Affected Cows', 'Heavy monsoon rains flooded lower pastures, trapping 25 indigenous cows without dry feed or shelter. We urgently require fodder, antibiotics, and waterproof roofing.', 150000.00, 98500.00, 'Active', 'Critical', '2026-08-01'),
(2, 'Specialized Surgical Treatment for Rescued Calf Kapila', 'Calf Kapila was rescued with a fractured hind leg. She requires emergency veterinary surgery, splints, and 2 months of intensive post-operative care.', 50000.00, 34200.00, 'Active', 'High', '2026-08-10');

-- 13. Sample Donations
INSERT INTO `donations` (`user_id`, `donor_name`, `donor_email`, `donor_phone`, `amount`, `purpose`, `payment_id`, `receipt_number`) VALUES
(1, 'Ananya Sharma', 'user@kamadenugoushala.org', '+91 99000 55443', 2500.00, 'Emergency Rescue Campaign', 'pay_Kamadenu_90812', 'KGR-2026-001'),
(NULL, 'Ramesh Rao', 'ramesh.rao@gmail.com', '+91 98441 22334', 5000.00, 'General Gouseva', 'pay_Kamadenu_90813', 'KGR-2026-002'),
(NULL, 'Venkatesh Bhat', 'vbhat@outlook.com', '+91 97312 88776', 1000.00, 'Green Fodder Seva', 'pay_Kamadenu_90814', 'KGR-2026-003');

-- 14. Stories
INSERT INTO `stories` (`id`, `title`, `title_kn`, `title_hi`, `slug`, `summary`, `content`, `published_at`) VALUES
(1, 'The Miracle Recovery of Gauri: From Highway Hazard to Herd Beloved', 'ಗೌರಿ ಹಸುವಿನ ಪವಾಡ ಚೇತರಿಕೆ', 'गौरी का चमत्कारिक पुनर्जन्म', 'miracle-recovery-gauri', 'Read how Gauri transformed from a frail injured cow into the most affectionate mother in our Goushala.', '<p>When Gauri first arrived at Kamadenu Goushala on an April night in 2023, she could barely stand. She had been rescued by our dedicated team along Highway 44...</p><p>With 24/7 care, clean water, organic jaggery, and daily love, Gauri was running freely across green pastures within two months!</p>', '2026-07-15'),
(2, 'Why Indigenous Indian Cow Breeds are Essential for Ecological Harmony', 'ದೇಶಿ ಗೋವುಗಳ ಮಹತ್ವ', 'भारतीय गायों का महत्त्व', 'indigenous-cow-breeds-harmony', 'Discover the scientific and spiritual reasons behind preserving Gir, Sahiwal, and Kankrej breeds.', '<p>Indigenous Indian cattle (Bos indicus) possess the distinctive hump and dewlap which regulate body temperature and produce A2 protein rich milk...</p>', '2026-08-05');

-- 15. Testimonials
INSERT INTO `testimonials` (`name`, `role`, `location`, `quote`, `rating`) VALUES
('Dr. Sunita Deshmukh', 'Ayurvedic Physician', 'Mumbai', 'Visiting Kamadenu Goushala filled my heart with deep devotional peace. The cows are maintained with such dignity, cleanliness, and Vedic tradition.', 5),
('Raghavendra Joshi', 'IT Professional & Cow Sponsor', 'Bengaluru', 'Adopting Lakshmi through the Kamadenu portal was seamless. Getting monthly health updates and video clips of her makes me feel connected to Gouseva every day.', 5);

-- 16. Daily Quotes
INSERT INTO `quotes` (`quote_en`, `quote_kn`, `quote_hi`, `source`, `date_active`) VALUES
('Gomaye Vasate Lakshmi, Kstage Vasate Saraswati — Prosperity and wisdom reside where cows are nurtured with devotion.', 'ಗೋಮಯೇ ವಸತೇ ಲಕ್ಷ್ಮೀಃ — ಗೋಸೇವೆಯಿಂದ ಶಾಂತಿ ಮತ್ತು ಸಮೃದ್ಧಿ ಲಭಿಸುತ್ತದೆ.', 'गावो विश्वस्य मातरः — गाय संपूर्ण विश्व की माता है।', 'Rigveda', '2026-08-20');

-- 17. Badges
INSERT INTO `badges` (`id`, `name`, `description`, `icon`, `min_points`) VALUES
(1, 'Gou Sevak', 'Awarded for starting your journey with Kamadenu Goushala.', 'fa-seedling', 50),
(2, 'Gou Protector', 'Awarded for sponsoring a cow or making significant contributions.', 'fa-shield-halved', 200),
(3, 'Gou Guardian', 'Awarded to dedicated lifetime supporters of Gouseva.', 'fa-crown', 500);

INSERT INTO `user_badges` (`user_id`, `badge_id`) VALUES
(1, 1),
(1, 2);

-- 18. Default Settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `description`) VALUES
('site_name', 'Kamadenu Goushala', 'general', 'Platform Name'),
('tagline_kn', 'ಗೋ ಮಾತಾ ಕಿ ಜೈ', 'general', 'Kannada Tagline'),
('contact_email', 'info@kamadenugoushala.org', 'contact', 'Public Contact Email'),
('contact_phone', '+91 98800 12345', 'contact', 'Public Contact Phone'),
('goushala_address', 'Kamadenu Sacred Grove, Nelamangala Road, Bengaluru Rural, Karnataka 562123', 'contact', 'Goushala Address'),
('razorpay_key_id', 'rzp_test_Kamadenu2026', 'payment', 'Razorpay Test Key ID'),
('razorpay_key_secret', 'secret_Kamadenu_test_2026_key', 'payment', 'Razorpay Test Key Secret'),
('currency_symbol', '₹', 'general', 'Currency Symbol'),
('whatsapp_adoption_default', '+91 98800 12345', 'whatsapp', 'Default WhatsApp number for cow adoptions'),
('whatsapp_order_default', '+91 98800 12345', 'whatsapp', 'Default WhatsApp number for product orders');

-- 19. WhatsApp Numbers Directory
CREATE TABLE IF NOT EXISTS `whatsapp_numbers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `label` VARCHAR(100) NOT NULL,
  `phone_number` VARCHAR(20) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `whatsapp_numbers` (`label`, `phone_number`) VALUES
('Gouseva Desk', '+91 98800 12345'),
('Store Sales Desk', '+91 98800 12345');

