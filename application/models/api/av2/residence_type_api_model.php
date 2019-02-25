<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Residence_Type_api_model extends CI_Model {

	public function get_residence_types($language_id){

		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('residence_type');
		$results = array();
		if($result->num_rows()>0){
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_residence_type($residence_type_id) {

		$this -> db -> where('residence_type_id', $residence_type_id);
		$result = $this -> db -> get('residence_type');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}
}
