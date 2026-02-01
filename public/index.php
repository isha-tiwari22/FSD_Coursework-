<?php
// public/index.php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';
?>

<main>
    <!-- Hero Slider -->
    <section class="slider-container" style="position: relative; z-index: 1;">
        <div class="slider-wrapper">
                <!-- Slide 1 -->
                <div class="slide active">
                    <img src="assets/images/slider/slide1.png" alt="Lost & Found" class="slide-image">
                    <div class="slide-content">
                        <h1 style="font-size: 3.5rem; margin-bottom: 1.5rem;">Find What's Gone.</h1>
                        <p class="hero-subtitle">The #1 community-driven platform to reunite you with your lost belongings.</p>
                        <div class="hero-actions">
                            <a href="items.php" class="btn btn-primary btn-lg">Browse Items</a>
                            <?php if (!isLoggedIn()): ?>
                                <a href="register.php" class="btn btn-secondary btn-lg">Join Now</a>
                            <?php else: ?>
                                <a href="add.php" class="btn btn-secondary btn-lg">Report Item</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="slide">
                    <img src="assets/images/slider/slide2.png" alt="Community" class="slide-image">
                    <div class="slide-content">
                        <h1 style="font-size: 3.5rem; margin-bottom: 1.5rem;">Community Trust.</h1>
                        <p class="hero-subtitle">Connect with honest people nearby who want to help return what's yours.</p>
                        <div class="hero-actions">
                            <a href="about.php" class="btn btn-primary btn-lg">Learn More</a>
                            <a href="items.php" class="btn btn-secondary btn-lg">Search Items</a>
                        </div>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div class="slide">
                    <img src="assets/images/slider/slide3.png" alt="Trusted" class="slide-image">
                    <div class="slide-content">
                        <h1 style="font-size: 3.5rem; margin-bottom: 1.5rem;">Safe & Secure.</h1>
                        <p class="hero-subtitle">Our verification system ensures items go back to their rightful owners.</p>
                        <div class="hero-actions">
                            <a href="items.php" class="btn btn-primary btn-lg">Browse Items</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controls -->
            <button class="slider-arrow prev-slide" onclick="moveSlide(-1)"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="slider-arrow next-slide" onclick="moveSlide(1)"><i class="fa-solid fa-chevron-right"></i></button>
            
            <div class="slider-controls">
                <div class="slider-dot active" onclick="currentSlide(0)"></div>
                <div class="slider-dot" onclick="currentSlide(1)"></div>
                <div class="slider-dot" onclick="currentSlide(2)"></div>
            </div>
        </section>

    <!-- Stats Section -->
    <section class="stats-section section fade-in">
        <div class="stats-grid container">
            <div class="stat-card">
                <h2 class="stat-number">2,500+</h2>
                <p class="stat-label">Items Recovered</p>
            </div>
            <div class="stat-card">
                <h2 class="stat-number">1,200+</h2>
                <p class="stat-label">Happy Reunions</p>
            </div>
            <div class="stat-card">
                <h2 class="stat-number">50+</h2>
                <p class="stat-label">Cities Covered</p>
            </div>
            <div class="stat-card">
                <h2 class="stat-number">10k+</h2>
                <p class="stat-label">Members</p>
            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section class="features-section section section-white fade-in">
        <div class="container">
            <h2 class="section-title text-center">How It Works</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-pen-to-square"></i></div>
                    <h3>1. Report</h3>
                    <p>Post details of your lost or found item to the community feed.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <h3>2. Search</h3>
                    <p>Browse the items list or wait for our system to find a potential match.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-clipboard-check"></i></div>
                    <h3>3. Recover</h3>
                    <p>Verify ownership and coordinate a safe return of the belongings.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section section fade-in">
        <div class="container">
            <div class="cta-card">
                <h2 class="cta-title">Ready to help?</h2>
                <p class="cta-subtitle">Every report brings someone one step closer to finding what they lost.</p>
                <div class="cta-buttons">
                    <a href="items.php" class="btn btn-secondary btn-lg" style="background: rgba(255,255,255,0.1); color: white; border-color: rgba(255,255,255,0.3); backdrop-filter: blur(5px);">View All Items</a>
                    <?php if (!isLoggedIn()): ?>
                        <a href="register.php" class="btn btn-primary btn-lg" style="background: white; color: var(--primary); border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">Sign Up Now</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <script>
        let slideIndex = 0;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.slider-dot');
        const wrapper = document.querySelector('.slider-wrapper');
        let autoPlayTimer;

        function showSlide(n) {
            slideIndex = n;
            if (slideIndex >= slides.length) slideIndex = 0;
            if (slideIndex < 0) slideIndex = slides.length - 1;

            // Move wrapper
            wrapper.style.transform = `translateX(-${slideIndex * 100}%)`;

            // Update active classes
            slides.forEach(slide => slide.classList.remove('active'));
            slides[slideIndex].classList.add('active');

            dots.forEach(dot => dot.classList.remove('active'));
            dots[slideIndex].classList.add('active');
            
            resetTimer();
        }

        function moveSlide(n) {
            showSlide(slideIndex + n);
        }

        function currentSlide(n) {
            showSlide(n);
        }

        function resetTimer() {
            clearInterval(autoPlayTimer);
            autoPlayTimer = setInterval(() => moveSlide(1), 5000); // Auto play ever 5 seconds
        }

        // Start auto play
        resetTimer();
    </script>
</main>

<?php require_once '../includes/footer.php'; ?>
