<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Child_Plan_api_model extends CI_Model {

	public function get_child_plans($language_id){

		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result        = $this->db->get('child_plan');
		$results = array();
		if($result->num_rows()>0){
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_child_plan($child_plan_id) {

		$this -> db -> where('child_plan_id', $child_plan_id);
		$result = $this -> db -> get('child_plan');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}
}
