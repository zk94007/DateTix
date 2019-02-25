<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Budget_api_model extends CI_Model {

	public function get_budgets($language_id = 1) {
		$this -> db -> select('budget_id,description,num_date_tix,view_order');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('budget');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_budget($budget_id) {

		$this -> db -> where('budget_id', $budget_id);
		$result = $this -> db -> get('budget');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}

}
