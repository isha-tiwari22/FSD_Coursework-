<?php
// public/view_request.php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$request_id = (int)$_GET['id'];

try {
    // Fetch request details ensuring the current user is the owner of the item
    $stmt = $pdo->prepare("SELECT r.*, i.item_name, i.user_id as owner_id, u.username as requester_name, u.email as requester_email 
                           FROM requests r 
                           JOIN items i ON r.item_id = i.id 
                           JOIN users u ON r.requester_id = u.id 
                           WHERE r.id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch();

    if (!$request || $request['owner_id'] != $_SESSION['user_id']) {
        header("Location: dashboard.php");
        exit;
    }

    // Fetch proof images
    $stmt_imgs = $pdo->prepare("SELECT id FROM request_images WHERE request_id = ?");
    $stmt_imgs->execute([$request_id]);
    $proof_images = $stmt_imgs->fetchAll();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST['csrf_token']);
    $new_status = $_POST['status'];
    
    if (in_array($new_status, ['accepted', 'rejected'])) {
        $stmt_upd = $pdo->prepare("UPDATE requests SET status = ? WHERE id = ?");
        $stmt_upd->execute([$new_status, $request_id]);
        
        // If accepted, we might want to mark the item as claimed if it was 'found' or 'lost'
        if ($new_status === 'accepted') {
            $stmt_item = $pdo->prepare("UPDATE items SET status = 'claimed' WHERE id = ?");
            $stmt_item->execute([$request['item_id']]);
        }
        
        $_SESSION['success'] = "Request has been marked as " . ucfirst($new_status);
        header("Location: dashboard.php");
        exit;
    }
}

require_once '../includes/header.php';
?>

<main class="main-content">
    <div class="container">
        <div class="page-header fade-in">
            <div class="page-title">
                <h1><i class="fa-solid fa-file-shield" style="color: var(--primary); margin-right: 0.5rem;"></i> Validate Claim</h1>
                <p style="color: var(--text-muted);">Review proof for: <?php echo h($request['item_name']); ?></p>
            </div>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <div class="details-grid fade-in">
            <!-- Left: Proof Images -->
            <div class="card">
                <div class="card-body">
                    <h3><i class="fa-solid fa-camera" style="color: var(--primary); margin-right: 0.5rem;"></i> Proof Gallery</h3>
                    <?php if (empty($proof_images)): ?>
                        <p style="color: var(--text-muted); padding: 3rem; text-align: center; border: 2px dashed var(--border); border-radius: 12px; margin-top: 1.5rem;">No photographic proof was provided.</p>
                    <?php else: ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
                            <?php foreach ($proof_images as $img): ?>
                                <div style="border-radius: 12px; overflow: hidden; border: 1px solid var(--border); transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    <a href="image_view.php?id=<?php echo $img['id']; ?>&type=request" target="_blank">
                                        <img src="image_view.php?id=<?php echo $img['id']; ?>&type=request" style="width: 100%; height: 180px; object-fit: cover; display: block;">
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Requester Message & Action -->
            <div class="details-column">
                <div class="card">
                    <div class="card-body">
                        <div class="info-group">
                            <div class="info-label"><i class="fa-solid fa-user"></i> Requester Information</div>
                            <div style="background: var(--bg-page); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border);">
                                <p style="margin-bottom: 0.5rem;"><strong>Name:</strong> <?php echo h($request['requester_name']); ?></p>
                                <p style="margin-bottom: 0.5rem;"><strong>Email:</strong> <?php echo h($request['requester_email']); ?></p>
                                <p><strong>Contact Info:</strong> <?php echo h($request['contact_info']); ?></p>
                            </div>
                        </div>

                        <div class="info-group">
                            <div class="info-label"><i class="fa-solid fa-comment-dots"></i> Message / Explanation</div>
                            <p style="background: var(--bg-page); padding: 2rem; border-radius: 12px; line-height: 1.8; color: var(--text-main); border: 1px solid var(--border);">
                                <?php echo nl2br(h($request['message'])); ?>
                            </p>
                        </div>

                        <?php if ($request['status'] == 'pending'): ?>
                        <form method="POST" class="grid-2-col" style="margin-top: 2.5rem;">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                            <button type="submit" name="status" value="rejected" class="btn btn-secondary" style="padding: 1rem;" onclick="return confirm('Are you sure you want to REJECT this claim?');">
                                <i class="fa-solid fa-xmark"></i> Reject
                            </button>
                            <button type="submit" name="status" value="accepted" class="btn btn-primary" style="padding: 1rem;" onclick="return confirm('Are you sure you want to ACCEPT this claim?');">
                                <i class="fa-solid fa-check"></i> Accept
                            </button>
                        </form>
                        <?php else: ?>
                            <div class="alert <?php echo $request['status'] == 'accepted' ? 'alert-success' : 'alert-error'; ?>" style="text-align: center; font-weight: 700; margin-top: 2rem; padding: 1.5rem; text-transform: uppercase; letter-spacing: 1px;">
                                REQUEST <?php echo $request['status']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
