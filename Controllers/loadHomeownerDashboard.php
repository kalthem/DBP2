<?php
require_once __DIR__ . '/../db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'homeowner') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

$pdo = Database::getInstance()->getdbConnection();
$userId = $_SESSION['user_id'];
$section = $_GET['section'] ?? '';

$baseUrl = 'http://20.126.5.244/~u202103011/BorrowMyCharger';

if ($section === 'chargepoint') {
    $stmt = $pdo->prepare("SELECT cp.*, l.city, l.road, l.block 
                           FROM charge_point cp 
                           JOIN location l ON cp.location_id = l.id 
                           WHERE cp.owner_id = ?");
    $stmt->execute([$userId]);
    $cp = $stmt->fetch();

    if ($cp) {
        $imgPath = !empty($cp['image_url']) ? htmlspecialchars($cp['image_url']) : "$baseUrl/images/default-charger.jpg";
        $price = number_format($cp['price_per_hour'], 2);

        echo "<div class='row justify-content-center'>
                <div class='col-md-6'>
                    <div class='card p-3 text-center'>
                        <h5 class='mb-3'>Your Charge Point</h5>
                        <img src='{$imgPath}' alt='Charge Point Image' 
                             style='width: 250px; height: 250px; object-fit: cover; border-radius: 8px; margin: 0 auto 15px; display: block;'>
                        <p><strong>Charger Type:</strong> " . htmlspecialchars($cp['charger_type']) . "</p>
                        <p><strong>Location:</strong> " . htmlspecialchars($cp['road']) . ", Block " . htmlspecialchars($cp['block']) . ", " . htmlspecialchars($cp['city']) . "</p>
                        <p><strong>Status:</strong> " . htmlspecialchars($cp['status']) . "</p>
                        <p><strong>Available Days:</strong> " . htmlspecialchars($cp['availability_days']) . "</p>
                        <p><strong>Price per Hour:</strong> {$price} BHD</p>
                    </div>
                </div>
              </div>";
    } else {
        echo "<div class='alert alert-warning text-center'>You have not added a charge point yet.</div>";
    }
}

elseif ($section === 'bookings') {
    $stmt = $pdo->prepare("SELECT b.*, u.name AS renter_name, u.username, m.message, m.id AS message_id
                           FROM booking b
                           JOIN users u ON b.user_id = u.id
                           JOIN charge_point cp ON b.charge_point_id = cp.id
                           LEFT JOIN messages m ON m.charge_point_id = cp.id AND m.sender_id = b.user_id
                           WHERE cp.owner_id = ? AND b.status = 'pending'
                           ORDER BY b.created_at DESC");
    $stmt->execute([$userId]);
    $bookings = $stmt->fetchAll();

    if ($bookings) {
        foreach ($bookings as $bk) {
            echo "<div class='booking-card card mb-3 p-3'>
                    <h5>Request from " . htmlspecialchars($bk['renter_name']) . " (" . htmlspecialchars($bk['username']) . ")</h5>
                    <p><strong>Start:</strong> " . htmlspecialchars($bk['start_time']) . "</p>
                    <p><strong>End:</strong> " . htmlspecialchars($bk['end_time']) . "</p>
                    <p><strong>Message:</strong> " . htmlspecialchars($bk['message']) . "</p>
                    <button class='btn btn-success approve-btn' data-id='" . $bk['id'] . "'>Approve</button>
                    <button class='btn btn-danger decline-btn' data-id='" . $bk['id'] . "'>Decline</button>
                  </div>";
        }
    } else {
        echo "<div class='alert alert-info text-center'>No booking requests at the moment.</div>";
    }
}

else {
    echo "<div class='alert alert-danger text-center'>Invalid section requested.</div>";
}
