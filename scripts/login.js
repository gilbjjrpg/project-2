// Wait until the HTML page is fully loaded before running any login code
window.addEventListener("DOMContentLoaded", function () {
    setupLoginPage();
});

// This function controls the behavior for the login page only
function setupLoginPage() {
    // Get the main login form, guest button, and error message box from the page
    const loginForm = document.getElementById("loginForm");
    const guestBtn = document.getElementById("guestBtn");
    const errorMessage = document.getElementById("errorMessage");

    // If this page does not have the login form or error box,
    // stop here so this file does not break on other pages
    if (!loginForm || !errorMessage) {
        return;
    }

    // Listen for the user submitting the login form
    loginForm.addEventListener("submit", async function (event) {
        // Stop the form from refreshing the page automatically
        // We want JavaScript to check the login first
        event.preventDefault();

        // Get whatever the user typed into the login fields
        const loginIdentifier = document.getElementById("loginIdentifier").value.trim();
        const loginPassword = document.getElementById("loginPassword").value.trim();

        // Clear any previous error message before doing a new check
        errorMessage.textContent = "";
        errorMessage.classList.add("hidden");

        // Basic validation:
        // If either field is empty, show an error and stop
        if (loginIdentifier === "" || loginPassword === "") {
            showError("Please enter both your username/email and password.");
            return;
        }

        try {
            // Load the users.json file
            const response = await fetch("../data/users.json");
            const users = await response.json();

            // Look for a user whose username OR email matches,
            // and whose password also matches
            const matchedUser = users.find(function (user) {
                return (
                    (user.username === loginIdentifier || user.email === loginIdentifier) &&
                    user.password === loginPassword
                );
            });

            // If a user match was found, login is successful
            if (matchedUser) {
                // Store the username in a cookie so dashboard.php can read it
                document.cookie =
                    "username=" + encodeURIComponent(matchedUser.username) + "; path=/";

                // Also store the full user object in sessionStorage
                // This is optional, but useful if other JS pages need quick access to the user
                sessionStorage.setItem("currentUser", JSON.stringify(matchedUser));

                // Send the user to the dashboard page
                window.location.href = "dashboard.php";
            } else {
                // No match found, so login failed
                showError("Invalid username/email or password.");
            }
        } catch (error) {
            // If something goes wrong loading the JSON file,
            // show an error on the page and log the real error in the console
            console.error("Login error:", error);
            showError("Could not load user data.");
        }
    });

    // If the page has a guest button, give it guest login behavior
    if (guestBtn) {
        guestBtn.addEventListener("click", function () {
            // Store "Guest" as the username cookie
            // This lets the next page know the user entered as a guest
            document.cookie = "username=Guest; path=/";

            // Redirect guest users to the dashboard page
            window.location.href = "dashboard.php";
        });
    }

    // Helper function:
    // Shows an error message inside the error box on the page
    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.remove("hidden");
    }
}