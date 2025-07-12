<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

function generate_jwt($user) {
    $secretKey = 'your_secret_key'; 
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600; // Token valid for 1 hour (3600 seconds)

    $payload = [
        'iss' => 'http://localhost/taskmanager', // Issuer
        'aud' => 'http://localhost',             // Audience
        'iat' => $issuedAt,                      // Issued at
        'exp' => $expirationTime,                // Expiry
        'sub' => $user['UserID'],                // Subject (user ID)
        'username' => $user['Username'],
        'email' => $user['Email'],
        'role' => $user['Role']
    ];

    return JWT::encode($payload, $secretKey, 'HS256');
}
