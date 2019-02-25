<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Preferred_Date_Type_api_model extends CI_Model {

    public function get_user_preferred_date_types_by_user_id($user_id) {
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db ->get('user_preferred_date_type');

        $results = array();
        if ($result -> num_rows() > 0) {
            $results = $result -> result_array();
        }
        return $results;
    }

    public function get_date_type_ids_by_user_id($user_id) {

        $this -> db -> select('date_type_id');
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db -> get('user_preferred_date_type');

        if ($result -> num_rows() > 0) {

            $result_array = $result -> result_array();

            $date_type_ids = array();
            foreach ($result_array as $row) {
                $date_type_ids[] = $row['date_type_id'];
            }

            return $date_type_ids;
        }

        return NULL;
    }

    public function update_user_want_records_with_date_type_ids($user_id, $date_type_ids) {

        // Delete records whose 'date_type_id' is not in 'date_type_ids'.
        $this -> db -> where('user_id', $user_id);
        $this -> db -> where_not_in('date_type_id', $date_type_ids);
        $this -> db -> delete('user_preferred_date_type');

        // Insert new records
        foreach ($date_type_ids as $date_type_id) {

            if ($this -> db -> get_where('user_preferred_date_type',
                    array(
                        'user_id' => $user_id,
                        'date_type_id' => $date_type_id)) -> num_rows() == 0) {

                $insert_array['user_id'] = $user_id;
                $insert_array['date_type_id'] = $date_type_id;

                $this -> insert_user_preferred_date_type($insert_array);
            }
        }
    }

    private function insert_user_preferred_date_type($insert_array) {

        $this -> db -> insert('user_preferred_date_type', $insert_array);
        return $this -> db -> insert_id();
    }

    public function delete_user_preferred_date_types_by_user_id($user_id) {

        $this -> db -> where('user_id', $user_id);
        $this -> db -> delete('user_preferred_date_type');
    }
}
