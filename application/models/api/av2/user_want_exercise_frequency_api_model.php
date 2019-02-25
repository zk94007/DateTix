<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Want_Exercise_Frequency_api_model extends CI_Model {

    public function get_user_want_exercise_frequencies_by_user_id($user_id) {
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db ->get('user_want_exercise_frequency');

        $results = array();
        if ($result -> num_rows() > 0) {
            $results = $result -> result_array();
        }
        return $results;
    }

    public function get_exercise_frequency_ids_by_user_id($user_id) {

        $this -> db -> select('exercise_frequency_id');
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db -> get('user_want_exercise_frequency');

        if ($result -> num_rows() > 0) {

            $result_array = $result -> result_array();

            $exercise_frequency_ids = array();
            foreach ($result_array as $row) {
                $exercise_frequency_ids[] = $row['exercise_frequency_id'];
            }

            return $exercise_frequency_ids;
        }

        return NULL;
    }

    public function update_user_want_records_with_exercise_frequency_ids($user_id, $exercise_frequency_ids) {

        // Delete records whose 'exercise_frequency_id' is not in 'exercise_frequency_ids'.
        $this -> db -> where('user_id', $user_id);
        $this -> db -> where_not_in('exercise_frequency_id', $exercise_frequency_ids);
        $this -> db -> delete('user_want_exercise_frequency');

        // Insert new records
        foreach ($exercise_frequency_ids as $exercise_frequency_id) {

            if ($this -> db -> get_where('user_want_exercise_frequency',
                    array(
                        'user_id' => $user_id,
                        'exercise_frequency_id' => $exercise_frequency_id)) -> num_rows() == 0) {

                $insert_array['user_id'] = $user_id;
                $insert_array['exercise_frequency_id'] = $exercise_frequency_id;

                $this -> insert_user_want_exercise_frequency($insert_array);
            }
        }
    }

    private function insert_user_want_exercise_frequency($insert_array) {

        $this -> db -> insert('user_want_exercise_frequency', $insert_array);
        return $this -> db -> insert_id();
    }

}
