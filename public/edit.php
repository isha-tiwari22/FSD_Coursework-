<?php
// public/edit.php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$item = $stmt->fetch();

if (!$item) {
    echo "Item not found.";
    exit; // Or redirect
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST['csrf_token']);
    
    $item_name = trim($_POST['item_name']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $status = $_POST['status'];
    
    if (empty($item_name) || empty($location)) {
        $error = "Item Name and Location are required.";
    } else {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE items SET item_name = ?, description = ?, location = ?, status = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$item_name, $description, $location, $status, $id, $_SESSION['user_id']]);

            // Handle Image Deletions
            if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
                foreach ($_POST['delete_images'] as $img_id) {
                    $stmt_del = $pdo->prepare("DELETE FROM item_images WHERE id = ? AND item_id = ?");
                    $stmt_del->execute([(int)$img_id, $id]);
                }
            }

            // Handle New Image Uploads
            if (isset($_FILES['item_images']) && is_array($_FILES['item_images']['name'])) {
                $uploadedCount = 0;
                $failedCount = 0;
                foreach ($_FILES['item_images']['tmp_name'] as $key => $tmp_name) {
                    if (empty($tmp_name) && $_FILES['item_images']['error'][$key] === UPLOAD_ERR_NO_FILE) continue;
                    
                    if ($_FILES['item_images']['error'][$key] === UPLOAD_ERR_OK) {
                        $image_data = file_get_contents($tmp_name);
                        $image_type = $_FILES['item_images']['type'][$key];
                        if (str_starts_with($image_type, 'image/')) {
                            $stmt_img = $pdo->prepare("INSERT INTO item_images (item_id, image_data, image_type) VALUES (?, ?, ?)");
                            $stmt_img->execute([$id, $image_data, $image_type]);
                            $uploadedCount++;
                        }
                    } else {
                        $failedCount++;
                    }
                }
                if ($failedCount > 0) {
                    $success .= " ($uploadedCount new photos added, $failedCount failed)";
                }
            }

            $pdo->commit();
            $success = "Item updated successfully.";
            
            // Refresh data
            $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to update item: " . $e->getMessage();
        }
    }
}

// Fetch existing images
$stmt_imgs = $pdo->prepare("SELECT id FROM item_images WHERE item_id = ?");
$stmt_imgs->execute([$id]);
$existing_images = $stmt_imgs->fetchAll();

require_once '../includes/header.php';
?>

<main class="main-content">
    <div class="container">
        <div class="page-header fade-in">
            <div class="page-title">
                <h1><i class="fa-solid fa-file-pen" style="color: var(--primary); margin-right: 0.5rem;"></i> Edit Item</h1>
                <p style="color: var(--text-muted);">Update details for: <?php echo h($item['item_name']); ?></p>
            </div>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <div class="card fade-in" style="max-width: 900px; margin: 0 auto;">
            <div class="card-body">
                <?php if($error): ?>
                    <div class="alert alert-error">
                        <i class="fa-solid fa-triangle-exclamation"></i> <?php echo h($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div class="alert alert-success">
                        <i class="fa-regular fa-circle-check"></i> <?php echo h($success); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-heading" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Item Name</label>
                        <input type="text" name="item_name" class="form-control" value="<?php echo h($item['item_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-align-left" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Description</label>
                        <textarea name="description" class="form-control" rows="4"><?php echo h($item['description']); ?></textarea>
                    </div>

                    <!-- Existing Images -->
                    <?php if (!empty($existing_images)): ?>
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-images" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Current Images (Select to Delete)</label>
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <?php foreach ($existing_images as $img): ?>
                                <div style="position: relative; width: 120px; height: 120px;">
                                    <img src="image_view.php?id=<?php echo $img['id']; ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px; border: 1px solid var(--border);">
                                    <div style="position: absolute; top: 5px; right: 5px; background: rgba(255,255,255,0.9); border-radius: 4px; padding: 2px;">
                                        <input type="checkbox" name="delete_images[]" value="<?php echo $img['id']; ?>" style="cursor: pointer;">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-plus" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Add More Images</label>
                        <input type="file" name="item_images[]" multiple accept="image/*" class="form-control" style="padding: 0.5rem;">
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-map-pin" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Location</label>
                            <input type="text" name="location" class="form-control" value="<?php echo h($item['location']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-toggle-on" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Status</label>
                            <select name="status" class="form-control">
                                <option value="lost" <?php echo $item['status'] == 'lost' ? 'selected' : ''; ?>>Lost</option>
                                <option value="found" <?php echo $item['status'] == 'found' ? 'selected' : ''; ?>>Found</option>
                                <option value="claimed" <?php echo $item['status'] == 'claimed' ? 'selected' : ''; ?>>Claimed</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="margin-top: 2rem; text-align: right;">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa-regular fa-floppy-disk"></i> Update Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
