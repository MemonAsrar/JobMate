<?php
session_start();
require_once "config.php"; // Connect to database

// Restrict access to only logged-in admin users
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || strtolower(trim($_SESSION['user_type'])) !== 'admin') {
    // If not logged in or not an admin, redirect to login page
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch all contractors (user_type = 'Contractor')
$query = "SELECT id, name, email, phone FROM users WHERE user_type = 'Contractor'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Contractors - Admin Panel</title>
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

        /* Manage Contractors Styles */
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
        <a href="admin_dashboard.php">Admin Dashboard</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<!-- Manage Contractors Content -->
<div class="container">
    <h2>Manage Contractors</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($contractor = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$contractor['id']."</td>";
                echo "<td>".$contractor['name']."</td>";
                echo "<td>".$contractor['email']."</td>";
                echo "<td>".$contractor['phone']."</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No contractors found!</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>

</body>
</html>
