<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Religious_Belief_api_model extends CI_Model {

	public function get_religious_beliefs($language_id) {
		$this -> db -> select('religious_belief_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('religious_belief');
		//$religious_belief   = array(''=>translate_phrase('Select religious belief'));
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$religious_belief[$row['religious_belief_id']] = ucfirst($row['description']);
			}
		}
		return $religious_belief;
	}

}
