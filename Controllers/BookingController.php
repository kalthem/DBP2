<?php
require_once __DIR__ . '/../Models/Booking.php';
require_once __DIR__ . '/../Models/ChargePoint.php';
require_once __DIR__ . '/../db_connect.php';

class BookingController {
    private $bookingModel;
    private $chargePointModel;
    private $baseUrl = 'http://20.126.5.244/~u202103011/BorrowMyCharger';

    public function __construct() {
        $database = Database::getInstance();
        $pdo = $database->getdbConnection();
        $this->bookingModel = new Booking($pdo);
        $this->chargePointModel = new ChargePoint($pdo);
    }

    public function handleRequest() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['user']['id'])) {
            header("Location: {$this->baseUrl}/index.php?action=login");
            exit;
        }

        $action = $_POST['action'] ?? $_GET['action'] ?? '';

        switch ($action) {
            case 'confirmBooking':
                $this->confirmBooking();
                break;
            case 'cancelBooking':
                $this->cancelBooking();
                break;
            default:
                header("Location: {$this->baseUrl}/Views/userDashBoard.phtml");
                exit;
        }
    }

    private function confirmBooking() {
        $userId = (int)$_SESSION['user']['id'];
        $chargePointId = (int)$_POST['charge_point_id'];
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];

        // Validate input
        if (empty($chargePointId) || empty($startTime) || empty($endTime)) {
            $_SESSION['error'] = 'All fields are required';
            header("Location: {$this->baseUrl}/Views/bookingConfirmation.phtml?charge_point_id=$chargePointId");
            exit;
        }

        // Check charger availability
        $isAvailable = $this->chargePointModel->checkAvailability($chargePointId, $startTime, $endTime);
        
        if (!$isAvailable) {
            $_SESSION['error'] = 'The charger is not available for the selected time slot';
            header("Location: {$this->baseUrl}/Views/bookingConfirmation.phtml?charge_point_id=$chargePointId");
            exit;
        }

        // Create booking
        $bookingId = $this->bookingModel->createBooking([
            'user_id' => $userId,
            'charge_point_id' => $chargePointId,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'confirmed'
        ]);

        if ($bookingId) {
            // Update charger status to "in_use" during the booked time
            $this->chargePointModel->updateStatus($chargePointId, 'in_use');
            
            $_SESSION['success'] = 'Booking confirmed successfully!';
            header("Location: {$this->baseUrl}/Views/bookingHistory.phtml");
            exit;
        } else {
            $_SESSION['error'] = 'Failed to create booking. Please try again.';
            header("Location: {$this->baseUrl}/Views/bookingConfirmation.phtml?charge_point_id=$chargePointId");
            exit;
        }
    }

    private function cancelBooking() {
        $bookingId = (int)$_POST['booking_id'];
        $userId = (int)$_SESSION['user']['id'];

        // Verify the booking belongs to the user
        $booking = $this->bookingModel->getBookingById($bookingId);
        
        if (!$booking || $booking['user_id'] !== $userId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized action']);
            exit;
        }

        // Only allow cancellation if booking is pending or confirmed
        if (!in_array($booking['status'], ['pending', 'confirmed'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking cannot be cancelled at this stage']);
            exit;
        }

        // Cancel the booking
        $success = $this->bookingModel->updateBookingStatus($bookingId, 'cancelled');
        
        if ($success) {
            // Update charger status back to available
            $this->chargePointModel->updateStatus($booking['charge_point_id'], 'available');
            
            echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
            exit;
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to cancel booking']);
            exit;
        }
    }
}

// Instantiate and run the controller
$controller = new BookingController();
$controller->handleRequest();