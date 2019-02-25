<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gender_api_model extends CI_Model {

	public function get_genders($language_id = '1') {
		$this -> db -> select('gender_id,description,view_order');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('gender');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_gender($gender_id) {

		$this -> db -> where('gender_id', $gender_id);
		$result = $this -> db -> get('gender');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}
}
