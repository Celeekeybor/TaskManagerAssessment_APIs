document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("registerForm").addEventListener("submit", registerUser);
});

const API_BASE = "http://localhost/taskmanager/index.php/api";

function registerUser(e) {
  e.preventDefault();

  const form = e.target;
  const data = {
    username: form.username.value.trim(),
    email: form.email.value.trim(),
    password: form.password.value.trim()
  };

  fetch(`${API_BASE}/register`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
  })
    .then(res => res.json())
    .then(result => {
      if (result.token) {
        // Save details in localStorage
        localStorage.setItem("token", result.token);
        localStorage.setItem("username", result.user?.username || "User");
        localStorage.setItem("email", result.user?.email || "");
        localStorage.setItem("role", result.user?.role || "User");

        // Redirect directly
        window.location.href = result.user?.role === "Admin" ? "admin.html" : "user.html";
      } else {
        alert(result.message || "Registration failed.");
      }
    })
    .catch(err => {
      console.error("Registration error:", err);
      alert("Something went wrong. Please try again.");
    });
}
