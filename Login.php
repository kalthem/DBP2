<?php
// Login.php

session_start();
require_once "Models/Login.php"; // make sure path is correct

$loginError = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $loginModel = new Login();

    if ($loginModel->authenticate($email, $password)) {
        $_SESSION['user'] = $email;
        header("Location: index.php");
        exit();
    } else {
        $loginError = "Invalid email or password.";
    }
}

include "Views/Login.phtml";
