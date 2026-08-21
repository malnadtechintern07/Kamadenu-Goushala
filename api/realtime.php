<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

// Total Donations
$stmt = $pdo->query("SELECT SUM(amount) as total FROM donations WHERE status = 'Completed'");
$row = $stmt->fetch();
$total_donations = $row && $row['total'] ? floatval($row['total']) : 0.00;

// Emergency Campaigns
$stmt = $pdo->query("SELECT id, title, target_amount, raised_amount, status FROM emergency_campaigns WHERE status = 'Active'");
$campaigns = $stmt->fetchAll();

// Unread notifications for user/admin
$unread = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$_SESSION['user_id']]);
    $unread = $stmt->fetch()['cnt'];
}

json_response(true, 'Live metrics retrieved', [
    'total_donations' => $total_donations,
    'campaigns' => $campaigns,
    'unread_notifications' => $unread
]);
