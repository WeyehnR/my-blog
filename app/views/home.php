<?php
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../helpers/UrlHelper.php';

// Render header with create button for logged in users
renderHeader('My Blog Posts', isset($_SESSION['user_id']), true);
?>

<div class="container">
    
    <div class="posts">
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <div class="post-card" id="post-<?php echo $post['id']; ?>">
                    <div class="vote-section">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button class="vote-btn upvote" 
                                    data-post-id="<?php echo $post['id']; ?>" 
                                    data-vote-type="up"
                                    title="Upvote"
                                    <?php echo ($post['userVote'] ?? '') === 'up' ? 'style="color:#ff4500;"' : ''; ?>>▲</button>
                        <?php else: ?>
                            <span class="vote-btn disabled">▲</span>
                        <?php endif; ?>
                        
                        <span class="vote-score"><?php echo $post['score'] ?? 0; ?></span>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button class="vote-btn downvote" 
                                    data-post-id="<?php echo $post['id']; ?>" 
                                    data-vote-type="down"
                                    title="Downvote"
                                    <?php echo ($post['userVote'] ?? '') === 'down' ? 'style="color:#7193ff;"' : ''; ?>>▼</button>
                        <?php else: ?>
                            <span class="vote-btn disabled">▼</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="post-content">
                        <h2><a href="<?php echo UrlHelper::url('post/' . $post['id']); ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                        <p class="post-meta">Posted on <?php echo date('F j, Y', strtotime($post['created_at'])); ?> by <?php echo htmlspecialchars($post['username']); ?></p>
                        <p><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 200))); ?><?php echo strlen($post['content']) > 200 ? '...' : ''; ?></p>
                        <?php if (strlen($post['content']) > 200): ?>
                            <a href="<?php echo UrlHelper::url('post/' . $post['id']); ?>" class="read-more">Read more</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No posts found. <?php if (!isset($_SESSION['user_id'])): ?><a href="<?php echo UrlHelper::url('login'); ?>">Login</a> to create your first post!<?php endif; ?></p>
        <?php endif; ?>
    </div>
</div>

<?php renderFooter(); ?>