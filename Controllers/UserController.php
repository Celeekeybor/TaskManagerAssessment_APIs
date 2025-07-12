<?php
require_once './models/User.php';

class UserController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    public function getAll($authUser) {
        if ($authUser->role !== 'Admin') {
            http_response_code(403);
            return ['message' => 'Access denied'];
        }

        return $this->userModel->getAll();
    }
    public function getById($id, $authUser) {
        $user = $this->userModel->findById($id);
        if (!$user) {
            http_response_code(404);
            return ["message" => "User not found"];
        }

        // Optional: Only allow admin or the user themselves
        if ($authUser->role !== 'Admin' && $authUser->sub != $id) {
            http_response_code(403);
            return ["message" => "Unauthorized"];
        }

        // Don't return password hash
        unset($user['PasswordHash']);
        return $user;
    }

    public function update($id, $data, $authUser) {
        // Admins can update anyone; users can only update themselves
        if ($authUser->role !== 'Admin' && $authUser->sub != $id) {
            http_response_code(403);
            return ['message' => 'Permission denied'];
        }

        if ($this->userModel->update($id, $data)) {
            return ['message' => 'User updated'];
        }

        http_response_code(500);
        return ['message' => 'Update failed'];
    }

    public function delete($id, $authUser) {
        if ($authUser->role !== 'Admin') {
            http_response_code(403);
            return ['message' => 'Only admins can delete users'];
        }

        if ($this->userModel->delete($id)) {
            return ['message' => 'User deleted'];
        }

        http_response_code(500);
        return ['message' => 'Deletion failed'];
    }

    public function create($data, $authUser) {
    if ($authUser->role !== 'Admin') {
        http_response_code(403);
        return ['message' => 'Only admins can create users'];
    }

    $username = $data['username'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $role = $data['role'] ?? 'User';

    if (!$username || !$email || !$password) {
        http_response_code(400);
        return ['message' => 'Username, email, and password are required'];
    }

    if (!in_array($role, ['User', 'Admin'])) {
        http_response_code(400);
        return ['message' => 'Invalid role'];
    }

    $userModel = $this->userModel;
    if ($userModel->findByEmail($email)) {
        http_response_code(409);
        return ['message' => 'Email already exists'];
    }

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    if ($userModel->create($username, $email, $passwordHash, $role)) {
        return ['message' => 'User created successfully'];
    }

    http_response_code(500);
    return ['message' => 'Failed to create user'];
}

}
