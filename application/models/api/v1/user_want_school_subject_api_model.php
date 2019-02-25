<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Want_School_Subject_api_model extends CI_Model {

    public function get_user_want_school_subject($user_want_school_subject_id) {

        $this -> db -> where('user_want_school_subject_id', $user_want_school_subject_id);
        $result = $this -> db -> get('user_want_school_subject');
        return $result -> num_rows() > 0 ? $result -> row() : NULL;
    }

    public function insert_user_want_school_subject($insert_array) {

        $this -> db -> insert('user_want_school_subject', $insert_array);
        return $this -> db -> insert_id();
    }

    public function delete_user_want_school_subject($user_want_school_subject_id) {

        $this -> db -> where('user_want_school_subject_id', $user_want_school_subject_id);
        $this -> db -> delete('user_want_school_subject');
    }
}
