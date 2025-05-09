<?php

class User {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Authenticate user for login
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
                return $user;
            }

            return null;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    // Insert new user during registration
    public function insertUser($username, $name, $hashedPassword, $role, $status) {
        // Check if email already exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->fetch()) {
            throw new Exception("Email already registered.");
        }

        // Insert user
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
    }
}
