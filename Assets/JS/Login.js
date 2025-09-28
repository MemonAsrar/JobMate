document.addEventListener("DOMContentLoaded", function () {
    const showPasswordCheckbox = document.getElementById("show-password");
    const passwordField = document.getElementById("password");

    // Toggle password visibility
    if (showPasswordCheckbox && passwordField) {
        showPasswordCheckbox.addEventListener("change", function () {
            passwordField.type = this.checked ? "text" : "password";
        });
    }

    // AJAX Login Form Submission
    document.getElementById("login-form").addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent default form submission

        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();

        if (!email || !password) {
            alert("❌ Both email and password are required.");
            return;
        }

        if (!/^\S+@\S+\.\S+$/.test(email)) {
            alert("⚠️ Invalid email format.");
            return;
        }

        // Send AJAX request
        fetch("login.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email, password })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("✅ Login successful!");
                window.location.href = data.redirect; // Redirect to dashboard
            } else {
                alert("❌ " + data.message);
            }
        })
        .catch(error => {
            alert("⚠️ Error: Unable to connect to server.");
            console.error("Error:", error);
        });
    });
});