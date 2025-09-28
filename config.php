<?php
// Enable error reporting for debugging (Remove this in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configurationlocal
define('DB_SERVER', 'localhost');  // Change if using a remote database
define('DB_USERNAME', 'root');     // Default for XAMPP
define('DB_PASSWORD', '');         // Default is empty for XAMPP
define('DB_NAME', 'jobmate');      // Ensure this matches your database name

// Create a connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

?>
