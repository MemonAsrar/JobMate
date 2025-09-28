<?php
// Enable error reporting for debugging (remove this in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please login first to make a booking.");
}

// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'jobmate');

// Connect to MySQL
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set character set to utf8mb4
$conn->set_charset("utf8mb4");

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];       // ID of the user who is booking
    $worker_id = intval($_POST['worker_id']); // ID of the worker being booked
    $service_type = trim($_POST['service_type']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $message = trim($_POST['message']);

    // Validate required fields
    if (empty($service_type) || empty($date) || empty($time)) {
        die("Please fill in all required fields.");
    }

    // Create a bookings table if not exist (updated to fit your form)
    $createTableSQL = "CREATE TABLE IF NOT EXISTS bookings (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        worker_id INT(11) NOT NULL,
        service_type VARCHAR(255) NOT NULL,
        booking_date DATE NOT NULL,
        booking_time TIME NOT NULL,
        message TEXT DEFAULT NULL,
        status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if (!$conn->query($createTableSQL)) {
        die("Error creating bookings table: " . $conn->error);
    }

    // Insert the booking into database
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, worker_id, service_type, booking_date, booking_time, message) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $user_id, $worker_id, $service_type, $date, $time, $message);

    if ($stmt->execute()) {
        echo "<script>alert('Booking request sent successfully!'); window.location.href='service.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
