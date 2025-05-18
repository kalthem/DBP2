<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Absolute base path to your project
$basePath = '/home/u202103011/public_html/BorrowMyCharger';
$viewsPath = $basePath . '/Views';

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: $baseUrl/index.php?action=home");
    exit;
}

// Allowed actions and their corresponding view files
$routes = [
    'about' => 'About.phtml',
    'howitworks' => 'HowItWorks.phtml',
    'login' => 'Login.phtml',
    'register' => 'register.phtml',
    'dashboard' => 'userDashBoard.phtml',
    'bookchargepoint' => 'bookChargePoint.phtml',
    'bookingconfirmation' => 'bookingConfirmation.phtml',
    'bookingsuccess' => 'booking_success.phtml',
    'mybookings' => 'my_bookings.phtml',
    'mapview' => 'mapView.phtml',
    'home' => 'index.phtml'
];

// Get route from URL and default to 'home'
$action = strtolower($_GET['action'] ?? 'home');

// Load the appropriate view
if (array_key_exists($action, $routes)) {
    $viewFile = $viewsPath . '/' . $routes[$action];
    if (file_exists($viewFile)) {
        require $viewFile;
    } else {
        echo "<h3 style='color: red;'>Error: View file for '$action' not found.</h3>";
    }
} else {
    echo "<h3 style='color: red;'>Error: Invalid page action '$action'.</h3>";
}

