document.addEventListener("DOMContentLoaded", function () {
    const sendOtpBtn = document.getElementById("send-otp");
    const phoneInput = document.getElementById("phone");
    const emailInput = document.getElementById("email");
    let generatedOtp = ""; // Variable to store OTP

    // Function to validate phone number
    function validatePhone(input) {
        input.value = input.value.slice(0, 10); // Restricts input to 10 digits
    }

    window.validatePhone = validatePhone; // Make it accessible in HTML

    // Send OTP function
    sendOtpBtn.addEventListener("click", function (event) {
        event.preventDefault(); // Prevent default anchor behavior

        const phoneNumber = phoneInput.value.trim();
        const email = emailInput.value.trim();

        // Validate phone number format
        if (!/^\d{10}$/.test(phoneNumber)) {
            alert("Please enter a valid 10-digit phone number before requesting OTP.");
            return;
        }

        // Validate email format
        if (!/^\S+@\S+\.\S+$/.test(email)) {
            alert("Please enter a valid email address.");
            return;
        }

        // Generate a random 4-digit OTP
        generatedOtp = Math.floor(1000 + Math.random() * 9000).toString();

        // Show OTP in an alert message
        alert(`Your OTP is: ${generatedOtp}`);
    });

    // Form validation before submitting
    document.querySelector("form").addEventListener("submit", function (event) {
        const otpInput = document.getElementById("text").value.trim();
    
        if (!otpInput) {
            alert("Please enter the OTP received.");
            event.preventDefault(); // Prevent form submission
            return;
        }
    
        if (otpInput !== generatedOtp) {
            alert("Invalid OTP. Please enter the correct OTP.");
            event.preventDefault(); // Prevent form submission
            return;
        }
    
        alert("OTP Verified Successfully!");
        event.preventDefault(); // <--- Add this line
        window.location.href = "Login.php"; // Redirect manually
    });
    
});
