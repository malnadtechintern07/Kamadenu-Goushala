<?php
/**
 * KAMADENU GOUSHALA - DATABASE & CORE UTILITIES
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'kamadenu_goushala');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}

// Auto-migration: Create videos table if it does not exist
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `videos` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT NULL,
        `youtube_url` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (PDOException $e) {
    // Fail silently or log if table creation fails
}

/**
 * Handle image file upload for admin forms
 */
function handle_file_upload($file_key, $fallback_url = '') {
    if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES[$file_key]['tmp_name'];
        $name = basename($_FILES[$file_key]['name']);
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif', 'svg'];
        if (in_array($ext, $allowed)) {
            $upload_dir = __DIR__ . '/../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $new_filename = 'photo_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $target_path = $upload_dir . $new_filename;
            if (move_uploaded_file($tmp_name, $target_path)) {
                return 'uploads/' . $new_filename;
            }
        }
    }
    return $fallback_url;
}

/**
 * Universal Image URL Formatter
 */
function img_url($path) {
    if (empty($path)) {
        return 'https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80';
    }
    if (preg_match('/^https?:\/\//i', $path)) {
        return $path;
    }
    $path = ltrim($path, '/');
    if (strpos($path, 'Kamadenu/') === 0) {
        $path = substr($path, 9);
    }
    return '/Kamadenu/' . $path;
}

/**
 * Language Helper
 */
function get_current_lang() {
    static $lang_cache = null;
    if ($lang_cache !== null) {
        return $lang_cache;
    }
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'kn', 'hi'])) {
        $_SESSION['lang'] = $_GET['lang'];
        if (!headers_sent()) {
            setcookie('kamadenu_lang', $_GET['lang'], time() + (86400 * 30), "/");
        }
    }
    if (empty($_SESSION['lang'])) {
        $_SESSION['lang'] = isset($_COOKIE['kamadenu_lang']) ? $_COOKIE['kamadenu_lang'] : 'en';
    }
    $lang_cache = $_SESSION['lang'];
    return $lang_cache;
}

$current_lang = get_current_lang();
$lang_file = __DIR__ . '/../languages/' . $current_lang . '.php';
if (file_exists($lang_file)) {
    $translations = require $lang_file;
} else {
    $translations = require __DIR__ . '/../languages/en.php';
}

function __t($key, $default = '') {
    global $translations;
    if (isset($translations[$key]) && $translations[$key] !== '') {
        return $translations[$key];
    }
    return $default !== '' ? $default : $key;
}

/**
 * Dynamic Database Row Field Translation Helper
 */
function __td($row, $field, $default = '') {
    $lang = get_current_lang();
    if ($lang !== 'en' && is_array($row) && isset($row[$field . '_' . $lang]) && !empty($row[$field . '_' . $lang])) {
        return $row[$field . '_' . $lang];
    }
    if (is_array($row) && isset($row[$field]) && !empty($row[$field])) {
        return $row[$field];
    }
    return $default;
}


/**
 * Theme Helper
 */
function get_current_theme() {
    static $theme_cache = null;
    if ($theme_cache !== null) {
        return $theme_cache;
    }
    if (isset($_GET['theme']) && in_array($_GET['theme'], ['light', 'dark', 'terracotta'])) {
        $_SESSION['theme'] = $_GET['theme'];
        if (!headers_sent()) {
            setcookie('kamadenu_theme', $_GET['theme'], time() + (86400 * 30), "/");
        }
    }
    if (!isset($_SESSION['theme'])) {
        $_SESSION['theme'] = isset($_COOKIE['kamadenu_theme']) ? $_COOKIE['kamadenu_theme'] : 'terracotta';
    }
    $theme_cache = $_SESSION['theme'];
    return $theme_cache;
}

/**
 * CSRF Protection
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitization & Escaping
 */
function e($string) {
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

/**
 * JSON Response Helper
 */
function json_response($success, $message, $data = [], $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('c')
    ]);
    exit;
}

/**
 * Auth Helpers
 */
function is_user_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function current_user($pdo) {
    if (!is_user_logged_in()) return null;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND status = 'active'");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function is_admin_logged_in() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function current_admin($pdo) {
    if (!is_admin_logged_in()) return null;
    $stmt = $pdo->prepare("SELECT a.*, r.name as role_name, r.display_name as role_display FROM admins a JOIN roles r ON a.role_id = r.id WHERE a.id = ? AND a.status = 'active'");
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch();
}

