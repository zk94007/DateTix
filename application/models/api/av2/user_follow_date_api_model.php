<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Follow_Date_api_model extends CI_Model {

    public function insert_user_follow_date($insert_array) {

        $this -> db -> insert('user_follow_date', $insert_array);
        return $this -> db -> insert_id();
    }

    public function do_follow_with_params($user_id, $date_id) {

        if ($this -> is_record_exists($user_id, $date_id) == TRUE) {

            $update_array['follow_time'] = SQL_DATETIME;
            $update_array['unfollow_time'] = NULL;

            $this -> db -> where('user_id', $user_id);
            $this -> db -> where('date_id', $date_id);

            $this -> db -> update('user_follow_date', $update_array);

        } else {

            $insert_array['user_id'] = $user_id;
            $insert_array['date_id'] = $date_id;
            $insert_array['follow_time'] = SQL_DATETIME;

            $this -> insert_user_follow_date($insert_array);
        }
    }

    public function do_unfollow_with_params($user_id, $date_id) {

        $update_array['unfollow_time'] = SQL_DATETIME;

        $this -> db -> where('user_id', $user_id);
        $this -> db -> where('date_id', $date_id);

        $this -> db -> update('user_follow_date', $update_array);
    }

    public function get_followed_date_ids_by_user_id($user_id) {

        $this -> db -> where('user_id', $user_id);
        $this -> db -> where('follow_time > unfollow_time');
        $result = $this -> db -> get('user_follow_date');

        $followed_date_ids = array();
        if ($result -> num_rows() > 0) {
            $records = $result -> result_array();
            foreach ($records as $record) {
                $followed_date_ids[] = $record['date_id'];
            }
        }
        return $followed_date_ids;
    }

    public function get_following_user_ids_by_date_id($date_id) {

        $this -> db -> where('date_id', $date_id);
        $this -> db -> where('follow_time > unfollow_time');
        $result = $this -> db -> get('user_follow_date');

        $following_user_ids = array();
        if ($result -> num_rows() > 0) {
            $records = $result -> result_array();
            foreach ($records as $record) {
                $following_user_ids[] = $record['user_id'];
            }
        }
        return $following_user_ids;
    }

    public function get_follow_time_with_params($user_id, $date_id) {

        $this -> db -> where('user_id', $user_id);
        $this -> db -> where('date_id', $date_id);
        $this -> db -> where('follow_time > unfollow_time');

        $result = $this -> db -> get('user_follow_date');

        return $result -> num_rows() > 0 ? $result -> row() -> follow_time : NULL;
    }

    private function is_record_exists($user_id, $date_id) {

        $this -> db -> where('user_id', $user_id);
        $this -> db -> where('date_id', $date_id);

        $result = $this -> db -> get('user_follow_date');

        return $result -> num_rows() > 0 ? TRUE : FALSE;
    }
}
