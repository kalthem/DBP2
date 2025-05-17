<?php
require_once __DIR__ . '/../db_connect.php';

class AdminReportsModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getdbConnection();
    }

    // Predefined SQL reports
    public function getPredefinedReports() {
        return [
            'All Users' => "SELECT id, name, username, role, status, created_at FROM users",
            'Users By Status' => "SELECT status, COUNT(*) as total FROM users GROUP BY status",
            'All Locations' => "SELECT id, name, city, road, block, latitude, longitude, governorate, type, created_at FROM location",
            'All Charge Points' => "SELECT c.id, l.name AS location_name, l.city, c.owner_id, c.charger_type, c.status, c.availability_days, c.created_at FROM charge_point c JOIN location l ON c.location_id = l.id",
            'Charge Points By Status' => "SELECT status, COUNT(*) as total FROM charge_point GROUP BY status",
            'Charge Points By City' => "SELECT l.city, COUNT(*) AS total FROM charge_point c JOIN location l ON c.location_id = l.id GROUP BY l.city",
            'All Bookings' => "SELECT id, user_id, charge_point_id, start_time, end_time, status, created_at FROM booking",
            'Bookings Per Status' => "SELECT status, COUNT(*) as total FROM booking GROUP BY status",
            'Total Revenue (estimated)' => "SELECT COUNT(*) * 5 AS total_revenue_BHD FROM booking WHERE status = 'completed'",
            '10 Most Recent Bookings' => "SELECT b.id, u.username, b.start_time, b.end_time, b.status FROM booking b JOIN users u ON b.user_id = u.id ORDER BY b.created_at DESC LIMIT 10"
        ];
    }

    // Execute a predefined report
    public function runPredefinedReport($title) {
        $reports = $this->getPredefinedReports();
        if (!isset($reports[$title])) {
            return false; // Invalid report selection
        }
        $sql = $reports[$title];
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}