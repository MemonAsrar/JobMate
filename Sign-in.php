<?php
session_start();
require_once "config.php"; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $repeat_password = trim($_POST['repeat-password']);
    $user_type = trim($_POST['user_type']);
    $profession = isset($_POST['profession']) ? trim($_POST['profession']) : null;
    $phone = trim($_POST['phone']);
    $charge = isset($_POST['charge']) ? trim($_POST['charge']) : null;
    $city = isset($_POST['city']) ? trim($_POST['city']) : null;

    $errors = [];

    // Basic Validations
    if (empty($name) || empty($email) || empty($password) || empty($repeat_password) || empty($user_type) || empty($phone)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if ($password !== $repeat_password) {
        $errors[] = "Passwords do not match.";
    }

    if ($user_type === "Worker" && empty($profession)) {
        $errors[] = "Profession is required for Workers.";
    }

    if (strlen($phone) < 10) {
        $errors[] = "Invalid phone number.";
    }

    if ($user_type === "Worker" && (empty($charge) || empty($city))) {
        $errors[] = "Charge and City are required for Workers.";
    }

    if (empty($errors)) {
        // Check if the email already exists
        $sql_check = "SELECT id FROM users WHERE email = ?";
        if ($stmt_check = $conn->prepare($sql_check)) {
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) {
                $errors[] = "Email is already registered. Please use a different email.";
            }
            $stmt_check->close();
        }

        if (empty($errors)) {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert user into database
            $sql = "INSERT INTO users (name, email, password, user_type, profession, phone, charge, city) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ssssssss", $name, $email, $hashed_password, $user_type, $profession, $phone, $charge, $city);

                if ($stmt->execute()) {
                    echo "<script>
                            alert('User details have been successfully submitted.');
                            window.location='login.php';
                          </script>";
                    exit();
                } else {
                    echo "<script>alert('Something went wrong. Please try again.');</script>";
                }
                $stmt->close();
            }
        }
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-in</title>
    <link rel="stylesheet" href="./Assets/CSS/Sign-in.css">
    <script src="./Assets/JS/Sign-in.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"></script>
</head>

<body>
    <div class="wrapper">
        <div class="title">Sign In Form</div>
        <form action="" method="POST">

            <div class="field">
                <input type="text" id="name" name="name" placeholder="Username" required>
            </div>

            <div class="field">
                <input type="email" id="email" name="email" placeholder="Enter your Email" required>
            </div>

            <div class="field">
                <input type="password" id="password" name="password" placeholder="Enter password" required>
            </div>

            <div class="field">
                <input type="password" id="repeat-password" name="repeat-password" placeholder="Repeat password" required>
            </div>

            <div class="field">
                <select id="user_type" name="user_type" required onchange="toggleProfession()">
                    <option value="">-- Select User Type --</option>
                    <option value="User">User</option>
                    <option value="Worker">Worker</option>
                </select>
            </div>

            <div class="field">
                <input type="text" id="profession" name="profession" placeholder="Enter Profession (for Workers)" required>
            </div>

            <div class="field">
                <input type="number" id="charge" name="charge" placeholder="Enter Charge (for Workers)" required>
            </div>

            <div class="field">
                <input type="text" id="city" name="city" placeholder="Enter City" required>
            </div>

            <div class="field">
                <input type="number" id="phone" name="phone" placeholder="Enter your phone number" required maxlength="10">
            </div>

                        <!-- Show Password Checkbox -->
                        <div class="field" id="show-password">
                <input type="checkbox" id="show-password"><span> Show Password</span>
            </div>

            <!-- Signup Link -->
            <div class="content">
                <div class="pass-link">
                    <a href="Login.php">Login</a>
                </div>
            </div>

            <div class="field">
                <input type="submit" value="SIGN IN">
            </div>
        </form>
    </div>
</body>
</html>
