<?php
// public/settings.php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireLogin();

$user = getCurrentUser($pdo);
$message = '';
$error = '';
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST['csrf_token']);
    

    if (isset($_POST['update_profile'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $phone = trim($_POST['phone']);
        $bio = trim($_POST['bio']);
        
        if (empty($username) || empty($email)) {
            $error = "Username and Email are required.";
        } else {
            // Check uniqueness
            $stmt = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
            $stmt->execute([$username, $email, $user['id']]);
            if ($stmt->fetch()) {
                $error = "Username or Email already in use.";
            } else {
                // Handle File Upload
                $profile_image = $user['profile_image'];
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['profile_image']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = 'user_' . $user['id'] . '_' . time() . '.' . $ext;
                        $upload_dir = __DIR__ . '/assets/uploads/profiles/';
                        
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }
                        
                        // Remove old image if exists and not default
                        if ($profile_image && file_exists($upload_dir . basename($profile_image))) {
                            unlink($upload_dir . basename($profile_image));
                        }
                        
                        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_dir . $new_filename)) {
                            $profile_image = 'assets/uploads/profiles/' . $new_filename;
                        } else {
                            $error = "Failed to upload image.";
                        }
                    } else {
                        $error = "Invalid file type. Only JPG, PNG, GIF allowed.";
                    }
                }

                if (!$error) {
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, first_name = ?, last_name = ?, phone = ?, bio = ?, profile_image = ? WHERE id = ?");
                    if ($stmt->execute([$username, $email, $first_name, $last_name, $phone, $bio, $profile_image, $user['id']])) {
                        $message = "Profile updated successfully.";
                        // Refresh user data
                        $user['username'] = $username;
                        $user['email'] = $email;
                        $user['first_name'] = $first_name;
                        $user['last_name'] = $last_name;
                        $user['phone'] = $phone;
                        $user['bio'] = $bio;
                        $user['profile_image'] = $profile_image;
                        $_SESSION['username'] = $username;
                    } else {
                        $error = "Failed to update profile.";
                    }
                }
            }
        }
        $activeTab = 'profile';

    } elseif (isset($_POST['change_password'])) {
        // ... (keep password logic same) ...
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (!password_verify($current_password, $user['password_hash'])) {
            $error = "Incorrect current password.";
        } elseif (strlen($new_password) < 6) {
            $error = "New password must be at least 6 characters.";
        } elseif ($new_password !== $confirm_password) {
            $error = "New passwords do not match.";
        } else {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            if ($stmt->execute([$new_hash, $user['id']])) {
                $message = "Password changed successfully.";
            } else {
                $error = "Failed to change password.";
            }
        }
        $activeTab = 'security';
    }
}

require_once '../includes/header.php';
?>

<main class="main-content">
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Account Settings</h1>
            <p style="color: var(--text-muted);">Manage your profile, security, and preferences</p>
        </div>
    </div>

    <div class="settings-layout">
        <aside class="settings-sidebar fade-in">
            <nav class="settings-nav">
                <a href="?tab=profile" class="settings-link <?php echo $activeTab === 'profile' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-user"></i> Profile
                </a>
                <a href="?tab=security" class="settings-link <?php echo $activeTab === 'security' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-shield-halved"></i> Security
                </a>
                <a href="?tab=danger" class="settings-link link-danger <?php echo $activeTab === 'danger' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-triangle-exclamation"></i> Danger Zone
                </a>
            </nav>
        </aside>

        <div class="settings-content fade-in">
            <?php if($message): ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-check-circle"></i> <?php echo h($message); ?>
                </div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo h($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($activeTab === 'profile'): ?>
                <div class="card">
                    <div class="card-header">
                        <h3>Profile Information</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="settings.php?tab=profile" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                            
                            <div class="form-group" style="text-align: center; margin-bottom: 2rem;">
                                <div class="profile-upload-wrapper">
                                    <div class="avatar-circle profile-preview" style="width: 100px; height: 100px; font-size: 2.5rem; margin: 0 auto;">
                                        <?php if (!empty($user['profile_image'])): ?>
                                            <img src="<?php echo h($user['profile_image']); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                        <?php else: ?>
                                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>
                                    <label for="profile_image" class="btn btn-secondary btn-sm" style="margin-top: 1rem; cursor: pointer;">
                                        <i class="fa-solid fa-camera"></i> Change Photo
                                    </label>
                                    <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display: none;" onchange="previewImage(this)">
                                </div>
                            </div>

                            <div class="form-group grid-2-col">
                                <div>
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" value="<?php echo h($user['first_name'] ?? ''); ?>">
                                </div>
                                <div>
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" value="<?php echo h($user['last_name'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" value="<?php echo h($user['username']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?php echo h($user['email']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" value="<?php echo h($user['phone'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Bio</label>
                                <textarea name="bio" class="form-control" rows="4" placeholder="Tell us a little about yourself..."><?php echo h($user['bio'] ?? ''); ?></textarea>
                            </div>
                            
                            <button type="submit" name="update_profile" class="btn btn-primary">
                                Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            
            <?php elseif ($activeTab === 'security'): ?>
                <div class="card">
                    <div class="card-header">
                        <h3>Change Password</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="settings.php?tab=security">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                            
                            <div class="form-group">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" minlength="6" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" minlength="6" required>
                            </div>
                            
                            <button type="submit" name="change_password" class="btn btn-primary">
                                Update Password
                            </button>
                        </form>
                    </div>
                </div>

            <?php elseif ($activeTab === 'danger'): ?>
                <div class="card" style="border-color: var(--status-lost-bg);">
                    <div class="card-header" style="background: var(--status-lost-bg); border-bottom-color: rgba(231, 76, 60, 0.1);">
                        <h3 style="color: var(--status-lost);">Delete Account</h3>
                    </div>
                    <div class="card-body">
                        <p style="margin-bottom: 1.5rem; color: var(--text-muted);">
                            Once you delete your account, there is no going back. Please be certain.
                        </p>
                        <a href="delete_account.php" class="btn btn-danger">
                            <i class="fa-solid fa-trash-can"></i> Proceed to Account Deletion
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>


<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.querySelector('.profile-preview');
            // Check if there is an image inside
            var img = preview.querySelector('img');
            if (img) {
                img.src = e.target.result;
            } else {
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">';
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
