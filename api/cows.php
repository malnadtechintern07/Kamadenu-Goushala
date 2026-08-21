<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : 'list');

// 1. GET / SEARCH COWS LIST
if ($action === 'list') {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $breed = isset($_GET['breed']) ? trim($_GET['breed']) : '';
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';

    $query = "SELECT * FROM cows WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $query .= " AND (name LIKE ? OR cow_code LIKE ? OR breed LIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }

    if (!empty($breed)) {
        $query .= " AND breed = ?";
        $params[] = $breed;
    }

    if (!empty($status)) {
        $query .= " AND adoption_status = ?";
        $params[] = $status;
    }

    $query .= " ORDER BY id DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $cows = $stmt->fetchAll();

    json_response(true, 'Cows retrieved', ['cows' => $cows, 'count' => count($cows)]);
}

// 2. GET SINGLE COW PASSPORT DETAILS
if ($action === 'detail') {
    $id = isset($_GET['id']) ? $_GET['id'] : 0;
    
    $stmt = $pdo->prepare("SELECT * FROM cows WHERE id = ? OR cow_code = ?");
    $stmt->execute([$id, $id]);
    $cow = $stmt->fetch();

    if (!$cow) {
        json_response(false, 'Cow not found');
    }

    // Health records
    $stmt = $pdo->prepare("SELECT * FROM cow_health WHERE cow_id = ? ORDER BY last_checkup_date DESC LIMIT 5");
    $stmt->execute([$cow['id']]);
    $health_logs = $stmt->fetchAll();

    // Journey Timeline
    $stmt = $pdo->prepare("SELECT * FROM cow_journey WHERE cow_id = ? ORDER BY milestone_date ASC");
    $stmt->execute([$cow['id']]);
    $journey = $stmt->fetchAll();

    // Updates
    $stmt = $pdo->prepare("SELECT * FROM cow_updates WHERE cow_id = ? ORDER BY id DESC");
    $stmt->execute([$cow['id']]);
    $updates = $stmt->fetchAll();

    json_response(true, 'Cow details retrieved', [
        'cow' => $cow,
        'health_logs' => $health_logs,
        'journey' => $journey,
        'updates' => $updates
    ]);
}

json_response(false, 'Invalid request');
