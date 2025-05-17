<?php
require_once __DIR__ . '/../db_connect.php';

class ManageUsersModel {
    public function getUsers($filter = 'all', $page = 1, $perPage = 10) {
        $db = Database::getInstance()->getdbConnection();
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT id, name, username, role, status FROM users";
        if ($filter !== 'all') $sql .= " WHERE status = :status";
        $sql .= " LIMIT :offset, :perPage";
        $stmt = $db->prepare($sql);
        if ($filter !== 'all') $stmt->bindParam(':status', $filter, PDO::PARAM_STR);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countSql = "SELECT COUNT(*) FROM users";
        if ($filter !== 'all') $countSql .= " WHERE status = :status";
        $countStmt = $db->prepare($countSql);
        if ($filter !== 'all') $countStmt->bindParam(':status', $filter, PDO::PARAM_STR);
        $countStmt->execute();
        $totalRecords = $countStmt->fetchColumn();
        $totalPages = ceil($totalRecords / $perPage);

        return ['users' => $users, 'totalPages' => $totalPages];
    }

    public function getUserStats() {
        $db = Database::getInstance()->getdbConnection();
        return [
            'total' => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'approved' => $db->query("SELECT COUNT(*) FROM users WHERE status = 'approved'")->fetchColumn(),
            'suspended' => $db->query("SELECT COUNT(*) FROM users WHERE status = 'suspended'")->fetchColumn(),
            'pending' => $db->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn(),
        ];
    }

    public function searchUsers($query) {
        $db = Database::getInstance()->getdbConnection();
        $stmt = $db->prepare("SELECT id, name, username, role, status FROM users WHERE name LIKE :query OR username LIKE :query");
        $searchQuery = "%$query%";
        $stmt->bindParam(':query', $searchQuery, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function performBulkAction($action, $userIds) {
        $db = Database::getInstance()->getdbConnection();
        $ids = implode(',', array_map('intval', $userIds));
        if ($action === 'approve') {
            $sql = "UPDATE users SET status = 'approved' WHERE id IN ($ids)";
        } elseif ($action === 'suspend') {
            $sql = "UPDATE users SET status = 'suspended' WHERE id IN ($ids)";
        } elseif ($action === 'delete') {
            $sql = "DELETE FROM users WHERE id IN ($ids)";
        } else {
            return false;
        }
        return $db->exec($sql);
    }
}