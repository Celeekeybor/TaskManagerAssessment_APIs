<?php
class Task {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createTask($title, $description, $deadline, $createdBy, $assignedTo) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("INSERT INTO Tasks (Title, Description, Deadline, CreatedBy) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $description, $deadline, $createdBy]);

            $taskId = $this->conn->lastInsertId();

            $assignStmt = $this->conn->prepare("INSERT INTO TaskAssignments (TaskID, UserID) VALUES (?, ?)");
            $assignStmt->execute([$taskId, $assignedTo]);

            $this->conn->commit();
            return ['message' => 'Task created and assigned', 'task_id' => $taskId];
        } catch (Exception $e) {
            $this->conn->rollBack();
            http_response_code(500);
            return ['message' => 'Task creation failed', 'error' => $e->getMessage()];
        }
    }

    public function getTasksByUser($userId) {
        $stmt = $this->conn->prepare("
            SELECT t.TaskID, t.Title, t.Description, t.Deadline, t.Status, t.CreatedAt
            FROM Tasks t
            JOIN TaskAssignments ta ON t.TaskID = ta.TaskID
            WHERE ta.UserID = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateTaskStatus($taskId, $userId, $status) {
        $allowed = ['Pending', 'In Progress', 'Completed'];
        if (!in_array($status, $allowed)) {
            http_response_code(400);
            return ['message' => 'Invalid status value'];
        }

        // Confirm the task is assigned to the user
        $stmt = $this->conn->prepare("
            SELECT * FROM TaskAssignments WHERE TaskID = ? AND UserID = ?
        ");
        $stmt->execute([$taskId, $userId]);

        if (!$stmt->fetch()) {
            http_response_code(403);
            return ['message' => 'Task not assigned to user'];
        }

        // Update task status
        $update = $this->conn->prepare("UPDATE Tasks SET Status = ? WHERE TaskID = ?");
        $update->execute([$status, $taskId]);

        return ['message' => 'Task status updated'];
    }

   public function getTasksCreatedBy($adminId) {
    $stmt = $this->conn->prepare("
        SELECT 
            t.TaskID, 
            t.Title, 
            t.Description, 
            t.Deadline, 
            t.Status, 
            t.CreatedAt,
            u.Username AS AssignedToUsername
        FROM Tasks t
        JOIN TaskAssignments ta ON t.TaskID = ta.TaskID
        JOIN Users u ON ta.UserID = u.UserID
        WHERE t.CreatedBy = ?
        ORDER BY t.CreatedAt DESC
    ");
    $stmt->execute([$adminId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}
