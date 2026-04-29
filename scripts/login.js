// Wait until the page is fully loaded before running any login code
window.addEventListener("DOMContentLoaded", function () {
    setupLoginPage();
});

// This function controls behavior for the login page
function setupLoginPage() {

    // Get the login form, guest button, and error message box
    const loginForm = document.getElementById("loginForm");
    const guestBtn = document.getElementById("guestBtn");
    const errorMessage = document.getElementById("errorMessage");

    // Stop if this is not the login page
    if (!loginForm || !errorMessage) {
        return;
    }

    // Handle normal login form submission
    loginForm.addEventListener("submit", function (event) {
      
        // Get the values from the form fields
        const loginIdentifier = document.getElementById("loginIdentifier").value.trim();
        const loginPassword = document.getElementById("loginPassword").value.trim();

        // Clear old error styling/message first
        errorMessage.textContent = "";
        errorMessage.classList.add("hidden");

        // If either field is empty, stop the form and show an error
        if (loginIdentifier === "" || loginPassword === "") {
            event.preventDefault();
            showError("Please enter both your username/email and password.");
        }

        // If both fields are filled in, let the form submit normally to PHP
    });

    // Handle guest login button
    if (guestBtn) {
        guestBtn.addEventListener("click", function () {

            // Username cookie is saved as "Guest"
            // This allows for PHP pages to know the visitor is in guest mode
            document.cookie = "username=Guest; path=/";

            // ALso store a guest user object in sessionStorage
            // This helps quiz.js know that this user is a guest
            sessionStorage.setItem("currentUser", JSON.stringify({
            username: "Guest",
            name: "Guest",
            isGuest: true
            }));

            // Redirect guest users to the dashboard page
            window.location.href = "dashboard.php";
        });
    }

    // Helper function for showing an error message
    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.remove("hidden");
    }
}