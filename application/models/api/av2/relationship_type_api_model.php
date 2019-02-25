<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Relationship_Type_api_model extends CI_Model {

	public function get_relationship_type($relationship_type_id, $language_id = '1') {

		$this -> db -> where('relationship_type_id', $relationship_type_id);
		$this -> db -> where('display_language_id', $language_id);
		$result = $this -> db -> get('relationship_type');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}

	public function get_relationship_types($language_id = '1') {
		$this -> db -> select('relationship_type_id,description,num_date_tix,view_order');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('relationship_type');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

}
