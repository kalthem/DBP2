<?php
session_start();
$baseUrl = 'http://20.126.5.244/~u202103011/BorrowMyCharger';

// Include your database connection
require_once __DIR__ . '/../db_connect.php';
$database = Database::getInstance();
$pdo = $database->getdbConnection();

// Build the query with optional filters
$whereClauses = [];
$params = [];

if (!empty($_GET['citySearch'])) {
    $whereClauses[] = "l.city LIKE :citySearch";
    $params[':citySearch'] = '%' . $_GET['citySearch'] . '%';
}

if (!empty($_GET['chargerTypeSearch'])) {
    $whereClauses[] = "c.charger_type LIKE :chargerTypeSearch";
    $params[':chargerTypeSearch'] = '%' . $_GET['chargerTypeSearch'] . '%';
}

if (!empty($_GET['availabilityFilter'])) {
    $whereClauses[] = "c.status = :availabilityFilter";
    $params[':availabilityFilter'] = $_GET['availabilityFilter'];
}

// Final SQL query with conditions
$sql = "SELECT c.id, c.location_id, c.charger_type, l.city, l.latitude, l.longitude 
        FROM charge_point c 
        JOIN location l ON c.location_id = l.id";

if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// Fetch the results
$chargePoints = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the filtered charge points as a JSON response
echo json_encode($chargePoints);
