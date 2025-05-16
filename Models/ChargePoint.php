<?php
class ChargePoint {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllChargePointsWithLocation() {
        $query = "SELECT 
                    cp.id,
                    cp.charger_type,
                    cp.status,
                    cp.availability_days,
                    cp.price_per_hour,
                    l.name as location_name,
                    l.city,
                    l.road,
                    l.block,
                    l.governorate,
                    l.latitude,
                    l.longitude
                  FROM charge_point cp
                  JOIN location l ON cp.location_id = l.id
                  ORDER BY cp.status, l.name";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFilteredChargePoints($minPrice = null, $maxPrice = null, $status = null, $city = null) {
        $query = "SELECT 
                    cp.id,
                    cp.charger_type,
                    cp.status,
                    cp.availability_days,
                    cp.price_per_hour,
                    cp.owner_id,
                    cp.location_id,
                    l.name as location_name,
                    l.city,
                    l.road,
                    l.block,
                    l.governorate,
                    l.latitude,
                    l.longitude
                  FROM charge_point cp
                  JOIN location l ON cp.location_id = l.id
                  WHERE 1=1";
        
        $params = [];
        
        // Add price filters
        if ($minPrice !== null) {
            $query .= " AND cp.price_per_hour >= ?";
            $params[] = $minPrice;
        }
        if ($maxPrice !== null) {
            $query .= " AND cp.price_per_hour <= ?";
            $params[] = $maxPrice;
        }
        
        // Add status filter
        if ($status !== null) {
            $query .= " AND cp.status = ?";
            $params[] = $status;
        }
        
        // Add city filter
        if ($city !== null) {
            $query .= " AND l.city = ?";
            $params[] = $city;
        }
        
        $query .= " ORDER BY cp.status, l.name";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllCities() {
        $query = "SELECT DISTINCT city FROM location ORDER BY city";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }
    
    public function updateStatus($chargePointId, $status) {
        $query = "UPDATE charge_point SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([':status' => $status, ':id' => $chargePointId]);
    }

    public function getChargePointById($id) {
        $query = "SELECT 
                    cp.*,
                    l.name as location_name,
                    l.city,
                    l.road,
                    l.block,
                    l.governorate,
                    l.latitude,
                    l.longitude
                  FROM charge_point cp
                  JOIN location l ON cp.location_id = l.id
                  WHERE cp.id = :id";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLocationDetails($locationId) {
        $query = "SELECT * FROM location WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $locationId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkAvailability($chargePointId, $startTime, $endTime) {
        $query = "SELECT COUNT(*) as count 
                FROM booking 
                WHERE charge_point_id = :charge_point_id 
                AND status IN ('pending', 'confirmed')
                AND (
                    (start_time <= :start_time AND end_time >= :start_time) OR
                    (start_time <= :end_time AND end_time >= :end_time) OR
                    (start_time >= :start_time AND end_time <= :end_time)
                )";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'charge_point_id' => $chargePointId,
            'start_time' => $startTime,
            'end_time' => $endTime
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] == 0;
    }

    public function getChargePointsByOwner($ownerId) {
        $query = "SELECT 
                    cp.id,
                    cp.charger_type,
                    cp.status,
                    l.name as location_name,
                    l.city,
                    COUNT(b.id) as active_bookings
                  FROM charge_point cp
                  JOIN location l ON cp.location_id = l.id
                  LEFT JOIN booking b ON b.charge_point_id = cp.id 
                    AND b.status IN ('pending', 'confirmed')
                  WHERE cp.owner_id = :owner_id
                  GROUP BY cp.id
                  ORDER BY cp.status";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['owner_id' => $ownerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}