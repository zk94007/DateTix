<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Want_Child_Status_api_model extends CI_Model {

    public function get_user_want_child_statuses_by_user_id($user_id) {
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db ->get('user_want_child_status');

        $results = array();
        if ($result -> num_rows() > 0) {
            $results = $result -> result_array();
        }
        return $results;
    }

    public function get_child_status_ids_by_user_id($user_id) {

        $this -> db -> select('child_status_id');
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db -> get('user_want_child_status');

        if ($result -> num_rows() > 0) {

            $result_array = $result -> result_array();

            $child_status_ids = array();
            foreach ($result_array as $row) {
                $child_status_ids[] = $row['child_status_id'];
            }

            return $child_status_ids;
        }

        return NULL;
    }

    public function update_user_want_records_with_child_status_ids($user_id, $child_status_ids) {

        // Delete records whose 'child_status_id' is not in 'child_status_ids'.
        $this -> db -> where('user_id', $user_id);
        $this -> db -> where_not_in('child_status_id', $child_status_ids);
        $this -> db -> delete('user_want_child_status');

        // Insert new records
        foreach ($child_status_ids as $child_status_id) {

            if ($this -> db -> get_where('user_want_child_status',
                    array(
                        'user_id' => $user_id,
                        'child_status_id' => $child_status_id)) -> num_rows() == 0) {

                $insert_array['user_id'] = $user_id;
                $insert_array['child_status_id'] = $child_status_id;

                $this -> insert_user_want_child_status($insert_array);
            }
        }
    }

    private function insert_user_want_child_status($insert_array) {

        $this -> db -> insert('user_want_child_status', $insert_array);
        return $this -> db -> insert_id();
    }


}
