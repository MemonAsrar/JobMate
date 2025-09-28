document.addEventListener("DOMContentLoaded", function () {
    // Show/hide password logic (only for "Enter password" field)
    const showPasswordCheckbox = document.querySelector("#show-password input");
    const passwordField = document.getElementById("password");

    if (showPasswordCheckbox) {
        showPasswordCheckbox.addEventListener("change", function () {
            passwordField.type = this.checked ? "text" : "password";
        });
    }

    // Enable/Disable Profession Dropdown
    document.getElementById("user_type").addEventListener("change", toggleProfession);

    function toggleProfession() {
        const userType = document.getElementById("user_type").value;
        const professionDropdown = document.getElementById("profession");
        const chargeDropdown = document.getElementById("charge");
        const cityDropdown = document.getElementById("city");

        if (userType === "Worker") {
            professionDropdown.removeAttribute("disabled");
            chargeDropdown.removeAttribute("disabled");
            cityDropdown.removeAttribute("disabled");
        } else {
            professionDropdown.setAttribute("disabled", "disabled");
            chargeDropdown.setAttribute("disabled", "disabled");
            cityDropdown.setAttribute("disabled", "disabled");

            professionDropdown.value = "";
            chargeDropdown.value = "";
            cityDropdown.value = "";
        }
    }

    // Function to validate phone number length (Restrict input to 10 digits)
    window.validatePhone = function (input) {
        input.value = input.value.slice(0, 10);
    };

    // Form validation function
    window.validateForm = function () {
        const name = document.getElementById("name").value.trim();
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();
        const repeatPassword = document.getElementById("repeat-password").value.trim();
        const userType = document.getElementById("user_type").value;
        const profession = document.getElementById("profession").value;
        const phone = document.getElementById("phone").value.trim();

        if (!name || !email || !password || !repeatPassword || !userType || !phone) {
            alert("All fields are required.");
            return false;
        }

        if (!/^\S+@\S+\.\S+$/.test(email)) {
            alert("Invalid email format.");
            return false;
        }

        if (password !== repeatPassword) {
            alert("Passwords do not match.");
            return false;
        }

        if (userType === "Worker" && !profession) {
            alert("Please select a profession.");
            return false;
        }

        if (!/^\d{10}$/.test(phone)) {
            alert("Phone number must be exactly 10 digits.");
            return false;
        }

        // ✅ If all validations pass, redirect to Login.html
        window.location.href = "Login.php";
        return false; // Prevents form from submitting normally

    };
});