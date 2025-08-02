<?php
class Database {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private static $instance = null;
    private $pdo = null;

    public function __construct() {
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->dbname = $_ENV['DB_NAME'] ?? 'my_blog';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? '';
    }

    // Singleton pattern - only one database connection
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function connect() {
        if ($this->pdo !== null) {
            return $this->pdo;
        }

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            
            $options = [
                // Security settings
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                
                // Performance settings
                PDO::MYSQL_ATTR_FOUND_ROWS => true,
                PDO::ATTR_PERSISTENT => false,
                
                // Connection timeout
                PDO::ATTR_TIMEOUT => 10,
            ];

            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
            return $this->pdo;
            
        } catch(PDOException $e) {
            // Log for developers
            error_log("Database connection failed: " . $e->getMessage());
            
            // Generic error for users
            throw new Exception("Unable to connect to database. Please try again later. :)");
        }
    }

    // Clean up connection
    public function disconnect() {
        $this->pdo = null;
    }

    // Prevent cloning of singleton
    private function __clone() {}
    
    // Prevent unserializing of singleton
    private function __wakeup() {}
}
?>