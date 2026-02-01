<?php
// public/submit_request.php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST['csrf_token']);
    
    $item_id = (int)$_POST['item_id'];
    $message = trim($_POST['message']);
    $contact_info = trim($_POST['contact_info']);
    
    if (empty($message)) {
        $_SESSION['error'] = "Please provide a message for your request.";
        header("Location: item_details.php?id=$item_id");
        exit;
    }

    try {
        $pdo->beginTransaction();
        
        // Check if item exists and user is not the reporter
        $stmt = $pdo->prepare("SELECT user_id FROM items WHERE id = ?");
        $stmt->execute([$item_id]);
        $item = $stmt->fetch();
        
        if (!$item) {
            throw new Exception("Item not found.");
        }
        
        if ($item['user_id'] == $_SESSION['user_id']) {
            throw new Exception("You cannot request your own item.");
        }

        // Insert request
        $stmt_req = $pdo->prepare("INSERT INTO requests (item_id, requester_id, message, contact_info) VALUES (?, ?, ?, ?)");
        $stmt_req->execute([$item_id, $_SESSION['user_id'], $message, $contact_info]);
        $request_id = $pdo->lastInsertId();

        // Handle Proof Images
        if (isset($_FILES['proof_images']) && is_array($_FILES['proof_images']['name'])) {
            $uploadedCount = 0;
            $failedCount = 0;
            foreach ($_FILES['proof_images']['tmp_name'] as $key => $tmp_name) {
                if (empty($tmp_name) && $_FILES['proof_images']['error'][$key] === UPLOAD_ERR_NO_FILE) continue;
                
                if ($_FILES['proof_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $image_data = file_get_contents($tmp_name);
                    $image_type = $_FILES['proof_images']['type'][$key];
                    
                    if (str_starts_with($image_type, 'image/')) {
                        $stmt_img = $pdo->prepare("INSERT INTO request_images (request_id, image_data, image_type) VALUES (?, ?, ?)");
                        $stmt_img->execute([$request_id, $image_data, $image_type]);
                        $uploadedCount++;
                    }
                } else {
                    $failedCount++;
                }
            }
            if ($failedCount > 0) {
                $_SESSION['success_extra'] = " ($uploadedCount photos uploaded, $failedCount failed)";
            }
        }

        $pdo->commit();
        $extra = $_SESSION['success_extra'] ?? '';
        unset($_SESSION['success_extra']);
        $_SESSION['success'] = "Your request has been submitted with proof. The reporter will be notified." . $extra;
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Failed to submit request: " . $e->getMessage();
    }
    
    header("Location: item_details.php?id=$item_id");
    exit;
}
?>
