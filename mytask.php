<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['User', 'Worker', 'Admin'])) {
    header('Location: login.php');
    exit();
}


$user_id = $_SESSION['user_id'];

// If task completion or rating is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['task_id']) && isset($_POST['rating'])) {
        $task_id = intval($_POST['task_id']);
        $rating = intval($_POST['rating']);

        // Step 1: Get the worker_id for this task
        $worker_sql = "SELECT worker_id FROM bookings WHERE id = ? AND user_id = ? AND status IN ('Confirmed', 'Pending')";
        $stmt = $conn->prepare($worker_sql);
        if (!$stmt) {
            die('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param('ii', $task_id, $user_id);
        $stmt->execute();
        $stmt->bind_result($worker_id);
        $stmt->fetch();
        $stmt->close();

        if ($worker_id) {
            // Step 2: Insert the new rating into task_ratings table with the worker_id (not the user_id)
            $insert_rating_sql = "INSERT INTO task_ratings (task_id, worker_id, user_id, rating) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_rating_sql);
            if (!$stmt) {
                die('Prepare failed: ' . $conn->error);
            }
            $stmt->bind_param('iiii', $task_id, $worker_id, $user_id, $rating);
            $stmt->execute();
            $stmt->close();

            // Step 3: Update the booking status to completed
            $update_sql = "UPDATE bookings SET status = 'Completed' WHERE id = ? AND user_id = ? AND status IN ('Confirmed', 'Pending')";
            $stmt = $conn->prepare($update_sql);
            if (!$stmt) {
                die('Prepare failed: ' . $conn->error);
            }
            $stmt->bind_param('ii', $task_id, $user_id);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: home.php"); // Refresh after rating and status update
        exit();
    }
}

// FETCH TASKS FOR CURRENT USER
$tasks = [];
$sql = "SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_date DESC, booking_time DESC";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    // Fetch the average rating for the task
    $avg_rating_sql = "SELECT AVG(rating) as avg_rating FROM task_ratings WHERE task_id = ?";
    $stmt_avg = $conn->prepare($avg_rating_sql);
    if (!$stmt_avg) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt_avg->bind_param('i', $row['id']);
    $stmt_avg->execute();
    $stmt_avg->bind_result($avg_rating);
    $stmt_avg->fetch();
    $stmt_avg->close();

    // Store the average rating in the task array
    $row['avg_rating'] = $avg_rating ? round($avg_rating, 2) : null;

    $tasks[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Tasks - JobMate</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9fafb; margin: 0; padding: 0; }
        .container { max-width: 1000px; margin: 40px auto; background: white; padding: 20px; border-radius: 10px; }
        h1 { text-align: center; margin-bottom: 20px; }
        .task { padding: 15px; border-bottom: 1px solid #ddd; }
        .task:last-child { border-bottom: none; }
        .complete-btn { background: green; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; }
        .submit-btn { background: #1f2937; color: white; padding: 8px 15px; border: none; border-radius: 5px; margin-top: 10px; cursor: pointer; }
        .rating-stars span { font-size: 30px; cursor: pointer; color: #ccc; }
        .rating-stars span.active { color: gold; }
    </style>
</head>
<body>

<div class="container">
    <h1>📋 My Tasks</h1>

    <?php if (count($tasks) > 0): ?>
        <?php foreach ($tasks as $task): ?>
            <div class="task" id="task-<?php echo $task['id']; ?>">
                <p><strong>Service:</strong> <?php echo htmlspecialchars($task['service_type']); ?></p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($task['booking_date']); ?> at <?php echo date("g:i A", strtotime($task['booking_time'])); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($task['status']); ?></p>

                <?php if ($task['status'] == 'Confirmed'): ?>
    <form method="post" action="" class="rating-form" id="rating-form-<?php echo $task['id']; ?>">
        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
        <div class="rating-stars" id="stars-<?php echo $task['id']; ?>">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <span onclick="setRating(<?php echo $task['id']; ?>, <?php echo $i; ?>)">&#9733;</span>
            <?php endfor; ?>
        </div>
        <input type="hidden" name="rating" id="rating-value-<?php echo $task['id']; ?>" value="0">
        <button type="submit" class="complete-btn" onclick="return validateRating(<?php echo $task['id']; ?>)">Mark as Complete & Submit Rating</button>
    </form>
<?php elseif ($task['status'] == 'Completed'): ?>
    <?php if ($task['avg_rating'] !== null): ?>
        <p><strong>Your Rating:</strong> <?php echo str_repeat("★", intval($task['avg_rating'])) . str_repeat("☆", 5 - intval($task['avg_rating'])); ?> (<?php echo $task['avg_rating']; ?>)</p>
    <?php endif; ?>
<?php endif; ?>

            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No tasks found yet!</p>
    <?php endif; ?>
</div>

<script>
function setRating(taskId, rating) {
    let stars = document.querySelectorAll('#stars-' + taskId + ' span');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
    document.getElementById('rating-value-' + taskId).value = rating;
}

function validateRating(taskId) {
    const rating = document.getElementById('rating-value-' + taskId).value;
    if (rating == 0) {
        alert('Please select a rating before submitting.');
        return false;
    }
    return true;
}
</script>

</body>
</html>
