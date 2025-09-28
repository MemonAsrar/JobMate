<?php
session_start();
require_once "config.php"; // Connect to database

// Restrict access to only logged-in admin users
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || strtolower(trim($_SESSION['user_type'])) !== 'admin') {
    // If not logged in or not an admin, redirect to login page
    header("Location: admin_dashboard.php");
    exit();
}

// Check if ID is set
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Always make sure it's integer

    // Delete the worker from database
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND user_type = 'Worker'");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Success
        header("Location: manage-workers.php?message=Worker+deleted+successfully");
        exit();
    } else {
        echo "Error deleting worker: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid worker ID.";
}
?>
