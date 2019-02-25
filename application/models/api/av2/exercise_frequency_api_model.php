<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Exercise_Frequency_api_model extends CI_Model {

	public function get_exercise_frequencies($language_id){
		$this->db->select('exercise_frequency_id,description');
		$this->db->where('display_language_id',$language_id);
		$this->db->order_by('view_order','ASC');
		$result = $this->db->get('exercise_frequency');
		$results        = array();
		if($result->num_rows()>0){
			$results    = $result->result_array();
		}
		return $results;
	}

	public function get_exercise_frequency($exercise_frequency_id) {

		$this -> db -> where('exercise_frequency_id', $exercise_frequency_id);
		$result = $this -> db -> get('exercise_frequency');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}
}
