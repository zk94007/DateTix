<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Date_Decision_api_model extends CI_Model {

	public function insert_date_decision($insert_array) {

		$this -> db -> insert('date_decision', $insert_array);
		return $this -> db -> insert_id();
	}

	public function get_date_decision($date_decision_id) {

		$this -> db -> where('date_decision_id', $date_decision_id);
		$result = $this -> db -> get('date_decision');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}

	public function get_date_decisions_by_user_id($user_id) {

		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('decision IS NOT NULL', NULL, FALSE);
		$result = $this -> db -> get('date_decision');

		return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
	}

	public function get_date_decisions_by_date_id($date_id) {

		$this -> db -> where('date_id', $date_id);
		$this -> db -> where('decision IS NOT NULL', NULL, FALSE);
		$result = $this -> db -> get('date_decision');

		return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
	}

	public function delete_date_decision($date_decision_id) {

		$this -> db -> where('date_decision_id', $date_decision_id);
		$this -> db -> delete('date_decision');
	}

	public function delete_date_decision_with_params($date_id, $user_id) {

		$this -> db -> where('date_id', $date_id);
		$this -> db -> where('user_id', $user_id);
		$this -> db -> delete('date_decision');
	}

	public function get_last_disliked_date_decision($user_id) {

		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('decision', 0);

		$this -> db -> order_by('decision_time', 'DESC');

		$result = $this -> db -> get('date_decision', 1);

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}
}
