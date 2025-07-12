<?php
require_once __DIR__ . '/jwt_helper.php'; // ensures validate_jwt() is available

function checkAuth() {
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["message" => "Missing Authorization header"]);
        exit;
    }

    $authHeader = $headers['Authorization'];
    $token = str_replace('Bearer ', '', $authHeader);

    $decoded = validate_jwt($token);

    if (!$decoded) {
        http_response_code(401);
        echo json_encode(["message" => "Invalid or expired token"]);
        exit;
    }

    return $decoded; 
}
