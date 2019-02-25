<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Decision_Undo_Request_api_model extends CI_Model {

    public function insert_user_decision_undo_request($insert_array) {

        $this -> db -> insert('user_decision_undo_request', $insert_array);
        return $this -> db -> insert_id();
    }

    public function get_user_decision_undo_requests_count_with_params($user_id, $date) {

        $this -> db -> where('user_id', $user_id);
        $this -> db -> where('DATE(requested_time)', $date);
        $result = $this -> db -> get('user_decision_undo_request');

        return $result -> num_rows();
    }
}
