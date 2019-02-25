<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Residence_Type_api_model extends CI_Model {

	public function get_residence_types($language_id){
		$this->db->select('residence_type_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('residence_type');
		$residence = array(''=>translate_phrase('Select residence type'));

		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$residence[$row['residence_type_id']]  = ucfirst($row['description']);
			}
		}
		return $residence;
	}
}
