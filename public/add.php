<?php
// public/add.php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST['csrf_token']);
    
    $item_name = trim($_POST['item_name']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $status = $_POST['status'];
    
    if (empty($item_name) || empty($location)) {
        $error = "Item Name and Location are required.";
    } else {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO items (user_id, item_name, description, location, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $item_name, $description, $location, $status]);
            $item_id = $pdo->lastInsertId();

            // Handle Multiple Image Uploads
            if (isset($_FILES['item_images']) && is_array($_FILES['item_images']['name'])) {
                $uploadedCount = 0;
                $failedCount = 0;
                $failedMessages = [];
                
                foreach ($_FILES['item_images']['tmp_name'] as $key => $tmp_name) {
                    if (empty($tmp_name) && $_FILES['item_images']['error'][$key] === UPLOAD_ERR_NO_FILE) {
                        continue; // Skip if no file was selected for this input
                    }
                    
                    if ($_FILES['item_images']['error'][$key] === UPLOAD_ERR_OK) {
                        $image_data = file_get_contents($tmp_name);
                        $image_type = $_FILES['item_images']['type'][$key];
                        
                        if (str_starts_with($image_type, 'image/')) {
                            $stmt_img = $pdo->prepare("INSERT INTO item_images (item_id, image_data, image_type) VALUES (?, ?, ?)");
                            $stmt_img->execute([$item_id, $image_data, $image_type]);
                            $uploadedCount++;
                        } else {
                            $failedCount++;
                            $failedMessages[] = "File '{$_FILES['item_images']['name'][$key]}' is not a valid image type.";
                        }
                    } else {
                        $failedCount++;
                    }
                }
                
                if ($failedCount > 0) {
                    $success .= " ($uploadedCount photos uploaded, $failedCount failed - possibly too large)";
                }
            }

            $pdo->commit();
            $success = "Item reported successfully. " . ($success ?: "");
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to add item: " . $e->getMessage();
        }
    }
}

require_once '../includes/header.php';
?>

<main class="main-content">
    <div class="container">
        <div class="page-header fade-in">
            <div class="page-title">
                <h1><i class="fa-solid fa-file-circle-plus" style="color: var(--primary); margin-right: 0.5rem;"></i> Report Item</h1>
                <p style="color: var(--text-muted);">Submit a new lost or found item with photos.</p>
            </div>
            <a href="items.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Items
            </a>
        </div>

        <div class="card fade-in" style="max-width: 900px; margin: 0 auto;">
            <div class="card-body">
                <?php if($error): ?>
                    <div class="alert alert-error">
                        <i class="fa-solid fa-triangle-exclamation"></i> <?php echo h($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div class="alert alert-success">
                        <i class="fa-regular fa-circle-check"></i> <?php echo h($success); ?> <a href="items.php">View All Items</a>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-heading" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Item Name</label>
                        <input type="text" name="item_name" class="form-control" placeholder="e.g. Red Backpack, iPhone 13" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-align-left" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Provide more details..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-images" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Item Images (Upload Multiple)</label>
                        <div class="file-upload-wrapper" style="border: 2px dashed var(--border); padding: 2rem; border-radius: 12px; text-align: center; transition: all 0.3s ease;">
                            <input type="file" name="item_images[]" id="imageUpload" multiple accept="image/*" style="opacity: 0; position: absolute; width: 0.1px; height: 0.1px; overflow: hidden; z-index: -1;">
                            <label for="imageUpload" style="cursor: pointer; display: block;">
                                <i class="fa-solid fa-cloud-arrow-up" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1rem;"></i>
                                <p style="font-weight: 600;">Click to upload photos</p>
                                <p style="font-size: 0.8rem; color: var(--text-muted);">PNG, JPG up to 10MB each</p>
                            </label>
                            <div id="imagePreview" class="grid-2-col" style="margin-bottom: 2rem;"></div>
                        </div>
                    </div>

                    <div class="grid-2-col">
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-map-pin" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Location</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g. Library, Cafeteria" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-toggle-on" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Status</label>
                            <select name="status" class="form-control">
                                <option value="lost">Lost</option>
                                <option value="found">Found</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="margin-top: 2rem; text-align: right;">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa-regular fa-paper-plane"></i> Submit Report
                        </button>
                    </div>
                </form>

                <script>
                // Advanced Incremental Image Upload Manager
                let selectedFiles = new DataTransfer();
                const imageUpload = document.getElementById('imageUpload');
                const preview = document.getElementById('imagePreview');

                imageUpload?.addEventListener('change', function(e) {
                    if (!e.target.files.length) return;
                    
                    // Add new files to our master collection
                    Array.from(e.target.files).forEach(file => {
                        selectedFiles.items.add(file);
                    });
                    
                    // Sync the input with our collection
                    imageUpload.files = selectedFiles.files;
                    
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
                            div.style.width = '120px';
                            div.style.height = '120px';
                            div.style.borderRadius = '12px';
                            div.style.overflow = 'hidden';
                            div.style.border = '1px solid var(--border)';
                            div.style.boxShadow = 'var(--shadow-sm)';
                            div.innerHTML = `<img src="${re.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
                            
                            // Delete Button
                            const delBtn = document.createElement('button');
                            delBtn.innerHTML = '&times;';
                            delBtn.style.cssText = 'position: absolute; -10px; right: -10px; background: var(--status-lost); color: white; border: none; border-radius: 50%; width: 28px; height: 28px; cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center; z-index: 10; box-shadow: var(--shadow-sm);';
                            delBtn.type = 'button';
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
                        addMore.style.width = '120px';
                        addMore.style.height = '120px';
                        addMore.style.border = '2px dashed var(--primary)';
                        addMore.style.borderRadius = '12px';
                        addMore.style.cursor = 'pointer';
                        addMore.style.color = 'var(--primary)';
                        addMore.style.background = 'var(--primary-light)';
                        addMore.style.transition = 'all 0.3s ease';
                        addMore.innerHTML = '<i class="fa-solid fa-plus" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i><span style="font-size: 0.8rem; font-weight: 600;">Add More</span>';
                        addMore.onclick = () => imageUpload.click();
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
                    imageUpload.files = selectedFiles.files;
                    renderPreviews();
                }
                </script>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
