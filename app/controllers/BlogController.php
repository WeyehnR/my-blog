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

    // Show create post form
    public function create() {
        // Only allow logged-in users
        if (!isset($_SESSION['user_id'])) {
            header('Location: /my-blog/public/?url=login');
            exit;
        }
        $errors = [];
        require_once __DIR__ . '/../views/create_post.php';
    }

    // Handle create post submission
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $user_id = $_SESSION['user_id'] ?? null;
            $errors = [];

            if (!$title) $errors[] = 'Title is required';
            if (!$content) $errors[] = 'Content is required';

            if (empty($errors) && $user_id) {
                $postId = $this->postModel->create($user_id, $title, $content);
                if ($postId) {
                    header('Location: /my-blog/public/?url=post/' . $postId);
                    exit;
                } else {
                    $errors[] = 'Failed to create post.';
                }
            }
            // Show form with errors
            require __DIR__ . '/../views/create_post.php';
        } else {
            header('Location: /my-blog/public/?url=create');
            exit;
        }
    }

    public function addComment($postId) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /my-blog/public/?url=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $author_name = $_SESSION['username'];
            $comment = trim($_POST['comment'] ?? '');
            $errors = [];

            if (empty($comment)) {
                $errors[] = 'Comment cannot be empty.';
            }

            if (empty($errors)) {
                $this->postModel->addComment($postId, $author_name, $comment);
                header('Location: /my-blog/public/?url=post/' . $postId);
                exit;
            }

            // Reload post and comments for redisplay with errors
            $post = $this->postModel->getPostById($postId);
            $comments = $this->postModel->getPostComments($postId);
            require __DIR__ . '/../views/post.php';
        }
    }
}
?>