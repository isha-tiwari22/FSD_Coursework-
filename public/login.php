<?php
// public/login.php
require_once '../config/db.php';
require_once '../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST['csrf_token']);
    
    $login = trim($_POST['login']); // can be username or email
    $password = $_POST['password'];
    
    if (empty($login) || empty($password)) {
        $error = "Please enter username/email and password.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid credentials.";
        }
    }
}

require_once '../includes/header.php';
?>

<main class="main-content">
<div class="auth-container fade-in">
    <div style="text-align: center; margin-bottom: 2rem;">
        <i class="fa-solid fa-layer-group" style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;"></i>
        <h2>Welcome Back</h2>
        <p style="color: var(--text-muted);">Enter your details to access the system</p>
    </div>

    <?php if($error): ?>
        <div class="alert alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i> <?php echo h($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
        
        <div class="form-group">
            <label class="form-label">Username or Email</label>
            <input type="text" name="login" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%;">
            <i class="fa-solid fa-arrow-right-to-bracket"></i> Login
        </button>

        <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted); font-size: 0.9rem;">
            Don't have an account? <a href="register.php" style="color: var(--primary); text-decoration: none;">Sign up</a>
        </p>
    </form>
</div>
</main>

<?php require_once '../includes/footer.php'; ?>
