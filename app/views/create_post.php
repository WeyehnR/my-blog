<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post - My MVC Blog</title>
    <link rel="stylesheet" href="/my-blog/public/css/dark-theme.css">
</head>
<body>
    <header>
        <div class="header-userbar">
            <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="/my-blog/public/?url=logout">Logout</a>
        </div>
        <h1>Create a New Post</h1>
        <p>Share your thoughts with the community</p>
    </header>
    
    <main>
        <a href="/my-blog/public/?url=home" class="back-link">← Back to Home</a>
        
        <div class="container">
            <?php if (!empty($errors)): ?>
                <div class="error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="/my-blog/public/?url=store">
                <div class="form-group">
                    <label for="title">Post Title</label>
                    <input type="text" name="title" id="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required placeholder="Enter an engaging title...">
                </div>
                
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea name="content" id="content" rows="12" required placeholder="Share your story, thoughts, or insights..."><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
                </div>
                
                <button type="submit" class="btn">Create Post</button>
            </form>
        </div>
    </main>
</body>
</html>