<?php
// app/controllers/BlogController.php
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Image.php';

class BlogController {
    private $postModel;
    private $imageModel;
    
    public function __construct() {
        // Model handles its own DB connection via singleton
        $this->postModel = new Post();
        $this->imageModel = new Image();
    }
    
    // Display all posts
    public function index() {
         try {
            $userId = $_SESSION['user_id'] ?? null;
            $posts = $this->postModel->getAllPostsWithUserVotes($userId);
            
            // Process the posts data if needed
            foreach ($posts as $key => $post) {
                $posts[$key]['score'] = $post['vote_score'] ?? 0;
                // userVote is now already included from the optimized query
                $posts[$key]['userVote'] = $post['user_vote'] ?? null;
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
            // Check if user is trying to vote on their own post
            $post = $this->postModel->getPostById($post_id);
            if (!$post) {
                echo json_encode(['success' => false, 'error' => 'Post not found']);
                return;
            }
            
            if ($post['user_id'] == $user_id) {
                echo json_encode(['success' => false, 'error' => 'You cannot vote on your own post']);
                return;
            }
            
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

    // Image upload endpoint for EasyMDE
    public function uploadImage() {
        header('Content-Type: application/json');
        
        // Debug logging
        error_log("Upload request received - Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("Files available: " . print_r(array_keys($_FILES), true));
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'noFileGiven']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'noFileGiven']);
            return;
        }
        
        // Check for different possible field names that EasyMDE might use
        $file = null;
        if (isset($_FILES['image'])) {
            $file = $_FILES['image'];
        } elseif (isset($_FILES['file'])) {
            $file = $_FILES['file'];
        } elseif (isset($_FILES[0])) {
            $file = $_FILES[0];
        } else {
            // No file found - debug what's actually being sent
            error_log("Upload debug - Available files: " . print_r(array_keys($_FILES), true));
            echo json_encode(['error' => 'noFileGiven']);
            return;
        }
        
        // Validation
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode(['error' => 'typeNotAllowed']);
            return;
        }
        
        if ($file['size'] > $maxSize) {
            echo json_encode(['error' => 'fileTooLarge']);
            return;
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['error' => 'importError']);
            return;
        }
        
        try {
            // Create uploads directory OUTSIDE public folder for security
            $uploadDir = __DIR__ . '/../../uploads/images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generate unique filename (internal use only)
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $storedFilename = uniqid('img_' . time() . '_') . '.' . $extension;
            $filepath = $uploadDir . $storedFilename;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Save to database and get UUID
                $uuid = $this->imageModel->create(
                    $_SESSION['user_id'],
                    $file['name'], // original filename
                    $storedFilename, // stored filename
                    $filepath, // full file path
                    $file['size'], // file size
                    $file['type'] // mime type
                );
                
                if ($uuid) {
                    // Return full image URL instead of just UUID for EasyMDE compatibility
                    $imageUrl = '/my-blog/public/?url=image/' . $uuid;
                    $response = ['data' => ['filePath' => $imageUrl]];
                    error_log("Upload successful, responding with: " . json_encode($response));
                    echo json_encode($response);
                } else {
                    error_log("Database save failed");
                    echo json_encode(['error' => 'importError']);
                }
            } else {
                error_log("File move failed");
                echo json_encode(['error' => 'importError']);
            }
            
        } catch (Exception $e) {
            error_log("Image upload error: " . $e->getMessage());
            echo json_encode(['error' => 'importError']);
        }
    }

    // Secure image serving endpoint
    public function serveImage($uuid) {
        // Basic security: only serve images if user is logged in
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            echo 'Access denied';
            return;
        }

        // Validate UUID format to prevent injection attacks
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $uuid)) {
            http_response_code(400);
            echo 'Invalid image ID';
            return;
        }
        
        // Get image metadata from database
        $image = $this->imageModel->getByUUID($uuid);
        
        if (!$image) {
            http_response_code(404);
            echo 'Image not found';
            return;
        }
        
        // Additional access control: only owner can view (optional)
        // if ($image['user_id'] != $_SESSION['user_id']) {
        //     http_response_code(403);
        //     echo 'Access denied';
        //     return;
        // }
        
        // Check if file exists
        if (!file_exists($image['file_path'])) {
            http_response_code(404);
            echo 'Image file not found';
            return;
        }
        
        // Set appropriate headers
        header('Content-Type: ' . $image['mime_type']);
        header('Content-Length: ' . $image['file_size']);
        header('Cache-Control: public, max-age=3600'); // Cache for 1 hour
        header('X-Content-Type-Options: nosniff'); // Security header
        
        // Serve the image
        readfile($image['file_path']);
    }
}
?>