<?php
// public/search_ajax.php
require_once '../config/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$scope = isset($_GET['scope']) ? trim($_GET['scope']) : 'all'; // 'all' or 'user'

try {
    $sql = "SELECT i.*, u.username, u.profile_image, 
            (SELECT id FROM item_images WHERE item_id = i.id LIMIT 1) as first_image_id,
            (SELECT COUNT(*) FROM item_images WHERE item_id = i.id) as image_count
            FROM items i JOIN users u ON i.user_id = u.id WHERE 1=1";
    $params = [];

    if ($scope === 'user' && isLoggedIn()) {
        $sql .= " AND i.user_id = ?";
        $params[] = $_SESSION['user_id'];
    }

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
    $results = $stmt->fetchAll();

    // Add ownership flag for editing logic
    if (isLoggedIn()) {
        foreach ($results as &$item) {
            $item['is_owner'] = ($item['user_id'] == $_SESSION['user_id']) ? 1 : 0;
        }
    } else {
        foreach ($results as &$item) {
            $item['is_owner'] = 0;
        }
    }

    echo json_encode($results);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>
