<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Want_Child_Plan_api_model extends CI_Model {

    public function get_user_want_child_plans_by_user_id($user_id) {
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db ->get('user_want_child_plan');

        $results = array();
        if ($result -> num_rows() > 0) {
            $results = $result -> result_array();
        }
        return $results;
    }

    public function get_child_plan_ids_by_user_id($user_id) {

        $this -> db -> select('child_plan_id');
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db -> get('user_want_child_plan');

        if ($result -> num_rows() > 0) {

            $result_array = $result -> result_array();

            $child_plan_ids = array();
            foreach ($result_array as $row) {
                $child_plan_ids[] = $row['child_plan_id'];
            }

            return $child_plan_ids;
        }

        return NULL;
    }

    public function update_user_want_records_with_child_plan_ids($user_id, $child_plan_ids) {

        // Delete records whose 'child_plan_id' is not in 'child_plan_ids'.
        $this -> db -> where('user_id', $user_id);
        $this -> db -> where_not_in('child_plan_id', $child_plan_ids);
        $this -> db -> delete('user_want_child_plan');

        // Insert new records
        foreach ($child_plan_ids as $child_plan_id) {

            if ($this -> db -> get_where('user_want_child_plan',
                    array(
                        'user_id' => $user_id,
                        'child_plan_id' => $child_plan_id)) -> num_rows() == 0) {

                $insert_array['user_id'] = $user_id;
                $insert_array['child_plan_id'] = $child_plan_id;

                $this -> insert_user_want_child_plan($insert_array);
            }
        }
    }

    private function insert_user_want_child_plan($insert_array) {

        $this -> db -> insert('user_want_child_plan', $insert_array);
        return $this -> db -> insert_id();
    }

}
