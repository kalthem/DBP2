<?php

require_once '../controllers/AdminController.php';
require_once '../config/database.php';

$db = new PDO($dsn, $username, $password);
$controller = new AdminController($db);

// Route actions
$action = $_GET['action'] ?? 'dashboard';

switch ($action) {
    case 'manage-users':
        $controller->manageUsers();
        break;
    case 'update-user':
        $controller->updateUser($_GET['id'], $_GET['status']);
        break;
    case 'manage-chargers':
        $controller->manageChargers();
        break;
    case 'delete-charger':
        $controller->deleteCharger($_GET['id']);
        break;
    case 'reports':
        $controller->generateReport($_POST['start_date'], $_POST['end_date']);
        break;
    default:
        $controller->dashboard();
}
