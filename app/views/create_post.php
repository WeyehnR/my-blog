<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Post - My MVC Blog</title>
    <link rel="stylesheet" href="/my-blog/public/css/home_page.css"> <!-- temporary style for now-->
</head>
<body>
    <header>
        <h1>Create a New Post</h1>
        <a href="/my-blog/public/?url=home">← Back to Home</a>
    </header>
    <main>
        <?php if (!empty($errors)): ?>
            <div class="error" style="color: #c33; margin-bottom: 20px;">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="POST" action="/my-blog/public/?url=store">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required style="width:100%;padding:8px;">
            </div>
            <div class="form-group">
                <label for="content">Content</label>
                <textarea name="content" id="content" rows="8" required style="width:100%;padding:8px;"><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn" style="background:#007bff;color:#fff;padding:10px 20px;border:none;border-radius:4px;">Create Post</button>
        </form>
    </main>
</body>
</html>