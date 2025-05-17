<?php
require_once __DIR__ . '/../db_connect.php';

class ManageChargePointsModel {
    private $db;
    private $table = 'charge_point';
    private $locationTable = 'location';
    private $usersTable = 'users';

    public function __construct() {
        $this->db = Database::getInstance()->getdbConnection();
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function getAllPaginated(int $page = 1, int $perPage = 10): array {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("
            SELECT cp.*, l.name AS location_name, l.latitude, l.longitude
            FROM {$this->table} cp
            JOIN {$this->locationTable} l ON cp.location_id = l.id
            ORDER BY cp.id ASC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCount(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table}");
        return (int)$stmt->fetchColumn();
    }

    public function addChargePoint(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (location_id, owner_id, charger_type, status, availability_days, price_per_hour, image_url)
            VALUES (:location_id, :owner_id, :charger_type, :status, :availability_days, :price_per_hour, :image_url)
        ");
        $stmt->execute([
            ':location_id' => $data['location_id'],
            ':owner_id' => $data['owner_id'],
            ':charger_type' => $data['charger_type'],
            ':status' => $data['status'],
            ':availability_days' => $data['availability_days'],
            ':price_per_hour' => $data['price_per_hour'],
            ':image_url' => $data['image_url'] ?? null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function updateChargePoint(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} SET
                location_id = :location_id,
                charger_type = :charger_type,
                status = :status,
                availability_days = :availability_days,
                price_per_hour = :price_per_hour,
                image_url = COALESCE(:image_url, image_url)
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id' => $id,
            ':location_id' => $data['location_id'],
            ':charger_type' => $data['charger_type'],
            ':status' => $data['status'],
            ':availability_days' => $data['availability_days'],
            ':price_per_hour' => $data['price_per_hour'],
            ':image_url' => $data['image_url'] ?? null
        ]);
    }

    public function deleteChargePoint(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function isOwner(int $chargePointId, int $userId): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM {$this->table}
            WHERE id = :id AND owner_id = :owner_id
        ");
        $stmt->execute([':id' => $chargePointId, ':owner_id' => $userId]);
        return $stmt->fetchColumn() > 0;
    }

    public function getChargePointsByOwner(int $ownerId, int $page = 1, int $perPage = 10): array {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("
            SELECT cp.*, l.name AS location_name, l.latitude, l.longitude
            FROM {$this->table} cp
            JOIN {$this->locationTable} l ON cp.location_id = l.id
            WHERE owner_id = :owner_id
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':owner_id', $ownerId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCountByOwner($ownerId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE owner_id = ?");
        $stmt->execute([$ownerId]);
        return (int)$stmt->fetchColumn();
    }

    public function getImagePath(int $id): ?string {
        $stmt = $this->db->prepare("SELECT image_url FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn() ?: null;
    }

    public function getAllLocations(): array {
        $stmt = $this->db->query("
            SELECT id, name, city, latitude, longitude 
            FROM {$this->locationTable}
            ORDER BY name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUsers(): array {
        $stmt = $this->db->query("
            SELECT id, name, role 
            FROM {$this->usersTable}
            WHERE role IN ('homeowner', 'admin')
            ORDER BY name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}