<?php
session_start();
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../Models/User.php';

<<<<<<< Updated upstream
=======
$baseUrl = 'http://20.126.5.244/~u202103011/BorrowMyCharger';

>>>>>>> Stashed changes
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
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'status' => $user['status']
                ];

                $dashboard = match ($user['role']) {
                    'admin' => '../Views/adminDashBoard.phtml',
                    'homeowner' => '../Views/homeOwnerDashBoard.phtml',
                    'user' => '../Views/userDashBoard.phtml',
                    default => '../index.php'
                };

                header("Location: $dashboard");
                exit();
            }
        }
    }
}

require_once __DIR__ . '/../Views/Login.phtml';
