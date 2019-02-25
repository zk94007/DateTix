<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Want_Gender_api_model extends CI_Model {

    public function get_user_want_genders_by_user_id($user_id) {
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db ->get('user_want_gender');

        $results = array();
        if ($result -> num_rows() > 0) {
            $results = $result -> result_array();
        }
        return $results;
    }

    public function get_gender_ids_by_user_id($user_id) {

        $this -> db -> select('gender_id');
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db -> get('user_want_gender');

        if ($result -> num_rows() > 0) {

            $result_array = $result -> result_array();

            $gender_ids = array();
            foreach ($result_array as $row) {
                $gender_ids[] = $row['gender_id'];
            }

            return $gender_ids;
        }

        return NULL;
    }

    public function update_user_want_records_with_gender_ids($user_id, $gender_ids) {

        // Delete records whose 'gender_id' is not in 'gender_ids'.
        $this -> db -> where('user_id', $user_id);
        $this -> db -> where_not_in('gender_id', $gender_ids);
        $this -> db -> delete('user_want_gender');

        // Insert new records
        foreach ($gender_ids as $gender_id) {

            if ($this -> db -> get_where('user_want_gender',
                    array(
                        'user_id' => $user_id,
                        'gender_id' => $gender_id)) -> num_rows() == 0) {

                $insert_array['user_id'] = $user_id;
                $insert_array['gender_id'] = $gender_id;

                $this -> insert_user_want_gender($insert_array);
            }
        }
    }

    public function insert_user_want_gender($insert_array) {

        $this -> db -> insert('user_want_gender', $insert_array);
        return $this -> db -> insert_id();
    }

}
