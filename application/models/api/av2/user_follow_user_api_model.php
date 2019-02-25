<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Follow_User_api_model extends CI_Model {

    public function insert_user_follow_user($insert_array) {

        $this -> db -> insert('user_follow_user', $insert_array);
        return $this -> db -> insert_id();
    }

    public function do_follow_with_params($user_id, $followed_user_id) {

        if ($this -> is_record_exists($user_id, $followed_user_id) == TRUE) {

            $update_array['follow_time'] = SQL_DATETIME;
            $update_array['unfollow_time'] = NULL;

            $this -> db -> where('user_id', $user_id);
            $this -> db -> where('follow_user_id', $followed_user_id);

            $this -> db -> update('user_follow_user', $update_array);

        } else {

            $insert_array['user_id'] = $user_id;
            $insert_array['follow_user_id'] = $followed_user_id;
            $insert_array['follow_time'] = SQL_DATETIME;

            $this -> insert_user_follow_user($insert_array);
        }
    }

    public function do_unfollow_with_params($user_id, $followed_user_id) {

        $update_array['unfollow_time'] = SQL_DATETIME;

        $this -> db -> where('user_id', $user_id);
        $this -> db -> where('follow_user_id', $followed_user_id);

        $this -> db -> update('user_follow_user', $update_array);
    }

    public function get_followed_friend_ids_by_user_id($user_id) {

        $this -> db -> where('user_id', $user_id);
        $this -> db -> where('follow_time > unfollow_time');
        $result = $this -> db -> get('user_follow_user');

        $followed_friend_ids = array();
        if ($result -> num_rows() > 0) {
            $records = $result -> result_array();
            foreach ($records as $record) {
                $followed_friend_ids[] = $record['follow_user_id'];
            }
        }
        return $followed_friend_ids;
    }

    public function get_user_follow_users_by_user_id($user_id) {

        $this -> db -> where('user_id', $user_id);
        $this -> db -> where('follow_time > unfollow_time');
        $result = $this -> db -> get('user_follow_user');

        return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
    }

    public function get_user_follow_users_by_follow_user_id($follow_user_id) {

        $this -> db -> where('follow_user_id', $follow_user_id);
        $this -> db -> where('follow_time > unfollow_time');
        $result = $this -> db -> get('user_follow_user');

        return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
    }

    public function get_follow_time_with_params($user_id, $followed_user_id) {

        $this -> db -> where('user_id', $user_id);
        $this -> db -> where('follow_user_id', $followed_user_id);
        $this -> db -> where('follow_time > unfollow_time');

        $result = $this -> db -> get('user_follow_user');

        return $result -> num_rows() > 0 ? $result -> row() -> follow_time : NULL;
    }

    private function is_record_exists($user_id, $followed_user_id) {

        $this -> db -> where('user_id', $user_id);
        $this -> db -> where('follow_user_id', $followed_user_id);

        $result = $this -> db -> get('user_follow_user');

        return $result -> num_rows() > 0 ? TRUE : FALSE;
    }
}
