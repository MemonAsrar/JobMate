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

// SQL query to create 'task_ratings' table and modify 'users' table
$sql = "
-- Create task_ratings table to store individual user ratings for tasks
CREATE TABLE IF NOT EXISTS task_ratings (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    task_id INT(11) NOT NULL,  -- Reference to the booking/task
    user_id INT(11) NOT NULL,  -- User who gave the rating
    rating INT(1) NOT NULL,    -- Rating value (1-5)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- When the rating was given
    FOREIGN KEY (task_id) REFERENCES bookings(id),   -- Assuming 'bookings' table exists
    FOREIGN KEY (user_id) REFERENCES users(id)       -- Assuming 'users' table exists
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Modify the users table to store average ratings for workers
ALTER TABLE users ADD COLUMN rating FLOAT DEFAULT 0;
";

// Execute the query
if ($conn->multi_query($sql)) {
    echo "✅ Rating system tables are successfully created/modified!";
} else {
    echo "❌ Error creating tables: " . $conn->error;
}

// Close the connection
$conn->close();
?>
