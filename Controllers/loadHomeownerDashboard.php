<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

if (!isset($_SESSION['user']['id']) || $_SESSION['user']['role'] !== 'homeowner') exit;

$pdo = Database::getInstance()->getdbConnection();
$ownerId = $_SESSION['user']['id'];
$section = $_GET['section'] ?? '';

if ($section === 'chargepoint') {
    $stmt = $pdo->prepare("SELECT c.*, l.city FROM charge_point c JOIN location l ON c.location_id = l.id WHERE c.owner_id = ?");
    $stmt->execute([$ownerId]);
    $cp = $stmt->fetch();

    if ($cp):
        echo "<h4>Your Charge Point</h4>
              <p><strong>Type:</strong> {$cp['charger_type']}</p>
              <p><strong>Status:</strong> {$cp['status']}</p>
              <p><strong>Price:</strong> {$cp['price_per_kwh']} BHD/kWh</p>
              <p><strong>City:</strong> {$cp['city']}</p>
              <p><strong>Available:</strong> {$cp['availability_days']}</p>";
    else:
        echo '<div class="charge-box">
                <h4>YOU HAVEN\'T ADDED A CHARGE POINT YET.</h4>
                <a href="index.php?action=addchargepoint" class="btn btn-dark mt-3">ADD NOW</a>
              </div>';
    endif;

} elseif ($section === 'bookings') {
    $stmt = $pdo->prepare("
        SELECT b.*, u.name AS user_name, m.message 
        FROM booking b
        JOIN users u ON b.user_id = u.id
        LEFT JOIN messages m ON m.charge_point_id = b.charge_point_id AND m.sender_id = u.id
        WHERE b.charge_point_id IN (SELECT id FROM charge_point WHERE owner_id = ?)
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$ownerId]);
    $bookings = $stmt->fetchAll();

    echo "<h4>Manage Bookings</h4>";
    if (!$bookings) {
        echo "<div class='alert alert-info'>No booking requests yet.</div>";
    }

    foreach ($bookings as $b) {
        echo "<div class='card mb-3 p-3'>
                <p><strong>User:</strong> {$b['user_name']}</p>
                <p><strong>From:</strong> {$b['start_time']} - {$b['end_time']}</p>
                <p><strong>Message:</strong> " . ($b['message'] ?? 'No message') . "</p>
                <p><strong>Status:</strong> {$b['status']}</p>";

        if ($b['status'] === 'pending') {
            echo "<button class='btn approve-btn' data-id='{$b['id']}'>Approve</button>
                  <button class='btn decline-btn' data-id='{$b['id']}'>Decline</button>";
        }

        echo "</div>";
    }
}
