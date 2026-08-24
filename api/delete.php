<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

// Ensure only authenticated admins can delete
if (!is_admin_logged_in()) {
    json_response(false, 'Unauthorized access. Admin login required.');
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$table = isset($input['table']) ? trim($input['table']) : '';
$id = isset($input['id']) ? intval($input['id']) : 0;

if (empty($table) || $id <= 0) {
    json_response(false, 'Invalid request parameters.');
}

// Whitelist tables to prevent arbitrary deletion
$allowed_tables = ['cows', 'products', 'emergency_campaigns', 'events', 'seva', 'stories', 'users', 'volunteers', 'sponsorships', 'donations', 'orders', 'videos'];

if (!in_array($table, $allowed_tables)) {
    json_response(false, 'Deletion not allowed for this table.');
}

try {
    $pdo->beginTransaction();
    
    // Disable foreign key checks to prevent constraints block
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    
    $stmt = $pdo->prepare("DELETE FROM `{$table}` WHERE id = ?");
    $stmt->execute([$id]);
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    
    log_audit($pdo, "Delete Record", $table, $id);
    
    $pdo->commit();
    
    json_response(true, 'Item deleted successfully.');
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_response(false, 'Error during deletion: ' . $e->getMessage());
}
