<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company_api_model extends CI_Model {

	public function insert_company($insert_array) {

		$this -> db -> insert('company', $insert_array);
		return $this -> db -> insert_id();
	}

	public function get_companies($language_id = 1) {

		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> where('is_active', '1');
		$result = $this -> db -> get('company');
		$companies = array();
		if ($result -> num_rows() > 0) {
			$companies = $result -> result_array();
		}
		return $companies;
	}

}
