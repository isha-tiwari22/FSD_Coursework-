<?php
// public/delete_account.php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireLogin();

$user = getCurrentUser($pdo); // We will define this function next

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    verifyCsrfToken($_POST['csrf_token']);
    
    // Delete user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$_SESSION['user_id']])) {
        // Log user out
        session_destroy();
        header("Location: index.php");
        exit;
    } else {
        $error = "Failed to delete account.";
    }
}

require_once '../includes/header.php';
?>

<main class="main-content">
    <div class="auth-container fade-in">
        <div style="text-align: center; margin-bottom: 2rem;">
            <i class="fa-solid fa-user-xmark" style="font-size: 3rem; color: var(--status-lost); margin-bottom: 1rem;"></i>
            <h2>Delete Account</h2>
            <p style="color: var(--text-muted);">Are you sure you want to delete your account?</p>
        </div>

        <div class="alert alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i> Warning: This action cannot be undone. All your data will be permanently removed.
        </div>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            
            <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem;">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" name="confirm_delete" class="btn btn-danger">
                    <i class="fa-solid fa-trash-can"></i> Yes, Delete Account
                </button>
            </div>
        </form>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
