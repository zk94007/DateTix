<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Spoken_Language_api_model extends CI_Model {

	public function get_spoken_languages($language_id){

		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result             = $this->db->get('spoken_language');
		$results = array();
		if($result->num_rows()>0){
			$results = $result -> result_array();
		}
		return $results;
	}
}
