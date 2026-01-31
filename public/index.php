<?php
// public/index.php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Remove strict requireLogin() to allow landing page
// requireLogin(); 

require_once '../includes/header.php';
?>

<?php if (!isLoggedIn()): ?>
    <!-- LANDING PAGE FOR VISITORS -->
    <main>
        <!-- Slider Section -->
        <section class="slider-container">
            <div class="slider-wrapper">
                <!-- Slide 1 -->
                <div class="slide active">
                    <img src="assets/images/slider/slide1.png" alt="Lost Keys" class="slide-image">
                    <div class="floating-shapes">
                        <div class="shape shape-1"></div>
                        <div class="shape shape-2"></div>
                        <div class="shape shape-3"></div>
                    </div>
                    <div class="slide-content">
                        <h1>Recover What Matters</h1>
                        <p class="hero-subtitle">The most reliable community-driven platform to reunite you with your lost belongings.</p>
                        <div class="hero-actions">
                             <a href="register.php" class="btn btn-primary btn-lg">I Lost Something</a>
                        </div>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="slide">
                    <img src="assets/images/slider/slide2.png" alt="Community" class="slide-image">
                    <div class="floating-shapes">
                        <div class="shape shape-1"></div>
                        <div class="shape shape-2"></div>
                    </div>
                    <div class="slide-content">
                        <h1>Community Trust</h1>
                        <p class="hero-subtitle">Connect with honest people nearby who want to help return what's yours.</p>
                        <div class="hero-actions">
                             <a href="add.php" class="btn btn-secondary btn-lg">I Found Something</a>
                        </div>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div class="slide">
                    <img src="assets/images/slider/slide3.png" alt="Happy Reunion" class="slide-image">
                    <div class="floating-shapes">
                        <div class="shape shape-3"></div>
                    </div>
                    <div class="slide-content">
                        <h1>Happy Reunions</h1>
                        <p class="hero-subtitle">Every item has a story. Creating happy endings, one lost item at a time.</p>
                        <div class="hero-actions">
                             <a href="register.php" class="btn btn-primary btn-lg">Join Us Today</a>
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

        <section class="stats-section section section-secondary fade-in">
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
                    <p class="stat-label">Community Members</p>
                </div>
            </div>
        </section>

        <section class="features-section section section-white fade-in">
            <div class="container">
                <h2 class="section-title text-center">How It Works</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-file-pen"></i></div>
                        <h3>1. Report an Item</h3>
                        <p>Lost something or found an unclaimed item? Create a detailed report in seconds.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-handshake-simple"></i></div>
                        <h3>2. Smart Matching</h3>
                        <p>Our system connects potential matches based on location, time, and description.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-check-double"></i></div>
                        <h3>3. Safe Return</h3>
                        <p>Verify ownership and coordinate a safe return of the belongings.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="testimonials-section section section-light fade-in">
            <div class="container">
                <h2 class="section-title text-center">What Our Users Say</h2>
                <div class="testimonials-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 3rem;">
                    <div class="card" style="padding: 2rem; text-align: left; border: none; margin-bottom: 0;">
                        <div style="color: var(--primary); margin-bottom: 1rem;">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <p style="font-style: italic; color: var(--text-muted); margin-bottom: 1.5rem;">"I lost my keys at the park and thought they were gone forever. Within two days, someone posted them here. Thank you Harayo/Bhetiyo!"</p>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div class="avatar-circle">S</div>
                            <strong>Suman Thapa</strong>
                        </div>
                    </div>
                    <div class="card" style="padding: 2rem; text-align: left; border: none; margin-bottom: 0;">
                        <div style="color: var(--primary); margin-bottom: 1rem;">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <p style="font-style: italic; color: var(--text-muted); margin-bottom: 1.5rem;">"I found a wallet near the bus stop and didn't know how to reach the owner. This platform made it so easy to return it safely."</p>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div class="avatar-circle">A</div>
                            <strong>Anjali Rai</strong>
                        </div>
                    </div>
                    <div class="card" style="padding: 2rem; text-align: left; border: none; margin-bottom: 0;">
                        <div style="color: var(--primary); margin-bottom: 1rem;">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <p style="font-style: italic; color: var(--text-muted); margin-bottom: 1.5rem;">"A great community initiative. Simple to use and very effective. Highly recommended for everyone."</p>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div class="avatar-circle">R</div>
                            <strong>Ramesh Kumar</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="faq-section section section-white fade-in">
            <div class="container" style="max-width: 800px;">
                <h2 class="section-title text-center">Frequently Asked Questions</h2>
                <div class="faq-list">
                    <div class="faq-item">
                        <h3 style="font-size: 1.2rem; margin-bottom: 0.8rem; color: var(--secondary);">Is this service free to use?</h3>
                        <p style="color: var(--text-muted);">Yes, Harayo/Bhetiyo is completely free for everyone to report lost or found items.</p>
                    </div>
                    <div class="faq-item">
                        <h3 style="font-size: 1.2rem; margin-bottom: 0.8rem; color: var(--secondary);">How do I verify the owner of a found item?</h3>
                        <p style="color: var(--text-muted);">We recommend asking for specific details that were not in the public post, like a serial number, a specific scratch, or a photo of them with the item.</p>
                    </div>
                    <div class="faq-item">
                        <h3 style="font-size: 1.2rem; margin-bottom: 0.8rem; color: var(--secondary);">Can I report an item without an account?</h3>
                        <p style="color: var(--text-muted);">You can browse items, but to report an item and be contacted, you need to create a secure account.</p>
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

