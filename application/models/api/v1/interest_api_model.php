<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Interest_api_model extends CI_Model {

	public function get_interests($language_id, $interest_category_id = NULL) {
		$this -> db -> select('interest_id, interest_category_id, description');
		$this -> db -> where('display_language_id', $language_id);
		if ($interest_category_id) {
			$this -> db -> where('interest_category_id', $interest_category_id);
		}
		$this -> db -> order_by('view_order', 'ASC');

		$result = $this -> db -> get('interest');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_interest($interest_id) {

		$this -> db -> where('interest_id', $interest_id);
		$result = $this -> db -> get('interest');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}
}
