<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class School_Subject_api_model extends CI_Model {

	public function get_school_subjects($language_id){
		$this->db->select('school_subject_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result     = $this->db->get('school_subject');
		$subject    = array();
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$subject[$row['school_subject_id']]  = ucfirst($row['description']);
			}
		}
		return $subject;
	}

}
