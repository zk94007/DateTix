<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Visit_User_api_model extends CI_Model {

    public function insert_user_visit_user($insert_array) {

        $this -> db -> insert('user_visit_user', $insert_array);
        return $this -> db -> insert_id();
    }

    public function get_user_visit_users_by_visited_user_id($visited_user_id) {

        $this -> db -> where('visited_user_id', $visited_user_id);
        $result = $this -> db -> get('user_visit_user');

        return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
    }
}
