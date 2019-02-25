<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Neighborhood_api_model extends CI_Model {

	public function get_neighborhoods($language_id = 1) {

		$this -> db -> where('display_language_id', $language_id);
		$result = $this -> db -> get('neighborhood');

		return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
	}

	public function filter_neighborhoods($city_id = 0, $language_id = 1){

		if ($city_id > 0) {
			$this -> db -> where('city_id', $city_id);
		}
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('neighborhood');

		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}
}
