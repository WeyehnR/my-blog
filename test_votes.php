<?php
// Test file to debug voting issues
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/models/Post.php';

try {
    $postModel = new Post();
    
    // Test if we can get vote count for post ID 1
    $voteCount = $postModel->getVoteCount(1);
    echo "Vote count for post 1: ";
    var_dump($voteCount);
    
    // Test if methods exist
    if (method_exists($postModel, 'vote')) {
        echo "<br>✓ vote() method exists";
    } else {
        echo "<br>✗ vote() method missing";
    }
    
    if (method_exists($postModel, 'getVoteCount')) {
        echo "<br>✓ getVoteCount() method exists";
    } else {
        echo "<br>✗ getVoteCount() method missing";
    }
    
    if (method_exists($postModel, 'getUserVote')) {
        echo "<br>✓ getUserVote() method exists";
    } else {
        echo "<br>✗ getUserVote() method missing";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    echo "<br>Trace: " . $e->getTraceAsString();
}
?>
