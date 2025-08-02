# Simple PHP MVC Blog

A demonstration of **Model-View-Controller (MVC)** architecture in PHP, built to showcase clean code organization and modern web development practices.

## 🏗️ Project Structure

```
MY-BLOG/
├── app/                          # Application core (business logic)
│   ├── controllers/              # Controllers - Handle user requests
│   │   └── BlogController.php    # Manages blog-related requests
│   ├── models/                   # Models - Data layer
│   │   └── Post.php              # Handles blog post data operations
│   └── views/                    # Views - Presentation layer
│       ├── home.php              # Homepage template
│       └── post.php              # Single post template
├── config/                       # Configuration files
│   └── database.php              # Database connection settings
├── database/                     # Database-related files
│   ├── schema.sql                # Database structure & sample data
│   └── seed_data.sql             # Additional sample data (optional)
├── public/                       # Web-accessible files only
│   ├── css/                      # Stylesheets
│   ├── js/                       # JavaScript files
│   └── index.php                 # Application entry point & router
└── README.md                     # This file
```

## 🧠 Understanding MVC Architecture

**MVC (Model-View-Controller)** is a design pattern that separates application logic into three interconnected components:

### 📊 **Model** (`app/models/`)

**Responsibility:** Data management and business logic

- Handles database operations (Create, Read, Update, Delete)
- Contains business rules and data validation
- Independent of user interface

**Example:** `Post.php` manages all blog post data - fetching posts from database, creating new posts, etc.

```php
// Model example - handles data
$posts = $postModel->getAllPosts();  // Gets data from database
```

### 🎨 **View** (`app/views/`)

**Responsibility:** User interface and presentation

- Contains HTML templates and styling
- Displays data provided by controllers
- No business logic - only presentation

**Example:** `home.php` displays the list of blog posts in HTML format

```php
// View example - displays data
<?php foreach ($posts as $post): ?>
    <h2><?= $post['title'] ?></h2>
    <p><?= $post['content'] ?></p>
<?php endforeach; ?>
```

### 🎮 **Controller** (`app/controllers/`)

**Responsibility:** Request handling and flow control

- Receives user requests (clicks, form submissions)
- Coordinates between Models and Views
- Decides what data to fetch and which view to display

**Example:** `BlogController.php` handles "/blog" requests, gets data from Post model, and loads the appropriate view

```php
// Controller example - coordinates everything
public function index() {
    $posts = $this->postModel->getAllPosts();  // Get data from Model
    require '../app/views/home.php';           // Load View with data
}
```

## 🔄 How It All Works Together

**User Request Flow:**

1. **User visits** → `http://localhost/my-blog/public/?url=home`
2. **Router** (`public/index.php`) → Receives request, determines what to do
3. **Controller** (`BlogController.php`) → Handles the request
4. **Model** (`Post.php`) → Fetches data from database
5. **View** (`home.php`) → Displays data as HTML
6. **Response** → User sees the formatted webpage

```
User Request → Router → Controller → Model → Database
                ↓         ↓         ↓
            Response ← View ← Controller ← Model
```

## 🚀 Getting Started

### Prerequisites

- **XAMPP** (or similar local server with PHP & MySQL)
- **Web browser**

### Installation

1. **Clone/place** this project in your `htdocs` folder
2. **Start** Apache and MySQL in XAMPP
3. **Create database:**
   - Go to `http://localhost/phpmyadmin/`
   - Run the SQL from `database/schema.sql`
4. **Visit:** `http://localhost/my-blog/public/`

### Available URLs

- **Homepage:** `?url=home` (lists all blog posts)
- **Single post:** `?url=post/1` (shows specific post)

## 🛠️ Technical Features

### Architecture Benefits

- **Separation of Concerns** - Each component has a single responsibility
- **Maintainability** - Easy to modify one part without affecting others
- **Scalability** - Simple to add new features (admin panel, user auth, etc.)
- **Testability** - Components can be tested independently
- **Reusability** - Models and views can be reused across different controllers

### Security Features

- **Protected application files** - Only `public/` folder is web-accessible
- **PDO prepared statements** - Prevents SQL injection
- **Input sanitization** - HTML special characters are escaped
- **Configuration separation** - Sensitive data kept in config files

### Modern PHP Practices

- **PSR-4 autoloading ready** - Follows PHP standards
- **Object-oriented design** - Uses classes and methods
- **Clean URLs** - SEO-friendly routing
- **Responsive design** - Mobile-friendly interface

## 🔧 Extension Possibilities

This MVC foundation can easily be extended with:

- **Admin Panel** - Add/edit/delete posts through web interface
- **User Authentication** - Login system for content management
- **Categories & Tags** - Organize posts by topic
- **Search Functionality** - Find posts by keyword
- **Comments System** - User interaction features
- **File Uploads** - Image management for posts
- **RESTful API** - JSON endpoints for mobile apps

## 📝 Code Quality

- **Clean Code** - Descriptive variable names and clear structure
- **Documentation** - Comments explaining complex logic
- **Error Handling** - Graceful failure management
- **Standards Compliance** - Follows PHP best practices

## 🎯 Why MVC Matters for Web Development

**For Agencies like Big Voodoo:**

1. **Team Collaboration** - Designers work on views, developers on models/controllers
2. **Client Changes** - Easy to modify appearance without touching business logic
3. **Maintenance** - Clear structure makes debugging and updates faster
4. **Scalability** - Can grow from simple blog to complex CMS
5. **Code Reuse** - Components can be shared across different projects

## 📚 Learning Outcomes

This project demonstrates understanding of:

- **MVC Architecture** - Industry-standard design pattern
- **PHP Object-Oriented Programming** - Modern PHP development
- **Database Integration** - MySQL with PDO
- **Web Security** - Basic security practices
- **Project Organization** - Professional file structure
- **Clean Code** - Readable and maintainable code

---

**Built by:** Weyehn Reeves  
**Purpose:** Demonstrate MVC understanding and PHP skills  
**Target:** Big Voodoo Interactive - Web Developer Position  
**Contact:** <weyehn1@gmail.com>
