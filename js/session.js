// ----------------------
// LOGOUT HANDLER
// ----------------------
document.addEventListener("DOMContentLoaded", () => {
  const logout = document.getElementById("logout");
  if (logout) {
    logout.addEventListener("click", () => {
      localStorage.clear();
      window.location.href = "index.html";
    });
  }
});


// ----------------------
// SESSION CHECK (runs immediately)
// ----------------------
(async function checkSession() {

  const username = localStorage.getItem("username");
  const token = localStorage.getItem("token");

  // Not logged in → redirect before page is shown
  if (!username || !token) {
    window.location.href = "index.html";
    return;
  }

  try {
    const request = await fetch("../backend/verify.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ username, token })
    });

    const response = await request.json();

    // Invalid session → redirect
    if (!response.ok) {
      window.location.href = "index.html";
      return;
    }

    // Save updated token + username
    localStorage.setItem("username", response.username);
    localStorage.setItem("token", response.token);

    // Session OK → show page
    document.body.style.display = "block";

  } catch (err) {
    console.error("ERROR:", err);
    window.location.href = "index.html";
  }

})();
