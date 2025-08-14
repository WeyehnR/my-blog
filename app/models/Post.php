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
    
    // Get all posts with author info, ordered by vote score
    public function getAllPosts() {
        $query = "SELECT p.*, u.username,
                  COALESCE(
                    (SELECT SUM(CASE WHEN vote_type = 'up' THEN 1 ELSE -1 END) 
                     FROM votes v WHERE v.post_id = p.id), 0
                  ) as vote_score
                  FROM {$this->table} p 
                  LEFT JOIN users u ON p.user_id = u.id 
                  ORDER BY vote_score DESC, p.created_at DESC";
        
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
        $stmt = $this->db->prepare("SELECT vote_type FROM votes WHERE post_id = :post_id AND user_id = :user_id");
        $stmt->execute(['post_id' => $post_id, 'user_id' => $user_id]);
        $existing = $stmt->fetchColumn();

        if ($existing) {
            if ($existing === $type) {
                // User clicked the same vote type - remove the vote (toggle off)
                $stmt = $this->db->prepare("DELETE FROM votes WHERE post_id = :post_id AND user_id = :user_id");
                return $stmt->execute(['post_id' => $post_id, 'user_id' => $user_id]);
            } else {
                // User clicked different vote type - this is where we check for your edge case
                // Check if this user's existing vote makes up the entire score (single-user post)
                $voteCount = $this->getVoteCount($post_id);
                $currentScore = ($voteCount['upvotes'] ?? 0) - ($voteCount['downvotes'] ?? 0);
                $totalVotes = ($voteCount['upvotes'] ?? 0) + ($voteCount['downvotes'] ?? 0);
                
                if ($totalVotes == 1 && (($currentScore == 1 && $existing == 'up' && $type == 'down') || 
                                        ($currentScore == -1 && $existing == 'down' && $type == 'up'))) {
                    // Special edge case: Only one vote exists (by this user), and they're switching to opposite
                    // This should jump directly from 1 to -1 or -1 to 1
                    $stmt = $this->db->prepare("UPDATE votes SET vote_type = :type WHERE post_id = :post_id AND user_id = :user_id");
                    return $stmt->execute(['post_id' => $post_id, 'user_id' => $user_id, 'type' => $type]);
                } else if ($totalVotes > 1) {
                    // Multiple users have voted - normal behavior (no double voting prevention)
                    $stmt = $this->db->prepare("UPDATE votes SET vote_type = :type WHERE post_id = :post_id AND user_id = :user_id");
                    return $stmt->execute(['post_id' => $post_id, 'user_id' => $user_id, 'type' => $type]);
                } else {
                    // Regular case: update the vote
                    $stmt = $this->db->prepare("UPDATE votes SET vote_type = :type WHERE post_id = :post_id AND user_id = :user_id");
                    return $stmt->execute(['post_id' => $post_id, 'user_id' => $user_id, 'type' => $type]);
                }
            }
        } else {
            // No existing vote - insert new vote
            $stmt = $this->db->prepare("INSERT INTO votes (post_id, user_id, vote_type) VALUES (:post_id, :user_id, :type)");
            return $stmt->execute(['post_id' => $post_id, 'user_id' => $user_id, 'type' => $type]);
        }
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

    public function getAllPostsWithUserVotes($userId = null) {
        $query = "SELECT p.*, u.username,
                COALESCE(
                    (SELECT SUM(CASE WHEN vote_type = 'up' THEN 1 ELSE -1 END) 
                    FROM votes v WHERE v.post_id = p.id), 0
                ) as vote_score";
        
        // Add user vote if logged in
        if ($userId) {
            $query .= ", uv.vote_type as user_vote";
        }
        
        $query .= " FROM {$this->table} p 
                LEFT JOIN users u ON p.user_id = u.id";
        
        if ($userId) {
            $query .= " LEFT JOIN votes uv ON uv.post_id = p.id AND uv.user_id = :user_id";
        }
        
        $query .= " ORDER BY vote_score DESC, p.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        
        if ($userId) {
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
}