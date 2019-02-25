<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Interest_Category_api_model extends CI_Model {

	public function get_interest_categories($language_id) {
		$this -> db -> select('interest_category_id, description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');

		$result = $this -> db -> get('interest_category');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}


}
