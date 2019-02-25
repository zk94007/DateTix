<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Membership_Option_api_model extends CI_Model {

	public function get_membership_option($membership_option_id) {

		$this -> db -> select('membership_option_id, description, view_order');
		$this -> db -> where('membership_option_id', $membership_option_id);
		$result = $this -> db -> get('membership_option');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}
}
