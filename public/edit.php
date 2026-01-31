<?php
// public/edit.php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
$stmt->execute([$id]);
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
        $stmt = $pdo->prepare("UPDATE items SET item_name = ?, description = ?, location = ?, status = ? WHERE id = ?");
        if ($stmt->execute([$item_name, $description, $location, $status, $id])) {
            $success = "Item updated successfully.";
            // Refresh item data
            $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();
        } else {
            $error = "Failed to update item.";
        }
    }
}

require_once '../includes/header.php';
?>

<main class="main-content">
<div class="page-header">
    <div class="page-title">
        <h1>Edit Item</h1>
        <p style="color: var(--text-muted);">Update details for: <?php echo h($item['item_name']); ?></p>
    </div>
    <a href="index.php" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<div class="card fade-in" style="max-width: 800px; margin: 0 auto;">
    <div class="card-body">
        <?php if($success): ?>
            <div class="alert alert-success">
                <i class="fa-regular fa-circle-check"></i> <?php echo h($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            
            <div class="form-group">
                <label class="form-label">Item Name</label>
                <input type="text" name="item_name" class="form-control" value="<?php echo h($item['item_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4"><?php echo h($item['description']); ?></textarea>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="<?php echo h($item['location']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="lost" <?php echo $item['status'] == 'lost' ? 'selected' : ''; ?>>Lost</option>
                        <option value="found" <?php echo $item['status'] == 'found' ? 'selected' : ''; ?>>Found</option>
                        <option value="claimed" <?php echo $item['status'] == 'claimed' ? 'selected' : ''; ?>>Claimed</option>
                    </select>
                </div>
            </div>
            
            <div style="margin-top: 1rem; text-align: right;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-regular fa-floppy-disk"></i> Update Item
                </button>
            </div>
        </form>
    </div>
</div>
</main>

<?php require_once '../includes/footer.php'; ?>
