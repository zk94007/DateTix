<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Relationship_Status_api_model extends CI_Model {

	public function get_relationship_statuses($language_id) {

		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('relationship_status');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_relationship_status($relationship_status_id) {

		$this -> db -> where('relationship_status_id', $relationship_status_id);
		$result = $this -> db -> get('relationship_status');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}
}
