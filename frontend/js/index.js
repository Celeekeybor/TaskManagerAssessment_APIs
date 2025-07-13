document.addEventListener("DOMContentLoaded", () => {
  const token = localStorage.getItem("token");

  if (!token) {
    window.location.href = "login.html";
  } else {
    // Optionally decode token or rely on a small role API
    // Example: redirect based on stored role if you save it in localStorage
    const role = localStorage.getItem("role");

    if (role === "Admin") {
      window.location.href = "admin.html";
    } else {
      window.location.href = "user.html";
    }
  }
});
