<?php
require_once __DIR__ . '/../db_connect.php';

class UserModel {
    private $pdo;

    public function __construct($pdo = null) {
        if ($pdo) {
            $this->pdo = $pdo;
        } else {
            $database = Database::getInstance();
            $this->pdo = $database->getdbConnection();
        }
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM users");
        return $stmt->fetchAll();
    }

    public function approveUser($id) {
        $stmt = $this->pdo->prepare("UPDATE users SET approved = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
