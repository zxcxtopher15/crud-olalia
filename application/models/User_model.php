<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function insertUser($data) {
        return $this->db->insert('users', $data);
    }

    public function getAllUsers() {
        $query = $this->db->get('users');
        return $query->result_array();
    }

    public function deleteUser($userId) {
        return $this->db->delete('users', array('id' => $userId));
    }

    public function updateUser($userId, $data) {
        $this->db->where('id', $userId);
        return $this->db->update('users', $data);
    }

    public function getUserById($userId) {
        $query = $this->db->get_where('users', array('id' => $userId));
        return $query->row_array();
    }
}
