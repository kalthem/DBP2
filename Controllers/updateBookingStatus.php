<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

if (!isset($_SESSION['user']['id']) || $_SESSION['user']['role'] !== 'homeowner') exit;

$pdo = Database::getInstance()->getdbConnection();
$id = $_GET['id'] ?? 0;
$status = $_GET['status'] ?? '';

if (!in_array($status, ['confirmed', 'declined'])) exit;

$stmt = $pdo->prepare("UPDATE booking SET status = ?, updated_at = NOW() WHERE id = ? AND charge_point_id IN (SELECT id FROM charge_point WHERE owner_id = ?)");
$stmt->execute([$status, $id, $_SESSION['user']['id']]);
echo "updated";
