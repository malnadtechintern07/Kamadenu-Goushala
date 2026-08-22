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
