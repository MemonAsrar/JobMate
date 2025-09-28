<?php
session_start();
require_once "config.php"; // Connect to database

// Restrict access to only logged-in admin users
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || strtolower(trim($_SESSION['user_type'])) !== 'admin') {
    // If not logged in or not an admin, redirect to login page
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch all workers
$query = "SELECT id, name, profession, city, phone, charge FROM users WHERE user_type = 'Worker'";
$result = $conn->query($query);

// Fetch average ratings for each worker
$worker_ratings = [];
if ($result && $result->num_rows > 0) {
    while ($worker = $result->fetch_assoc()) {
        $worker_id = $worker['id'];
        
        // Fetch the average rating for each worker
        $rating_sql = "SELECT AVG(rating) as avg_rating FROM task_ratings WHERE worker_id = ?";
        $rating_stmt = $conn->prepare($rating_sql);
        $rating_stmt->bind_param('i', $worker_id);
        $rating_stmt->execute();
        $rating_stmt->bind_result($avg_rating);
        $rating_stmt->fetch();
        $rating_stmt->close();

        // Store the worker's rating (rounded to 1 decimal place) or set 0 if no ratings are found
        $worker['rating'] = $avg_rating ? round($avg_rating, 1) : 0;
        $worker_ratings[] = $worker;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Workers - Admin Panel</title>
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

        .container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #1f2937;
            color: white;
        }
        .actions a {
            margin: 0 5px;
            text-decoration: none;
            color: white;
            background: #007bff;
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 14px;
        }
        .actions a.delete {
            background: #dc3545;
        }
        .back-btn {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background: #1f2937;
            color: #fff;
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
        <a href="admin_dashboard.php">Admin Dashboard</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<!-- Manage Workers Content -->
<div class="container">
    <h2>Manage Workers</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Profession</th>
                <th>City</th>
                <th>Phone</th>
                <th>Charge</th>
                <th>Rating</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (count($worker_ratings) > 0) {
            foreach ($worker_ratings as $worker) {
                echo "<tr>";
                echo "<td>".$worker['id']."</td>";
                echo "<td>".$worker['name']."</td>";
                echo "<td>".$worker['profession']."</td>";
                echo "<td>".$worker['city']."</td>";
                echo "<td>".$worker['phone']."</td>";
                echo "<td>₹".$worker['charge']."</td>";
                echo "<td>⭐ ".$worker['rating']."</td>";
                echo "<td class='actions'>
                        <a href='edit-worker.php?id=".$worker['id']."'>Edit</a>
                        <a href='delete-worker.php?id=".$worker['id']."' class='delete' onclick='return confirm(\"Are you sure?\");'>Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No workers found!</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>

</body>
</html>
