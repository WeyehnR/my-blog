<?php
// app/models/Image.php

require_once __DIR__ . '/../../config/database.php';

class Image {
    private $db;
    private $table = 'images';
    
    public function __construct() {
        $this->db = Database::getInstance()->connect();
    }
    
    // Save image metadata to database
    public function create($userId, $originalFilename, $storedFilename, $filePath, $fileSize, $mimeType) {
        $uuid = $this->generateUUID();
        
        $query = "INSERT INTO {$this->table} (uuid, original_filename, stored_filename, file_path, file_size, mime_type, user_id) 
                  VALUES (:uuid, :original_filename, :stored_filename, :file_path, :file_size, :mime_type, :user_id)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
        $stmt->bindParam(':original_filename', $originalFilename, PDO::PARAM_STR);
        $stmt->bindParam(':stored_filename', $storedFilename, PDO::PARAM_STR);
        $stmt->bindParam(':file_path', $filePath, PDO::PARAM_STR);
        $stmt->bindParam(':file_size', $fileSize, PDO::PARAM_INT);
        $stmt->bindParam(':mime_type', $mimeType, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $uuid; // Return UUID instead of database ID
        }
        
        return false;
    }
    
    // Get image by UUID (secure lookup)
    public function getByUUID($uuid) {
        $query = "SELECT * FROM {$this->table} WHERE uuid = :uuid";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Check if user owns the image
    public function isOwner($uuid, $userId) {
        $query = "SELECT user_id FROM {$this->table} WHERE uuid = :uuid";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
        $stmt->execute();
        
        $image = $stmt->fetch();
        return $image && $image['user_id'] == $userId;
    }
    
    // Generate secure UUID
    private function generateUUID() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
    
    // Delete image and file
    public function delete($uuid, $userId) {
        $image = $this->getByUUID($uuid);
        
        if (!$image || $image['user_id'] != $userId) {
            return false;
        }
        
        // Delete file from filesystem
        if (file_exists($image['file_path'])) {
            unlink($image['file_path']);
        }
        
        // Delete from database
        $query = "DELETE FROM {$this->table} WHERE uuid = :uuid";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
        
        return $stmt->execute();
    }
}
