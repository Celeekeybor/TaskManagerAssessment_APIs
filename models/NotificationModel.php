<?php
class NotificationModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($taskId, $userId, $status = 'Sent') {
        $sql = "INSERT INTO Notifications (TaskID, UserID, Status) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$taskId, $userId, $status]);
    }
}
