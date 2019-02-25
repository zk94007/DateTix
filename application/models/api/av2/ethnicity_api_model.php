<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ethnicity_api_model extends CI_Model {

	public function get_ethnicities($language_id = '1') {
		$this -> db -> select('ethnicity_id,description,view_order');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('ethnicity');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_ethnicity($ethnicity_id) {

		$this -> db -> where('ethnicity_id', $ethnicity_id);
		$result = $this -> db -> get('ethnicity');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}
}
