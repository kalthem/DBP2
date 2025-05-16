<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Set base path - adjust this to your exact server path
$basePath = '/home/u202103011/public_html/BorrowMyCharger';

// Set headers to prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

try {
    // Validate and sanitize inputs
    $minPrice = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : null;
    $maxPrice = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : null;
    $statusFilter = isset($_GET['status']) && in_array($_GET['status'], ['available', 'in_use', 'offline']) ? $_GET['status'] : null;
    $cityFilter = isset($_GET['city']) ? htmlspecialchars($_GET['city']) : null;

    // Debug: Log received parameters
    error_log("Filter params - min: $minPrice, max: $maxPrice, status: $statusFilter, city: $cityFilter");

    // Include required files using absolute paths
    require_once $basePath . '/Models/ChargePoint.php';
    require_once $basePath . '/db_connect.php';

    // Get database connection
    $database = Database::getInstance();
    $pdo = $database->getdbConnection();
    
    // Get filtered data
    $chargePointModel = new ChargePoint($pdo);
    $chargePoints = $chargePointModel->getFilteredChargePoints($minPrice, $maxPrice, $statusFilter, $cityFilter);
    
    // Pass variables to partial
    $tableChargePoints = $chargePoints;
    $tableBaseUrl = 'http://20.126.5.244/~u202103011/BorrowMyCharger';
    
    // Include the table partial
    $tablePath = $basePath . '/Views/partials/charge_points_table.phtml';
    if (file_exists($tablePath) && is_readable($tablePath)) {
        include $tablePath;
    } else {
        throw new Exception("Table partial file not found at: $tablePath");
    }
    
} catch (Exception $e) {
    // Log the full error
    error_log("Error in load_charge_points.php: " . $e->getMessage());
    
    // Return error message
    echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
}