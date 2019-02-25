<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_School_api_model extends CI_Model {

	public function get_user_schools_by_user_id($user_id,$getAllFields = TRUE ){
		//$this->db->select('user_school_id');
		if($getAllFields ===  FALSE)
		{
			$this->db->select('user_school_id');
		}
		$this->db->where('user_id',$user_id);
		$result          = $this->db->get('user_school')->result_array();
		$user_school     = array();
		$user_school = $result;

		return $user_school;
	}

	public function get_user_school($user_school_id) {

		$this -> db -> where('user_school_id', $user_school_id);
		$result = $this -> db -> get('user_school');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}

	public function insert_user_school($insert_array) {
		$this -> db -> insert('user_school', $insert_array);
		return $this -> db -> insert_id();
	}

	public function update_user_school($user_school_id, $update_array){
		$this->db->where('user_school_id',$user_school_id);
		$this->db->update('user_school',$update_array);
	}

	public function delete_user_school_and_links($user_school_id){
		$CI = & get_instance();
		$CI -> load -> model('api/v1/user_school_major_minor_api_model');

		$CI -> user_school_major_minor_api_model -> delete_user_school_majors_by_user_school_id($user_school_id);
		$CI -> user_school_major_minor_api_model -> delete_user_school_minors_by_user_school_id($user_school_id);

		$this->db->where('user_school_id',$user_school_id);
		$this->db->delete('user_school');
	}

	public function get_user_school_details($user_school_id,$language_id = ""){
		$CI = & get_instance();
		$CI -> load -> model('api/v1/user_school_major_minor_api_model');
		$CI -> load -> model('api/v1/school_api_model');

		$this->db->where('user_school_id',$user_school_id);
		$result             = $this->db->get('user_school');
		$school_details     = array();

		if($result->num_rows()>0){
			$school_details = $result->result_array();

			if($school_details['0']['school_id']!="")
			{
				//Lang Change
				//$school_details['0']['school_name']    =  $this->get_school_name($school_details['0']['school_id'],$language_id);
				$school_details['email']['domain']     =  $CI -> school_api_model -> get_school_email_domain_by_school_name($school_details['0']['school_name'],$language_id);
				if($school_details['email']['domain'] )
				{
					if($school_details['0']['school_email_address'])
					{
						//$email_address = substr($school_details['0']['school_email_address'],0, strpos($school_details['0']['school_email_address'], '@'));
						$email_address = str_replace(reset($school_details['email']['domain']),'',$school_details['0']['school_email_address']);
						$email_address = rtrim($email_address,'@');
					}
				}
				else
					$email_address     = $school_details['0']['school_email_address'];
				$school_details['email']['adress']     =  isset($email_address)?$email_address:"";
			}else{
				$school_details['email']['adress']    = $school_details['0']['school_email_address'];
				$school_details['email']['domain']    = "";
			}
			$school_details['school_logo']    = $CI -> school_api_model -> get_school_logo_by_school_name($school_details['0']['school_name']);
			$school_majors = $CI -> user_school_major_minor_api_model -> get_user_school_majors_by_user_school_id($user_school_id);
			foreach($school_majors as $row){
				$school_details['majors'][$row['major_id']]=$row['description'];
			}
			$school_minors = $CI -> user_school_major_minor_api_model -> get_user_school_minors_by_user_school_id($user_school_id);
			foreach($school_minors as $row){
				$school_details['minors'][$row['minor_id']]=$row['description'];
			}
		}
		return $school_details;
	}
}
