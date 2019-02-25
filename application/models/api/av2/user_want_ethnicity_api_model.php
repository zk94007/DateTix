<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Want_Ethnicity_api_model extends CI_Model {

    public function get_user_want_ethnicities_by_user_id($user_id) {
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db ->get('user_want_ethnicity');

        $results = array();
        if ($result -> num_rows() > 0) {
            $results = $result -> result_array();
        }
        return $results;
    }

    public function get_ethnicity_ids_by_user_id($user_id) {

        $this -> db -> select('ethnicity_id');
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db -> get('user_want_ethnicity');

        if ($result -> num_rows() > 0) {

            $result_array = $result -> result_array();

            $ethnicity_ids = array();
            foreach ($result_array as $row) {
                $ethnicity_ids[] = $row['ethnicity_id'];
            }

            return $ethnicity_ids;
        }

        return NULL;
    }

    public function update_user_want_records_with_ethnicity_ids($user_id, $ethnicity_ids) {

        // Delete records whose 'ethnicity_id' is not in 'ethnicity_ids'.
        $this -> db -> where('user_id', $user_id);
        $this -> db -> where_not_in('ethnicity_id', $ethnicity_ids);
        $this -> db -> delete('user_want_ethnicity');

        // Insert new records
        foreach ($ethnicity_ids as $ethnicity_id) {

            if ($this -> db -> get_where('user_want_ethnicity',
                    array(
                        'user_id' => $user_id,
                        'ethnicity_id' => $ethnicity_id)) -> num_rows() == 0) {

                $insert_array['user_id'] = $user_id;
                $insert_array['ethnicity_id'] = $ethnicity_id;

                $this -> insert_user_want_ethnicity($insert_array);
            }
        }
    }

    private function insert_user_want_ethnicity($insert_array) {

        $this -> db -> insert('user_want_ethnicity', $insert_array);
        return $this -> db -> insert_id();
    }

    public function delete_user_want_ethnicities_by_user_id($user_id) {

        $this -> db -> where('user_id', $user_id);
        $this -> db -> delete('user_want_ethnicity');
    }
}
