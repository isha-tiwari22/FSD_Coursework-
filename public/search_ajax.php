<?php
// public/search_ajax.php
require_once '../config/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : 'full';

try {
    if ($type === 'names') {
        // Just return names for autocomplete
        $sql = "SELECT DISTINCT item_name FROM items WHERE item_name LIKE ? LIMIT 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["%$query%"]);
        echo json_encode($stmt->fetchAll());
        exit;
    }

    $sql = "SELECT i.*, u.username, u.profile_image FROM items i JOIN users u ON i.user_id = u.id WHERE 1=1";
    $params = [];

    if (!empty($query)) {
        $sql .= " AND (i.item_name LIKE ? OR i.description LIKE ? OR i.location LIKE ?)";
        $term = "%$query%";
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
    }

    if (!empty($status)) {
        $sql .= " AND i.status = ?";
        $params[] = $status;
    }

    $sql .= " ORDER BY i.date_reported DESC LIMIT 50";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode($stmt->fetchAll());

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>
