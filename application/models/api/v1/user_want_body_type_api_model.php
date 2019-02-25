<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Want_Body_Type_api_model extends CI_Model {

    public function get_user_want_body_types_by_user_id($user_id) {

        $this -> db -> where('user_id', $user_id);
        $result = $this -> db ->get('user_want_body_type');

        $results = array();
        if ($result -> num_rows() > 0) {
            $results = $result -> result_array();
        }
        return $results;
    }

    public function get_body_type_ids_by_user_id($user_id) {

        $this -> db -> select('body_type_id');
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db -> get('user_want_body_type');

        if ($result -> num_rows() > 0) {

            $result_array = $result -> result_array();

            $body_type_ids = array();
            foreach ($result_array as $row) {
                $body_type_ids[] = $row['body_type_id'];
            }

            return $body_type_ids;
        }

        return NULL;
    }

    public function update_user_want_records_with_body_type_ids($user_id, $body_type_ids) {

        // Delete records whose 'body_type_id' is not in 'body_type_ids'.
        $this -> db -> where('user_id', $user_id);
        $this -> db -> where_not_in('body_type_id', $body_type_ids);
        $this -> db -> delete('user_want_body_type');

        // Insert new records
        foreach ($body_type_ids as $body_type_id) {

            if ($this -> db -> get_where('user_want_body_type',
                    array(
                        'user_id' => $user_id,
                        'body_type_id' => $body_type_id)) -> num_rows() == 0) {

                $insert_array['user_id'] = $user_id;
                $insert_array['body_type_id'] = $body_type_id;

                $this -> insert_user_want_body_type($insert_array);
            }
        }
    }

    private function insert_user_want_body_type($insert_array) {

        $this -> db -> insert('user_want_body_type', $insert_array);
        return $this -> db -> insert_id();
    }

}
