<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Want_Descriptive_Word_api_model extends CI_Model {

    public function get_user_want_descriptive_words_by_user_id($user_id) {
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db ->get('user_want_descriptive_word');

        $results = array();
        if ($result -> num_rows() > 0) {
            $results = $result -> result_array();
        }
        return $results;
    }

    public function get_descriptive_word_ids_by_user_id($user_id) {

        $this -> db -> select('descriptive_word_id');
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db -> get('user_want_descriptive_word');

        if ($result -> num_rows() > 0) {

            $result_array = $result -> result_array();

            $descriptive_word_ids = array();
            foreach ($result_array as $row) {
                $descriptive_word_ids[] = $row['descriptive_word_id'];
            }

            return $descriptive_word_ids;
        }

        return NULL;
    }

    public function update_user_want_records_with_descriptive_word_ids($user_id, $descriptive_word_ids) {

        // Delete records whose 'descriptive_word_id' is not in 'descriptive_word_ids'.
        $this -> db -> where('user_id', $user_id);
        $this -> db -> where_not_in('descriptive_word_id', $descriptive_word_ids);
        $this -> db -> delete('user_want_descriptive_word');

        // Insert new records
        foreach ($descriptive_word_ids as $descriptive_word_id) {

            if ($this -> db -> get_where('user_want_descriptive_word',
                    array(
                        'user_id' => $user_id,
                        'descriptive_word_id' => $descriptive_word_id)) -> num_rows() == 0) {

                $insert_array['user_id'] = $user_id;
                $insert_array['descriptive_word_id'] = $descriptive_word_id;

                $this -> insert_user_want_descriptive_word($insert_array);
            }
        }
    }

    private function insert_user_want_descriptive_word($insert_array) {

        $this -> db -> insert('user_want_descriptive_word', $insert_array);
        return $this -> db -> insert_id();
    }

    public function delete_user_want_descriptive_words_by_user_id($user_id) {

        $this -> db -> where('user_id', $user_id);
        $this -> db -> delete('user_want_descriptive_word');
    }
}
