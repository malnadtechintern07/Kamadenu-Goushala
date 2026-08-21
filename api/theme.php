<?php
require_once __DIR__ . '/../config/database.php';

$input = json_decode(file_get_contents('php://input'), true);
$theme = isset($input['theme']) ? $input['theme'] : (isset($_GET['theme']) ? $_GET['theme'] : 'terracotta');

if (in_array($theme, ['light', 'dark', 'terracotta'])) {
    $_SESSION['theme'] = $theme;
    setcookie('kamadenu_theme', $theme, time() + (86400 * 30), "/");
    json_response(true, 'Theme updated to ' . $theme, ['theme' => $theme]);
} else {
    json_response(false, 'Invalid theme option');
}
