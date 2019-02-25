<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Relationship_Status_api_model extends CI_Model {

	public function get_relationship_statuses($language_id) {
		$this -> db -> select('relationship_status_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('relationship_status');
		$results = array();
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$relationship_status[$row['relationship_status_id']] = ucfirst($row['description']);
			}
		}
		return $relationship_status;
	}

}
