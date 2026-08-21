<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : 'list');

if ($action === 'list') {
    $stmt = $pdo->query("SELECT * FROM seva WHERE is_active = 1 ORDER BY id ASC");
    $items = $stmt->fetchAll();

    $stmt = $pdo->query("SELECT sl.*, s.title as seva_title, c.name as cow_name FROM seva_logs sl JOIN seva s ON sl.seva_id = s.id LEFT JOIN cows c ON sl.cow_id = c.id ORDER BY sl.id DESC LIMIT 10");
    $recent_logs = $stmt->fetchAll();

    json_response(true, 'Seva data fetched', ['seva_items' => $items, 'recent_logs' => $recent_logs]);
}

json_response(false, 'Invalid request');
