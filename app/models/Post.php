<?php
// app/models/Post.php

require_once __DIR__ . '/../../config/database.php';

class Post {
    private $db;
    private $table = 'posts';
    
    // Post properties
    public $id;
    public $user_id;
    public $title;
    public $content;
    public $created_at;
    
    // Constructor uses singleton database instance
    public function __construct() {
        // Get singleton instance and connect
        $this->db = Database::getInstance()->connect();
    }
    
    // Get all posts with author info
    public function getAllPosts() {
        $query = "SELECT p.*, u.username 
                  FROM {$this->table} p 
                  LEFT JOIN users u ON p.user_id = u.id 
                  ORDER BY p.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();  // Uses FETCH_ASSOC from your DB config
    }
    
    // Get single post
    public function getPostById($id) {
        $query = "SELECT p.*, u.username 
                  FROM {$this->table} p 
                  LEFT JOIN users u ON p.user_id = u.id 
                  WHERE p.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Create new post
    public function create($user_id, $title, $content) {
        $query = "INSERT INTO {$this->table} (user_id, title, content) 
                  VALUES (:user_id, :title, :content)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    // Update post
    public function update($id, $title, $content) {
        $query = "UPDATE {$this->table} 
                  SET title = :title, content = :content 
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        
        return $stmt->execute();
    }
    
    // Delete post
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // Get all posts by a specific user
    public function getPostsByUser($user_id) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Get comments for a post
    public function getPostComments($post_id) {
        $query = "SELECT * FROM comments 
                  WHERE post_id = :post_id 
                  ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Add comment to post
    public function addComment($post_id, $author_name, $comment) {
        $query = "INSERT INTO comments (post_id, author_name, comment) 
                  VALUES (:post_id, :author_name, :comment)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindParam(':author_name', $author_name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        
        return $stmt->execute();
    }
}