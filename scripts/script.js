window.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");
  const guestBtn = document.getElementById("guestBtn");
  const errorMessage = document.getElementById("errorMessage");
  // Redirect protected pages to login if no user is stored
  const currentUser = JSON.parse(sessionStorage.getItem("currentUser"));

  const protectedPages = [
    "dashboard.html",
    "quizCustomize.html",
    "quiz.html",
    "scores.html",
    "leaderboard.html"
  ];

  const currentPage = window.location.pathname.split("/").pop();

if (protectedPages.includes(currentPage) && !currentUser) {
  window.location.href = "login.html";
}

  // Stop the rest of the file from breaking on pages
  // that do not have the login form
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
      const response = await fetch("users_dummy.json");
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
      showError("Could not load user data. Make sure the JSON file is in the same folder.");
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
});