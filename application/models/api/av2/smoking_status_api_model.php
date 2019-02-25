<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Smoking_Status_api_model extends CI_Model {

	public function get_smoking_statuses($language_id){
		$this->db->select('smoking_status_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('smoking_status');
		$results        = array();
		if($result->num_rows()>0){
			$results    = $result->result_array();
		}
		return $results;
	}

	public function get_smoking_status($smoking_status_id) {

		$this -> db -> where('smoking_status_id', $smoking_status_id);
		$result = $this -> db -> get('smoking_status');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}
}
