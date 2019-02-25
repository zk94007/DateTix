<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Want_Relationship_Type_api_model extends CI_Model {

    public function get_user_want_relationship_types_by_user_id($user_id) {
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db ->get('user_want_relationship_type');

        $results = array();
        if ($result -> num_rows() > 0) {
            $results = $result -> result_array();
        }
        return $results;
    }

    public function get_relationship_type_ids_by_user_id($user_id) {

        $this -> db -> select('relationship_type_id');
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db -> get('user_want_relationship_type');

        if ($result -> num_rows() > 0) {

            $result_array = $result -> result_array();

            $relationship_type_ids = array();
            foreach ($result_array as $row) {
                $relationship_type_ids[] = $row['relationship_type_id'];
            }

            return $relationship_type_ids;
        }

        return NULL;
    }

    public function update_user_want_records_with_relationship_type_ids($user_id, $relationship_type_ids) {

        // Delete records whose 'relationship_type_id' is not in 'relationship_type_ids'.
        $this -> db -> where('user_id', $user_id);
        $this -> db -> where_not_in('relationship_type_id', $relationship_type_ids);
        $this -> db -> delete('user_want_relationship_type');

        // Insert new records
        foreach ($relationship_type_ids as $relationship_type_id) {

            if ($this -> db -> get_where('user_want_relationship_type',
                    array(
                        'user_id' => $user_id,
                        'relationship_type_id' => $relationship_type_id)) -> num_rows() == 0) {

                $insert_array['user_id'] = $user_id;
                $insert_array['relationship_type_id'] = $relationship_type_id;

                $this -> insert_user_want_relationship_type($insert_array);
            }
        }
    }

    private function insert_user_want_relationship_type($insert_array) {

        $this -> db -> insert('user_want_relationship_type', $insert_array);
        return $this -> db -> insert_id();
    }

    public function delete_user_want_relationship_types_by_user_id($user_id) {

        $this -> db -> where('user_id', $user_id);
        $this -> db -> delete('user_want_relationship_type');
    }
}
