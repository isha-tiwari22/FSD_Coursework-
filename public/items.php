<?php
// public/items.php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

// Initial fetch
$stmt = $pdo->query("SELECT i.*, u.username, u.profile_image, 
        (SELECT id FROM item_images WHERE item_id = i.id LIMIT 1) as first_image_id 
        FROM items i JOIN users u ON i.user_id = u.id ORDER BY i.date_reported DESC LIMIT 20");
$items = $stmt->fetchAll();
?>

<main class="main-content">
    <div class="container">
        <div class="page-header fade-in">
            <div class="page-title">
                <h1><i class="fa-solid fa-box-open" style="color: var(--primary); margin-right: 0.5rem;"></i> All Items</h1>
                <p style="color: var(--text-muted);">Browse all reported lost and found items in the community.</p>
            </div>
            <?php if (isLoggedIn()): ?>
                <a href="add.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Report Item
                </a>
            <?php endif; ?>
        </div>

        <div class="card fade-in">
            <div class="card-header">
                <div class="search-container">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="searchInput" class="search-input" placeholder="Search items, locations..." autocomplete="off">
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
                            <th><i class="fa-solid fa-image" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Image</th>
                            <th><i class="fa-solid fa-calendar-days" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Date</th>
                            <th><i class="fa-solid fa-user" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Reporter</th>
                            <th><i class="fa-solid fa-tag" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Item Name</th>
                            <th><i class="fa-solid fa-location-dot" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Location</th>
                            <th><i class="fa-solid fa-signal" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Status</th>
                            <th><i class="fa-solid fa-eye" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Details</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if (empty($items)): ?>
                            <tr><td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">No reports found.</td></tr>
                        <?php else: 
                            foreach ($items as $item): 
                        ?>
                        <tr class="fade-in">
                            <td data-label="Image">
                                <?php if ($item['first_image_id']): ?>
                                    <div style="width: 60px; height: 60px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border);">
                                        <img src="image_view.php?id=<?php echo $item['first_image_id']; ?>" alt="Item" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                <?php else: ?>
                                    <div style="width: 60px; height: 60px; border-radius: 8px; background: var(--bg-page); display: flex; align-items: center; justify-content: center; border: 1px solid var(--border); color: var(--text-muted);">
                                        <i class="fa-solid fa-camera" style="opacity: 0.2;"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td data-label="Date"><?php echo date('M d, Y', strtotime($item['date_reported'])); ?></td>
                            <td data-label="Reporter">
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
                            <td data-label="Item Name">
                                <div>
                                    <strong><?php echo h($item['item_name']); ?></strong>
                                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem;"><?php echo h(substr($item['description'], 0, 40)) . (strlen($item['description']) > 40 ? '...' : ''); ?></div>
                                </div>
                            </td>
                            <td data-label="Location"><i class="fa-solid fa-location-dot" style="color: var(--primary); opacity: 0.6; margin-right: 0.4rem;"></i> <?php echo h($item['location']); ?></td>
                            <td data-label="Status">
                                <span class="status-badge status-<?php echo h($item['status']); ?>">
                                    <?php echo ucfirst(h($item['status'])); ?>
                                </span>
                            </td>
                            <td data-label="Details">
                                <a href="item_details.php?id=<?php echo $item['id']; ?>" class="btn btn-secondary btn-sm">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i> View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; 
                        endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const filterStatus = document.getElementById('filterStatus');
        const tableBody = document.getElementById('tableBody');

        function fetchResults() {
            const query = searchInput.value;
            const status = filterStatus.value;
            
            fetch(`search_ajax.php?q=${encodeURIComponent(query)}&status=${status}&scope=all`)
                .then(response => response.json())
                .then(data => {
                    tableBody.innerHTML = '';
                    if (data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">No items match your search.</td></tr>';
                        return;
                    }

                    data.forEach(item => {
                        const row = `
                            <tr class="fade-in">
                                <td data-label="Image">
                                    ${item.first_image_id 
                                        ? `<div style="width: 60px; height: 60px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border);">
                                              <img src="image_view.php?id=${item.first_image_id}" style="width: 100%; height: 100%; object-fit: cover;">
                                           </div>`
                                        : `<div style="width: 60px; height: 60px; border-radius: 8px; background: var(--bg-page); display: flex; align-items: center; justify-content: center; border: 1px solid var(--border); color: var(--text-muted);">
                                              <i class="fa-solid fa-camera" style="opacity: 0.2;"></i>
                                           </div>`
                                    }
                                </td>
                                <td data-label="Date">${new Date(item.date_reported).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' })}</td>
                                <td data-label="Reporter">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div class="avatar-circle">
                                            ${item.profile_image 
                                                ? `<img src="${escapeHtml(item.profile_image)}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">` 
                                                : item.username.charAt(0).toUpperCase()}
                                        </div>
                                        ${escapeHtml(item.username)}
                                    </div>
                                </td>
                                <td data-label="Item Name">
                                    <div>
                                        <strong>${escapeHtml(item.item_name)}</strong>
                                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem;">${escapeHtml(item.description ? item.description.substring(0, 40) : '')}...</div>
                                    </div>
                                </td>
                                <td data-label="Location"><i class="fa-solid fa-location-dot" style="color: var(--primary); opacity: 0.6; margin-right: 0.4rem;"></i> ${escapeHtml(item.location)}</td>
                                <td data-label="Status">
                                    <span class="status-badge status-${escapeHtml(item.status)}">
                                        ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
                                    </span>
                                </td>
                                <td data-label="Details">
                                    <a href="item_details.php?id=${item.id}" class="btn btn-secondary btn-sm">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i> View
                                    </a>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                });
        }

        searchInput.addEventListener('input', fetchResults);
        filterStatus.addEventListener('change', fetchResults);

        function escapeHtml(text) {
            if (!text) return '';
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    });
    </script>
</main>

<?php require_once '../includes/footer.php'; ?>
