<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $db;
    private $userModel;

    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new User($this->db);
    }

    /**
     * Gets all users. (Admin only)
     * Returns a consistent JSON structure for the frontend.
     */
    public function getAll($authUser) {
        if ($authUser['role'] !== 'Admin') {
            http_response_code(403); // Forbidden
            return ['success' => false, 'message' => 'Access denied: Admin role required.'];
        }

        try {
            $users = $this->userModel->getAll();
            
            // ✅ THE KEY FIX: Always wrap the result in a success/data structure.
            // This is what solves the "not listing" problem.
            http_response_code(200);
            return [
                'success' => true,
                'data' => $users
            ];
        } catch (Exception $e) {
            http_response_code(500);
            return ['success' => false, 'message' => 'Failed to retrieve users.'];
        }
    }

    /**
     * Gets a single user by their ID.
     */
    public function getById($id, $authUser) {
        // Security check: Admins can get anyone, users can only get themselves.
        if ($authUser['role'] !== 'Admin' && $authUser['id'] != $id) {
            http_response_code(403);
            return ["success" => false, "message" => "Unauthorized to view this user."];
        }
        
        $user = $this->userModel->findById($id);
        if (!$user) {
            http_response_code(404);
            return ["success" => false, "message" => "User not found."];
        }

        // Don't return the password hash to the frontend.
        unset($user['passwordhash']);
        
        return ['success' => true, 'data' => $user];
    }

    /**
     * Creates a new user. (Admin only)
     * Now returns a consistent JSON structure.
     */
    public function create($data, $authUser) {
        if ($authUser['role'] !== 'Admin') {
            http_response_code(403);
            return ['success' => false, 'message' => 'Only admins can create users.'];
        }

        $username = $data['username'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $role = $data['role'] ?? 'User';

        if (empty($username) || empty($email) || empty($password)) {
            http_response_code(400); // Bad Request
            return ['success' => false, 'message' => 'Username, email, and password are required.'];
        }
        
        if ($this->userModel->findByEmail($email) || $this->userModel->findByUsername($username)) {
            http_response_code(409); // Conflict
            return ['success' => false, 'message' => 'A user with this email or username already exists.'];
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        if ($this->userModel->create($username, $email, $passwordHash, $role)) {
            http_response_code(201); // Created
            return ['success' => true, 'message' => 'User created successfully.'];
        }

        http_response_code(500);
        return ['success' => false, 'message' => 'Failed to create user due to a server error.'];
    }

    /**
     * Updates a user's details.
     */
    public function update($id, $data, $authUser) {
        if ($authUser['role'] !== 'Admin' && $authUser['id'] != $id) {
            http_response_code(403);
            return ['success' => false, 'message' => 'Permission denied.'];
        }
        
        // You should add more specific update logic here based on what fields can be changed.
        if ($this->userModel->update($id, $data)) {
            return ['success' => true, 'message' => 'User updated successfully.'];
        }

        http_response_code(500);
        return ['success' => false, 'message' => 'Failed to update user.'];
    }

    /**
     * Deletes a user. (Admin only)
     */
    public function delete($id, $authUser) {
        if ($authUser['role'] !== 'Admin') {
            http_response_code(403);
            return ['success' => false, 'message' => 'Only admins can delete users.'];
        }
        
        // Prevent an admin from deleting themselves
        if ($authUser['id'] == $id) {
            http_response_code(400);
            return ['success' => false, 'message' => 'You cannot delete your own admin account.'];
        }

        if ($this->userModel->delete($id)) {
            return ['success' => true, 'message' => 'User deleted successfully.'];
        }

        http_response_code(500);
        return ['success' => false, 'message' => 'Failed to delete user.'];
    }
}