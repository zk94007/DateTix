<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Education_Level_api_model extends CI_Model {

	public function get_education_levels($language_id){

		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result     = $this->db->get('education_level');
		$results    = array();
		if($result->num_rows()>0){
			$results = $result->result_array();
		}
		return $results;
	}

	public function get_education_level($education_level_id) {

		$this -> db -> where('education_level_id', $education_level_id);
		$result = $this -> db -> get('education_level');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}
}
