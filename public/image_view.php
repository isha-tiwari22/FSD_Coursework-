<?php
// public/image_view.php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_GET['id'])) {
    exit;
}

$id = (int)$_GET['id'];
$type = $_GET['type'] ?? 'item';

try {
    if ($type === 'request') {
        $stmt = $pdo->prepare("SELECT image_data, image_type FROM request_images WHERE id = ?");
    } else {
        $stmt = $pdo->prepare("SELECT image_data, image_type FROM item_images WHERE id = ?");
    }
    
    $stmt->execute([$id]);
    $image = $stmt->fetch();

    if ($image) {
        header("Content-Type: " . $image['image_type']);
        echo $image['image_data'];
    } else {
        header("HTTP/1.0 404 Not Found");
    }
} catch (PDOException $e) {
    header("HTTP/1.0 500 Internal Server Error");
}
?>
