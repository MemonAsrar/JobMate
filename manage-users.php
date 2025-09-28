<?php
session_start();
require_once "config.php"; // Connect to database

// Restrict access to only logged-in admin users
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || strtolower(trim($_SESSION['user_type'])) !== 'admin') {
    // If not logged in or not an admin, redirect to login page
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch all users (user_type = 'User')
$query = "SELECT id, name, email, phone FROM users WHERE user_type = 'User'";
$result = $conn->query($query);

// Fetch user ratings and task counts
$user_data = [];
if ($result && $result->num_rows > 0) {
    while ($user = $result->fetch_assoc()) {
        $user_id = $user['id'];

        // Fetch the average rating given by this user (assuming the ratings are from tasks the user has rated)
        $rating_sql = "SELECT AVG(rating) as avg_rating FROM task_ratings WHERE user_id = ?";
        $rating_stmt = $conn->prepare($rating_sql);

        // Check if the query preparation was successful
        if ($rating_stmt === false) {
            die("Error preparing query for ratings: " . $conn->error);
        }

        // Bind parameters and execute the query
        $rating_stmt->bind_param('i', $user_id);
        $rating_stmt->execute();
        $rating_stmt->bind_result($avg_rating);
        $rating_stmt->fetch();
        $rating_stmt->close();

        // Fetch the number of tasks the user has rated (task count)
        $task_count_sql = "SELECT COUNT(*) as task_count FROM task_ratings WHERE user_id = ?";
        $task_count_stmt = $conn->prepare($task_count_sql);

        // Check if the query preparation was successful
        if ($task_count_stmt === false) {
            die("Error preparing query for task count: " . $conn->error);
        }

        // Bind parameters and execute the query
        $task_count_stmt->bind_param('i', $user_id);
        $task_count_stmt->execute();
        $task_count_stmt->bind_result($task_count);
        $task_count_stmt->fetch();
        $task_count_stmt->close();

        // Store user information along with ratings and task count
        $user['avg_rating'] = $avg_rating ? round($avg_rating, 1) : 0;
        $user['task_count'] = $task_count;
        $user_data[] = $user;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f2f5;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        /* Navbar Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #1f2937;
            padding: 20px 60px;
        }
        .navbar h2 {
            color: white;
            margin: 0;
        }
        .nav-links a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
            font-weight: bold;
        }
        .nav-links a:hover {
            text-decoration: underline;
        }
        .logout-btn {
            background: red;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .logout-btn:hover {
            background: darkred;
        }

        /* Manage Users Styles */
        .container {
            max-width: 1000px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #1f2937;
            color: #fff;
        }
        .back-btn {
            margin-top: 20px;
            display: inline-block;
            background: #1f2937;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <h2>JobMate</h2>
    <div class="nav-links">
        <a href="home.php">Home</a>
        <a href="admin_dashboard.php">Admin dashboard</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<!-- Manage Users Content -->
<div class="container">
    <h2>Manage Users</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Avg Rating</th>
                <th>Assigned Tasks</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (count($user_data) > 0) {
            foreach ($user_data as $user) {
                echo "<tr>";
                echo "<td>".$user['id']."</td>";
                echo "<td>".$user['name']."</td>";
                echo "<td>".$user['email']."</td>";
                echo "<td>".$user['phone']."</td>";
                echo "<td>⭐ ".$user['avg_rating']."</td>";
                echo "<td>".$user['task_count']."</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No users found!</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>

</body>
</html>
