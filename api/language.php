<?php
require_once __DIR__ . '/../config/database.php';

$input = json_decode(file_get_contents('php://input'), true);
$lang = isset($input['lang']) ? $input['lang'] : (isset($_GET['lang']) ? $_GET['lang'] : 'en');

if (in_array($lang, ['en', 'kn', 'hi'])) {
    $_SESSION['lang'] = $lang;
    setcookie('kamadenu_lang', $lang, time() + (86400 * 30), "/");
    json_response(true, 'Language updated to ' . $lang, ['lang' => $lang]);
} else {
    json_response(false, 'Invalid language option');
}
