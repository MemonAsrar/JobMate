<?php
// Enable error reporting for debugging (remove this in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Default for XAMPP
define('DB_PASSWORD', ''); // Default password is blank in XAMPP
define('DB_NAME', 'jobmate'); // Ensure this matches your database name

// Connect to MySQL
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set character set to utf8mb4 for better security and compatibility
$conn->set_charset("utf8mb4");

// SQL query to create a 'users' table with additional columns (experience and rating added)
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('User', 'Worker', 'Contractor') NOT NULL,
    profession VARCHAR(50) DEFAULT NULL,
    phone VARCHAR(15) NOT NULL,
    charge VARCHAR(50) DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    experience VARCHAR(100) DEFAULT NULL,
    rating FLOAT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// Execute the query
if ($conn->query($sql) === TRUE) {
    echo "✅ Table 'users' is ready with experience and rating columns!";
} else {
    echo "❌ Error creating table: " . $conn->error;
}

// Close the connection
$conn->close();
?>
