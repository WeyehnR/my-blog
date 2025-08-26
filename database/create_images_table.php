<?php
// Run this once to create the images table
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->connect();
    
    $sql = "CREATE TABLE IF NOT EXISTS images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        uuid VARCHAR(36) UNIQUE NOT NULL,
        original_filename VARCHAR(255) NOT NULL,
        stored_filename VARCHAR(255) NOT NULL,
        file_path VARCHAR(500) NOT NULL,
        file_size INT NOT NULL,
        mime_type VARCHAR(50) NOT NULL,
        user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_uuid (uuid),
        INDEX idx_user_id (user_id)
    )";
    
    $db->exec($sql);
    echo "Images table created successfully!\n";
    
} catch (PDOException $e) {
    echo "Error creating images table: " . $e->getMessage() . "\n";
}
