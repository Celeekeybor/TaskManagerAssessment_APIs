<?php
require_once './models/Task.php';

class TaskController {
    private $taskModel;

    public function __construct($db) {
        $this->taskModel = new Task($db);
    }

    // Admin creates a task
    public function create($data, $authUser) {
        if ($authUser->role !== 'Admin') {
            http_response_code(403);
            return ['message' => 'Only admins can assign tasks'];
        }

        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $deadline = $data['deadline'] ?? '';
        $assignedTo = $data['user_id'] ?? '';

        if (!$title || !$assignedTo) {
            http_response_code(400);
            return ['message' => 'Title and user_id are required'];
        }

        return $this->taskModel->createTask($title, $description, $deadline, $authUser->sub, $assignedTo);
    }

    // User gets their tasks
    public function getMyTasks($authUser) {
        return $this->taskModel->getTasksByUser($authUser->sub);
    }

    // User updates task status
    public function updateStatus($taskId, $data, $authUser) {
        $status = $data['status'] ?? '';
        return $this->taskModel->updateTaskStatus($taskId, $authUser->sub, $status);
    }
}
