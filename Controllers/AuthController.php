<?php
require_once 'models/User.php';
require_once 'helpers/jwt_helper.php';
 require_once 'vendor/autoload.php';

class AuthController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function register($data) {
        $userModel = new User($this->db);

        $username = $data['username'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (!$username || !$email || !$password) {
            http_response_code(400);
            return ['message' => 'All fields are required'];
        }

        if ($userModel->findByEmail($email)) {
            http_response_code(409);
            return ['message' => 'Email already exists'];
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        if ($userModel->create($username, $email, $passwordHash)) {
            return ['message' => 'User registered successfully'];
        }

        http_response_code(500);
        return ['message' => 'Registration failed'];
    }

    public function login($data) {
    $userModel = new User($this->db);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    if (!$email || !$password) {
        http_response_code(400);
        return ['message' => 'Email and password are required'];
    }

    $user = $userModel->findByEmail($email);
    if ($user && password_verify($password, $user['PasswordHash'])) {
        $token = generate_jwt($user);

        // Clean up user data (remove sensitive info like password hash)
        $userInfo = [
            'username' => $user['Username'],
            'email' => $user['Email'],
            'role' => $user['Role'], 
        ];

        return [
            'message' => 'Login successful',
            'token' => $token,
            'user' => $userInfo
        ];
    }

    http_response_code(401);
    return ['message' => 'Invalid credentials'];
}

}
