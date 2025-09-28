<?php
session_start();
require_once "config.php"; // Connect to database

// Restrict access to only logged-in admin users
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || strtolower(trim($_SESSION['user_type'])) !== 'admin') {
    // If not logged in or not an admin, redirect to login page
    header("Location: admin_dashboard.php");
    exit();
}

// Check if Worker ID is provided
if (!isset($_GET['id'])) {
    echo "Invalid Worker ID.";
    exit();
}

$id = intval($_GET['id']);

// Fetch worker info
$stmt = $conn->prepare("SELECT name, profession, city, phone, charge, rating FROM users WHERE id = ? AND user_type = 'Worker'");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$worker = $result->fetch_assoc();

if (!$worker) {
    echo "Worker not found.";
    exit();
}

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $profession = trim($_POST['profession']);
    $city = trim($_POST['city']);
    $phone = trim($_POST['phone']);
    $charge = trim($_POST['charge']);
    $rating = trim($_POST['rating']);

    // Update worker info
    $update = $conn->prepare("UPDATE users SET name=?, profession=?, city=?, phone=?, charge=?, rating=? WHERE id=? AND user_type='Worker'");
    $update->bind_param("ssssssi", $name, $profession, $city, $phone, $charge, $rating, $id);

    if ($update->execute()) {
        header("Location: manage-workers.php?message=Worker+updated+successfully");
        exit();
    } else {
        echo "Error updating worker: " . $update->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Worker - Admin Panel</title>
    <style>
        body {
            background: #f1f1f1;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
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
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="text"], input[type="number"] {
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        input[type="submit"] {
            background: #1f2937;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            margin-top: 15px;
            font-weight: bold;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #374151;
        }
        .back-btn {
            margin-top: 15px;
            display: inline-block;
            padding: 10px 20px;
            background: #1f2937;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Worker Details</h2>

    <form method="POST" action="">
        <input type="text" name="name" value="<?php echo htmlspecialchars($worker['name']); ?>" placeholder="Name" required>
        <input type="text" name="profession" value="<?php echo htmlspecialchars($worker['profession']); ?>" placeholder="Profession" required>
        <input type="text" name="city" value="<?php echo htmlspecialchars($worker['city']); ?>" placeholder="City" required>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($worker['phone']); ?>" placeholder="Phone" required>
        <input type="number" name="charge" value="<?php echo htmlspecialchars($worker['charge']); ?>" placeholder="Charge per Day" required>
        <input type="number" step="0.1" name="rating" value="<?php echo htmlspecialchars($worker['rating']); ?>" placeholder="Rating (0-5)" required>

        <input type="submit" value="Update Worker">
    </form>

    <a href="manage-workers.php" class="back-btn">← Back to Manage Workers</a>
</div>

</body>
</html>
