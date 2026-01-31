<?php
// includes/header.php
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harayo/Bhetiyo - Lost & Found Management</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-container container">
                <a href="index.php" class="nav-logo">
                    <i class="fa-solid fa-magnifying-glass-location"></i> Harayo<b>/</b>Bhetiyo
                </a>
                <div class="nav-links">
                    <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
                    <a href="index.php" class="nav-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a>
                    <?php if (isLoggedIn()): ?>
                        <a href="index.php" class="nav-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Dashboard</a>
                        <a href="add.php" class="nav-item <?php echo $current_page == 'add.php' ? 'active' : ''; ?>">Report Item</a>
                    <?php endif; ?>
                    <a href="about.php" class="nav-item <?php echo $current_page == 'about.php' ? 'active' : ''; ?>">About</a>
                    <a href="contact.php" class="nav-item <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact Us</a>
                    
                    <?php if (isLoggedIn()): ?>
                        <?php 
                        $currentUser = isset($pdo) ? getCurrentUser($pdo) : null;
                        if ($currentUser): 
                        ?>
                        <div class="user-menu" style="position: relative;">
                            <button onclick="toggleUserDropdown()" class="btn btn-secondary btn-sm" style="display: flex; align-items: center; gap: 0.5rem; border: none; background: transparent; padding: 0;">
                                <span class="avatar-circle">
                                    <?php if (!empty($currentUser['profile_image'])): ?>
                                        <img src="<?php echo h($currentUser['profile_image']); ?>" alt="User" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                    <?php else: ?>
                                        <?php echo strtoupper(substr($currentUser['username'], 0, 1)); ?>
                                    <?php endif; ?>
                                </span>
                                <span class="username"><?php echo h($currentUser['username']); ?></span>
                                <i class="fa-solid fa-chevron-down" style="font-size: 0.8rem; color: var(--text-muted);"></i>
                            </button>
                            
                            <div id="userDropdownMenu" class="dropdown-menu">
                                <div class="dropdown-header">
                                    <div class="avatar-circle" style="width: 48px; height: 48px; font-size: 1.2rem; margin-bottom: 0.5rem;">
                                        <?php if (!empty($currentUser['profile_image'])): ?>
                                            <img src="<?php echo h($currentUser['profile_image']); ?>" alt="User" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                        <?php else: ?>
                                            <?php echo strtoupper(substr($currentUser['username'], 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>
                                    <strong><?php echo h($currentUser['username']); ?></strong>
                                    <div style="font-size: 0.85rem; color: var(--text-muted); word-break: break-all;"><?php echo h($currentUser['email']); ?></div>
                                </div>
                                <hr style="border: 0; border-top: 1px solid var(--border); margin: 0.5rem 0;">

                                <a href="settings.php" class="dropdown-item">
                                    <i class="fa-solid fa-gear"></i> Settings
                                </a>
                                <a href="logout.php" class="dropdown-item">
                                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="login.php" class="nav-item">Login</a>
                        <a href="register.php" class="btn btn-primary">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

    <script>
    function toggleUserDropdown() {
        document.getElementById("userDropdownMenu").classList.toggle("show");
    }

    // Close dropdown when clicking outside
    window.onclick = function(event) {
        if (!event.target.closest('.user-menu')) {
            var dropdowns = document.getElementsByClassName("dropdown-menu");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
    </script>
    </header>
