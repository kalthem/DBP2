<?php
require_once __DIR__ . '/../db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'homeowner') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

$bookingId = $_GET['id'] ?? null;
$status = $_GET['status'] ?? null;

if (!$bookingId || !in_array($status, ['confirmed', 'declined'])) {
    http_response_code(400);
    echo "Invalid request.";
    exit;
}

$pdo = Database::getInstance()->getdbConnection();
$stmt = $pdo->prepare("UPDATE booking SET status = ?, updated_at = NOW() WHERE id = ?");
$stmt->execute([$status, $bookingId]);

echo "Status updated.";
