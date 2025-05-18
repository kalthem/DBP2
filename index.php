<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$basePath = '/home/u202103011/public_html/BorrowMyCharger';
$viewsPath = $basePath . '/Views';
$controllersPath = $basePath . '/Controllers';

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.php?action=home");
    exit;
}

// Define view routes
$viewRoutes = [
    'about' => 'About.phtml',
    'howitworks' => 'HowItWorks.phtml',
    'login' => 'Login.phtml',
    'register' => 'register.phtml',
    'userdashboard' => 'userDashBoard.phtml',
    'bookchargepoint' => 'bookChargePoint.phtml',
    'bookingconfirmation' => 'bookingConfirmation.phtml',
    'bookingsuccess' => 'booking_success.phtml',
    'mybookings' => 'my_bookings.phtml',
    'mapview' => 'mapView.phtml',
    'homeownerdashboard' => 'homeOwnerDashBoard.phtml',  // ✅ Homeowner Dashboard
    'managechargepoint' => 'manageChargePointHomeOwner.phtml', // ✅ Clean final view for management
    'home' => 'index.phtml'
];

// Define controller routes
$controllerRoutes = [
    'admindashboard' => 'AdminController.php',
    'manageusers' => 'ManageUsersController.php',
    'managechargers' => 'ManageChargePointsController.php',
    'reports' => 'AdminReportsController.php',
    'updatebookingstatus' => 'updateBookingStatus.php',
    'loadhomeownerbookings' => 'loadHomeownerBookings.php',
    'loadchargepoint' => 'loadChargePoint.php',
    'updatechargepoint' => 'updateChargePoint.php',
    'homeownermanagecharger' => 'homeownermanagecharger.php' // ✅ Controller to handle form logic
];

// Get requested action
$action = strtolower($_GET['action'] ?? 'home');

// First check if it’s a controller route
if (array_key_exists($action, $controllerRoutes)) {
    $file = $controllersPath . '/' . $controllerRoutes[$action];
    if (file_exists($file)) {
        require $file;
        exit;
    } else {
        echo "<h3 style='color: red;'>Error: Controller for '$action' not found.</h3>";
        exit;
    }
}

// Otherwise, check if it’s a view route
if (array_key_exists($action, $viewRoutes)) {
    $file = $viewsPath . '/' . $viewRoutes[$action];
    if (file_exists($file)) {
        require $file;
        exit;
    } else {
        echo "<h3 style='color: red;'>Error: View for '$action' not found.</h3>";
        exit;
    }
}

// If nothing matches
echo "<h3 style='color: red;'>Error: Invalid action '$action'.</h3>";
