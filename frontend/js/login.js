document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("loginForm").addEventListener("submit", loginUser);
});

const API_BASE = "http://localhost/taskmanager/index.php/api";

function loginUser(e) {
  e.preventDefault();

  const form = e.target;
  const data = {
    email: form.email.value.trim(),
    password: form.password.value.trim()
  };

  fetch(`${API_BASE}/login`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
  })
    .then(res => res.json())
    .then(result => {
      if (result.token) {
        // Save user details in localStorage
        localStorage.setItem("token", result.token);
        localStorage.setItem("username", result.user?.username || "User");
        localStorage.setItem("email", result.user?.email || "");
        localStorage.setItem("role", result.user?.role || "User");

        // Redirect based on role
        window.location.href = result.user?.role === "Admin" ? "admin.html" : "user.html";
      } else {
        alert(result.message || "Login failed.");
      }
    })
    .catch(err => {
      console.error("Login error:", err);
      alert("Something went wrong. Please try again.");
    });
}
