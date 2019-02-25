<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Order_api_model extends CI_Model {

	public function insert_user_order($insert_array) {

		$this -> db -> insert('user_order', $insert_array);
		return $this -> db -> insert_id();
	}

	public function get_user_order($user_order_id) {

		$this -> db -> where('user_order_id', $user_order_id);
		$result = $this -> db -> get('user_order');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}

	public function get_last_upgrade_date($user_id) {

		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('order_membership_options IS NOT NULL');
		$this -> db -> order_by('order_time', 'DESC');

		$result = $this -> db -> get('user_order');
		if ($result -> num_rows() > 0) {
			return $result -> row() -> order_time;
		}
		return null;
	}
}
