<?php
// public/about.php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';
?>

<main class="main-content section">
    <div class="container">
        <div class="page-header fade-in">
            <div class="page-title">
                <h1>About Us</h1>
                <p style="color: var(--text-muted);">Bringing communities together to recover lost belongings</p>
            </div>
        </div>

        <div class="card fade-in">
            <div class="card-body" style="padding: 4rem;">
                <div style="max-width: 800px; margin: 0 auto; line-height: 1.8;">
                    <h2 style="margin-bottom: 2rem; color: var(--primary);">Our Mission</h2>
                    <p style="font-size: 1.1rem; margin-bottom: 1.5rem;">
                        Harayo/Bhetiyo was created with a simple yet powerful goal: to provide a reliable, community-driven platform that helps people reunite with their lost belongings. We believe that in every community, there are many honest people who want to help, but often lack the means to connect with those who have lost something.
                    </p>
                    <p style="font-size: 1.1rem; margin-bottom: 2rem;">
                        Our platform leverages technology to bridge this gap, offering a transparent and efficient way to report lost and found items, matching potential reports, and facilitating safe returns.
                    </p>

                    <div class="features-grid" style="margin-top: 4rem; text-align: left;">
                        <div class="feature-card" style="padding: 2rem; background: var(--primary-light);">
                            <i class="fa-solid fa-users-line" style="font-size: 2rem; color: var(--primary); margin-bottom: 1rem;"></i>
                            <h3>Community First</h3>
                            <p>We rely on the honesty and cooperation of community members to make this work.</p>
                        </div>
                        <div class="feature-card" style="padding: 2rem; background: var(--primary-light);">
                            <i class="fa-solid fa-shield-halved" style="font-size: 2rem; color: var(--primary); margin-bottom: 1rem;"></i>
                            <h3>Secure & Trusted</h3>
                            <p>We prioritize the privacy and safety of our users throughout the return process.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
