<?php
require_once './db/connection.php';
require_once './controllers/UserController.php';
require_once   './helpers/auth_helper.php';


header("Content-Type: application/json");

// Authenticate and check if user is Admin
$token = getBearerToken();
$adminData = isAdmin($token);

if (!$adminData) {
    http_response_code(403);
    echo json_encode(['message' => 'Admin access required']);
    exit;
}

$userController = new UserController($db);

// Route based on HTTP method
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode($userController->listUsers());
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode($userController->createUser($data));
        break;

    case 'PUT':
        parse_str($_SERVER['QUERY_STRING'], $query);
        $id = $query['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['message' => 'User ID required']);
            break;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode($userController->updateUser($id, $data));
        break;

    case 'DELETE':
        parse_str($_SERVER['QUERY_STRING'], $query);
        $id = $query['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['message' => 'User ID required']);
            break;
        }

        echo json_encode($userController->deleteUser($id));
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
        break;
}
