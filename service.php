<?php
include 'config.php'; // Database connection

// Get parameters from URL
$query = isset($_GET['query']) ? $_GET['query'] : ''; // 'query' is the search term
$city = isset($_GET['city']) ? $_GET['city'] : ''; // 'city' parameter
$charge = isset($_GET['charge']) ? (int)$_GET['charge'] : ''; // 'charge' parameter (integer)

// Modify the SQL query to handle the filter
$sql = "SELECT id, name, profession, city, experience, charge FROM users WHERE user_type = 'Worker'";

// Apply filter if the query, city, or charge is set
$conditions = [];
if ($query) {
    $conditions[] = "profession LIKE '%" . $conn->real_escape_string($query) . "%'";
}
if ($city) {
    $conditions[] = "city LIKE '%" . $conn->real_escape_string($city) . "%'";
}
if ($charge) {
    // Apply the range filter for charge (±500)
    $min_charge = $charge - 500;
    $max_charge = $charge + 500;
    $conditions[] = "charge BETWEEN $min_charge AND $max_charge";
}

// Append conditions to SQL query
if (count($conditions) > 0) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY id DESC LIMIT 3"; // Limit for 3 results (can be adjusted)

$result = $conn->query($sql);

// Fetch average ratings from the 'task_ratings' table for each worker
$worker_ratings = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Get the average rating for the worker from the task_ratings table
        $worker_id = $row['id'];
        $rating_sql = "SELECT AVG(rating) as avg_rating FROM task_ratings WHERE worker_id = ?";  // Updated column name
        $rating_stmt = $conn->prepare($rating_sql);
        $rating_stmt->bind_param('i', $worker_id);
        $rating_stmt->execute();
        $rating_stmt->bind_result($avg_rating);
        $rating_stmt->fetch();
        $rating_stmt->close();
        
        // Store the worker's rating in an array
        $row['rating'] = $avg_rating ? round($avg_rating, 1) : 0; // Default to 0 if no rating
        $worker_ratings[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobMate | Services</title>
    <link rel="stylesheet" href="Assets/CSS/hero.css">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f9f9f9;
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            text-align: center;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        .filters {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }
        input, button {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #1f2937;
            color: #fff;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background-color: #374151;
        }
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            justify-content: center;
            align-items: center;
            max-width: 900px;
            margin: 0 auto;
        }
        .card {
            background: #f1f5f9;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .card h3 { margin-bottom: 10px; }
        .card p { margin-bottom: 5px; }
        .rating {
            font-weight: bold;
            color:red;
        }
        a.card-link {
            text-decoration: none;
            color: inherit;
        }
    </style>

    <script>
        function filterServices() {
            var query = document.getElementById("profession").value.trim();
            var city = document.getElementById("city").value.trim();
            var charge = document.getElementById("charge").value.trim();

            // Redirect to the same page with the filter query parameters
            window.location.href = `service.php?query=${query}&city=${city}&charge=${charge}`;
        }
    </script>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h1>Find Services</h1>
        <div class="filters">
            <input type="text" id="profession" placeholder="Enter Profession" value="<?php echo htmlspecialchars($query); ?>">
            <input type="text" id="city" placeholder="Enter City" value="<?php echo htmlspecialchars($city); ?>">
            <input type="number" id="charge" placeholder="Enter Price (e.g., 1000)" value="<?php echo htmlspecialchars($charge); ?>">
            <button onclick="filterServices()">Search</button>
        </div>

        <div class="services-grid">
            <?php
            if (count($worker_ratings) > 0) {
                foreach ($worker_ratings as $worker) {
                    echo "<a href='worker.php?id=" . $worker['id'] . "' class='card-link'>";
                    echo "<div class='card'>";
                    echo "<h3>" . htmlspecialchars($worker['name']) . "</h3>";
                    echo "<p>" . htmlspecialchars($worker['profession']) . " - " . htmlspecialchars($worker['city']) . "</p>";
                    echo "<p>₹" . htmlspecialchars($worker['charge']) . "/day</p>";
                    echo "<p class='rating'>⭐ " . htmlspecialchars($worker['rating']) . "/5</p>";
                    echo "</div>";
                    echo "</a>";
                }
            } else {
                echo "<p>No workers found.</p>";
            }
            ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
