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

    // Add or update a vote
    public function vote($post_id, $user_id, $type) {
        // Check if user already voted
        $stmt = $this->db->prepare("SELECT * FROM votes WHERE post_id = :post_id AND user_id = :user_id");
        $stmt->execute(['post_id' => $post_id, 'user_id' => $user_id]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $this->db->prepare("UPDATE votes SET vote_type = :type WHERE post_id = :post_id AND user_id = :user_id");
        } else {
            $stmt = $this->db->prepare("INSERT INTO votes (post_id, user_id, vote_type) VALUES (:post_id, :user_id, :type)");
        }
        return $stmt->execute(['post_id' => $post_id, 'user_id' => $user_id, 'type' => $type]);
    }

    // Get vote counts for a post
    public function getVoteCount($post_id) {
        $stmt = $this->db->prepare("SELECT 
            SUM(vote_type = 'up') AS upvotes, 
            SUM(vote_type = 'down') AS downvotes 
            FROM votes WHERE post_id = :post_id");
        $stmt->execute(['post_id' => $post_id]);
        return $stmt->fetch();
    }

    // Get current user's vote for a post
    public function getUserVote($post_id, $user_id) {
        $stmt = $this->db->prepare("SELECT vote_type FROM votes WHERE post_id = :post_id AND user_id = :user_id");
        $stmt->execute(['post_id' => $post_id, 'user_id' => $user_id]);
        return $stmt->fetchColumn();
    }
}