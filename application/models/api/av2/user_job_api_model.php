<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Job_api_model extends CI_Model {

	public function get_user_jobs_by_user_id($user_id) {
		$this -> db -> where('user_id', $user_id);
		$result = $this -> db ->get('user_job');

		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_user_job($user_job_id){
		$this->db->where('user_company_id',$user_job_id);
		$result        = $this->db->get('user_job');
		$user_school[0] = array();
		if($result->num_rows()>0){
			$user_school = $result->result_array();
		}
		return $user_school[0];

	}

	public function insert_user_job($insert_array) {
		$this -> db -> insert('user_job', $insert_array);
		return $this -> db -> insert_id();
	}

	public function update_user_job($user_job_id, $update_array){
		$this->db->where('user_company_id',$user_job_id);
		$this->db->update('user_job',$update_array);
	}

	public function delete_user_job($user_job_id){
		$this->db->where('user_company_id',$user_job_id);
		$this->db->delete('user_job');
	}

	public function delete_user_jobs_by_user_id($user_id) {

		$this -> db -> where('user_id', $user_id);
		$this -> db -> delete('user_job');
	}

	public function delete_excluded_user_jobs_by_ids($user_id, $user_job_ids) {

		$this -> db -> where('user_id', $user_id);
		$this -> db -> where_not_in('user_company_id', $user_job_ids);
		$this -> db -> delete('user_job');
	}
}