<?php else: ?>
    <!-- DASHBOARD FOR LOGGED IN USERS -->
    <?php
    // Fetch items logic (moved inside the loop)
    $stmt = $pdo->query("SELECT i.*, u.username, u.profile_image FROM items i JOIN users u ON i.user_id = u.id ORDER BY i.date_reported DESC LIMIT 20");
    $items = $stmt->fetchAll();
    ?>

    <main class="main-content">
        <div class="page-header fade-in">
            <div class="page-title">
                <h1>Dashboard</h1>
                <p style="color: var(--text-muted);">Manage reported items and status</p>
            </div>
            <a href="add.php" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Report Item
            </a>
        </div>

        <div class="card fade-in">
            <div class="card-header">
                <div class="search-container">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="searchInput" class="search-input" placeholder="Search items, categories, or descriptions..." autocomplete="off">
                    <div id="suggestions" class="suggestions-box"></div>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <select class="form-control" id="filterStatus" style="width: auto; padding: 0.5rem 1rem;">
                        <option value="">All Status</option>
                        <option value="lost">Lost</option>
                        <option value="found">Found</option>
                        <option value="claimed">Claimed</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table" id="itemsTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reporter</th>
                            <th>Item Name</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($item['date_reported'])); ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div class="avatar-circle">
                                        <?php if (!empty($item['profile_image'])): ?>
                                            <img src="<?php echo h($item['profile_image']); ?>" alt="User" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                        <?php else: ?>
                                            <?php echo strtoupper(substr($item['username'], 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php echo h($item['username']); ?>
                                </div>
                            </td>
                            <td>
                                <strong><?php echo h($item['item_name']); ?></strong>
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem;"><?php echo h(substr($item['description'], 0, 50)) . '...'; ?></div>
                            </td>
                            <td><i class="fa-solid fa-location-dot" style="color: var(--primary);"></i> <?php echo h($item['location']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo h($item['status']); ?>">
                                    <?php echo ucfirst(h($item['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <?php if($_SESSION['user_id'] == $item['user_id']): ?>
                                        <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn btn-secondary btn-sm" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this item?');" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($items)): ?>
                            <tr><td colspan="6" style="text-align: center; padding: 2rem;">No items found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        if(!searchInput) return; // Guard for landing page
        
        const filterStatus = document.getElementById('filterStatus');
        const tableBody = document.getElementById('tableBody');
        const suggestions = document.getElementById('suggestions');

        function fetchResults() {
            const query = searchInput.value;
            const status = filterStatus.value;
            
            fetch(`search_ajax.php?q=${encodeURIComponent(query)}&status=${status}`)
                .then(response => response.json())
                .then(data => {
                    tableBody.innerHTML = '';
                    
                    if (data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem;">No items found.</td></tr>';
                        return;
                    }

                    data.forEach(item => {
                        const row = `
                            <tr class="fade-in">
                                <td>${new Date(item.date_reported).toLocaleDateString()}</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div class="avatar-circle">
                                            ${item.profile_image 
                                                ? `<img src="${escapeHtml(item.profile_image)}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">` 
                                                : item.username.charAt(0).toUpperCase()}
                                        </div>
                                        ${escapeHtml(item.username)}
                                    </div>
                                </td>
                                <td>
                                    <strong>${escapeHtml(item.item_name)}</strong>
                                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem;">${escapeHtml(item.description.substring(0, 50))}...</div>
                                </td>
                                <td><i class="fa-solid fa-location-dot" style="color: var(--primary);"></i> ${escapeHtml(item.location)}</td>
                                <td>
                                    <span class="status-badge status-${escapeHtml(item.status)}">
                                        ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <!-- Note: Edit/Delete buttons logic simplified for AJAX demo -->
                                         <a href="edit.php?id=${item.id}" class="btn btn-secondary btn-sm" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                });
        }

        searchInput.addEventListener('input', function() {
            fetchResults();
            if (this.value.length > 1) {
                fetch(`search_ajax.php?q=${encodeURIComponent(this.value)}&type=names`)
                    .then(res => res.json())
                    .then(names => {
                        suggestions.innerHTML = '';
                        if (names.length > 0) {
                            suggestions.style.display = 'block';
                            names.slice(0, 5).forEach(n => {
                                const div = document.createElement('div');
                                div.className = 'suggestion-item';
                                div.textContent = n.item_name;
                                div.onclick = () => {
                                    searchInput.value = n.item_name;
                                    suggestions.style.display = 'none';
                                    fetchResults();
                                };
                                suggestions.appendChild(div);
                            });
                        } else {
                            suggestions.style.display = 'none';
                        }
                    });
            } else {
                suggestions.style.display = 'none';
            }
        });

        filterStatus.addEventListener('change', fetchResults);
        
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestions.contains(e.target)) {
                suggestions.style.display = 'none';
            }
        });

        function escapeHtml(text) {
            if (!text) return '';
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    });
    </script>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
