<?php
// MUST be first - no whitespace before!
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../Models/User.php';

$baseUrl = 'http://20.126.5.244/~u202103011/BorrowMyCharger';
$loginError = "";

// Show success alert from registration
if (isset($_SESSION['registration_success'])) {
    echo '<script>alert("Registration successful. Please log in.");</script>';
    unset($_SESSION['registration_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $loginError = "Please enter both username and password.";
    } else {
        $database = Database::getInstance();
        $pdo = $database->getdbConnection();
        $userModel = new User($pdo);

        $user = $userModel->authenticate($username, $password);

        if ($user === false) {
            $loginError = "System error. Please try again later.";
        } elseif ($user === null) {
            $loginError = "Username or password is incorrect.";
        } else {
            if ($user['status'] === 'suspended') {
                $loginError = "Your account has been suspended.";
            } elseif ($user['role'] === 'homeowner' && $user['status'] === 'pending') {
                $loginError = "Your homeowner account is pending approval.";
            } else {
                session_regenerate_id(true);
                
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'status' => $user['status']
                ];

                // Always redirect through index.php
                header("Location: $baseUrl/index.php?action=dashboard");
                exit();
            }
        }
    }
}

require_once __DIR__ . '/../Views/Login.phtml';
ob_end_flush();
?>