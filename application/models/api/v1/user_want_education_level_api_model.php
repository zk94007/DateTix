<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Want_Education_Level_api_model extends CI_Model {

    public function get_user_want_education_levels_by_user_id($user_id) {
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db ->get('user_want_education_level');

        $results = array();
        if ($result -> num_rows() > 0) {
            $results = $result -> result_array();
        }
        return $results;
    }

    public function get_education_level_ids_by_user_id($user_id) {

        $this -> db -> select('education_level_id');
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db -> get('user_want_education_level');

        if ($result -> num_rows() > 0) {

            $result_array = $result -> result_array();

            $education_level_ids = array();
            foreach ($result_array as $row) {
                $education_level_ids[] = $row['education_level_id'];
            }

            return $education_level_ids;
        }

        return NULL;
    }

    public function update_user_want_records_with_education_level_ids($user_id, $education_level_ids) {

        // Delete records whose 'education_level_id' is not in 'education_level_ids'.
        $this -> db -> where('user_id', $user_id);
        $this -> db -> where_not_in('education_level_id', $education_level_ids);
        $this -> db -> delete('user_want_education_level');

        // Insert new records
        foreach ($education_level_ids as $education_level_id) {

            if ($this -> db -> get_where('user_want_education_level',
                    array(
                        'user_id' => $user_id,
                        'education_level_id' => $education_level_id)) -> num_rows() == 0) {

                $insert_array['user_id'] = $user_id;
                $insert_array['education_level_id'] = $education_level_id;

                $this -> insert_user_want_education_level($insert_array);
            }
        }
    }

    private function insert_user_want_education_level($insert_array) {

        $this -> db -> insert('user_want_education_level', $insert_array);
        return $this -> db -> insert_id();
    }

}
