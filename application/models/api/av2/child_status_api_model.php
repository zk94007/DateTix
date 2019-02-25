<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Child_Status_api_model extends CI_Model {

	public function get_child_statuses($language_id){

		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result         = $this->db->get('child_status');
		$results = array();
		if($result->num_rows()>0){
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_child_status($child_status_id) {

		$this -> db -> where('child_status_id', $child_status_id);
		$result = $this -> db -> get('child_status');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}
}
