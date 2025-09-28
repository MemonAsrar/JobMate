<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Check if user is a Worker
$isWorker = ($isLoggedIn && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'Worker');

// Check if user is an Admin
$isAdmin = ($isLoggedIn && isset($_SESSION['user_type']) && strtolower(trim($_SESSION['user_type'])) === 'admin');
?>

<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
/* Navbar styles */
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

.search-bar {
    flex: 1;
    display: flex;
    justify-content: center;
}

.search-container {
    position: relative;
    display: inline-block;
}

.search-container input {
    padding: 8px 35px 8px 35px;
    width: 300px;
    border-radius: 5px;
    border: none;
}

/* Search icon */
.search-container .fa-search {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: gray;
}

/* Filter icon */
.search-container .fa-filter {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: gray;
    cursor: pointer;
}

/* Nav links styles */
.nav-links {
    display: flex;
    align-items: center;
}

.nav-links a {
    color: white;
    margin: 0 10px;
    text-decoration: none;
}

.nav-links a:hover {
    text-decoration: underline;
    color: #f8d7da; /* Change color on hover */
}

/* Button styles */
.logout-btn { background: red; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; }
.logout-btn:hover { background: darkred; }

.dashboard-btn, .home-btn, .contact-btn, .about-btn {
    color: white;
    margin: 0 10px;
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 4px;
}

.dashboard-btn:hover, .home-btn:hover, .contact-btn:hover, .about-btn:hover {
    background-color: #218838;
}
</style>

<div class="navbar">
    <div class="logo">
        <h2>JobMate</h2>
    </div>

    <!-- Search bar -->
    <div class="search-bar">
        <form action="service.php" method="GET" class="search-container">
            <i class="fa fa-search"></i>
            <input type="text" name="query" placeholder="Search..." />
            <i class="fa fa-filter" id="filterIcon"></i>
        </form>
    </div>

    <!-- Navigation Links -->
    <div class="nav-links">
        <a href="home.php" class="home-btn">Home</a>
        <a href="mytask.php" class="home-btn">My Task</a>
        <a href="service.php" class="home-btn">Services</a> <!-- Services button -->
        <a href="about-us.php" class="about-btn">About Us</a> <!-- About Us button -->
        <a href="contact.php" class="contact-btn">Contact</a>

        <?php if ($isWorker || $isAdmin): ?>
            <a href="<?php echo $isAdmin ? 'admin_dashboard.php' : 'Profile.php'; ?>" class="dashboard-btn">Dashboard</a>
        <?php endif; ?>

        <?php if ($isLoggedIn): ?>
            <a href="logout.php" class="logout-btn">Logout</a>
        <?php else: ?>
            <a href="Login.php">Login</a>
        <?php endif; ?>
    </div>
</div>

<script>
// Redirect to service.php when filter icon is clicked
document.getElementById("filterIcon").addEventListener("click", function() {
    const query = document.querySelector('input[name="query"]').value.trim();
    if (query) {
        window.location.href = `service.php?query=${encodeURIComponent(query)}`;
    } else {
        window.location.href = "service.php";
    }
});
</script>
