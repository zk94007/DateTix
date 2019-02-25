<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Religious_Belief_api_model extends CI_Model {

	public function get_religious_beliefs($language_id) {

		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('religious_belief');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_religious_belief($religious_belief_id) {

		$this -> db -> where('religious_belief_id', $religious_belief_id);
		$result = $this -> db -> get('religious_belief');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}

}
