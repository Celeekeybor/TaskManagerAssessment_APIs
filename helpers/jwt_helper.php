<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

function generate_jwt($user) {
    $secretKey = 'your_secret_key'; 
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600;

    $payload =$issuedAt = time();
$expirationTime = $issuedAt + 3600;

$payload = [
    'iss' => 'http://localhost/taskmanager',
    'aud' => 'http://localhost',
    'iat' => $issuedAt,
    'exp' => $expirationTime,
    'sub' => $user['UserID'],
    'username' => $user['Username'],
    'email' => $user['Email'],
    'role' => $user['Role']
];

    return JWT::encode($payload, $secretKey, 'HS256');
}

function validate_jwt($token) {
    $secretKey = 'your_secret_key';

    try {
        return JWT::decode($token, new Key($secretKey, 'HS256'));
    } catch (Exception $e) {
        return null;
    }
}
