<?php
// public/dashboard.php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireLogin();

require_once '../includes/header.php';

// Fetch only current user's items
$stmt = $pdo->prepare("SELECT i.*, u.username, u.profile_image,
        (SELECT id FROM item_images WHERE item_id = i.id LIMIT 1) as first_image_id
        FROM items i JOIN users u ON i.user_id = u.id WHERE i.user_id = ? ORDER BY i.date_reported DESC");
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();

// Fetch incoming requests for user's reports
$stmt_req = $pdo->prepare("SELECT r.*, i.item_name, u.username as requester_name 
                           FROM requests r 
                           JOIN items i ON r.item_id = i.id 
                           JOIN users u ON r.requester_id = u.id 
                           WHERE i.user_id = ? 
                           ORDER BY r.created_at DESC");
$stmt_req->execute([$_SESSION['user_id']]);
$incoming_requests = $stmt_req->fetchAll();
?>

<main class="main-content">
    <div class="container">
        <div class="page-header fade-in">
            <div class="page-title">
                <h1><i class="fa-solid fa-gauge" style="color: var(--primary); margin-right: 0.5rem;"></i> Dashboard</h1>
                <p style="color: var(--text-muted);">Manage your reports and incoming claim requests.</p>
            </div>
            <a href="add.php" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Report Item
            </a>
        </div>

        <!-- Incoming Requests Section -->
        <div class="card fade-in" style="margin-bottom: 2rem; border-left: 5px solid var(--primary);">
            <div class="card-header">
                <h2 style="font-size: 1.25rem;"><i class="fa-solid fa-inbox" style="color: var(--primary); margin-right: 0.5rem;"></i> Incoming Claim Requests</h2>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Requester</th>
                            <th>Date Received</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($incoming_requests)): ?>
                            <tr><td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">No incoming requests yet.</td></tr>
                        <?php else: foreach ($incoming_requests as $req): ?>
                            <tr>
                                <td data-label="Item Name"><strong><?php echo h($req['item_name']); ?></strong></td>
                                <td data-label="Requester"><?php echo h($req['requester_name']); ?></td>
                                <td data-label="Date Received"><?php echo date('M d, Y', strtotime($req['created_at'])); ?></td>
                                <td data-label="Status">
                                    <span class="status-badge" style="background: <?php 
                                        echo $req['status'] == 'pending' ? 'var(--warning-light)' : 
                                            ($req['status'] == 'accepted' ? 'var(--success-light)' : 'var(--error-light)'); 
                                    ?>; color: <?php 
                                        echo $req['status'] == 'pending' ? 'var(--warning)' : 
                                            ($req['status'] == 'accepted' ? 'var(--success)' : 'var(--error)'); 
                                    ?>;">
                                        <?php echo ucfirst($req['status']); ?>
                                    </span>
                                </td>
                                <td data-label="Action">
                                    <a href="view_request.php?id=<?php echo $req['id']; ?>" class="btn btn-secondary btn-sm">
                                        <i class="fa-solid fa-eye"></i> View Proof
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card fade-in">
            <div class="card-header">
                <h2 style="font-size: 1.25rem;"><i class="fa-solid fa-list" style="color: var(--primary); margin-right: 0.5rem;"></i> My Reports</h2>
            </div>
            <div class="table-responsive">
                <table class="table" id="myItemsTable">
                    <thead>
                        <tr>
                            <th><i class="fa-solid fa-image" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Image</th>
                            <th><i class="fa-solid fa-calendar-days" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Date</th>
                            <th><i class="fa-solid fa-tag" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Item Name</th>
                            <th><i class="fa-solid fa-location-dot" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Location</th>
                            <th><i class="fa-solid fa-signal" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Status</th>
                            <th><i class="fa-solid fa-wrench" style="margin-right: 0.5rem; font-size: 0.8rem; opacity: 0.7;"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                            <tr><td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-muted);">You haven't reported any items yet. <a href="add.php">Report one now.</a></td></tr>
                        <?php else: 
                            foreach ($items as $item): 
                        ?>
                        <tr>
                            <td data-label="Image">
                                <?php if ($item['first_image_id']): ?>
                                    <div style="width: 50px; height: 50px; border-radius: 6px; overflow: hidden; border: 1px solid var(--border);">
                                        <img src="image_view.php?id=<?php echo $item['first_image_id']; ?>" alt="Item" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                <?php else: ?>
                                    <div style="width: 50px; height: 50px; border-radius: 6px; background: var(--bg-page); display: flex; align-items: center; justify-content: center; border: 1px solid var(--border); color: var(--text-muted);">
                                        <i class="fa-solid fa-camera" style="opacity: 0.1; font-size: 0.8rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td data-label="Date"><?php echo date('M d, Y', strtotime($item['date_reported'])); ?></td>
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
                            <td data-label="Actions">
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="item_details.php?id=<?php echo $item['id']; ?>" class="btn btn-secondary btn-sm" title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn btn-secondary btn-sm" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this report?');" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; 
                        endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
