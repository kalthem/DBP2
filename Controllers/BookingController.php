<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../db_connect.php';

$baseUrl = 'http://20.126.5.244/~u202103011/BorrowMyCharger';

// Validate input
$userId = $_SESSION['user']['id'] ?? null;
$chargePointId = $_POST['charge_point_id'] ?? null;
$startTime = $_POST['start_time'] ?? null;
$endTime = $_POST['end_time'] ?? null;
$messageText = trim($_POST['message'] ?? '');

if (!$userId || !$chargePointId || !$startTime || !$endTime) {
    $_SESSION['booking_error'] = "All booking fields are required.";
    header("Location: $baseUrl/index.php?action=bookchargepoint&id=$chargePointId");
    exit;
}

try {
    $db = Database::getInstance()->getdbConnection();

    // Insert into bookings
    $stmt = $db->prepare("
        INSERT INTO booking (user_id, charge_point_id, start_time, end_time, status)
        VALUES (?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([$userId, $chargePointId, $startTime, $endTime]);

    // Get the homeowner's user ID
    $stmtHomeowner = $db->prepare("SELECT owner_id FROM charge_point WHERE id = ?");
    $stmtHomeowner->execute([$chargePointId]);
    $ownerId = $stmtHomeowner->fetchColumn();

    // Insert message to homeowner
    if ($messageText && $ownerId) {
        $stmtMessage = $db->prepare("
            INSERT INTO messages (sender_id, receiver_id, charge_point_id, message)
            VALUES (?, ?, ?, ?)
        ");
        $stmtMessage->execute([$userId, $ownerId, $chargePointId, $messageText]);
    }

    // Save booking info for confirmation page
    $_SESSION['booking_confirmation'] = [
        'charge_point_id' => $chargePointId,
        'start_time' => $startTime,
        'end_time' => $endTime,
        'message' => $messageText
    ];

    header("Location: $baseUrl/index.php?action=bookingconfirmation");
    exit;

} catch (Throwable $e) {
    error_log("Booking Error: " . $e->getMessage());
    $_SESSION['booking_error'] = "Booking failed. Please try again.";
    header("Location: $baseUrl/index.php?action=bookchargepoint&id=$chargePointId");
    exit;
}
