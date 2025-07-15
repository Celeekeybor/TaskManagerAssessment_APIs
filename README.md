# ✅ Task Management System

A full-featured **Task Management System** built using **PHP**, **MySQL**, and **Vanilla JavaScript** — styled with **Bootstrap 5**. This system was developed to meet the Cytonn Investments internship challenge and is designed for real-world team collaboration and productivity.

---

## 🚀 Features Overview

### 👩‍💼 Admin Capabilities
-  Login securely
-  Add new users
-  Edit existing users
-  Delete users (except fellow admins)
-  Assign tasks to users
-  Set task deadlines
-  View all task statuses in real-time
-  Sends **email notifications** on task assignment

### 👤 User Capabilities
-  Register their details
-  Login to dashboard
-  View assigned tasks
-  Update task status (`Pending`, `In Progress`, `Completed`)
-  Receive email when a new task is assigned

---

## 🛠️ Built With

| Technology   | Purpose                             |
|--------------|-------------------------------------|
| PHP (OOP)    | Backend logic and API                 |
| MySQL        | Relational database                 |
| Vanilla JS   | Frontend logic                      |
| Bootstrap 5  | UI styling                          |
| PHPMailer    | Sending email notifications         |

---

## 📁 Project Structure

```bash
taskmanager/
├── backend/
│   ├── Controllers/        # Logic for users, tasks
│   ├── Models/             # DB operations (User, Task, etc.)
│   ├── routes/             # API routing
│   ├── helpers/            # JWT, DB connection, mailer
│   ├── middleware/         # Auth middleware
│   └── index.php           # Entry point
│
├── frontend/
│   ├── admin.html          # Admin dashboard
│   ├── user.html           # User dashboard
│   ├── login.html          # Login form
│   ├── register.html       # Register new users
│   ├── css/                # Bootstrap or custom styles
│   └── js/                 # JavaScript logic (admin.js, user.js, etc.)
│
├── sql/
│   └── TaskManagerDBSchema.sql  # Full DB schema
│
└── README.md               # Project documentation
