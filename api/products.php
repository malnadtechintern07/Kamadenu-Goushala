<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$query = "SELECT p.*, c.name as category_name FROM products p JOIN product_categories c ON p.category_id = c.id WHERE p.is_active = 1";
$params = [];

if ($category_id > 0) {
    $query .= " AND p.category_id = ?";
    $params[] = $category_id;
}

if (!empty($search)) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$query .= " ORDER BY p.id DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

json_response(true, 'Products fetched', ['products' => $products]);
