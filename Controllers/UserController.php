<?php
require_once '../models/User.php';
require_once '../helpers/auth_helper.php';

class UserController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function listUsers() {
        $userModel = new User($this->db);
        return $userModel->getAll();
    }

    public function createUser($data) {
        $userModel = new User($this->db);
        $username = $data['username'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $role = $data['role'] ?? 'User';

        if (!$username || !$email || !$password) {
            http_response_code(400);
            return ['message' => 'Missing fields'];
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        return $userModel->create($username, $email, $passwordHash, $role)
            ? ['message' => 'User created']
            : ['message' => 'Error creating user'];
    }

    public function updateUser($id, $data) {
        $userModel = new User($this->db);
        return $userModel->update($id, $data)
            ? ['message' => 'User updated']
            : ['message' => 'Update failed'];
    }

    public function deleteUser($id) {
        $userModel = new User($this->db);
        return $userModel->delete($id)
            ? ['message' => 'User deleted']
            : ['message' => 'Delete failed'];
    }
}
