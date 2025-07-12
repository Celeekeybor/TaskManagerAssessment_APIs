<?php
require_once './db/connection.php';
require_once './controllers/AuthController.php';

header("Content-Type: application/json");
$requestMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$db = (new Database())->connect();
$auth = new AuthController($db);

// Route: /api/register
if (preg_match('/\/api\/register/', $uri) && $requestMethod === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($auth->register($data));
    exit;
}

// Route: /api/login
if (preg_match('/\/api\/login/', $uri) && $requestMethod === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($auth->login($data));
    exit;
}

http_response_code(404);
echo json_encode(["message" => "Route not found"]);
