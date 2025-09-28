<?php
require_once "config.php";
session_start();

if (!isset($_GET['id'])) {
    echo "Worker not found.";
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT name, profession, experience, charge, city, phone FROM users WHERE id = ? AND user_type = 'Worker'");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$worker = $result->fetch_assoc();

// Fetch the average rating from task_ratings table
$rating_sql = "SELECT AVG(rating) as avg_rating FROM task_ratings WHERE worker_id = ?";
$rating_stmt = $conn->prepare($rating_sql);
$rating_stmt->bind_param('i', $id);
$rating_stmt->execute();
$rating_stmt->bind_result($avg_rating);
$rating_stmt->fetch();
$rating_stmt->close();

$avg_rating = $avg_rating ? round($avg_rating, 1) : 0; // Default to 0 if no ratings

if (!$worker) {
    echo "No worker found.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($worker['name']); ?> - Worker Info</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 40px;
        }
        .worker-profile {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .worker-profile h2 {
            margin-bottom: 10px;
        }
        .worker-profile p {
            margin: 10px 0;
        }
        a.back {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #1f2937;
        }
        .booking-form {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
        }
        .booking-form input, .booking-form textarea, .booking-form button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .booking-form button {
            background-color: #1f2937;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        .booking-form button:hover {
            background-color: #374151;
        }
    </style>
</head>
<body>

<div class="worker-profile">
    <h2><?php echo htmlspecialchars($worker['name']); ?></h2>
    <p><strong>Profession:</strong> <?php echo htmlspecialchars($worker['profession']); ?></p>
    <p><strong>Experience:</strong> <?php echo htmlspecialchars($worker['experience'] ?? 'Not specified'); ?></p>
    <p><strong>Charge:</strong> ₹<?php echo htmlspecialchars($worker['charge']); ?> / day</p>

    <p><strong>Rating:</strong>
    <?php
    $fullStars = floor($avg_rating);
    $halfStar = ($avg_rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

    // Display full stars
    for ($i = 0; $i < $fullStars; $i++) echo "⭐";
    // Display half star if applicable
    if ($halfStar) echo "⭐½";
    // Display empty stars
    for ($i = 0; $i < $emptyStars; $i++) echo "☆";

    echo " (" . $avg_rating . "/5)";
    ?>
    </p>

    <p><strong>City:</strong> <?php echo htmlspecialchars($worker['city']); ?></p>
    <a href="service.php" class="back">← Back to Services</a>
    <a href="tel:<?php echo $worker['phone']; ?>" style="
    display: inline-block;
    margin-top: 20px;
    padding: 12px 25px;
    background-color: #1f2937;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
">📞 Hire Now</a>

    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="booking-form">
        <h3>Book This Worker</h3>
        <form action="book_worker.php" method="POST">
            <input type="hidden" name="worker_id" value="<?php echo $id; ?>">
            
            <label>Service Type:</label>
            <input type="text" name="service_type" required>
            
            <label>Date:</label>
            <input type="date" name="date" required>

            <label>Time:</label>
            <input type="time" name="time" required>

            <label>Message (optional):</label>
            <textarea name="message" rows="3"></textarea>

            <button type="submit">Send Booking Request</button>
        </form>
    </div>
    <?php else: ?>
        <p><strong>🔒 Please <a href="login.php">login</a> to book this worker.</strong></p>
    <?php endif; ?>
</div>

</body>
</html>
