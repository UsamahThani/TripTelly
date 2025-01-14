function checkPasswordStrength(passwordInputId) {
    const passwordInput = document.getElementById(passwordInputId);
    const feedbackElement = document.getElementById("feedback");
    feedbackElement.style.color = '#ff0000';
    const submitBtn = document.getElementById("submit");

    passwordInput.addEventListener('input', function () {
        const password = passwordInput.value;
        const regex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

        if (password.length === 0) {
            feedbackElement.textContent = '';
            // submitBtn.disabled = true; // Disable submit when input is empty
        } else if (!regex.test(password)) {
            feedbackElement.textContent = 'Password must be at least 8 characters long and include alphabets, numbers, and symbols.';
            feedbackElement.style.color = '#ff0000'; // Ensure red color on invalid input
            submitBtn.disabled = true;
            return false;
        } else {
            feedbackElement.textContent = 'Strong password!';
            feedbackElement.style.color = '#28a745'; // Change to green for a strong password
            submitBtn.disabled = false;
            return true;
        }
    });
}

// Example usage:
// Add this script to your page and call checkPasswordStrength('passwordChecker');
// where 'passwordChecker' is the ID of the password input field.
