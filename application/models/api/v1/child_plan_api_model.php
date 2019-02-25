<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Child_Plan_api_model extends CI_Model {

	public function get_child_plans($language_id){
		$this->db->select('child_plan_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result        = $this->db->get('child_plan');
		$child_plans   = array(''=>translate_phrase('Select child plans'));
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$child_plans[$row['child_plan_id']]  = ucfirst($row['description']);
			}
		}
		return $child_plans;
	}
}