function require_admin_login($pdo) {
    if (!is_admin_logged_in()) {
        header("Location: /Kamadenu/admin/login.php");
        exit;
    }
}

function require_user_login() {
    if (!is_user_logged_in()) {
        $target = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/Kamadenu/products.php';
        header("Location: /Kamadenu/login.php?redirect=" . urlencode($target) . "&msg=login_required");
        exit;
    }
}

/**
 * Audit Log Helper
 */
function log_audit($pdo, $action, $target_table = null, $record_id = null, $old_vals = null, $new_vals = null) {
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
    $admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'System/Guest';
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';

    $stmt = $pdo->prepare("INSERT INTO audit_logs (admin_id, admin_name, action, target_table, record_id, old_values, new_values, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $admin_id,
        $admin_name,
        $action,
        $target_table,
        $record_id,
        is_array($old_vals) ? json_encode($old_vals) : $old_vals,
        is_array($new_vals) ? json_encode($new_vals) : $new_vals,
        $ip
    ]);
}

/**
 * Get system setting value by key
 */
function get_setting($pdo, $key, $default = '') {
    static $settings_cache = [];
    if (isset($settings_cache[$key])) {
        return $settings_cache[$key];
    }
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $val = $stmt->fetchColumn();
    if ($val === false) {
        return $default;
    }
    $settings_cache[$key] = $val;
    return $val;
}

/**
 * Get homepage-specific setting with multi-language fallback
 * Tries language-suffixed key first (e.g., hero_1_title_kn), falls back to English
 */
function get_hp($pdo, $key, $default = '') {
    static $hp_cache = [];
    $lang = get_current_lang();
    $localized_key = ($lang !== 'en') ? $key . '_' . $lang : $key;

    if (isset($hp_cache[$localized_key])) return $hp_cache[$localized_key];

    // Try localized version first
    if ($lang !== 'en') {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$localized_key]);
        $val = $stmt->fetchColumn();
        if ($val !== false && $val !== '') {
            $hp_cache[$localized_key] = $val;
            return $val;
        }
    }

    // Fallback to base English key
    if (isset($hp_cache[$key])) return $hp_cache[$key];
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $val = $stmt->fetchColumn();
    $result = ($val !== false && $val !== '') ? $val : $default;
    $hp_cache[$key] = $result;
    return $result;
}

/**
 * Ensure database schema has necessary columns for contact methods & WhatsApp setting configurations
 */
