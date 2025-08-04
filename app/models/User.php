<?php
// app/models/User.php

require_once __DIR__ . '/../../config/database.php';

class User {
    private $db;
    private $table = 'users';
    
    public function __construct() {
        $this->db = Database::getInstance()->connect();
    }
    
    // Find user by username or email
    public function findByUsername($username) {
        $query = "SELECT * FROM {$this->table} WHERE username = :username OR email = :username LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Find user by ID
    public function findById($id) {
        $query = "SELECT id, username, email, created_at FROM {$this->table} WHERE id = :id LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Check if username exists
    public function usernameExists($username) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE username = :username";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    // Check if email exists
    public function emailExists($email) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    // Create new user
    public function create($username, $email, $password) {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO {$this->table} (username, email, password_hash) 
                  VALUES (:username, :email, :password_hash)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password_hash', $password_hash);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    // Verify user login
    public function login($username, $password) {
        // Find user by username or email
        $user = $this->findByUsername($username);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Password is correct
            return $user;
        }
        
        return false;
    }
}