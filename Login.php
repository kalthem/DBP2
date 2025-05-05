<?php
session_start();

$loginError = "";

// Hardcoded credentials for demo
$validUser = "user@user.com";
$validPass = "123456";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email === $validUser && $password === $validPass) {
        $_SESSION['userId'] = $email;
        header("Location: index.php");
        exit;
    } else {
        $loginError = "Invalid username or password.";
    }
}

// Show form with layout
require 'Views/template/header.phtml';
require 'Views/login.phtml';
require 'Views/template/footer.phtml';
?>
