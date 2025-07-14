document.addEventListener("DOMContentLoaded", () => {
  const token = localStorage.getItem("token");

  // --- 1. Authentication Check ---
  // If there's no token, the user is not logged in. Redirect immediately.
  if (!token) {
    window.location.href = "login.html";
    return; // Stop the rest of the script from running
  }

  // --- 2. Setup the Dashboard ---
  // Set the personalized welcome message
  const username = localStorage.getItem("username") || "User";
  document.getElementById("welcomeMessage").innerText = `Welcome, ${username}!`;

  // Add the event listener for the logout button
  document.getElementById("logoutBtn").addEventListener("click", logout);
  
  // --- 3. Load the User's Tasks ---
  loadTasks(token);
});

// --- API Base URL ---
// Make sure this path is correct for your server setup.
const API_BASE = "http://localhost/taskmanager/api";

/**
 * Fetches tasks from the API and populates the table.
 * @param {string} token - The user's authentication token.
 */
function loadTasks(token) {
  const tableBody = document.querySelector("#tasksTable tbody");

  fetch(`${API_BASE}/tasks`, {
    method: 'GET',
    headers: {
      "Authorization": `Bearer ${token}`
    }
  })
  .then(res => {
    if (!res.ok) {
      throw new Error('Could not get tasks. Please try logging in again.');
    }
    return res.json();
  })
  .then(response => {
    const tasks = Array.isArray(response) ? response : [];
    console.log("response:", response);

    tableBody.innerHTML = "";

    // if (tasks.length === 0) {
      // tableBody.innerHTML = '<tr><td colspan="4" class="text-center p-4">You have no tasks assigned. Great job!</td></tr>';
      // return;
    // }

    tasks.forEach(task => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${task.Title}</td>
        <td>${task.Description}</td>
        <td>${new Date(task.Deadline).toLocaleDateString()}</td>
        <td>
          <select class="form-select form-select-sm status-dropdown" data-task-id="${task.TaskID}">
            <option value="Pending">Pending</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
          </select>
        </td>
      `;

      const statusDropdown = row.querySelector('.status-dropdown');
      statusDropdown.value = task.Status;
      setDropdownColor(statusDropdown, task.Status);

      statusDropdown.addEventListener('change', (event) => {
        const newStatus = event.target.value;
        const taskId = event.target.dataset.taskId;
        updateStatus(taskId, newStatus, token, event.target);
      });

      tableBody.appendChild(row);
    });
  })
  .catch(err => {
    console.error("Error in loadTasks:", err);
    tableBody.innerHTML = `
      <tr>
        <td colspan="4" class="text-center text-danger p-4">
          <strong>Oops! Something went wrong.</strong><br>
          <small>${err.message}</small>
        </td>
      </tr>`;
  });
}


/**
 * Updates the status of a specific task via an API call.
 * @param {number} taskId - The ID of the task to update.
 * @param {string} newStatus - The new status ('Pending', 'In Progress', 'Completed').
 * @param {string} token - The user's authentication token.
 * @param {HTMLElement} dropdownElement - The <select> element to apply color to.
 */
function updateStatus(taskId, newStatus, token, dropdownElement) {
  // This URL for updating status must also match your api.php file
  fetch(`${API_BASE}/tasks/${taskId}`, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
      "Authorization": `Bearer ${token}`
    },
    body: JSON.stringify({ status: newStatus })
  })
  .then(res => {
    if (!res.ok) throw new Error('Failed to update status on the server.');
    return res.json();
  })
  .then(result => {
    console.log("Status updated successfully:", result.message || newStatus);
    // Give the user instant visual feedback
    setDropdownColor(dropdownElement, newStatus);
  })
  .catch(err => {
    console.error("Error in updateStatus:", err);
    alert("Could not update task status. Please refresh and try again.");
  });
}

/**
 * A helper function to change the color of the dropdown based on status.
 * @param {HTMLElement} element - The <select> dropdown element.
 * @param {string} status - The current status of the task.
 */
function setDropdownColor(element, status) {
    element.classList.remove('bg-warning', 'bg-primary', 'bg-success', 'text-white');
    if (status === 'Pending') {
        element.classList.add('bg-warning');
    } else if (status === 'In Progress') {
        element.classList.add('bg-primary', 'text-white');
    } else if (status === 'Completed') {
        element.classList.add('bg-success', 'text-white');
    }
}

/**
 * Clears user data from localStorage and redirects to the login page.
 */
function logout() {
  localStorage.removeItem("token");
  localStorage.removeItem("username");
  window.location.href = "login.html";
}