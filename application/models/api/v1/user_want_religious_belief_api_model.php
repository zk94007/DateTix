<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Want_Religious_Belief_api_model extends CI_Model {

    public function get_user_want_religious_beliefs_by_user_id($user_id) {
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db ->get('user_want_religious_belief');

        $results = array();
        if ($result -> num_rows() > 0) {
            $results = $result -> result_array();
        }
        return $results;
    }

    public function get_religious_belief_ids_by_user_id($user_id) {

        $this -> db -> select('religious_belief_id');
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db -> get('user_want_religious_belief');

        if ($result -> num_rows() > 0) {

            $result_array = $result -> result_array();

            $religious_belief_ids = array();
            foreach ($result_array as $row) {
                $religious_belief_ids[] = $row['religious_belief_id'];
            }

            return $religious_belief_ids;
        }

        return NULL;
    }

    public function update_user_want_records_with_religious_belief_ids($user_id, $religious_belief_ids) {

        // Delete records whose 'religious_belief_id' is not in 'religious_belief_ids'.
        $this -> db -> where('user_id', $user_id);
        $this -> db -> where_not_in('religious_belief_id', $religious_belief_ids);
        $this -> db -> delete('user_want_religious_belief');

        // Insert new records
        foreach ($religious_belief_ids as $religious_belief_id) {

            if ($this -> db -> get_where('user_want_religious_belief',
                    array(
                        'user_id' => $user_id,
                        'religious_belief_id' => $religious_belief_id)) -> num_rows() == 0) {

                $insert_array['user_id'] = $user_id;
                $insert_array['religious_belief_id'] = $religious_belief_id;

                $this -> insert_user_want_religious_belief($insert_array);
            }
        }
    }

    private function insert_user_want_religious_belief($insert_array) {

        $this -> db -> insert('user_want_religious_belief', $insert_array);
        return $this -> db -> insert_id();
    }

}
