<?php
// migrate_images.php
require_once 'config/db.php';

try {
    echo "Running migrations...\n";

    // 1. Update users table
    $cols = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('first_name', $cols)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN first_name VARCHAR(50)");
        echo "Added first_name to users.\n";
    }
    if (!in_array('last_name', $cols)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN last_name VARCHAR(50)");
        echo "Added last_name to users.\n";
    }
    if (!in_array('profile_image', $cols)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN profile_image TEXT");
        echo "Added profile_image to users.\n";
    }

    // 2. Create item_images table
    $pdo->exec("CREATE TABLE IF NOT EXISTS item_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        item_id INT NOT NULL,
        image_data LONGBLOB NOT NULL,
        image_type VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
    )");
    echo "Ensured item_images table exists.\n";

    echo "Migration complete.\n";
} catch (Exception $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
?>
