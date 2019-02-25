<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Education_Level_api_model extends CI_Model {

	public function get_user_education_levels_by_user_id($user_id, $language_id = 1) {
		$this -> db -> select('el.education_level_id, el.description');
		$this -> db -> join('user_education_level as uel', 'uel.education_level_id = el.education_level_id');
		$this -> db -> where('uel.user_id', $user_id);
		$this -> db -> where('el.display_language_id', $language_id);
		$result = $this -> db -> get('education_level as el');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function delete_user_education_levels_by_user_id($user_id) {

		$this -> db -> where('user_id', $user_id);
		$this -> db -> delete('user_education_level');
	}

	public function insert_user_education_level($user_id, $education_level_id) {

		if (!empty($user_id) && !empty($education_level_id)) {
			$insert_array = array(
				'user_id' => $user_id,
				'education_level_id' => $education_level_id
			);
			$this -> db -> insert('user_education_level', $insert_array);
		}
	}

	public function insert_user_education_level_with_array($insert_array) {

		$this -> db -> insert('user_education_level', $insert_array);
		return $this -> db -> insert_id();
	}

	public function update_user_records_with_education_level_ids($user_id, $education_level_ids) {

		// Delete records whose 'education_level_id' is not in 'education_level_ids'.
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where_not_in('education_level_id', $education_level_ids);
		$this -> db -> delete('user_education_level');

		// Insert new records
		foreach ($education_level_ids as $education_level_id) {

			if ($this -> db -> get_where('user_education_level',
				array(
					'user_id' => $user_id,
					'education_level_id' => $education_level_id)) -> num_rows() == 0) {

				$insert_array['user_id'] = $user_id;
				$insert_array['education_level_id'] = $education_level_id;

				$this -> insert_user_education_level_with_array($insert_array);
			}
		}
	}
}
