<?php
require_once __DIR__ . '/../helpers/FormHelper.php';
require_once __DIR__ . '/../helpers/UrlHelper.php';
require_once __DIR__ . '/partials/header.php';

renderHeader(htmlspecialchars($post['title']), false, true);
?>

<div class="container">
    <a href="<?php echo UrlHelper::url(''); ?>" class="back-link">← back to all posts</a>
    
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
            
            <span class="vote-score"><?php echo $post['vote_score'] ?? 0; ?></span>
            
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
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            <p class="post-meta">Posted on <?php echo date('F j, Y', strtotime($post['created_at'])); ?> by <?php echo htmlspecialchars($post['username']); ?></p>
            
            <div class="post-content-full">
                <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            </div>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="comments">
        <h2>Comments</h2>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php echo FormHelper::displayErrors($errors ?? []); ?>
            <form method="POST" action="<?php echo UrlHelper::url('post/' . $post['id'] . '/comment'); ?>" class="comment-form">
                <div class="form-group">
                    <textarea name="comment" placeholder="Add your comment..." rows="4" required><?php echo FormHelper::oldValue('comment'); ?></textarea>
                </div>
                <button type="submit" class="btn">Post Comment</button>
            </form>
        <?php else: ?>
            <p><a href="<?php echo UrlHelper::url('login'); ?>">Login</a> to post a comment.</p>
        <?php endif; ?>
        
        <div class="comments-list">
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <div class="comment-header">
                            <strong><?php echo htmlspecialchars($comment['author_name']); ?></strong>
                            <span class="comment-date">on <?php echo date('F j, Y \a\t g:i A', strtotime($comment['created_at'])); ?></span>
                        </div>
                        <div class="comment-content">
                            <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No comments yet. Be the first to comment!</p>
            <?php endif; ?>
        </div>
    </div>
    </div>
</div>

<?php renderFooter(); ?>