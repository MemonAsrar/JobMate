<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Dummy authentication check (replace with actual authentication logic)
    if ($email === "test@example.com" && $password === "password123") {
        echo "<script>alert('Login Successful!');</script>";
    } else {
        echo "<script>alert('Invalid Credentials!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./Assets/CSS/Forgate.css">
    <script src="./Assets/JS/Forgate.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <div class="title">Forgot Page</div>
        <form action="" method="POST">
            <!-- Email Field -->
            <div class="field">
                <input type="email" id="email" name="email" placeholder="Email or Username" required>
            </div>
            
            <div class="field">
                <input type="number" id="phone" name="phone" placeholder="Enter your phone number" required 
                    maxlength="10" oninput="validatePhone(this)">
            </div>
            
            <!-- OTP Field -->
            <div class="field">
                <input type="password" id="text" name="password" placeholder="Enter OTP" required>
            </div>
            <br>
            <!-- Forgot Password Link -->
            <div class="content">
                <div class="pass-link">
                    <a href="#" id="send-otp">Send OTP</a> <!-- Left -->
                    <a href="Login.php" id="login">Login</a> <!-- Right -->
                </div>
            </div>        
            <!-- Submit Button -->
            <div class="field">
                <input type="submit" value="Verify OTP">
            </div>
            
        </form>
    </div>
</body>
</html>