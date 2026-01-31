<?php
// public/setup_db.php
require_once '../config/db.php';

try {
    $sql = file_get_contents('../config/schema.sql');
    $pdo->exec($sql);
    echo "Database and tables created successfully. <a href='index.php'>Go to Home</a>";
} catch (PDOException $e) {
    echo "Error creating database: " . $e->getMessage();
}
?>
