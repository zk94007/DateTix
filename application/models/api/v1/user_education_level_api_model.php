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

}
