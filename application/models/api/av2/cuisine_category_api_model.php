<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cuisine_Category_api_model extends CI_Model {

	public function get_cuisine_categories($language_id = 1) {

		$this -> db -> where('display_language_id', $language_id);
		$result = $this -> db -> get('cuisine_category');

		return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
	}
}
