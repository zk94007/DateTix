<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Body_Type_api_model extends CI_Model {

	public function get_body_types($language_id = 1) {

		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('body_type');

		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_body_type($body_type_id, $language_id = 1) {

		$this -> db -> where('body_type_id', $body_type_id);
		$this -> db -> where('display_language_id', $language_id);
		$result = $this -> db -> get('body_type');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}
}
