<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My MVC Blog</title>
    <link rel="stylesheet" href="/my-blog/public/css/home_page.css">
    <script src="/my-blog/public/js/voting.js" defer></script>
</head>
<body>
    <div class="reddit-inspired-container">
        <header>
            <div class="header-userbar">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="/my-blog/public/?url=login" style="margin-right: 10px;">Login</a>
                    <a href="/my-blog/public/?url=register">Register</a>
                <?php else: ?>
                    <span><?= htmlspecialchars($_SESSION['username']) ?></span>
                    <a href="/my-blog/public/?url=logout" style="margin-left: 10px;">Logout</a>
                    <a href="/my-blog/public/?url=create" class="create-post-btn">Create Post</a>
                <?php endif; ?>
            </div>
            <h1>Welcome to My MVC Blog</h1>
            <p>Built with PHP MVC architecture for Big Voodoo Interactive</p>
        </header>

        <main>
            <?php if (empty($posts)): ?>
                <p>No posts found.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <article class="post" id="post-<?= $post['id'] ?>">
                        <div class="post-votes">
                            <a href="#" class="vote-up" data-post-id="<?= $post['id'] ?>"
                               <?= ($post['userVote'] ?? '') === 'up' ? 'style="color:#0079d3;"' : '' ?>>
                                <svg width="20" height="20" viewBox="0 0 32 32" fill="currentColor">
                                    <polygon points="16,8 24,24 8,24"/>
                                </svg>
                            </a>
                            <div class="vote-score"><?= $post['score'] ?? 0 ?></div>
                            <a href="#" class="vote-down" data-post-id="<?= $post['id'] ?>"
                               <?= ($post['userVote'] ?? '') === 'down' ? 'style="color:#0079d3;"' : '' ?>>
                                <svg width="20" height="20" viewBox="0 0 32 32" fill="currentColor" style="transform:rotate(180deg)">
                                    <polygon points="16,8 24,24 8,24"/>
                                </svg>
                            </a>
                        </div>
                        <div class="post-content">
                            <h2><a href="?url=post/<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                            <div class="post-meta">
                                Posted on <?= date('F j, Y', strtotime($post['created_at'])) ?>
                                <?php if (!empty($post['username'])): ?>
                                    by <span class="author"><?= htmlspecialchars($post['username']) ?></span>
                                <?php endif; ?>
                            </div>
                            <p><?= htmlspecialchars(substr($post['content'], 0, 150)) ?>...</p>
                            <a class="read-more" href="?url=post/<?= $post['id'] ?>">Read more →</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>