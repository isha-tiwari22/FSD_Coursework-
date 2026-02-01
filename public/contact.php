<?php
// public/contact.php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';
?>

<main class="main-content section">
    <div class="container">
        <div class="page-header fade-in">
            <div class="page-title">
                <h1><i class="fa-solid fa-envelope" style="color: var(--primary); margin-right: 0.5rem;"></i> Contact Us</h1>
                <p style="color: var(--text-muted);">We're here to help you</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 3rem;" class="fade-in">
            <!-- Contact Info -->
            <div class="card">
                <div class="card-body">
                    <h3>Get in touch</h3>
                    <p style="color: var(--text-muted); margin-bottom: 2rem;">Have questions or need assistance? Feel free to reach out to our support team.</p>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--primary); font-weight: 600; margin-bottom: 0.3rem;"><i class="fa-solid fa-envelope" style="margin-right: 0.5rem;"></i> Email Support</div>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">support@harayobhetiyo.com</p>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--primary); font-weight: 600; margin-bottom: 0.3rem;"><i class="fa-solid fa-map-location-dot" style="margin-right: 0.5rem;"></i> Office Address</div>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">123 University Ave, Kathmandu, Nepal</p>
                    </div>

                    <div>
                        <div style="color: var(--primary); font-weight: 600; margin-bottom: 0.3rem;"><i class="fa-solid fa-clock" style="margin-right: 0.5rem;"></i> Business Hours</div>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">Sun - Fri: 9:00 AM - 6:00 PM</p>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="card">
                <div class="card-body">
                    <h3>Send a Message</h3>
                    <form style="margin-top: 1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" placeholder="your@email.com" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" rows="5" placeholder="How can we help?" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fa-solid fa-paper-plane" style="margin-right: 0.5rem;"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
