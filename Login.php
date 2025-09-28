<?php
session_start();
require_once "config.php"; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Read JSON input
    $data = json_decode(file_get_contents("php://input"), true);
    
    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');

    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "❌ Both email and password are required."]);
        exit();
    }

    // Fetch user details
    $sql = "SELECT id, name, email, password, user_type FROM users WHERE email = ? LIMIT 1";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $email, $hashed_password, $user_type);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email; // ✅ Store email for fetching user details
                $_SESSION['user_type'] = $user_type;

                // Redirect based on user type
                if (trim(strtolower($user_type)) === "worker") {
                    $redirect_url = "Profile.php"; // ✅ Worker -> Profile page
                }
                else if (trim(strtolower($user_type)) === "admin") {
                    $redirect_url = "admin_dashboard.php"; // ✅ Worker -> Profile page
                }
                else {
                    $redirect_url = "home.php"; // ✅ Others -> Home page
                }

                echo json_encode(["success" => true, "message" => "✅ Login successful!", "redirect" => $redirect_url]);
            } else {
                echo json_encode(["success" => false, "message" => "❌ Incorrect password."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "❌ No account found with this email."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "⚠️ Database query failed."]);
    }
    $conn->close();
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./Assets/CSS/Login.css">
    <script src="./Assets/JS/Login.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <div class="title">Login Form</div>
        <form id="login-form">
            <div class="field">
                <input type="email" id="email" name="email" placeholder="Enter your Email" required>
            </div>
            <div class="field">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <!-- Show Password Checkbox -->
            <div class="field">
                <input type="checkbox" id="show-password">
                <span style="margin-left: 5px;">Show Password</span>
            </div>
            <div class="content">
                <div class="pass-link">
                    <a href="Sign-in.php">Sign-in</a>
                    <a href="Forgate.php" id="forgot-password">Forgot password</a>
                </div>
            </div>
            <br>
            <div class="field">
                <input type="submit" value="LOGIN">
            </div>
        </form>
    </div>
</body>
</html>
