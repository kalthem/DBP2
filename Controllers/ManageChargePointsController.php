<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../Models/ManageChargePointsModel.php';
require_once __DIR__ . '/../Models/LocationModel.php';
class ManageChargePointsController {
    private $model;
    private $currentUser;
    private $isAdmin;
    private $itemsPerPage = 10;

    public function __construct() {
        $this->currentUser = $_SESSION['user'] ?? null;
        if (!$this->currentUser) {
            $this->redirectWithError('Authentication required. Please login.', '/BorrowMyCharger/index.php?action=login');
        }

        $this->model = new ManageChargePointsModel();
        $this->isAdmin = ($this->currentUser['role'] === 'admin');
        $this->handleRequest();
        $this->showView();
    }

    private function handleRequest(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            try {
                switch ($action) {
                    case 'add': $this->addChargePoint(); break;
                    case 'edit': $this->editChargePoint(); break;
                    case 'delete': $this->deleteChargePoint(); break;
                    default: throw new Exception('Invalid action');
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->redirectBack();
            }
        }
    }

    private function addChargePoint(): void {
        $data = $this->validateChargePointData();
        $data['owner_id'] = $this->isAdmin
            ? intval($_POST['owner_id'] ?? 0)
            : intval($this->currentUser['id']);
        $data['image_url'] = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $data['image_url'] = $this->handleImageUpload($_FILES['image']);
        }
        $this->model->addChargePoint($data);
        $_SESSION['success'] = 'Charge point added successfully';
        $this->redirectBack();
    }

    private function editChargePoint(): void {
        $id = intval($_POST['id'] ?? 0);
        if (!$id) throw new Exception('Invalid charge point ID');
        if (!$this->isAdmin && !$this->model->isOwner($id, $this->currentUser['id'])) {
            throw new Exception('You do not have permission to edit this charge point');
        }
        $data = $this->validateChargePointData();
        $data['owner_id'] = $this->isAdmin
            ? intval($_POST['owner_id'] ?? 0)
            : intval($this->currentUser['id']);
        $existingImage = $this->model->getImagePath($id);
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $data['image_url'] = $this->handleImageUpload($_FILES['image'], $existingImage);
        } else {
            $data['image_url'] = $existingImage;
        }
        $this->model->updateChargePoint($id, $data);
        $_SESSION['success'] = 'Charge point updated successfully';
        $this->redirectBack();
    }

    private function deleteChargePoint(): void {
        $id = intval($_POST['id'] ?? 0);
        if (!$id) throw new Exception('Invalid charge point ID');
        if (!$this->isAdmin && !$this->model->isOwner($id, $this->currentUser['id'])) {
            throw new Exception('You do not have permission to delete this charge point');
        }
        $this->model->deleteChargePoint($id);
        $_SESSION['success'] = 'Charge point deleted successfully';
        $this->redirectBack();
    }

    private function validateChargePointData(): array {
        $location_id = intval($_POST['location_id'] ?? 0);
        $charger_type = trim($_POST['charger_type'] ?? '');
        $status = trim($_POST['status'] ?? '');
        $availability_days = $_POST['availability_days'] ?? [];
        $latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
        $longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;
        $price_per_hour = isset($_POST['price_per_hour']) ? floatval($_POST['price_per_hour']) : null;

        if (!$location_id) throw new Exception('Location is required');
        if ($charger_type === '') throw new Exception('Charger type is required');
        if ($status === '') throw new Exception('Status is required');
        if (empty($availability_days) || !is_array($availability_days)) throw new Exception('Select at least one availability day');
        if ($latitude === null || $longitude === null) throw new Exception('Latitude and Longitude are required');
        if ($price_per_hour === null || $price_per_hour < 0) throw new Exception('Price per hour is required');
        return [
            'location_id' => $location_id,
            'charger_type' => $charger_type,
            'status' => $status,
            'availability_days' => implode(',', $availability_days),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'price_per_hour' => $price_per_hour
        ];
    }

    private function handleImageUpload(array $file, ?string $existingPath = null): ?string {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/';
            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) throw new Exception('Could not create uploads directory');
            }
            if (!is_writable($uploadDir)) throw new Exception('Upload directory is not writable');
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif'];
            if (!in_array($extension, $allowed)) throw new Exception('Unsupported image type');
            if ($file['size'] > 5 * 1024 * 1024) throw new Exception('Image exceeds 5MB');
            $newFilename = uniqid('cp_', true) . '.' . $extension;
            $targetPath = $uploadDir . $newFilename;
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) throw new Exception('Image upload failed');
            if ($existingPath) {
                $oldPath = $uploadDir . basename($existingPath);
                if (file_exists($oldPath)) unlink($oldPath);
            }
            return 'uploads/' . $newFilename;
        }
        return $existingPath ?? null;
    }

    private function showView(): void {
        $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? intval($_GET['page']) : 1;
        if ($this->isAdmin) {
            $totalItems = $this->model->getTotalCount();
            $chargePoints = $this->model->getAllPaginated($currentPage, $this->itemsPerPage);
        } else {
            $totalItems = $this->model->getTotalCountByOwner($this->currentUser['id']);
            $chargePoints = $this->model->getChargePointsByOwner($this->currentUser['id'], $currentPage, $this->itemsPerPage);
        }
        $totalPages = max(1, ceil($totalItems / $this->itemsPerPage));
        $viewData = [
            'chargePoints' => $chargePoints,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'locations' => $this->model->getAllLocations(),
            'users' => $this->isAdmin ? $this->model->getAllUsers() : [],
            'isAdmin' => $this->isAdmin,
        ];
        extract($viewData);
        require __DIR__ . '/../Views/manageChargePoints.phtml';
    }

    private function redirectBack(): void {
        $location = $_SERVER['HTTP_REFERER'] ?? '/BorrowMyCharger/index.php?action=dashboard';
        header('Location: ' . $location);
        exit;
    }

    private function redirectWithError(string $message, string $path): void {
        $_SESSION['error'] = $message;
        header("Location: $path");
        exit;
    }
}

// Instantiate and run controller
new ManageChargePointsController();