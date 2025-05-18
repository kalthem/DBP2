<?php
require_once __DIR__ . '/../db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'homeowner') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

$pdo = Database::getInstance()->getdbConnection();
$userId = $_SESSION['user_id'];
$chargePointId = $_POST['id'] ?? null;

$chargerType = $_POST['charger_type'] ?? '';
$status = $_POST['status'] ?? 'available';
$availability = $_POST['availability_days'] ?? '';
$price = $_POST['price_per_hour'] ?? 0.00;

// Handle image upload if present
$imagePath = null;
if (!empty($_FILES['image']['name'])) {
    $uploadDir = '../uploads/';
    $fileName = basename($_FILES['image']['name']);
    $targetPath = $uploadDir . time() . '_' . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        $imagePath = str_replace('../', '', $targetPath);
    }
}

$query = "UPDATE charge_point SET charger_type = ?, status = ?, availability_days = ?, price_per_hour = ?";
$params = [$chargerType, $status, $availability, $price];

if ($imagePath) {
    $query .= ", image_url = ?";
    $params[] = $imagePath;
}

$query .= " WHERE id = ? AND owner_id = ?";
$params[] = $chargePointId;
$params[] = $userId;

$stmt = $pdo->prepare($query);
$stmt->execute($params);

header("Location: ../index.php?action=homeownerdashboard");
exit;
