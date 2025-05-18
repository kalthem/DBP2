<?php

$baseUrl = 'http://20.126.5.244/~u202103011/BorrowMyCharger';
require_once __DIR__ . '/../db_connect.php';

class AdminModel {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Get all users with pagination
    public function getUsers($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("SELECT * FROM users LIMIT :offset, :perPage");
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update user status
    public function updateUserStatus($userId, $status) {
        $validStatuses = ['pending', 'approved', 'suspended'];
        if(!in_array($status, $validStatuses)) return false;
        
        $stmt = $this->db->prepare("UPDATE users SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $userId]);
    }
}