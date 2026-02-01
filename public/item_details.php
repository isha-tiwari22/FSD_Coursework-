<?php
// public/item_details.php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_GET['id'])) {
    header("Location: items.php");
    exit;
}

$id = (int)$_GET['id'];

try {
    // Fetch item details
    $stmt = $pdo->prepare("SELECT i.*, u.username, u.email as reporter_email, u.profile_image 
                            FROM items i 
                            JOIN users u ON i.user_id = u.id 
                            WHERE i.id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();

    if (!$item) {
        header("Location: items.php");
        exit;
    }

    // Fetch all images
    $stmt_imgs = $pdo->prepare("SELECT id FROM item_images WHERE item_id = ?");
    $stmt_imgs->execute([$id]);
    $images = $stmt_imgs->fetchAll();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

require_once '../includes/header.php';
?>

<main class="main-content">
    <div class="container">
        <div class="page-header fade-in">
            <div class="page-title">
                <h1><i class="fa-solid fa-circle-info" style="color: var(--primary); margin-right: 0.5rem;"></i> Item Details</h1>
                <p style="color: var(--text-muted);">Detailed view of the reported item</p>
            </div>
            <a href="items.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>

        <!-- Alerts -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success fade-in" style="margin-bottom: 2rem;">
                <i class="fa-regular fa-circle-check"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error fade-in" style="margin-bottom: 2rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="details-grid fade-in">
            <!-- Left Column: Media & Requests -->
            <div class="details-column">
                <!-- Images -->
                <div class="card">
                    <div class="card-body">
                        <h3><i class="fa-solid fa-images" style="color: var(--primary); margin-right: 0.5rem;"></i> Photos</h3>
                        <?php if (empty($images)): ?>
                            <div style="aspect-ratio: 16/9; background: var(--bg-page); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--text-muted); border: 2px dashed var(--border); margin-top: 1rem;">
                                <div style="text-align: center;">
                                    <i class="fa-solid fa-camera" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                                    <p>No photos provided</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Main Image Container -->
                            <div style="width: 100%; aspect-ratio: 16/9; border-radius: 16px; overflow: hidden; border: 1px solid var(--border); margin-top: 1.5rem; background: #000; display: flex; align-items: center; justify-content: center;">
                                <img id="mainImage" src="image_view.php?id=<?php echo $images[0]['id']; ?>" style="width: 100%; height: 100%; display: block; object-fit: contain;">
                            </div>
                            
                            <!-- Thumbnails -->
                            <?php if (count($images) > 1): ?>
                            <div style="display: flex; gap: 1rem; margin-top: 1.5rem; overflow-x: auto; padding-bottom: 0.5rem;">
                                <?php foreach ($images as $img): ?>
                                    <div onclick="document.getElementById('mainImage').src = 'image_view.php?id=<?php echo $img['id']; ?>'" 
                                         style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden; border: 2px solid var(--border); cursor: pointer; flex-shrink: 0; transition: all 0.3s ease;">
                                        <img src="image_view.php?id=<?php echo $img['id']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Status Card (Mobile/Sidebar context) -->
                <div class="card" style="border-top: 5px solid <?php echo $item['status'] == 'lost' ? 'var(--status-lost)' : 'var(--status-found)'; ?>;">
                    <div class="card-body" style="text-align: center;">
                        <i class="fa-solid <?php echo $item['status'] == 'lost' ? 'fa-magnifying-glass' : 'fa-hand-holding-heart'; ?>" style="font-size: 2.5rem; color: <?php echo $item['status'] == 'lost' ? 'var(--status-lost)' : 'var(--status-found)'; ?>; margin-bottom: 1rem;"></i>
                        <h4 style="margin-bottom: 0.5rem;"><?php echo $item['status'] == 'lost' ? 'Missing Item' : 'Found Item'; ?></h4>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">
                            <?php echo $item['status'] == 'lost' ? 'The owner is looking for this.' : 'Someone is waiting to return this.'; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Info -->
            <div class="details-column">
                <div class="card">
                    <div class="card-body">
                        <div style="margin-bottom: 2.5rem;">
                            <span class="status-badge status-<?php echo h($item['status']); ?>" style="margin-bottom: 0.8rem;">
                                <?php echo ucfirst(h($item['status'])); ?>
                            </span>
                            <h2 style="font-size: 2.4rem; line-height: 1.2;"><?php echo h($item['item_name']); ?></h2>
                        </div>

                        <div class="info-group">
                            <div class="info-label">
                                <i class="fa-solid fa-align-left"></i> Description
                            </div>
                            <p style="line-height: 1.8; color: var(--text-main); font-size: 1.05rem;"><?php echo nl2br(h($item['description'])); ?></p>
                        </div>

                        <div class="grid-2-col" style="margin-bottom: 2rem;">
                            <div class="info-group" style="margin-bottom: 0;">
                                <div class="info-label">
                                    <i class="fa-solid fa-location-dot"></i> Location
                                </div>
                                <p style="font-weight: 500;"><?php echo h($item['location']); ?></p>
                            </div>
                            <div class="info-group" style="margin-bottom: 0;">
                                <div class="info-label">
                                    <i class="fa-solid fa-calendar-days"></i> Reported On
                                </div>
                                <p style="font-weight: 500;"><?php echo date('M d, Y', strtotime($item['date_reported'])); ?></p>
                            </div>
                        </div>

                        <div style="background: var(--primary-light); padding: 2rem; border-radius: 20px; border-left: 5px solid var(--primary); margin-top: 1rem;">
                            <div class="info-label" style="margin-bottom: 1.5rem;">
                                <i class="fa-solid fa-user-shield"></i> Reporter Information
                            </div>
                            <div style="display: flex; align-items: center; gap: 1.2rem;">
                                <div class="avatar-circle" style="width: 56px; height: 56px; font-size: 1.4rem;">
                                    <?php if (!empty($item['profile_image'])): ?>
                                        <img src="<?php echo h($item['profile_image']); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                    <?php else: ?>
                                        <?php echo strtoupper(substr($item['username'], 0, 1)); ?>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 1.1rem; color: var(--secondary);"><?php echo h($item['username']); ?></div>
                                    <div style="font-size: 0.95rem; color: var(--text-muted); text-transform: none;"><?php echo h($item['reporter_email']); ?></div>
                                </div>
                            </div>
                            <div style="margin-top: 2rem;">
                                <?php if (isLoggedIn()): ?>
                                    <?php if ($item['user_id'] != $_SESSION['user_id']): ?>
                                        <button onclick="document.getElementById('claimModal').style.display='flex'" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">
                                            <i class="fa-solid fa-envelope"></i> Contact Reporter
                                        </button>
                                    <?php else: ?>
                                        <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">
                                            <i class="fa-solid fa-pen"></i> Edit My Report
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">
                                        <i class="fa-solid fa-lock" style="margin-right: 0.5rem;"></i> Login to Contact
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Claim Modal -->
        <?php if (isLoggedIn() && $item['user_id'] != $_SESSION['user_id']): ?>
        <div id="claimModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
            <div class="card fade-in" style="width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto;">
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h3><i class="fa-solid fa-paper-plane" style="color: var(--primary); margin-right: 0.5rem;"></i> Claim / Contact Form</h3>
                        <button onclick="document.getElementById('claimModal').style.display='none'" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.5rem;">&times;</button>
                    </div>
                    
                    <form action="submit_request.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="item_id" value="<?php echo $id; ?>">
                        
                        <div class="form-group">
                            <label class="form-label">Message to Reporter</label>
                            <textarea name="message" class="form-control" rows="4" placeholder="Describe how/where you found it or why you believe it's yours..." required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Proof of Ownership / Found Item (Photos)</label>
                            <div style="border: 2px dashed var(--border); padding: 1.5rem; border-radius: 12px; text-align: center; position: relative; transition: all 0.3s ease;">
                                <input type="file" name="proof_images[]" id="proofUpload" multiple accept="image/*" style="opacity: 0; position: absolute; width: 0.1px; height: 0.1px; overflow: hidden; z-index: -1;">
                                <label for="proofUpload" style="cursor: pointer; display: block;">
                                    <i class="fa-solid fa-cloud-arrow-up" style="font-size: 2rem; color: var(--primary); margin-bottom: 0.8rem;"></i>
                                    <p style="font-weight: 600;">Choose Evidence Photos</p>
                                    <p style="font-size: 0.8rem; color: var(--text-muted);">Select one or more clear images.</p>
                                </label>
                                <div id="proofPreview" style="display: flex; gap: 0.8rem; flex-wrap: wrap; margin-top: 1.5rem; justify-content: center;"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Your Contact Details</label>
                            <input type="text" name="contact_info" class="form-control" placeholder="Phone number or secondary email" required>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 1rem;">
                            <i class="fa-solid fa-check"></i> Send Request for Validation
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <script>
            // Advanced Incremental Image Upload Manager
            let selectedFiles = new DataTransfer();
            const proofUpload = document.getElementById('proofUpload');
            const preview = document.getElementById('proofPreview');

            proofUpload?.addEventListener('change', function(e) {
                if (!e.target.files.length) return;
                
                // Add new files to our master collection
                Array.from(e.target.files).forEach(file => {
                    selectedFiles.items.add(file);
                });
                
                // Sync the input with our collection
                proofUpload.files = selectedFiles.files;
                
                renderPreviews();
            });

            function renderPreviews() {
                preview.innerHTML = '';
                
                Array.from(selectedFiles.files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(re) {
                        const wrapper = document.createElement('div');
                        wrapper.style.position = 'relative';
                        
                        const div = document.createElement('div');
                        div.style.width = '80px';
                        div.style.height = '80px';
                        div.style.borderRadius = '8px';
                        div.style.overflow = 'hidden';
                        div.style.border = '1px solid var(--border)';
                        div.style.boxShadow = 'var(--shadow-sm)';
                        div.innerHTML = `<img src="${re.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
                        
                        // Delete Button
                        const delBtn = document.createElement('button');
                        delBtn.innerHTML = '&times;';
                        delBtn.style.cssText = 'position: absolute; -5px; right: -5px; background: var(--status-lost); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; z-index: 10;';
                        delBtn.onclick = (e) => {
                            e.preventDefault();
                            removeFile(index);
                        };
                        
                        wrapper.appendChild(div);
                        wrapper.appendChild(delBtn);
                        preview.appendChild(wrapper);
                    }
                    reader.readAsDataURL(file);
                });

                // Add More Button
                if (selectedFiles.files.length > 0) {
                    const addMore = document.createElement('div');
                    addMore.style.display = 'flex';
                    addMore.style.flexDirection = 'column';
                    addMore.style.alignItems = 'center';
                    addMore.style.justifyContent = 'center';
                    addMore.style.width = '80px';
                    addMore.style.height = '80px';
                    addMore.style.border = '2px dashed var(--primary)';
                    addMore.style.borderRadius = '8px';
                    addMore.style.cursor = 'pointer';
                    addMore.style.color = 'var(--primary)';
                    addMore.style.background = 'var(--primary-light)';
                    addMore.style.transition = 'all 0.3s ease';
                    addMore.innerHTML = '<i class="fa-solid fa-plus" style="margin-bottom: 4px;"></i><span style="font-size: 0.65rem; font-weight: 700;">ADD MORE</span>';
                    addMore.onclick = () => proofUpload.click();
                    addMore.onmouseover = () => addMore.style.background = 'var(--bg-page)';
                    addMore.onmouseout = () => addMore.style.background = 'var(--primary-light)';
                    preview.appendChild(addMore);
                }
            }

            function removeFile(index) {
                const newFiles = new DataTransfer();
                const files = selectedFiles.files;
                for (let i = 0; i < files.length; i++) {
                    if (i !== index) newFiles.items.add(files[i]);
                }
                selectedFiles = newFiles;
                proofUpload.files = selectedFiles.files;
                renderPreviews();
            }

            // Close modal if clicking outside
            window.onclick = function(event) {
                const modal = document.getElementById('claimModal');
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        </script>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
