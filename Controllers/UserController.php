<?php
require_once './models/User.php';

class UserController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    public function getAll($authUser) {
        if ($authUser->role !== 'Admin') {
            http_response_code(403);
            return ['message' => 'Access denied'];
        }

        return $this->userModel->getAll();
    }

    public function update($id, $data, $authUser) {
        // Admins can update anyone; users can only update themselves
        if ($authUser->role !== 'Admin' && $authUser->sub != $id) {
            http_response_code(403);
            return ['message' => 'Permission denied'];
        }

        if ($this->userModel->update($id, $data)) {
            return ['message' => 'User updated'];
        }

        http_response_code(500);
        return ['message' => 'Update failed'];
    }

    public function delete($id, $authUser) {
        if ($authUser->role !== 'Admin') {
            http_response_code(403);
            return ['message' => 'Only admins can delete users'];
        }

        if ($this->userModel->delete($id)) {
            return ['message' => 'User deleted'];
        }

        http_response_code(500);
        return ['message' => 'Deletion failed'];
    }
}
