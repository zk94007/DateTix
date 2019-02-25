<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Descriptive_Word_api_model extends CI_Model {

	public function get_user_descriptive_words_by_user_id($user_id, $language_id = 1) {
		$this -> db -> select('dw.descriptive_word_id, dw.description');
		$this -> db -> join('user_descriptive_word as udw', 'udw.descriptive_word_id = dw.descriptive_word_id');
		$this -> db -> where('udw.user_id', $user_id);
		$this -> db -> where('dw.display_language_id', $language_id);

		$result = $this -> db -> get('descriptive_word as dw');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function insert_user_descriptive_word_with_params($user_id, $descriptive_word_id) {
		$insert_array = array(
			'user_id' => $user_id,
			'descriptive_word_id' => $descriptive_word_id
		);
		$this -> db -> insert('user_descriptive_word', $insert_array);
		return $this -> db -> insert_id();
	}

	public function insert_user_descriptive_word($insert_array) {

		$this -> db -> insert('user_descriptive_word', $insert_array);
		return $this -> db -> insert_id();
	}

	public function delete_user_descriptive_words_by_user_id($user_id) {

		$this -> db -> where('user_id', $user_id);
		$this -> db -> delete('user_descriptive_word');
	}

	public function update_user_records_with_descriptive_word_ids($user_id, $descriptive_word_ids) {

		// Delete records whose 'descriptive_word_id' is not in 'descriptive_word_ids'.
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where_not_in('descriptive_word_id', $descriptive_word_ids);
		$this -> db -> delete('user_descriptive_word');

		// Insert new records
		foreach ($descriptive_word_ids as $descriptive_word_id) {

			if ($this -> db -> get_where('user_descriptive_word',
					array(
						'user_id' => $user_id,
						'descriptive_word_id' => $descriptive_word_id)) -> num_rows() == 0) {

				$insert_array['user_id'] = $user_id;
				$insert_array['descriptive_word_id'] = $descriptive_word_id;

				$this -> insert_user_descriptive_word($insert_array);
			}
		}
	}

}
