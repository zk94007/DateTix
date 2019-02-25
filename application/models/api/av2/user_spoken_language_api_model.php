<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Spoken_Language_api_model extends CI_Model {

	public function get_user_spoken_languages_by_user_id($user_id, $language_id = 1) {
		$this -> db -> select('sl.spoken_language_id, usl.spoken_language_level_id, sl.description');
		$this -> db -> join('user_spoken_language as usl', 'usl.spoken_language_id = sl.spoken_language_id');
		$this -> db -> where('usl.user_id', $user_id);
		$this -> db -> where('sl.display_language_id', $language_id);

		$result = $this -> db -> get('spoken_language as sl');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_user_spoken_language($user_spoken_language_id) {

		$this -> db -> where('user_spoken_language_id', $user_spoken_language_id);
		$result = $this -> db -> get('user_spoken_language');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}

	public function insert_user_spoken_language($insert_array) {

		$this->db->insert('user_spoken_language', $insert_array);
		return $this->db->insert_id();
	}

	public function delete_user_spoken_language($user_spoken_language_id){

		$this->db->where('user_spoken_language_id ',$user_spoken_language_id);
		$this->db->delete('user_spoken_language');
	}

	public function delete_user_spoken_languages_by_user_id($user_id) {

		$this -> db -> where('user_id', $user_id);
		$this -> db -> delete('user_spoken_language');
	}

	public function update_user_records_with_spoken_language_ids($user_id, $spoken_language_ids) {

		// Delete records whose 'spoken_language_id' is not in 'spoken_language_ids'.
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where_not_in('spoken_language_id', $spoken_language_ids);
		$this -> db -> delete('user_spoken_language');

		// Insert new records
		foreach ($spoken_language_ids as $spoken_language_id) {

			if ($this -> db -> get_where('user_spoken_language',
				array(
					'user_id' => $user_id,
					'spoken_language_id' => $spoken_language_id)) -> num_rows() == 0) {

				$insert_array['user_id'] = $user_id;
				$insert_array['spoken_language_id'] = $spoken_language_id;

				$this -> insert_user_spoken_language($insert_array);
			}
		}
	}
}
