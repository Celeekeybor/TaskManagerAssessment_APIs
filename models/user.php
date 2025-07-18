<?php
class User {
    private $conn;
    private $table = 'Users';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE Email = :email LIMIT 1");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($username, $email, $passwordHash, $role = 'User') {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (Username, Email, PasswordHash, Role) 
                                      VALUES (:username, :email, :passwordHash, :role)");
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":passwordHash", $passwordHash);
        $stmt->bindParam(":role", $role);
        return $stmt->execute();
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT UserID, Username, Email, Role, CreatedAt FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   public function update($id, $data) {
    $fields = [];
    $params = [];

    if (!empty($data['username'])) {
        $fields[] = "Username = ?";
        $params[] = $data['username'];
    }

    if (!empty($data['email'])) {
        $fields[] = "Email = ?";
        $params[] = $data['email'];
    }

    if (empty($fields)) {
        return false; // Nothing to update
    }

    $params[] = $id;
    $sql = "UPDATE Users SET " . implode(', ', $fields) . " WHERE UserID = ?";

    $stmt = $this->conn->prepare($sql);
    return $stmt->execute($params);
}


    public function findById($id) {
    $stmt = $this->conn->prepare("SELECT UserID, Username, Email, Role, CreatedAt FROM {$this->table} WHERE UserID = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE UserID = ?");
        return $stmt->execute([$id]);
    }


}



