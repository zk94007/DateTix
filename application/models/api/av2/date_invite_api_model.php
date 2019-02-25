<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Date_Invite_api_model extends CI_Model {

	public function insert_date_invite($insert_array) {

		$this -> db -> insert('date_invite', $insert_array);
		return $this -> db -> insert_id();
	}

	public function get_date_invite($date_invite_id) {

		$this -> db -> where('date_invite_id', $date_invite_id);
		$result = $this -> db -> get('date_invite');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}

	public function delete_date_invite_with_params($date_id, $invited_user_id) {

		$this -> db -> where('date_id', $date_id);
		$this -> db -> where('invite_user_id', $invited_user_id);
		$this -> db -> delete('date_invite');
	}

}
