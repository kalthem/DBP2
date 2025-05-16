<?php
session_start();
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../Models/User.php';

$baseUrl = 'http://20.126.5.244/~u202103011/BorrowMyCharger';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if (empty($username)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($name)) {
        $errors[] = "Full name is required.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
        try {
            $database = Database::getInstance();
            $pdo = $database->getdbConnection();
            $userModel = new User($pdo);

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $status = ($role === 'homeowner') ? 'pending' : 'approved';

            $userModel->insertUser($username, $name, $hashedPassword, $role, $status);

            $_SESSION['registration_success'] = true;
            header('Location: ' . $baseUrl . '/Controllers/LoginController.php');
            exit();
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../Views/register.phtml';
