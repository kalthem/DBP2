<?php
require_once __DIR__ . '/../Models/Message.php';
require_once __DIR__ . '/../db_connect.php';

class MessageController {
    private $messageModel;
    private $baseUrl = 'http://20.126.5.244/~u202103011/BorrowMyCharger';

    public function __construct() {
        $database = Database::getInstance();
        $pdo = $database->getdbConnection();
        $this->messageModel = new Message($pdo);
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

        if ($action === 'sendMessage') {
            $this->sendMessage();
        } else {
            header("Location: {$this->baseUrl}/Views/userDashBoard.phtml");
            exit;
        }
    }

    private function sendMessage() {
        $senderId = (int)$_SESSION['user']['id'];
        $ownerId = (int)$_POST['owner_id'];
        $chargePointId = (int)$_POST['charge_point_id'];
        $message = trim($_POST['message']);

        if (empty($message)) {
            $_SESSION['error'] = 'Message cannot be empty';
            header("Location: {$this->baseUrl}/Views/bookingConfirmation.phtml?charge_point_id=$chargePointId");
            exit;
        }

        $success = $this->messageModel->createMessage([
            'sender_id' => $senderId,
            'receiver_id' => $ownerId,
            'charge_point_id' => $chargePointId,
            'message' => $message,
            'status' => 'unread'
        ]);

        if ($success) {
            $_SESSION['success'] = 'Message sent successfully!';
        } else {
            $_SESSION['error'] = 'Failed to send message. Please try again.';
        }

        header("Location: {$this->baseUrl}/Views/bookingConfirmation.phtml?charge_point_id=$chargePointId");
        exit;
    }
}

// Instantiate and run the controller
$controller = new MessageController();
$controller->handleRequest();