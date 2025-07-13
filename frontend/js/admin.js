document.addEventListener("DOMContentLoaded", () => {
    // --- GLOBAL VARIABLES ---
    const token = localStorage.getItem('token');
    const API_BASE = "http://localhost/taskmanager/api";

    // --- ELEMENT SELECTORS (Matching your HTML) ---
    const userListTableBody = document.querySelector('#usersTable tbody');
    const taskListTableBody = document.querySelector('#tasksTable tbody');
    const addUserForm = document.getElementById('addUserForm');
    const assignTaskForm = document.getElementById('assignTaskForm');
    const userSelectDropdown = document.querySelector('select[name="user_id"]');
    
    // --- AUTHENTICATION & INITIALIZATION ---
    function initializeAdminDashboard() {
        if (!token) {
            window.location.href = 'login.html';
            return;
        }
        
        const username = localStorage.getItem('username') || 'Admin';
        document.getElementById('welcomeUser').textContent = `Welcome, ${username}!`;
        
        document.getElementById('logoutBtn').addEventListener('click', logout);

        loadAllUsers();
        loadAllTasks();

        addUserForm.addEventListener('submit', handleAddUser);
        assignTaskForm.addEventListener('submit', handleAssignTask);
        userListTableBody.addEventListener('click', handleDeleteUser);
    }

    // --- API HELPER FUNCTION ---
    async function apiFetch(endpoint, method = 'GET', body = null) {
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        };
        if (body) {
            options.body = JSON.stringify(body);
        }
        
        const response = await fetch(`${API_BASE}${endpoint}`, options);
        const responseData = await response.json();

        if (!response.ok) {
            throw new Error(responseData.message || `Request failed with status ${response.status}`);
        }
        return responseData;
    }

    // --- USER MANAGEMENT FUNCTIONS ---
    
    // ✅ THIS FUNCTION HAS BEEN CORRECTED
    async function loadAllUsers() {
        try {
            const response = await apiFetch('/users');
            const users = response.data || [];
            
            userListTableBody.innerHTML = '';
            userSelectDropdown.innerHTML = '<option value="">-- Select User --</option>';

            if (users.length === 0) {
                userListTableBody.innerHTML = '<tr><td colspan="3" class="text-center">No users found.</td></tr>';
                return;
            }

            users.forEach(user => {
                const row = document.createElement('tr');
                // Using lowercase keys to match what your API sends
                row.innerHTML = `
                    <td>${user.username}</td>
                    <td>${user.email}</td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-user-btn" data-user-id="${user.userid}">Delete</button>
                    </td>
                `;
                userListTableBody.appendChild(row);

                // Using lowercase keys here as well
                if (user.role !== 'Admin') {
                    const option = document.createElement('option');
                    option.value = user.userid;
                    option.textContent = user.username;
                    userSelectDropdown.appendChild(option);
                }
            });
        } catch (error) {
            console.error('Failed to load users:', error);
            userListTableBody.innerHTML = `<tr><td colspan="3" class="text-center text-danger">${error.message}</td></tr>`;
        }
    }
    
    async function handleAddUser(event) {
        event.preventDefault();
        
        const form = event.currentTarget;
        const formData = new FormData(form);
        const userData = Object.fromEntries(formData.entries());
        
        try {
            const result = await apiFetch('/admin/users', 'POST', {
                username: userData.name,
                email: userData.email,
                password: userData.password,
                role: 'User'
            });

            alert(result.message || 'User added successfully!');
            form.reset();
            loadAllUsers(); // This will now work correctly after adding a user
        } catch (error) {
            console.error('Error adding user:', error);
            alert(`Error: ${error.message}`);
        }
    }
    
    async function handleDeleteUser(event) {
        if (!event.target.classList.contains('delete-user-btn')) return;

        const userId = event.target.dataset.userId;
        if (!confirm(`Are you sure you want to delete user with ID: ${userId}?`)) return;
        
        try {
            const result = await apiFetch(`/users/${userId}`, 'DELETE');
            alert(result.message || 'User deleted successfully!');
            loadAllUsers();
        } catch (error) {
            console.error('Error deleting user:', error);
            alert(`Error: ${error.message}`);
        }
    }

    // --- TASK MANAGEMENT FUNCTIONS ---
    async function loadAllTasks() {
        try {
            const response = await apiFetch('/admin/tasks');
            const tasks = response.data || [];
            
            taskListTableBody.innerHTML = '';

            if (tasks.length === 0) {
                taskListTableBody.innerHTML = '<tr><td colspan="4" class="text-center">No tasks found.</td></tr>';
                return;
            }

            tasks.forEach(task => {
                const row = document.createElement('tr');
                // Assuming your tasks API sends PascalCase keys. Adjust if needed.
                row.innerHTML = `
                    <td>${task.Title}</td>
                    <td>${task.AssignedToUsername || 'N/A'}</td> 
                    <td><span class="badge bg-secondary">${task.Status}</span></td>
                    <td>${new Date(task.Deadline).toLocaleDateString()}</td>
                `;
                taskListTableBody.appendChild(row);
            });
        } catch (error) {
            console.error('Failed to load tasks:', error);
            taskListTableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">${error.message}</td></tr>`;
        }
    }

    async function handleAssignTask(event) {
        event.preventDefault();

        const form = event.target;
        const title = form.elements['title'].value;
        const description = form.elements['description'].value;
        const deadline = form.elements['deadline'].value;
        const assignedTo = form.elements['user_id'].value;

        try {
            const result = await apiFetch('/tasks', 'POST', {
                Title: title,
                Description: description,
                Deadline: deadline,
                AssignedTo: assignedTo
            });
            alert(result.message || 'Task assigned successfully!');
            form.reset();
            loadAllTasks();
        } catch (error) {
            console.error('Error assigning task:', error);
            alert(`Error: ${error.message}`);
        }
    }

    // --- LOGOUT FUNCTION ---
    function logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('username');
        window.location.href = 'login.html';
    }

    // --- START THE DASHBOARD ---
    initializeAdminDashboard();
});