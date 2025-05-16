<?php
class Booking {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createBooking($data) {
        $sql = "INSERT INTO booking 
                (user_id, charge_point_id, start_time, end_time, status, created_at)
                VALUES (:user_id, :charge_point_id, :start_time, :end_time, :status, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':user_id' => $data['user_id'],
            ':charge_point_id' => $data['charge_point_id'],
            ':start_time' => $data['start_time'],
            ':end_time' => $data['end_time'],
            ':status' => $data['status']
        ]) ? $this->pdo->lastInsertId() : false;
    }

    public function getUserBookings($userId) {
        $sql = "SELECT 
                    b.id,
                    b.start_time,
                    b.end_time,
                    b.status,
                    b.created_at,
                    cp.charger_type,
                    cp.location_id,
                    l.name as location_name,
                    l.city,
                    l.road,
                    l.block,
                    l.governorate
                FROM booking b
                JOIN charge_point cp ON b.charge_point_id = cp.id
                JOIN location l ON cp.location_id = l.id
                WHERE b.user_id = :user_id
                ORDER BY b.start_time DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateBookingStatus($bookingId, $status) {
        $sql = "UPDATE booking 
                SET status = :status, 
                    updated_at = NOW() 
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id' => $bookingId
        ]);
    }

    public function getBookingById($bookingId) {
        $sql = "SELECT 
                    b.*,
                    cp.charger_type,
                    cp.owner_id,
                    l.name as location_name,
                    l.city,
                    l.road,
                    l.block,
                    l.governorate
                FROM booking b
                JOIN charge_point cp ON b.charge_point_id = cp.id
                JOIN location l ON cp.location_id = l.id
                WHERE b.id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $bookingId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkBookingConflict($chargePointId, $startTime, $endTime, $excludeBookingId = null) {
        $sql = "SELECT COUNT(*) as count
                FROM booking
                WHERE charge_point_id = :charge_point_id
                AND status IN ('pending', 'confirmed')
                AND (
                    (start_time <= :start_time AND end_time >= :start_time) OR
                    (start_time <= :end_time AND end_time >= :end_time) OR
                    (start_time >= :start_time AND end_time <= :end_time)
                )";
        
        if ($excludeBookingId !== null) {
            $sql .= " AND id != :exclude_id";
        }
        
        $stmt = $this->pdo->prepare($sql);
        
        $params = [
            ':charge_point_id' => $chargePointId,
            ':start_time' => $startTime,
            ':end_time' => $endTime
        ];
        
        if ($excludeBookingId !== null) {
            $params[':exclude_id'] = $excludeBookingId;
        }
        
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}