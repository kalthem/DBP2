<?php
require_once __DIR__ . '/../Models/AdminReportsModel.php';

session_start();

// Ensure only admins can access the reports page
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit('Forbidden');
}

$model = new AdminReportsModel();
$predefinedReports = $model->getPredefinedReports();
$selectedReport = null;
$reportData = [];

// Check if a report was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report'])) {
    $selectedReport = $_POST['report'];
    $reportData = $model->runPredefinedReport($selectedReport);
}

// Load the view
require_once __DIR__ . '/../Views/adminReports.phtml';