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
            $userId = $_SESSION['user_id'] ?? null;
            $posts = $this->postModel->getAllPostsWithUserVotes($userId);
            
            // Process the posts data if needed
            foreach ($posts as &$post) {
                $post['score'] = $post['vote_score'] ?? 0;
                // userVote is now already included from the optimized query
                $post['userVote'] = $post['user_vote'] ?? null;
            }
            
            require_once __DIR__ . '/../views/home.php';
            
        } catch (PDOException $e) {
            // Database-specific error handling
            error_log("Database error in BlogController::index(): " . $e->getMessage());
            
            if ($_ENV['APP_DEBUG'] ?? false) {
                echo "Database error loading posts: " . $e->getMessage();
            } else {
                echo "Sorry, we're having trouble loading the posts right now. Please try again later.";
            }
            
        } catch (Exception $e) {
            // General error handling
            error_log("General error in BlogController::index(): " . $e->getMessage());
            
            if ($_ENV['APP_DEBUG'] ?? false) {
                echo "Error loading posts: " . $e->getMessage();
            } else {
                echo "Something went wrong. Please refresh the page.";
            }
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
            
            // Get vote data for the view
            $voteCount = $this->postModel->getVoteCount($id);
            $post['vote_score'] = ($voteCount['upvotes'] ?? 0) - ($voteCount['downvotes'] ?? 0);
            $post['userVote'] = isset($_SESSION['user_id']) ? $this->postModel->getUserVote($id, $_SESSION['user_id']) : null;
            
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
            try {
                $title = trim($_POST['title'] ?? '');
                $content = trim($_POST['content'] ?? '');
                $user_id = $_SESSION['user_id'] ?? null;
                $errors = [];

                // Validation
                if (!$title) $errors[] = 'Title is required';
                if (!$content) $errors[] = 'Content is required';
                if (!$user_id) $errors[] = 'You must be logged in to create a post';

                if (empty($errors)) {
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
                
            } catch (PDOException $e) {
                // Database-specific error handling
                error_log("Database error in BlogController::store(): " . $e->getMessage());
                $errors[] = 'Database error occurred. Please try again.';
                require __DIR__ . '/../views/create_post.php';
                
            } catch (Exception $e) {
                // General error handling
                error_log("General error in BlogController::store(): " . $e->getMessage());
                $errors[] = 'An unexpected error occurred. Please try again.';
                require __DIR__ . '/../views/create_post.php';
            }
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
            try {
                $author_name = $_SESSION['username'];
                $comment = trim($_POST['comment'] ?? '');
                $errors = [];

                // Validation
                if (empty($comment)) {
                    $errors[] = 'Comment cannot be empty.';
                }
                
                if (empty($author_name)) {
                    $errors[] = 'Author name is required.';
                }

                if (empty($errors)) {
                    $result = $this->postModel->addComment($postId, $author_name, $comment);
                    
                    if ($result) {
                        header('Location: /my-blog/public/?url=post/' . $postId);
                        exit;
                    } else {
                        $errors[] = 'Failed to add comment.';
                    }
                } 

                // If there are errors, reload post and comments for redisplay
                $post = $this->postModel->getPostById($postId);
                $comments = $this->postModel->getPostComments($postId);
                
                // Get vote data for the view
                $voteCount = $this->postModel->getVoteCount($postId);
                $post['vote_score'] = ($voteCount['upvotes'] ?? 0) - ($voteCount['downvotes'] ?? 0);
                $post['userVote'] = isset($_SESSION['user_id']) ? $this->postModel->getUserVote($postId, $_SESSION['user_id']) : null;
                
                require __DIR__ . '/../views/post.php';
                
            } catch (PDOException $e) {
                // Database-specific error handling
                error_log("Database error in BlogController::addComment(): " . $e->getMessage());
                
                // Load post data for error display
                try {
                    $post = $this->postModel->getPostById($postId);
                    $comments = $this->postModel->getPostComments($postId);
                    $errors[] = 'Failed to add comment due to database error.';
                    require __DIR__ . '/../views/post.php';
                } catch (Exception $innerE) {
                    // If we can't even load the post, redirect to home
                    header('Location: /my-blog/public/');
                    exit;
                }
                
            } catch (Exception $e) {
                // General error handling
                error_log("General error in BlogController::addComment(): " . $e->getMessage());
                
                // Load post data for error display
                try {
                    $post = $this->postModel->getPostById($postId);
                    $comments = $this->postModel->getPostComments($postId);
                    $errors[] = 'An unexpected error occurred while adding comment.';
                    require __DIR__ . '/../views/post.php';
                } catch (Exception $innerE) {
                    // If we can't even load the post, redirect to home
                    header('Location: /my-blog/public/');
                    exit;
                }
            }
        }
    }

    public function vote($post_id, $type) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /my-blog/public/?url=login');
            exit;
        }
        
        $user_id = $_SESSION['user_id'];
        $type = ($type === 'up') ? 'up' : 'down';
        
        try {
            $this->postModel->vote($post_id, $user_id, $type);
            // Redirect back to home page
            header('Location: /my-blog/public/');
            exit;
        } catch (Exception $e) {
            error_log("Vote error: " . $e->getMessage());
            // Redirect back to home page even on error
            header('Location: /my-blog/public/');
            exit;
        }
    }

    // AJAX vote endpoint
    public function voteAjax() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'redirect' => '/my-blog/public/?url=login']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }
        
        $post_id = (int)($_POST['post_id'] ?? 0);
        $type = $_POST['vote_type'] ?? '';
        $user_id = $_SESSION['user_id'];
        
        if (!$post_id || !in_array($type, ['up', 'down'])) {
            echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
            return;
        }
        
        try {
            // Perform the vote
            $this->postModel->vote($post_id, $user_id, $type);
            
            // Get updated vote data
            $voteCount = $this->postModel->getVoteCount($post_id);
            $score = ($voteCount['upvotes'] ?? 0) - ($voteCount['downvotes'] ?? 0);
            $userVote = $this->postModel->getUserVote($post_id, $user_id);
            
            echo json_encode([
                'success' => true,
                'score' => $score,
                'userVote' => $userVote
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Vote failed']);
        }
    }
}
?>