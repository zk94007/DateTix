<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Child_Status_api_model extends CI_Model {

	public function get_child_statuses($language_id){
		$this->db->select('child_status_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result         = $this->db->get('child_status');
		$child_status   = array(''=>translate_phrase('Select child status'));
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$child_status[$row['child_status_id']]  = ucfirst($row['description']);
			}
		}
		return $child_status;
	}
}
