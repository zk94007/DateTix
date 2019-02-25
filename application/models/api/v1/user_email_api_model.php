<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Email_api_model extends CI_Model {

	public function insert_user_email_with_params($user_id, $email_address) {

		$insert_array['user_id'] = $user_id;
		$insert_array['email_address'] = $email_address;

		$this -> db -> insert('user_email', $insert_array);
		return $this -> db -> insert_id();
	}

	public function get_user_email_by_user_id($user_id) {

		$this -> db -> where('user_id', $user_id);
		$result = $this -> db -> get('user_email');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}
}
