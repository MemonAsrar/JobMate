<?php
session_start();
require_once "config.php"; // Connect to database

// Restrict access to only logged-in admin users
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || strtolower(trim($_SESSION['user_type'])) !== 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch Admin Name
$admin_name = "Admin"; // Default
if (isset($_SESSION['name'])) {
    $admin_name = $_SESSION['name'];
} else {
    $admin_id = $_SESSION['user_id'];
    $query = "SELECT name FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->bind_result($name);
    if ($stmt->fetch()) {
        $admin_name = $name;
    }
    $stmt->close();
}

// Fetch Statistics
$total_users = 0;
$total_workers = 0;
$total_contractors = 0;

$result = $conn->query("SELECT user_type, COUNT(*) as total FROM users GROUP BY user_type");
while ($row = $result->fetch_assoc()) {
    if ($row['user_type'] == 'User') {
        $total_users = $row['total'];
    } elseif ($row['user_type'] == 'Worker') {
        $total_workers = $row['total'];
    } elseif ($row['user_type'] == 'Contractor') {
        $total_contractors = $row['total'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - JobMate</title>
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

        /* Dashboard Styles */
        .dashboard {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 40px;
        }
        .stats {
            display: flex;
            justify-content: space-evenly;
        }
        .stat {
            background: #f1f5f9;
            padding: 20px;
            border-radius: 10px;
            width: 250px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .stat:hover {
            transform: translateY(-5px);
        }
        .stat h2 {
            margin-bottom: 10px;
        }
        .logout-section {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <h2>JobMate</h2>
    <div class="nav-links">
        <a href="home.php">Home</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<!-- Dashboard Content -->
<div class="dashboard">
<h1>Welcome, Admin <?php echo htmlspecialchars($admin_name); ?></h1>


    <div class="stats">
        <a href="manage-users.php" style="text-decoration: none; color: inherit;">
            <div class="stat">
                <h2>Total Users</h2>
                <p><?php echo $total_users; ?></p>
            </div>
        </a>
        
        <a href="manage-workers.php" style="text-decoration: none; color: inherit;">
            <div class="stat">
                <h2>Total Workers</h2>
                <p><?php echo $total_workers; ?></p>
            </div>
        </a>
    </div>
</div>

</body>
</html>
