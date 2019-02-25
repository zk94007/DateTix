<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Want_Residence_Type_api_model extends CI_Model {

    public function get_user_want_residence_types_by_user_id($user_id) {
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db ->get('user_want_residence_type');

        $results = array();
        if ($result -> num_rows() > 0) {
            $results = $result -> result_array();
        }
        return $results;
    }

    public function get_residence_type_ids_by_user_id($user_id) {

        $this -> db -> select('residence_type_id');
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db -> get('user_want_residence_type');

        if ($result -> num_rows() > 0) {

            $result_array = $result -> result_array();

            $residence_type_ids = array();
            foreach ($result_array as $row) {
                $residence_type_ids[] = $row['residence_type_id'];
            }

            return $residence_type_ids;
        }

        return NULL;
    }

    public function update_user_want_records_with_residence_type_ids($user_id, $residence_type_ids) {

        // Delete records whose 'residence_type_id' is not in 'residence_type_ids'.
        $this -> db -> where('user_id', $user_id);
        $this -> db -> where_not_in('residence_type_id', $residence_type_ids);
        $this -> db -> delete('user_want_residence_type');

        // Insert new records
        foreach ($residence_type_ids as $residence_type_id) {

            if ($this -> db -> get_where('user_want_residence_type',
                    array(
                        'user_id' => $user_id,
                        'residence_type_id' => $residence_type_id)) -> num_rows() == 0) {

                $insert_array['user_id'] = $user_id;
                $insert_array['residence_type_id'] = $residence_type_id;

                $this -> insert_user_want_residence_type($insert_array);
            }
        }
    }

    private function insert_user_want_residence_type($insert_array) {

        $this -> db -> insert('user_want_residence_type', $insert_array);
        return $this -> db -> insert_id();
    }

}
