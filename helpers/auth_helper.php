<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function getBearerToken() {
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
        $matches = [];
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function isAdmin($token) {
    $secretKey = 'your_secret_key';
    try {
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
        return ($decoded->role === 'Admin') ? $decoded : false;
    } catch (Exception $e) {
        return false;
    }
}
