<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cuisine_api_model extends CI_Model {

	public function get_cuisines($language_id = '1') {

		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('cuisine');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_cuisines_with_category($language_id = '1') {

		$this -> db -> select('cuisine_category_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('cuisine_category');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
			foreach ($results as $key => $value) {
				$this -> db -> select('cuisine_id,description');
				$this -> db -> where('display_language_id', $language_id);
				$this -> db -> where('cuisine_category_id', $value['cuisine_category_id']);
				$this -> db -> order_by('view_order', 'ASC');
				$query = $this -> db -> get('cuisine');
				if ($query -> num_rows() > 0) {
					$results[$key]['list'] = $query -> result_array();
				}
			}
		}
		return $results;
	}


}
