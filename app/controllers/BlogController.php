<?php
// app/controllers/BlogController.php
require_once __DIR__ . '/../models/Post.php';

class BlogController {
    private $postModel;
    
    public function __construct() {
        // Model handles its own DB connection via singleton
        $this->postModel = new Post();
    }
    
    // Display all posts
    public function index() {
        try {
            $posts = $this->postModel->getAllPosts();
            require_once __DIR__ . '/../views/home.php';
        } catch (Exception $e) {
            // Handle error
            echo "Error loading posts: " . $e->getMessage();
        }
    }
    
    // Show single post
    public function show($id) {
        try {
            $post = $this->postModel->getPostById($id);
            $comments = $this->postModel->getPostComments($id);
            
            if (!$post) {
                header('Location: /');
                exit;
            }
            
            require_once __DIR__ . '/../views/post.php';
        } catch (Exception $e) {
            echo "Error loading post: " . $e->getMessage();
        }
    }
    
    // Create new post (from form submission)
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Simple validation
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $user_id = $_SESSION['user_id'] ?? null;
            
            if ($title && $content && $user_id) {
                $postId = $this->postModel->create($user_id, $title, $content);
                
                if ($postId) {
                    header('Location: /post/' . $postId);
                    exit;
                }
            }
            
            // If we get here, something went wrong
            $_SESSION['error'] = 'Failed to create post';
            header('Location: /');
        }
    }
}
?>