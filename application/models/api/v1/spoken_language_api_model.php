<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Spoken_Language_api_model extends CI_Model {

	public function get_spoken_languages($language_id){
		$this->db->select('spoken_language_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result             = $this->db->get('spoken_language');
		$spoken_language   = array(''=>translate_phrase('Select language'));
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$spoken_language[$row['spoken_language_id']]  = ucfirst($row['description']);
			}
		}
		return $spoken_language;
	}
}
