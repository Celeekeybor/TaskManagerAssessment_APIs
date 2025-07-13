<?php
require_once './db/connection.php';
require_once './controllers/AuthController.php';
require_once './controllers/UserController.php';
require_once './helpers/auth_middleware.php';
require_once './controllers/TaskController.php';


header("Content-Type: application/json");
$requestMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$db = (new Database())->connect();
$auth = new AuthController($db);
$userController = new UserController($db); 
$taskController = new TaskController($db);

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

// GET /api/users
if (preg_match('/\/api\/users$/', $uri) && $requestMethod === 'GET') {
    $authUser = checkAuth();
    echo json_encode($userController->getAll($authUser));
    exit;
}

// PUT /api/users/{id}
if (preg_match('/\/api\/users\/(\d+)$/', $uri, $matches) && $requestMethod === 'PUT') {
    $authUser = checkAuth();
    $userId = $matches[1];
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($userController->update($userId, $data, $authUser));
    exit;
}

// DELETE /api/users/{id}
if (preg_match('/\/api\/users\/(\d+)$/', $uri, $matches) && $requestMethod === 'DELETE') {
    $authUser = checkAuth();
    $userId = $matches[1];
    echo json_encode($userController->delete($userId, $authUser));
    exit;
}

// GET /api/users/{id}
if (preg_match('/\/api\/users\/(\d+)$/', $uri, $matches) && $requestMethod === 'GET') {
    $authUser = checkAuth();
    $userId = $matches[1];
    echo json_encode($userController->getById($userId, $authUser));
    exit;
}

// POST /api/tasks - Admin creates & assigns a task
if (preg_match('/\/api\/tasks$/', $uri) && $requestMethod === 'POST') {
    $authUser = checkAuth();
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($taskController->create($data, $authUser));
    exit;
}

// GET /api/tasks - User fetches their tasks
if (preg_match('/\/api\/tasks$/', $uri) && $requestMethod === 'GET') {
    $authUser = checkAuth();
    echo json_encode($taskController->getMyTasks($authUser));
    exit;
}

// PUT /api/tasks/{id} - User updates task status
if (preg_match('/\/api\/tasks\/(\d+)$/', $uri, $matches) && $requestMethod === 'PUT') {
    $authUser = checkAuth();
    $taskId = $matches[1];
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode($taskController->updateStatus($taskId, $data, $authUser));
    exit;
}


http_response_code(404);
echo json_encode(["message" => "Route not found"]);
