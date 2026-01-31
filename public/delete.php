<?php
// public/delete.php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireLogin();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Optional: Check if the user owns the item or is admin (omitted for simple requirement)
    // $stmt = $pdo->prepare("DELETE FROM items WHERE id = ? AND user_id = ?");
    // $stmt->execute([$id, $_SESSION['user_id']]);
    
    // Allow deleting any for demo purposes, or implement ownership check
    $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php");
exit;
