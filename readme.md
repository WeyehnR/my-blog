# Simple PHP MVC Blog

A demonstration of **Model-View-Controller (MVC)** architecture in PHP, built to showcase clean code organization and modern web development practices.

I primarly work with javascript as the "modern" web development but it doesnt hurt to learn php as it wasnt too bad. Just had to learn syntaxtically

![What I learn about PHP](https://miro.medium.com/v2/resize:fit:720/format:webp/0*uk1W9MPZP5gK-pVK.jpg)

## 🏗️ Project Structure

```
MY-BLOG/
├── app/                          # Application core (business logic)
│   ├── controllers/              # Controllers - Handle user requests
│   │   ├── AuthController.php    # Manages user authentication (login/register)
│   │   └── BlogController.php    # Manages blog posts, voting, and comments
│   ├── helpers/                  # Helper utilities for common tasks
│   │   ├── FormHelper.php        # Form rendering and validation helpers
│   │   ├── UrlHelper.php         # URL generation and routing helpers
│   │   └── ValidationHelper.php  # Input validation utilities
│   ├── models/                   # Models - Data layer
│   │   ├── Post.php              # Handles blog posts, votes, and comments
│   │   └── User.php              # Handles user data and authentication
│   └── views/                    # Views - Presentation layer
│       ├── auth/                 # Authentication-related views
│       │   ├── login.php         # User login form
│       │   └── register.php      # User registration form
│       ├── partials/             # Reusable view components
│       │   └── header.php        # Site header with navigation
│       ├── create_post.php       # Create new blog post form
│       ├── home.php              # Homepage with post list and voting
│       └── post.php              # Single post view with comments
├── config/                       # Configuration files
│   └── database.php              # Database connection settings
├── database/                     # Database-related files
│   ├── schema.sql                # Complete database structure (users, posts, votes, comments)
│   └── seed_data.sql             # Sample data for testing
├── docs/                         # Project documentation
│   └── learning-notes.md         # Development notes and learning insights
├── public/                       # Web-accessible files only
│   ├── css/                      # Stylesheets
│   │   └── dark-theme.css        # Reddit-style dark theme
│   ├── js/                       # JavaScript files
│   │   └── voting.js             # AJAX voting system with event delegation
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

- **Homepage:** `?url=home` (lists all blog posts with voting)
- **Single post:** `?url=post/1` (shows specific post with comments)
- **Create post:** `?url=create` (create new blog post - requires login)
- **User login:** `?url=login` (user authentication)
- **User registration:** `?url=register` (create new account)
- **Logout:** `?url=logout` (end user session)
- **Vote on post:** AJAX endpoint for upvote/downvote functionality
- **Post comment:** `?url=post/1/comment` (add comment to specific post)

## 🛠️ Technical Features

### Core Functionality

- **User Authentication System** - Secure login/registration with session management
- **AJAX Voting System** - Reddit-style upvote/downvote with real-time updates
- **Comment System** - Threaded comments with proper user attribution
- **Content Management** - Create, read, and display blog posts
- **Responsive Design** - Dark theme optimized for all devices

### Advanced Features

- **Event Delegation** - Clean JavaScript implementation preventing duplicate listeners
- **Custom Voting Logic** - Handles edge cases for single-user and multi-user scenarios
- **Form Helpers** - Reusable components for consistent form handling
- **URL Helpers** - Clean URL generation and routing utilities
- **Input Validation** - Server-side validation with user-friendly error messages

### Architecture Benefits

- **Separation of Concerns** - Each component has a single responsibility
- **Maintainability** - Easy to modify one part without affecting others
- **Scalability** - Simple to add new features (admin panel, user auth, etc.)
- **Testability** - Components can be tested independently
- **Reusability** - Models and views can be reused across different controllers

### Security Features

This is how I imagine a SQL injection attack:
![What I think of SQL injection attack like any smart 12 year old can do!](https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExa2RiYmoydmg1NDcxdjg0cXQxNXdsYnMzaXVmMHpjdWNmYjBvampwYSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/eCqFYAVjjDksg/giphy.gif)

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

- ~~**User Authentication** - Login system for content management~~ ✅ **COMPLETED**
- ~~**Comments System** - User interaction features~~ ✅ **COMPLETED**
- ~~**Voting System** - Reddit-style post rating~~ ✅ **COMPLETED**
- **Admin Panel** - Advanced content management dashboard
- **Categories & Tags** - Organize posts by topic
- **Search Functionality** - Find posts by keyword
- **File Uploads** - Image management for posts
- **RESTful API** - JSON endpoints for mobile apps
- **Email Notifications** - Comment and post alerts
- **User Profiles** - Extended user information and avatars
- **Nested Comments** - Reply threads and comment hierarchies

## 📝 Code Quality

- **Clean Code** - Descriptive variable names and clear structure
- **Documentation** - Comments explaining complex logic
- **Error Handling** - Graceful failure management
- **Standards Compliance** - Follows PHP best practices

## 🎯 Key Features Implemented

### 🔐 User Authentication System

- **Secure Registration** - Password hashing with PHP's `password_hash()`
- **Session Management** - Persistent login sessions
- **Form Validation** - Server-side input validation with error handling
- **Login Protection** - Route guards for authenticated-only features

### ⬆️⬇️ AJAX Voting System

- **Real-time Updates** - Vote without page refresh
- **Custom Logic** - Handles edge cases (1→-1, -1→1 transitions)
- **Event Delegation** - Clean JavaScript preventing duplicate event listeners
- **Multi-user Support** - Proper vote counting across different users

### 💬 Comment System

- **Threaded Comments** - Associated with specific posts
- **User Attribution** - Comments linked to authenticated users
- **Timestamp Display** - Human-readable comment dates
- **Form Integration** - Seamless comment posting with validation

### 🎨 Professional UI/UX

- **Reddit-inspired Design** - Dark theme with modern aesthetics
- **Consistent Layout** - Unified design across all pages
- **Responsive Design** - Mobile-friendly interface
- **Intuitive Navigation** - Clear user flow and interactions

## 🎯 Why MVC Matters for Web Development

**For Agencies like Big Voodoo:**

1. **Team Collaboration** - Designers work on views, developers on models/controllers
2. **Client Changes** - Easy to modify appearance without touching business logic
3. **Maintenance** - Clear structure makes debugging and updates faster
4. **Scalability** - Can grow from simple blog to complex CMS
5. **Code Reuse** - Components can be shared across different projects

## 📚 Learning Outcomes

This project demonstrates understanding of:

### Backend Development

- **MVC Architecture** - Industry-standard design pattern implementation
- **PHP Object-Oriented Programming** - Modern PHP development practices
- **Database Integration** - MySQL with PDO prepared statements
- **User Authentication** - Secure login/registration systems
- **Session Management** - Persistent user state handling

### Frontend Development

- **AJAX Implementation** - Asynchronous JavaScript for voting system
- **Event Delegation** - Advanced JavaScript event handling
- **Responsive Design** - Mobile-first CSS development
- **Form Handling** - Client-server form integration

### Security & Best Practices

- **SQL Injection Prevention** - Parameterized queries with PDO
- **XSS Protection** - Input sanitization and output escaping
- **Password Security** - PHP password hashing functions
- **Input Validation** - Server-side validation with error handling

### Architecture & Organization

- **Clean Code** - Readable and maintainable code structure
- **Separation of Concerns** - Logical component organization
- **Helper Classes** - Reusable utility components
- **Project Organization** - Professional file structure

---

**Built by:** Weyehn Reeves  
**Purpose:** Demonstrate MVC understanding and PHP skills  
**Target:** Big Voodoo Interactive - Web Developer Position  
**Contact:** <weyehn1@gmail.com>
