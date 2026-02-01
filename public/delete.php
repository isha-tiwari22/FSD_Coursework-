<?php
// public/delete.php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireLogin();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Securely delete only if the user owns the item
    $stmt = $pdo->prepare("DELETE FROM items WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
}

header("Location: index.php");
exit;
