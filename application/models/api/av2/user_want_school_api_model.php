<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Want_School_api_model extends CI_Model {

    public function get_user_want_schools_by_user_id($user_id) {
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db ->get('user_want_school');

        $results = array();
        if ($result -> num_rows() > 0) {
            $results = $result -> result_array();
        }
        return $results;
    }

    public function get_school_ids_by_user_id($user_id) {

        $this -> db -> select('school_id');
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db -> get('user_want_school');

        if ($result -> num_rows() > 0) {

            $result_array = $result -> result_array();

            $school_ids = array();
            foreach ($result_array as $row) {
                $school_ids[] = $row['school_id'];
            }

            return $school_ids;
        }

        return NULL;
    }

    public function get_user_want_school($user_want_school_id) {

        $this -> db -> where('user_want_school_id', $user_want_school_id);
        $result = $this -> db -> get('user_want_school');
        return $result -> num_rows() > 0 ? $result -> row() : NULL;
    }

    public function insert_user_want_school($insert_array) {

        $this -> db -> insert('user_want_school', $insert_array);
        return $this -> db -> insert_id();
    }

    public function delete_user_want_school($user_want_school_id) {

        $this -> db -> where('user_want_school_id', $user_want_school_id);
        $this -> db -> delete('user_want_school');
    }

}
