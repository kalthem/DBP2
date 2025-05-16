<?php
class User {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Authentication method (updated table name to 'users')
    public function authenticate($username, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return null;
            }

            if (password_verify($password, $user['password'])) {
                unset($user['password']); // Remove password from returned array
                return $user;
            }

            return null;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    // Registration method (updated table name to 'users')
    public function insertUser($username, $name, $hashedPassword, $role, $status) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->fetch()) {
            throw new Exception("Username already registered.");
        }

        $stmt = $this->db->prepare("INSERT INTO users (username, name, password, role, status) 
                                  VALUES (:username, :name, :password, :role, :status)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':status', $status);

        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Registration failed: " . $errorInfo[2]);
        }
        return $this->db->lastInsertId();
    }

    // Get user by ID (updated table name to 'users')
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT id, username, name, role, status FROM users WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get homeowner's charge points (unchanged, uses correct charge_point table)
    public function getHomeownerChargePoints($userId) {
        $stmt = $this->db->prepare("SELECT * FROM charge_point WHERE owner_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update user profile (updated table name to 'users')
    public function updateUser($userId, $data) {
        $query = "UPDATE users SET name = :name";
        $params = [':id' => $userId, ':name' => $data['name']];

        if (!empty($data['password'])) {
            $query .= ", password = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $query .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        return $stmt->execute();
    }

    // Check if user is homeowner (updated table name to 'users')
    public function isHomeowner($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE id = :id AND role = 'homeowner'");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // NEW: Get all pending homeowners (useful for admin approval)
    public function getPendingHomeowners() {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = 'homeowner' AND status = 'pending'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // NEW: Update user status (for admin approvals/suspensions)
    public function updateUserStatus($userId, $status) {
        $stmt = $this->db->prepare("UPDATE users SET status = :status WHERE id = :id");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }
}