<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Date_Applicant_api_model extends CI_Model {

	public function insert_date_applicant($insert_array) {

		$this -> db -> insert('date_applicant', $insert_array);
		return $this -> db -> insert_id();
	}

	public function get_date_applicant($date_applicant_id) {

		$this -> db -> where('date_applicant_id', $date_applicant_id);
		$result = $this -> db -> get('date_applicant');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}

	public function get_date_applicant_with_params($user_id, $date_id) {

		$this -> db -> where('applicant_user_id', $user_id);
		$this -> db -> where('date_id', $date_id);
		$result = $this -> db -> get('date_applicant');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}

	public function get_date_applicants_by_date_id($date_id) {

		$this -> db -> where('date_id', $date_id);
		$result = $this -> db -> get('date_applicant');

		return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
	}

	public function get_applicant_user_ids_by_date_id($date_id) {

		$this -> db -> where('date_id', $date_id);
		$result = $this -> db -> get('date_applicant');

		$applicant_user_ids = array();
		if ($result -> num_rows() > 0) {
			$records = $result -> result_array();
			foreach ($records as $record) {
				$applicant_user_ids[] = $record['applicant_user_id'];
			}
		}
		return $applicant_user_ids;
	}

	public function update_date_applicant($date_applicant_id, $update_array) {

		$this -> db -> where('date_applicant_id', $date_applicant_id);
		$this -> db -> update('date_applicant', $update_array);
	}

	public function cancel_date_applicant_from_params($date_id, $user_id) {

		$update_array['status'] = 3;

		$this -> db -> where('date_id', $date_id);
		$this -> db -> where('applicant_user_id', $user_id);
		$this -> db -> update('date_applicant', $update_array);
	}
}
