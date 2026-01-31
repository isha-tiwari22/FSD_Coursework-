<?php
// config/db.php

$host = 'localhost';
$dbname = 'lost_found_db';
$username = 'root'; // Default, user might need to change
$password = 'RootPassword123!'; // Default

try {
    // First connect without database selected to check/create it
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    
    // Switch to the database
    $pdo->exec("USE `$dbname`");
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Check if tables exist
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() == 0) {
        // Tables don't exist, create them
        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                item_name VARCHAR(100) NOT NULL,
                description TEXT,
                location VARCHAR(100),
                status ENUM('lost', 'found', 'claimed') DEFAULT 'lost',
                date_reported DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            );

            INSERT IGNORE INTO users (username, email, password_hash) VALUES 
            ('testuser', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
        ";
        $pdo->exec($sql);
    }
    
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>
