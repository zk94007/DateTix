<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Phone_Verification_Code_api_model extends CI_Model {

	public function insert_or_update_phone_verification_code($full_mobile_number, $verification_code) {

		$query = "INSERT INTO phone_verification_code VALUES('{$full_mobile_number}', '{$verification_code}')
				  ON DUPLICATE KEY UPDATE verification_code = VALUES(verification_code)";

		$this -> db -> query($query);
	}

	public function get_phone_verification_code($full_mobile_number) {

		$this -> db -> where('mobile_phone_number', $full_mobile_number);
		$result = $this -> db -> get('phone_verification_code');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}

	public function get_verification_code($full_mobile_number) {

		$this -> db -> where('mobile_phone_number', $full_mobile_number);
		$result = $this -> db -> get('phone_verification_code');

		return $result -> num_rows() > 0 ? $result -> row() -> verification_code : NULL;
	}

	public function delete_phone_verification_code($full_mobile_number) {

		$this -> db -> where('mobile_phone_number', $full_mobile_number);
		$this -> db -> delete('phone_verification_code');
	}
}
