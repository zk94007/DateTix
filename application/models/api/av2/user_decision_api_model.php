<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Decision_api_model extends CI_Model {

	public function insert_user_decision($insert_array) {

		$this -> db -> insert('user_decision', $insert_array);
		return $this -> db -> insert_id();
	}

	public function get_user_decision($user_decision_id) {

		$this -> db -> where('user_decision_id', $user_decision_id);
		$result = $this -> db -> get('user_decision');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}

	public function get_user_decisions_by_user_id($user_id) {

		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('decision IS NOT NULL', NULL, FALSE);
		$result = $this -> db -> get('user_decision');

		return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
	}

	public function delete_user_decision($user_decision_id) {

		$this -> db -> where('user_decision_id', $user_decision_id);
		$this -> db -> delete('user_decision');
	}

	public function delete_user_decision_with_params($target_user_id, $user_id) {

		$this -> db -> where('target_user_id', $target_user_id);
		$this -> db -> where('user_id', $user_id);
		$this -> db -> delete('user_decision');
	}

	public function get_last_disliked_user_decision($user_id) {

		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('decision', 0);

		$this -> db -> order_by('decision_time', 'DESC');

		$result = $this -> db -> get('user_decision', 1);

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}
}
