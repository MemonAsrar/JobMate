<?php
// Start session
session_start();

// Enable error reporting (remove this in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
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

// Get email of logged-in user
$email = $_SESSION['user_email'];

// Fetch worker details
$sql = "SELECT id, name, email, phone, profession, charge, city FROM users WHERE email = ? LIMIT 1";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $worker_id = $row['id'];  // Important: store worker_id
        $name = $row['name'];
        $email = $row['email'];
        $phone = $row['phone'];
        $profession = $row['profession'];
        $charge = $row['charge'];
        $city = $row['city'];
    } else {
        header("Location: login.php");
        exit();
    }

    $stmt->close();
} else {
    die("Database query failed.");
}

// Fetch average rating for the worker
$rating_sql = "SELECT AVG(rating) as avg_rating FROM task_ratings WHERE worker_id = ?";
$rating_stmt = $conn->prepare($rating_sql);
$rating_stmt->bind_param("i", $worker_id);
$rating_stmt->execute();
$rating_stmt->bind_result($avg_rating);
$rating_stmt->fetch();
$rating_stmt->close();

// Format the average rating
$avg_rating = round($avg_rating, 1);
$fullStars = floor($avg_rating);
$halfStar = ($avg_rating - $fullStars) >= 0.5;
$emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

// Fetch all bookings where this worker_id matches
$task_sql = "SELECT * FROM bookings WHERE worker_id = ? ORDER BY booking_date DESC, booking_time DESC";
$task_stmt = $conn->prepare($task_sql);
$task_stmt->bind_param("i", $worker_id);
$task_stmt->execute();
$task_result = $task_stmt->get_result();

$tasks = [];
if ($task_result->num_rows > 0) {
    while ($task_row = $task_result->fetch_assoc()) {
        $tasks[] = $task_row;
    }
}

$task_stmt->close();

// Handle task status update for "Confirm" or "Reject"
if (isset($_GET['task_id']) && isset($_GET['action'])) {
    $task_id = $_GET['task_id'];
    $action = $_GET['action'];

    if ($action == 'confirm') {
        // Update the task status to "Confirmed"
        $update_sql = "UPDATE bookings SET status = 'Confirmed' WHERE id = ?";
    } elseif ($action == 'reject') {
        // Update the task status to "Rejected"
        $update_sql = "UPDATE bookings SET status = 'Rejected' WHERE id = ?";
    }

    if ($update_stmt = $conn->prepare($update_sql)) {
        $update_stmt->bind_param("i", $task_id);
        $update_stmt->execute();
        $update_stmt->close();
        
        // Redirect to profile page after the status update
        header("Location: profile.php");
        exit();
    } else {
        die("Error updating task status.");
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Page - JobMate</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"></script>
    <style>
        <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .navbar { display: flex; justify-content: space-between; align-items: center; background-color: #1f2937; padding: 20px 60px; }
        .navbar h2 { color: white; }
        .nav-links a { color: white; margin: 0 10px; text-decoration: none; }
        .nav-links a:hover { text-decoration: underline; }
        .logout-btn { background: red; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; }
        .logout-btn:hover { background: darkred; }
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; }
        .profile-container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 30px; text-align: center; }
        .profile-icon { width: 150px; height: 150px; border-radius: 50%; background-color: #ddd; margin: 0 auto 20px; display: flex; justify-content: center; align-items: center; }
        .fa-user { font-size: 80px; color: #555; }
        .info { margin-top: 20px; color: #374151; }
        .task-list { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .task { border-bottom: 1px solid #ccc; padding: 10px 0; }
        .task:last-child { border-bottom: none; }
        .status { font-weight: bold; }
        .status.Pending { color: orange; }
        .status.Confirmed { color: green; }
        .status.Cancelled { color: red; }
        .status.Completed { color: blue; }
        .complete-btn {
            background-color: green;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .complete-btn:hover {
            background-color: darkgreen;
        }
        .footer { text-align: center; background-color: #111827; color: white; padding: 10px; margin-top: 50px; }
    </style>
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <h2>JobMate</h2>
    <div class="nav-links">
        <a href="home.php">Home</a>
        <a href="contact.php">Contact</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="container">

    <!-- Profile Section -->
    <div class="profile-container">
        <div class="profile-icon">
            <i class="fas fa-user"></i>
        </div>
        <h2><?php echo htmlspecialchars($name); ?></h2>
        <p><strong>Profession:</strong> <?php echo htmlspecialchars($profession); ?></p>
        <p><strong>Charge:</strong> ₹<?php echo htmlspecialchars($charge); ?></p>
        <p><strong>City:</strong> <?php echo htmlspecialchars($city); ?></p>
        <p><strong>Rating:</strong> 
            <?php
                // Display stars based on average rating
                for ($i = 0; $i < $fullStars; $i++) echo "⭐";
                if ($halfStar) echo "⭐½";
                for ($i = 0; $i < $emptyStars; $i++) echo "☆";
                echo " ($avg_rating/5)";
            ?>
        </p>

        <div class="info">
            <p><i class="fas fa-envelope"></i> Email: <a href="mailto:<?php echo htmlspecialchars($email); ?>"><?php echo htmlspecialchars($email); ?></a></p>
            <p><i class="fas fa-phone"></i> Phone: <?php echo htmlspecialchars($phone); ?></p>
        </div>
    </div>

    <!-- My Tasks Section -->
    <div class="task-list">
        <h2>📋 My Bookings</h2>

        <?php if (count($tasks) > 0): ?>
            <?php foreach ($tasks as $task): ?>
                <div class="task">
                    <p><strong>Service:</strong> <?php echo htmlspecialchars($task['service_type']); ?></p>
                    <?php
                        // Convert 24-hour time to 12-hour AM/PM format
                        $formatted_time = date("g:i A", strtotime($task['booking_time']));
                    ?>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($task['booking_date']); ?> at <?php echo htmlspecialchars($formatted_time); ?></p>
                    <p><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($task['message'])); ?></p>
                    <p><strong>Status:</strong> 
                        <span class="status <?php echo htmlspecialchars($task['status']); ?>">
                            <?php echo htmlspecialchars($task['status']); ?>
                        </span>
                    </p>

                    <!-- Show "Confirm" and "Reject" buttons only if status is "Pending" -->
                    <?php if ($task['status'] == 'Pending'): ?>
                        <a href="profile.php?task_id=<?php echo $task['id']; ?>&action=confirm" class="complete-btn">Confirm</a>
                        <a href="profile.php?task_id=<?php echo $task['id']; ?>&action=reject" class="complete-btn" style="background-color: red;">Reject</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No bookings found yet!</p>
        <?php endif; ?>
    </div>

</div>

<div class="footer">JobMate © 2025</div>

</body>
</html>
