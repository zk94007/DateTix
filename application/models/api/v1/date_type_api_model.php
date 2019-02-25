<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Date_Type_api_model extends CI_Model {

	public function get_date_types($language_id = '1') {
		$this -> db -> select('date_type_id,description,view_order');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> where('is_active', 1);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('date_type');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_date_type($date_type_id, $language_id = '1') {

		$this -> db -> where('date_type_id', $date_type_id);
		$this -> db -> where('display_language_id', $language_id);
		$result = $this -> db -> get('date_type');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}
}
