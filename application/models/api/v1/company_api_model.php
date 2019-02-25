<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company_api_model extends CI_Model {

	public function insert_company($insert_array) {

		$this -> db -> insert('company', $insert_array);
		return $this -> db -> insert_id();
	}

	public function get_companies($language_id = "") {
		$this -> db -> select('company_id,company_name');

		//$this->db->where('display_language_id',$language_id);

		if ($language_id != "")
			$this -> db -> where('display_language_id', $language_id);

		$this -> db -> where('is_active', '1');
		$result = $this -> db -> get('company');
		$school = array();
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$school[$row['company_id']] = ucfirst($row['company_name']);
			}
		}
		return $school;
	}

}
