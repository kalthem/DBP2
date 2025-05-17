<?php
require_once __DIR__ . '/../Models/LocationModel.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_SESSION['user']['role'] ?? '') === 'admin') {
    $model = new LocationModel();
    try {
        $id = $model->add([
            'name' => $_POST['name'],
            'city' => $_POST['city'],
            'road' => $_POST['road'] ?? '',
            'block' => $_POST['block'] ?? '',
            'latitude' => $_POST['latitude'] ?? null,
            'longitude' => $_POST['longitude'] ?? null,
            'governorate' => $_POST['governorate'] ?? 'Capital',
            'type' => $_POST['type'] ?? 'public'
        ]);
        $loc = $model->getById($id);
        echo json_encode(['success'=>true, 'location'=>$loc]);
    } catch (Exception $e) {
        echo json_encode(['success'=>false, 'error'=>$e->getMessage()]);
    }
    exit;
}
echo json_encode(['success'=>false, 'error'=>'Unauthorized']);
exit;