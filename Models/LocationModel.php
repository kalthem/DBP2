<?php
require_once __DIR__ . '/../db_connect.php';

class LocationModel {
    private $pdo;

    public function __construct($pdo = null) {
        if ($pdo) {
            $this->pdo = $pdo;
        } else {
            $database = Database::getInstance();
            $this->pdo = $database->getdbConnection();
        }
    }

    public function getAll() {
        $sql = "SELECT * FROM location";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT * FROM location WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}