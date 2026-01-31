<?php
// public/add.php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireLogin();

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
        $stmt = $pdo->prepare("INSERT INTO items (user_id, item_name, description, location, status) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $item_name, $description, $location, $status])) {
            $success = "Item reported successfully.";
            // Reset fields if needed, or redirect
            // header("Location: index.php");
        } else {
            $error = "Failed to add item.";
        }
    }
}

require_once '../includes/header.php';
?>

<main class="main-content">
<div class="page-header">
    <div class="page-title">
        <h1>Report Item</h1>
        <p style="color: var(--text-muted);">Submit a new lost or found item.</p>
    </div>
    <a href="index.php" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<div class="card fade-in" style="max-width: 800px; margin: 0 auto;">
    <div class="card-body">
        <?php if($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo h($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success">
                <i class="fa-regular fa-circle-check"></i> <?php echo h($success); ?> <a href="index.php">View All Items</a>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            
            <div class="form-group">
                <label class="form-label">Item Name</label>
                <input type="text" name="item_name" class="form-control" placeholder="e.g. Red Backpack, iPhone 13" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Provide more details..."></textarea>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" placeholder="e.g. Library, Cafeteria" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="lost">Lost</option>
                        <option value="found">Found</option>
                    </select>
                </div>
            </div>
            
            <div style="margin-top: 1rem; text-align: right;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-regular fa-paper-plane"></i> Submit Report
                </button>
            </div>
        </form>
    </div>
</div>
</main>

<?php require_once '../includes/footer.php'; ?>
