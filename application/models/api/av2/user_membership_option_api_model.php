<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Membership_Option_api_model extends CI_Model {

	public function get_user_membership_options_by_user_id($user_id) {

		$this -> db -> where('user_id', $user_id);
		$result = $this -> db -> get('user_membership_option');

		return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
	}

	public function get_membership_options_by_user_id($user_id) {

		$user_membership_options = $this -> get_user_membership_options_by_user_id($user_id);

		$membership_options = array();

		if (!empty($user_membership_options)) {

			$CI = & get_instance();
			$CI -> load -> model('api/av2/membership_option_api_model');

			foreach ($user_membership_options as $user_membership_option) {

				$membership_option = $CI -> membership_option_api_model -> get_membership_option($user_membership_option['membership_option_id']);

				if (!empty($membership_option)) {

					$membership_option -> expiry_date = $user_membership_option['expiry_date'];

					$membership_options[] = $membership_option;
				}
			}
		}

		return $membership_options;
	}

	public function is_upgraded_user($user_id) {

		$membership_options = $this -> get_membership_options_by_user_id($user_id);

		if (empty($membership_options)) return FALSE;

		if ($membership_options[0] -> expiry_date >= date('Y-m-d')) {
			return TRUE;
		}

		return FALSE;
	}

	private function get_user_membership_option($user_membership_option_id) {

		$this -> db -> where('user_membership_option_id', $user_membership_option_id);
		$result = $this -> db -> get('user_membership_option');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}

	private function insert_user_membership_option($insert_array) {

		$this -> db -> insert('user_membership_option', $insert_array);
		return $this -> db -> insert_id();
	}

	private function update_user_membership_option($user_membership_option_id, $update_array) {

		$this -> db -> where('user_membership_option_id', $user_membership_option_id);
		$this -> db -> update('user_membership_option', $update_array);
	}

	private function get_user_membership_option_id_with_params($user_id, $membership_option_id) {

		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('membership_option_id', $membership_option_id);
		$result = $this -> db -> get('user_membership_option');

		return $result -> num_rows() > 0 ? $result -> row() -> user_membership_option_id : NULL;
	}

	public function update_user_membership_options_with_params($user_id, $membership_option_ids, $membership_duration_months) {

		foreach ($membership_option_ids as $membership_option_id) {

			$user_membership_option_id = $this -> get_user_membership_option_id_with_params($user_id, $membership_option_id);

			if (empty($user_membership_option_id)) {	// Insert new record

				$insert_array['user_id'] = $user_id;
				$insert_array['membership_option_id'] = $membership_option_id;
				$insert_array['expiry_date'] = date('Y-m-d', strtotime($membership_duration_months . " month"));

				$this -> insert_user_membership_option($insert_array);

			} else {	// Update existing record

				$update_array['expiry_date'] = date('Y-m-d', strtotime($membership_duration_months . " month"));

				// Check if expiry is not ended
				$user_membership_option = $this -> get_user_membership_option($user_membership_option_id);
				if ($user_membership_option -> expiry_date >= date('Y-m-d')) {
					$update_array['expiry_date'] = date('Y-m-d', strtotime($membership_duration_months . " month", strtotime($user_membership_option -> expiry_date)));
				}

				$this -> update_user_membership_option($user_membership_option_id, $update_array);
			}
		}
	}
}
