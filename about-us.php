<?php
// Start session
session_start();

// Enable error reporting (remove in production)
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
        $name = $row['name'];
    } else {
        header("Location: login.php");
        exit();
    }
    $stmt->close();
} else {
    die("Database query failed.");
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us - JobMate</title>
    <link rel="stylesheet" href="Assets/CSS/about-us.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; margin: 0; padding: 0; }
        .navbar { display: flex; justify-content: space-between; align-items: center; background-color: #1f2937; padding: 20px 60px; }
        .navbar h2 { color: white; }
        .nav-links a { color: white; margin: 0 10px; text-decoration: none; }
        .nav-links a:hover { text-decoration: underline; }
        .logout-btn { background: red; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; }
        .logout-btn:hover { background: darkred; }
        .about-container {
            max-width: 1000px;
            margin: 50px auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        .about-container h1 {
            text-align: center;
            color: #4158d0;
            margin-bottom: 30px;
        }
        .about-container p {
            font-size: 18px;
            line-height: 1.8;
            color: #333;
            text-align: justify;
            margin-bottom: 20px;
        }
        .about-container h3 {
            color: #4158d0;
            margin-top: 30px;
        }
        .team {
            margin-top: 40px;
        }
        .team h3 {
            text-align: center;
            margin-bottom: 20px;
        }
        .team-members {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .member {
            background: #f1f5f9;
            padding: 20px;
            border-radius: 10px;
            width: 220px;
            text-align: center;
            margin: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .member:hover {
            transform: scale(1.05);
        }
        .member img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 2px solid #4158d0;
        }
        .member h4 {
            margin: 10px 0 5px 0;
            font-size: 18px;
            color: #333;
        }
        .member p {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        .member a {
            color: #4158d0;
            font-weight: bold;
            font-size: 14px;
            text-decoration: none;
        }
        .member a:hover {
            text-decoration: underline;
        }
        .footer { text-align: center; background-color: #111827; color: white; padding: 10px; margin-top: 50px; }
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

<div class="about-container">
    <h1>About JobMate</h1>
    <p>At <strong>JobMate</strong>, we bridge the gap between skilled workers and those who need them. Whether you are looking for a professional plumber, electrician, painter, carpenter, or any other skilled worker, JobMate helps you find trusted workers easily and quickly.</p>

    <p>Our mission is to empower workers by giving them the right platform to showcase their expertise and help users find the right talent for their needs — effortlessly, reliably, and securely.</p>

    <h3>Our Vision</h3>
    <p>We envision a world where finding reliable services is not a hassle but a seamless and enjoyable experience. JobMate is working towards connecting workers and users across cities to build a better and more connected community.</p>

    <div class="team">
        <h3>Our Team</h3>
        <div class="team-members">
            <!-- Krishna -->
            <div class="member">
                <img src="Assets/Images/krishna.jpg" alt="Krishna M. Chauhan">
                <h4>Krishna M. Chauhan</h4>
                <p>Team Leader & Developer</p>
                <a href="https://www.instagram.com/i_am_krsna_/" target="_blank">📸 Instagram</a>
            </div>

            <!-- Asrar -->
            <div class="member">
                <img src="Assets/Images/asrar.jpg" alt="Asrar H. Memon">
                <h4>Asrar H. Memon</h4>
                <p>Backend Developer</p>
                <a href="https://www.instagram.com/memon_asrar_0007/" target="_blank">📸 Instagram</a>
            </div>

            <!-- Mihir -->
            <div class="member">
                <img src="Assets/Images/mihir.jpg" alt="Mihir H. Darji">
                <h4>Mihir H. Darji</h4>
                <p>Tester & Documentation</p>
                <a href="https://www.instagram.com/darjimihir_13/" target="_blank">📸 Instagram</a>
            </div>

        </div>
    </div>
</div>

<div class="footer">JobMate © 2025</div>

</body>
</html>