function ensure_database_schema($pdo) {
    $tables = ['cows', 'products', 'seva', 'emergency_campaigns'];
    foreach ($tables as $table) {
        try {
            // Check for whatsapp_number_id
            $check = $pdo->query("SHOW COLUMNS FROM `$table` LIKE 'whatsapp_number_id'")->fetch();
            if (!$check) {
                $pdo->exec("ALTER TABLE `$table` ADD COLUMN `whatsapp_number_id` INT DEFAULT NULL");
            }
            
            // Check for contact_method
            $check = $pdo->query("SHOW COLUMNS FROM `$table` LIKE 'contact_method'")->fetch();
            if (!$check) {
                $pdo->exec("ALTER TABLE `$table` ADD COLUMN `contact_method` VARCHAR(20) DEFAULT 'website'");
            }
            
            // Check for whatsapp_message
            $check = $pdo->query("SHOW COLUMNS FROM `$table` LIKE 'whatsapp_message'")->fetch();
            if (!$check) {
                $pdo->exec("ALTER TABLE `$table` ADD COLUMN `whatsapp_message` TEXT DEFAULT NULL");
            }
        } catch (PDOException $e) {
            // Table might not exist or other issues; skip silently
        }
    }

    // Ensure product_checkout_method key exists in settings table
    try {
        $check = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = 'product_checkout_method'");
        $check->execute();
        if ($check->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO settings (setting_key, setting_value, setting_group, description) VALUES ('product_checkout_method', 'both', 'whatsapp', 'Checkout action mode for all products: website, whatsapp, or both')");
        }
    } catch (PDOException $e) {
        // Table might not exist or other issues; skip silently
    }

    // Ensure donation_action_mode key exists in settings table
    try {
        $check = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = 'donation_action_mode'");
        $check->execute();
        if ($check->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO settings (setting_key, setting_value, setting_group, description) VALUES ('donation_action_mode', 'both', 'payment', 'Checkout action mode for donations: website, whatsapp, qrcode, website_qrcode, whatsapp_qrcode, or all')");
        }
    } catch (PDOException $e) {
        // Table might not exist or other issues; skip silently
    }

    // Ensure donation_qr_code key exists in settings table
    try {
        $check = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = 'donation_qr_code'");
        $check->execute();
        if ($check->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO settings (setting_key, setting_value, setting_group, description) VALUES ('donation_qr_code', 'assets/images/donation_qr.png', 'payment', 'QR code image path for donations')");
        }
    } catch (PDOException $e) {
        // Table might not exist or other issues; skip silently
    }

    // Ensure donation_upi_id key exists in settings table
    try {
        $check = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = 'donation_upi_id'");
        $check->execute();
        if ($check->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO settings (setting_key, setting_value, setting_group, description) VALUES ('donation_upi_id', 'kamadenu@upi', 'payment', 'Official Goushala UPI ID for donation transfers')");
        }
    } catch (PDOException $e) {
        // Table might not exist or other issues; skip silently
    }

    // Ensure videos table has default active videos if empty
    try {
        $check = $pdo->query("SELECT COUNT(*) FROM videos");
        if ($check->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO videos (title, description, youtube_url) VALUES 
                ('Daily Gouseva & Feeding Rituals', 'Experience the serene atmosphere during our morning grass feeding programs and daily Gouseva rituals performed with devotion.', 'https://www.youtube.com/watch?v=zF7hG8sBqA4'),
                ('Cattle Rescue & Medical Rehabilitation', 'A glimpse into our rescue operations for stray, injured, and orphaned cows, and their medical recovery journey at our sanctuary hospital.', 'https://www.youtube.com/watch?v=t31V7Xy3pCo'),
                ('Vedic Sanctuary Introduction & Tour', 'A guided walk through our Nelamangala sanctuary, showing how the cows are housed, fed and treated.', 'https://www.youtube.com/watch?v=s4X2gSg-J_U')
            ");
        }
    } catch (PDOException $e) {
        // Fail silently
    }

    // Ensure gallery table has default photos if empty
    try {
        $check = $pdo->query("SELECT COUNT(*) FROM gallery");
        if ($check->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO gallery (title, category, image, caption) VALUES 
                ('Morning Fodder Seva', 'Seva', 'https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80', 'Devotees offering fresh Napier grass to Gir cows.'),
                ('Sacred Gou Aarti', 'Worship', 'https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?auto=format&fit=crop&w=600&q=80', 'Daily evening Aarti ceremony conducted with Vedic chants.'),
                ('Rescued Calf Playtime', 'Goushala Life', 'https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=600&q=80', 'Kapila enjoying open pasture sunshine.')
            ");
        }
    } catch (PDOException $e) {
        // Fail silently
    }

    // Ensure feed_items table exists
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `feed_items` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `title_kn` VARCHAR(255) NULL,
            `title_hi` VARCHAR(255) NULL,
            `description` TEXT NOT NULL,
            `description_kn` TEXT NULL,
            `description_hi` TEXT NULL,
            `cost` DECIMAL(10,2) NOT NULL,
            `image` VARCHAR(255) NOT NULL,
            `contact_method` VARCHAR(20) DEFAULT 'website',
            `whatsapp_number_id` INT DEFAULT NULL,
            `whatsapp_message` TEXT DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    } catch (PDOException $e) {
        // Fail silently
    }

    // Ensure feed_items table has default items if empty
    try {
        $check = $pdo->query("SELECT COUNT(*) FROM feed_items");
        if ($check->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO feed_items (title, title_kn, title_hi, description, description_kn, description_hi, cost, image) VALUES 
                ('Fresh Napier Grass (100 kg)', 'ಹಸಿರು ಹುಲ್ಲು (100 ಕೆಜಿ)', 'हरी घास (100 किलो)', 'Sponsor 100 kg of fresh green Napier grass harvested from our bio-farms.', 'ನಮ್ಮ ಜೈವಿಕ ಫಾರ್ಮ್‌ಗಳಿಂದ ಕೊಯ್ಲು ಮಾಡಿದ 100 ಕೆಜಿ ತಾಜಾ ಹಸಿರು ನೇಪಿಯರ್ ಹುಲ್ಲು.', 'हमारे जैव-खेतों से काटी गई 100 किलोग्राम ताजी हरी नेपियर घास।', 500.00, 'https://images.unsplash.com/photo-1546445317-29f4545f9d52?auto=format&fit=crop&w=600&q=80'),
                ('Nutritious Wheat Bran (50 kg)', 'ಗೋಧಿ ಹೊಟ್ಟು (50 ಕೆಜಿ)', 'गेहूं का चोकर (50 किलो)', 'Sponsor a bag of high-fiber, highly digestible wheat bran mash for weak and milking cows.', 'ಬಲಹೀನ ಮತ್ತು ಹಾಲು ಕೊಡುವ ಹಸುಗಳಿಗೆ ಹೆಚ್ಚಿನ ನಾರಿನಂಶವಿರುವ ಗೋಧಿ ಹೊಟ್ಟು.', 'कमजोर और दुधारू गायों के लिए उच्च फाइबर युक्त गेहूं का चोकर।', 1200.00, 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=600&q=80'),
                ('Sacred Jaggery & Oil Cake Feast', 'ಬೆಲ್ಲ ಮತ್ತು ಹಿಂಡಿ ಔತಣ', 'गुड़ और खली भोज', 'A special energy feast containing mustard cake, cotton seed cake, and pure jaggery.', 'ಸಾಸಿವೆ ಹಿಂಡಿ, ಹತ್ತಿ ಬೀಜದ ಹಿಂಡಿ ಮತ್ತು ಶುದ್ಧ ಬೆಲ್ಲವನ್ನು ಹೊಂದಿರುವ ವಿಶೇಷ ಶಕ್ತಿ ಹಬ್ಬ.', 'सरसों की खली, बिनौला खली और शुद्ध गुड़ से युक्त एक विशेष ऊर्जा भोज।', 800.00, 'https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=600&q=80')
            ");
        }
    } catch (PDOException $e) {
        // Fail silently
    }

    // Ensure feed_logs table exists
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `feed_logs` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `feed_item_id` INT NOT NULL,
            `user_id` INT NULL,
            `sponsor_name` VARCHAR(255) NOT NULL,
            `date_sponsored` DATE NOT NULL,
            `status` VARCHAR(50) NOT NULL,
            `amount_paid` DECIMAL(10,2) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    } catch (PDOException $e) {
        // Fail silently
    }

    // Ensure feeding_cows table exists
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `feeding_cows` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `cow_code` VARCHAR(20) NOT NULL UNIQUE,
            `name` VARCHAR(100) NOT NULL,
            `description` TEXT NULL,
            `photo` VARCHAR(255) DEFAULT 'assets/images/cow-default.jpg',
            `feed_amount` DECIMAL(10,2) NOT NULL DEFAULT 500.00,
            `is_available` TINYINT(1) DEFAULT 1,
            `payment_method` VARCHAR(20) DEFAULT 'both',
            `whatsapp_number_id` INT DEFAULT NULL,
            `whatsapp_message` TEXT DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    } catch (PDOException $e) {
        // Fail silently
    }

    // Ensure feeding_cow_logs table exists
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `feeding_cow_logs` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `feeding_cow_id` INT NOT NULL,
            `user_id` INT NULL,
            `sponsor_name` VARCHAR(255) NOT NULL,
            `sponsor_email` VARCHAR(255) NOT NULL,
            `sponsor_phone` VARCHAR(50) NOT NULL,
            `date_sponsored` DATE NOT NULL,
            `status` VARCHAR(50) NOT NULL,
            `amount_paid` DECIMAL(10,2) NOT NULL,
            `payment_id` VARCHAR(100) NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    } catch (PDOException $e) {
        // Fail silently
    }
}

// Auto-run schema check
ensure_database_schema($pdo);

/**
 * Ensure homepage settings exist in DB with default values (self-healing)
 */
function ensure_hp_settings($pdo, array $defaults) {
    ensure_database_schema($pdo);
    foreach ($defaults as $key => $value) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value, setting_group, description) VALUES (?, ?, 'homepage', ?)");
        $stmt->execute([$key, $value, 'Homepage setting: ' . $key]);
    }
}

