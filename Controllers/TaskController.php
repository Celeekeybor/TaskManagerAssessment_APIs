<?php
require_once './models/Task.php';
require_once './helpers/mail_helper.php';

class TaskController {
    private $taskModel;

    public function __construct($db) {
        $this->taskModel = new Task($db);
    }

    /**
     * Admin assigns a task to multiple users
     */
  public function create($data, $authUser) {
    if ($authUser->role !== 'Admin') {
        http_response_code(403);
        echo json_encode(['message' => 'Only admins can assign tasks']);
        exit;
    }

    $title = trim($data['title'] ?? '');
    $description = trim($data['description'] ?? '');
    $deadline = trim($data['deadline'] ?? '');
    $assignedToUser = trim($data['user_id'] ?? '');

    if (!$title || !$assignedToUser) {
        http_response_code(400);
        echo json_encode(['message' => 'Title and user_id are required']);
        exit;
    }

    // Create the task
    $result = $this->taskModel->createTask(
        $title, $description, $deadline, $authUser->sub, $assignedToUser
    );

    // Send email
    try {
        $conn = $this->taskModel->getConnection();
        $stmt = $conn->prepare("SELECT UserID, Email, Username FROM Users WHERE UserID = ?");
        $stmt->execute([$assignedToUser]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && !empty($user['Email'])) {
            error_log("📧 Sending task email to: {$user['Email']}");
            $emailSent = sendTaskNotification(
                $user['Email'],
                $user['Username'] ?? 'Team Member',
                $title, $description, $deadline,
                $authUser->username
            );
            error_log($emailSent ? " Email sent" : " Failed to send email");
        }
    } catch (Exception $e) {
        error_log("❗ Email error: " . $e->getMessage());
    }

    // Respond to client
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}


    /**
     * Logged-in user fetches assigned tasks
     */
    public function getMyTasks($authUser) {
        return $this->taskModel->getTasksByUser($authUser->sub);
    }

    /**
     * User updates task status
     */
    public function updateStatus($taskId, $data, $authUser) {
        $status = $data['status'] ?? '';
        return $this->taskModel->updateTaskStatus($taskId, $authUser->sub, $status);
    }

    /**
     * Admin views tasks they created
     */
    public function getCreatedTasks($authUser) {
        if ($authUser->role !== 'Admin') {
            http_response_code(403);
            return ['message' => 'Access denied: only admins can view created tasks'];
        }

        return $this->taskModel->getTasksCreatedBy($authUser->sub);
    }
}
