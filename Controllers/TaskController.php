<?php
require_once './models/Task.php';
require_once './helpers/mail_helper.php';



class TaskController {
    private $taskModel;

    public function __construct($db) {
        $this->taskModel = new Task($db);
    }

    // Admin assign a task
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
        
 if ($user && !empty($user['Email'])) {
        sendTaskNotification($user['Email'], $title);  // ✅ Send the email
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
    
    public function getCreatedTasks($authUser) {
    if ($authUser->role !== 'Admin') {
        http_response_code(403);
        return ['message' => 'Access denied: only admins can view created tasks'];
    }

    return $this->taskModel->getTasksCreatedBy($authUser->sub);
}

}
