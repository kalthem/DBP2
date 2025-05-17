<?php
require_once __DIR__ . '/../Models/ManageUsersModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

$model = new ManageUsersModel();

// AJAX Search
if ($action === 'search' && isset($_GET['query'])) {
    $users = $model->searchUsers($_GET['query']);
    echo renderTableBody($users);
    exit;
}

// AJAX Filter or Pagination
if (($action === 'filter' || $action === 'paginate') && isset($_GET['filter'])) {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $data = $model->getUsers($_GET['filter'], $page);
    echo renderTableBody($data['users']);
    exit;
}

// AJAX Action Buttons
if ($action === 'perform' && isset($_POST['userId'], $_POST['userAction'])) {
    $result = $model->performBulkAction($_POST['userAction'], [intval($_POST['userId'])]);
    echo $result ? "success" : "error";
    exit;
}

// AJAX fetch stats only
if ($action === 'stats') {
    $stats = $model->getUserStats();
    // Return as JSON for easy JS update
    header('Content-Type: application/json');
    echo json_encode($stats);
    exit;
}

// Helper: Render table body for AJAX
function renderTableBody($users) {
    $html = "";
    foreach ($users as $user) {
        $html .= "<tr>
            <td>" . htmlspecialchars($user['id']) . "</td>
            <td>" . htmlspecialchars($user['name']) . "</td>
            <td>" . htmlspecialchars($user['username']) . "</td>
            <td>" . htmlspecialchars($user['role']) . "</td>
            <td>" . htmlspecialchars($user['status']) . "</td>
            <td>
                <button class='btn btn-success btn-sm action-btn' data-id='{$user['id']}' data-action='approve'>Approve</button>
                <button class='btn btn-warning btn-sm action-btn' data-id='{$user['id']}' data-action='suspend'>Suspend</button>
                <button class='btn btn-danger btn-sm action-btn' data-id='{$user['id']}' data-action='delete'>Delete</button>
            </td>
        </tr>";
    }
    return $html;
}

require_once __DIR__ . '/../Views/manageUsers.phtml';