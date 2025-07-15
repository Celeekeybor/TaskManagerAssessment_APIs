document.addEventListener("DOMContentLoaded", () => {
    // --- GLOBAL VARIABLES ---
    const token = localStorage.getItem('token');
    const API_BASE = "http://localhost/taskmanager/api";

    // --- ELEMENT SELECTORS ---
    const userListTableBody = document.querySelector('#usersTable tbody');
    const taskListTableBody = document.querySelector('#tasksTable tbody');
    const addUserForm = document.getElementById('addUserForm');
    const assignTaskForm = document.getElementById('assignTaskForm');
    const userSelectDropdown = document.querySelector('select[name="user_id"]');
    

    const editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
const editUserForm = document.getElementById('editUserForm');
const editUsernameInput = document.getElementById('editUsername');
const editEmailInput = document.getElementById('editEmail');
const editUserIdInput = document.getElementById('editUserId');


    // --- AUTH & INIT ---
    function initializeAdminDashboard() {
        if (!token) {
            window.location.href = 'login.html';
            return;
        }

        const username = localStorage.getItem('username') || 'Admin';
        document.getElementById('welcomeUser').textContent = `Welcome, ${username}!`;

        document.getElementById('logoutBtn').addEventListener('click', logout);
        addUserForm.addEventListener('submit', handleAddUser);
        assignTaskForm.addEventListener('submit', handleAssignTask);
        userListTableBody.addEventListener('click', handleUserActions);

        editUserForm.addEventListener('submit', async function (e) {
    e.preventDefault();

    const userId = editUserIdInput.value;
    const newUsername = editUsernameInput.value.trim();
    const newEmail = editEmailInput.value.trim();

    try {
        await apiFetch(`/users/${userId}`, 'PUT', {
            username: newUsername,
            email: newEmail
        });
        editUserModal.hide();
        loadAllUsers();
    } catch (error) {
        alert(`Update failed: ${error.message}`);
    }
});



        loadAllUsers();
        loadAllTasks();
    }

    // --- API HELPER ---
    async function apiFetch(endpoint, method = 'GET', body = null) {
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        };
        if (body) options.body = JSON.stringify(body);

        const response = await fetch(`${API_BASE}${endpoint}`, options);
        if (response.status === 401) {
            logout();
            throw new Error('Session expired.');
        }

        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Request failed.');
        return data;
    }

    // --- USERS ---
    async function loadAllUsers() {
        try {
            const response = await apiFetch('/users');
            const users = Array.isArray(response.data) ? response.data : [];

            userListTableBody.innerHTML = '';
            userSelectDropdown.innerHTML = '<option value="">-- Select User --</option>';

            if (users.length === 0) {
                userListTableBody.innerHTML = '<tr><td colspan="4" class="text-center">No users found.</td></tr>';
                return;
            }

     users.forEach(user => {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${user.UserID}</td>
        <td>${user.Username}</td>
        <td>${user.Email}</td>
        <td>
            <button class="btn btn-primary btn-sm edit-user-btn" data-user-id="${user.UserID}">Edit</button>
            <button class="btn btn-danger btn-sm delete-user-btn" data-user-id="${user.UserID}">Delete</button>
        </td>
    `;
    userListTableBody.appendChild(row);

    if (user.Role !== 'Admin') {
        const option = document.createElement('option');
        option.value = user.UserID;
        option.textContent = user.Username;
        userSelectDropdown.appendChild(option);
    }
});

        } catch (error) {
            console.error('Failed to load users:', error);
            userListTableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">${error.message}</td></tr>`;
        }
    }

    async function handleAddUser(event) {
        event.preventDefault();

        const form = event.currentTarget;
        const formData = new FormData(form);
        const userData = Object.fromEntries(formData.entries());

        try {
            await apiFetch('/admin/users', 'POST', {
                username: userData.name,
                email: userData.email,
                password: userData.password,
                role: 'User'
            });
            form.reset();
            loadAllUsers();
        } catch (error) {
            console.error('Error adding user:', error);
            alert(`Error: ${error.message}`);
        }
    }

   function handleUserActions(event) {
    const target = event.target;
    const userId = target.dataset.userId;

    if (target.classList.contains('delete-user-btn')) {
        if (confirm(`Are you sure you want to delete user with ID: ${userId}?`)) {
            deleteUser(userId);
        }
    }

    if (target.classList.contains('edit-user-btn')) {
        openEditUserModal(userId);
    }
}

function openEditUserModal(userId) {
    // Fetch the current user info from table (or make API call if needed)
    const row = document.querySelector(`button[data-user-id="${userId}"]`).closest('tr');
    const currentName = row.children[1].textContent;
    const currentEmail = row.children[2].textContent;

    editUserIdInput.value = userId;
    editUsernameInput.value = currentName;
    editEmailInput.value = currentEmail;

    editUserModal.show();
}


    async function deleteUser(userId) {
        try {
            await apiFetch(`/users/${userId}`, 'DELETE');
            loadAllUsers();
        } catch (error) {
            console.error('Error deleting user:', error);
            alert(`Error: ${error.message}`);
        }
    }

    // --- TASKS ---
 async function loadAllTasks() {
    try {
        const response = await apiFetch('/admin/tasks');
        console.log('Tasks response:', response);

        const tasks = Array.isArray(response) ? response : [];

        taskListTableBody.innerHTML = '';

        if (tasks.length === 0) {
            taskListTableBody.innerHTML = '<tr><td colspan="4" class="text-center">No tasks found.</td></tr>';
            return;
        }

        tasks.forEach(task => {
            let badgeClass = 'bg-secondary';

            // Set badge color based on status
            switch (task.Status.toLowerCase()) {
                case 'completed':
                    badgeClass = 'bg-success';
                    break;
                case 'pending':
                    badgeClass = 'bg-warning text-dark';
                    break;
                case 'in progress':
                    badgeClass = 'bg-primary';
                    break;
            }

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${task.Title}</td>
                <td>${task.AssignedToUsername || 'N/A'}</td>
                <td><span class="badge ${badgeClass}">${task.Status}</span></td>
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
            await apiFetch('/tasks', 'POST', {
    title: title,
    description: description,
    deadline: deadline,
    user_id: assignedTo
});

            form.reset();
            loadAllTasks();
        } catch (error) {
            console.error('Error assigning task:', error);
            alert(`Error: ${error.message}`);
        }
    }

    // --- LOGOUT ---
    function logout() {
        localStorage.clear();
        window.location.href = 'login.html';
    }

    // INIT
    initializeAdminDashboard();
});
