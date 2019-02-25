<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class School_Subject_api_model extends CI_Model {

	public function get_school_subjects($language_id){

		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result     = $this->db->get('school_subject');
		$subjects    = array();
		if($result->num_rows()>0){
			$subjects = $result -> result_array();
		}
		return $subjects;
	}

}
