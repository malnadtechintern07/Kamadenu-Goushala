<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) $input = $_POST;

    $name = trim($input['name']);
    $email = trim($input['email']);
    $phone = trim($input['phone']);
    $skills = isset($input['skills']) ? trim($input['skills']) : '';
    $availability = isset($input['availability']) ? trim($input['availability']) : '';
    $interest_area = isset($input['interest_area']) ? trim($input['interest_area']) : '';
    $message = isset($input['message']) ? trim($input['message']) : '';
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if (empty($name) || empty($email) || empty($phone)) {
        json_response(false, 'Name, email, and phone number are required.');
    }

    $stmt = $pdo->prepare("INSERT INTO volunteers (user_id, name, email, phone, skills, availability, interest_area, message, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->execute([$user_id, $name, $email, $phone, $skills, $availability, $interest_area, $message]);

    json_response(true, 'Your volunteer application has been submitted successfully! Our team will get in touch soon.');
}

json_response(false, 'Invalid request');
