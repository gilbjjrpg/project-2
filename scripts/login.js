window.addEventListener("DOMContentLoaded", function () {
  setupRouteProtection();
  setupLoginPage();
});

function setupRouteProtection() {
  const currentUser = JSON.parse(sessionStorage.getItem("currentUser"));
  const currentPage = window.location.pathname.split("/").pop();

  // Add every page here that should require login or guest access first
  const protectedPages = [
    "dashboard.php",
    "quizCustomize.html",
    "quiz.html",
    "scores.html",
    "leaderboard.html"
  ];

  // If the user tries to access a protected page without logging in,
  // send them back to the login page
  if (protectedPages.includes(currentPage) && !currentUser) {
    window.location.href = "index.html";
  }
}

function setupLoginPage() {
  const loginForm = document.getElementById("loginForm");
  const guestBtn = document.getElementById("guestBtn");
  const errorMessage = document.getElementById("errorMessage");

  // If this page is not the login page, stop here
  if (!loginForm || !guestBtn || !errorMessage) {
    return;
  }

  loginForm.addEventListener("submit", async function (event) {
    event.preventDefault();

    const loginIdentifier = document.getElementById("loginIdentifier").value.trim();
    const loginPassword = document.getElementById("loginPassword").value.trim();

    errorMessage.classList.add("hidden");
    errorMessage.textContent = "";

    if (loginIdentifier === "" || loginPassword === "") {
      showError("Please enter both your username/email and password.");
      return;
    }

    try {
      const response = await fetch("../data/users_dummy.json");
      const users = await response.json();

      const matchedUser = users.find(function (user) {
        return (
          (user.username === loginIdentifier || user.email === loginIdentifier) &&
          user.password === loginPassword
        );
      });

      if (matchedUser) {
        sessionStorage.setItem("currentUser", JSON.stringify({
          id: matchedUser.id,
          username: matchedUser.username,
          email: matchedUser.email,
          isGuest: false,
          playHistory: matchedUser.playHistory
        }));

        window.location.href = "dashboard.php";
      } else {
        showError("Invalid username/email or password.");
      }
    } catch (error) {
      showError("Could not load user data. Make sure users_dummy.json is in the same folder.");
      console.error("Login error:", error);
    }
  });

  guestBtn.addEventListener("click", function () {
    sessionStorage.setItem("currentUser", JSON.stringify({
      id: null,
      username: "Guest",
      email: "",
      isGuest: true,
      playHistory: []
    }));

    window.location.href = "dashboard.php";
  });

  function showError(message) {
    errorMessage.textContent = message;
    errorMessage.classList.remove("hidden");
  }
}