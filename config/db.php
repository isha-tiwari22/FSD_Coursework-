<?php
// config/db.php

// Production environment detection
define('IS_PRODUCTION', false); // Set to true in production

if (IS_PRODUCTION) {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Database Configuration (Hardcoded for convenience)
$host = 'localhost';
$dbname = 'np03cs4a230203';
$username = 'np03cs4a230203';
$password = 'sG9GJ1KoXH';

try {
    // Connect to MySQL
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Ensure database exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname` ");

    // --- AUTO-REPAIR / INITIALIZATION LOGIC ---
    
    // 1. Ensure tables exist
    $tables = [
        "users" => "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            first_name VARCHAR(50),
            last_name VARCHAR(50),
            phone VARCHAR(20),
            bio TEXT,
            profile_image TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "items" => "CREATE TABLE IF NOT EXISTS items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            item_name VARCHAR(100) NOT NULL,
            description TEXT,
            location VARCHAR(100),
            status ENUM('lost', 'found', 'claimed') DEFAULT 'lost',
            date_reported DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )",
        "item_images" => "CREATE TABLE IF NOT EXISTS item_images (
            id INT AUTO_INCREMENT PRIMARY KEY,
            item_id INT NOT NULL,
            image_data LONGBLOB NOT NULL,
            image_type VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
        )",
        "requests" => "CREATE TABLE IF NOT EXISTS requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            item_id INT NOT NULL,
            requester_id INT NOT NULL,
            message TEXT NOT NULL,
            contact_info VARCHAR(255),
            status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
            FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE
        )",
        "request_images" => "CREATE TABLE IF NOT EXISTS request_images (
            id INT AUTO_INCREMENT PRIMARY KEY,
            request_id INT NOT NULL,
            image_data LONGBLOB NOT NULL,
            image_type VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE
        )"
    ];

    foreach ($tables as $name => $sql) {
        $pdo->exec($sql);
    }

    // 2. CHECK FOR MISSING COLUMNS (The "Unidentified Column" Fix)
    // Automatically adds columns if they are missing from an existing table.
    $check_columns = [
        'users' => [
            'first_name' => "VARCHAR(50)",
            'last_name' => "VARCHAR(50)",
            'phone' => "VARCHAR(20)",
            'bio' => "TEXT",
            'profile_image' => "TEXT"
        ]
    ];

    foreach ($check_columns as $table => $cols) {
        $existing_cols = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($cols as $col_name => $definition) {
            if (!in_array($col_name, $existing_cols)) {
                $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$col_name` $definition");
            }
        }
    }

    // Insert test user if not present
    $pdo->exec("INSERT IGNORE INTO users (username, email, password_hash, first_name, last_name) VALUES 
        ('testuser', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test', 'User')");

} catch (PDOException $e) {
    if (!IS_PRODUCTION) {
        die("Database Error: " . $e->getMessage());
    } else {
        die("Database Connection Failed.");
    }
}
?>
