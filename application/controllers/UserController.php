<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function index() {
        $view_data['record'] = $this->User_model->getAllUsers();
        $this->load->view('user_form', $view_data);
    }

    public function insertUser() {
        $data = array(
            'username' => $this->input->post('username'),
            'email' => $this->input->post('email'),
            'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT)
        );
    
        $inserted = $this->User_model->insertUser($data);
    
        if ($inserted) {
            echo json_encode(array('success' => true, 'message' => 'User inserted successfully'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Failed to insert user'));
        }
    }

    public function getAllUsers() {
        $users = $this->User_model->getAllUsers();
        echo json_encode($users);
    }
    
    public function deleteUser($userId) {
        $deleted = $this->User_model->deleteUser($userId);
        echo json_encode(array('success' => $deleted, 'message' => 'User deleted successfully'));
    }

    public function updateUser($userId) {
        $data = array(
            'username' => $this->input->post('username'),
            'email' => $this->input->post('email'),
            'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT)
        );

        $updated = $this->User_model->updateUser($userId, $data);

        if ($updated) {
            echo json_encode(array('success' => true, 'message' => 'User updated successfully'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Failed to update user'));
        }
    }

    public function editUser($userId) {
        $user = $this->User_model->getUserById($userId);
        echo json_encode($user);
    }
}
