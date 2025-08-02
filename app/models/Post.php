<?php
require_once __DIR__ . '/../../config/database.php';

class Post {
    private $db;
    private $connection;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->connection = $this->db->connect();
    }

    // Get all blog posts
    public function getAllPosts() {
        try {
            $stmt = $this->connection->prepare("
                SELECT id, title, content, created_at 
                FROM posts 
                ORDER BY created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            error_log("Error fetching posts: " . $e->getMessage());
            return [];
        }
    }

    // Get a single post by ID
    public function getPost($id) {
        try {
            $stmt = $this->connection->prepare("
                SELECT id, title, content, created_at 
                FROM posts 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            error_log("Error fetching post: " . $e->getMessage());
            return null;
        }
    }

    // Create a new post
    public function createPost($title, $content) {
        try {
            $stmt = $this->connection->prepare("
                INSERT INTO posts (title, content, created_at) 
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$title, $content]);
            return $this->connection->lastInsertId();
            
        } catch(PDOException $e) {
            error_log("Error creating post: " . $e->getMessage());
            return false;
        }
    }

    // Update an existing post
    public function updatePost($id, $title, $content) {
        try {
            $stmt = $this->connection->prepare("
                UPDATE posts 
                SET title = ?, content = ? 
                WHERE id = ?
            ");
            return $stmt->execute([$title, $content, $id]);
            
        } catch(PDOException $e) {
            error_log("Error updating post: " . $e->getMessage());
            return false;
        }
    }

    // Delete a post
    public function deletePost($id) {
        try {
            $stmt = $this->connection->prepare("DELETE FROM posts WHERE id = ?");
            return $stmt->execute([$id]);
            
        } catch(PDOException $e) {
            error_log("Error deleting post: " . $e->getMessage());
            return false;
        }
    }

    // Search posts by keyword
    public function searchPosts($keyword) {
        try {
            $stmt = $this->connection->prepare("
                SELECT id, title, content, created_at 
                FROM posts 
                WHERE title LIKE ? OR content LIKE ?
                ORDER BY created_at DESC
            ");
            $searchTerm = "%$keyword%";
            $stmt->execute([$searchTerm, $searchTerm]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            error_log("Error searching posts: " . $e->getMessage());
            return [];
        }
    }
}
?>