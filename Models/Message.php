<?php
class Message {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createMessage($data) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, charge_point_id, message, status, created_at)
                VALUES (:sender_id, :receiver_id, :charge_point_id, :message, :status, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'sender_id' => $data['sender_id'],
            'receiver_id' => $data['receiver_id'],
            'charge_point_id' => $data['charge_point_id'],
            'message' => $data['message'],
            'status' => $data['status']
        ]);
    }
}