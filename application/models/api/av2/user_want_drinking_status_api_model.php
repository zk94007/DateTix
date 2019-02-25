<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Want_Drinking_Status_api_model extends CI_Model {

    public function get_user_want_drinking_statuses_by_user_id($user_id) {
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db ->get('user_want_drinking_status');

        $results = array();
        if ($result -> num_rows() > 0) {
            $results = $result -> result_array();
        }
        return $results;
    }

    public function get_drinking_status_ids_by_user_id($user_id) {

        $this -> db -> select('drinking_status_id');
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db -> get('user_want_drinking_status');

        if ($result -> num_rows() > 0) {

            $result_array = $result -> result_array();

            $drinking_status_ids = array();
            foreach ($result_array as $row) {
                $drinking_status_ids[] = $row['drinking_status_id'];
            }

            return $drinking_status_ids;
        }

        return NULL;
    }

    public function update_user_want_records_with_drinking_status_ids($user_id, $drinking_status_ids) {

        // Delete records whose 'drinking_status_id' is not in 'drinking_status_ids'.
        $this -> db -> where('user_id', $user_id);
        $this -> db -> where_not_in('drinking_status_id', $drinking_status_ids);
        $this -> db -> delete('user_want_drinking_status');

        // Insert new records
        foreach ($drinking_status_ids as $drinking_status_id) {

            if ($this -> db -> get_where('user_want_drinking_status',
                    array(
                        'user_id' => $user_id,
                        'drinking_status_id' => $drinking_status_id)) -> num_rows() == 0) {

                $insert_array['user_id'] = $user_id;
                $insert_array['drinking_status_id'] = $drinking_status_id;

                $this -> insert_user_want_drinking_status($insert_array);
            }
        }
    }

    private function insert_user_want_drinking_status($insert_array) {

        $this -> db -> insert('user_want_drinking_status', $insert_array);
        return $this -> db -> insert_id();
    }

}
