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

// Fetch worker details (you can use if needed)
$sql = "SELECT name FROM users WHERE email = ? LIMIT 1";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
    } else {
        $name = "User";
    }

    $stmt->close();
} else {
    die("Database query failed.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_name = $conn->real_escape_string($_POST['name']);
    $sender_email = $conn->real_escape_string($_POST['email']);
    $sender_message = $conn->real_escape_string($_POST['message']);

    // Save contact message to database or email (here you can expand it)

    echo "<script>alert('Thank you for contacting us!');</script>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - JobMate</title>
    <link rel="stylesheet" href="Assets/CSS/contact.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
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
        }
        .nav-links a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
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
        .contact-container {
            max-width: 900px;
            margin: 50px auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .contact-container h1 {
            text-align: center;
            color: #4158d0;
            margin-bottom: 30px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        input, textarea {
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }
        button {
            padding: 12px;
            background-color: #4158d0;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #2e3a8c;
        }
        .contact-info {
            margin-top: 40px;
            text-align: center;
        }
        .contact-info p {
            margin: 5px 0;
            font-size: 16px;
            color: #555;
        }
        .footer {
            text-align: center;
            background-color: #111827;
            color: white;
            padding: 10px;
            margin-top: 50px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <h2>JobMate</h2>
    <div class="nav-links">
        <a href="home.php">Home</a>
        <a href="about-us.php">About Us</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<!-- Contact Form -->
<div class="contact-container">
    <h1>Contact Us</h1>
    <form method="POST" action="">
        <input type="text" name="name" placeholder="Your Full Name" required>
        <input type="email" name="email" placeholder="Your Email Address" required>
        <textarea name="message" rows="6" placeholder="Your Message" required></textarea>
        <button type="submit">Send Message</button>
    </form>

    <div class="contact-info">
        <p>📧 Email: krishnamchauhan4@gmail.com</p>
        <p>📞 Phone: +91 9484808058</p>
    </div>
</div>

<div class="footer">JobMate © 2025</div>

</body>
</html>
