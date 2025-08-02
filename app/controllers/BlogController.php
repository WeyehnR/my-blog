<?php
class BlogController {
    private $postModel;

    public function __construct() {
        $this->postModel = new Post();
    }

    // Show homepage with all posts
    public function index() {
        $posts = $this->postModel->getAllPosts();
        require __DIR__ . '/../views/home.php';
    }

    // Show single post
    public function show($id) {
        $post = $this->postModel->getPost($id);
        
        if ($post) {
            require __DIR__ . '/../views/post.php';
        } else {
            http_response_code(404); //Hell yea famous 404! :)
            echo "<h1>Post Not Found</h1>";
            echo "<p>The post you're looking for doesn't exist.</p>";
            echo "<a href='?url=home'>← Back to Home</a>";
        }
    }
}
?>