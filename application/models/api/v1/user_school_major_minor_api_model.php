<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_School_Major_Minor_api_model extends CI_Model {

	public function insert_user_school_majors($major_ids, $user_school_id){
		$this->delete_user_school_majors_by_user_school_id($user_school_id);
		$major_id_array     = explode(',',$major_ids);
		for($i=0;$i<count($major_id_array);$i++){
			$insert_array   = array('user_school_id'=>$user_school_id,
				'major_id'=>$major_id_array[$i]);
			$result         = $this->db->insert('user_school_major',$insert_array);
		}
	}

	public function get_user_school_majors_by_user_school_id($user_school_id,$language_id = 1){
		$this->db->select('major_id,description');
		$this->db->join('school_subject','school_subject.school_subject_id=user_school_major.major_id');
		$this->db->where('user_school_id',$user_school_id);

		$this->db->where('school_subject.display_language_id',$language_id);

		$result    = $this->db->get('user_school_major');
		$row       = array();
		if($result->num_rows()>0){
			$row  = $result->result_array();
		}

		return $row;
	}

	public function delete_user_school_majors_by_user_school_id($user_school_id){
		$this->db->where('user_school_id',$user_school_id);
		$this->db->delete('user_school_major');
	}

	public function insert_user_school_minors($minor_ids, $user_school_id){
		$this->delete_user_school_minors_by_user_school_id($user_school_id);
		$minor_id_array     = explode(',',$minor_ids);
		for($i=0;$i<count($minor_id_array);$i++){
			$insert_array   = array('user_school_id'=>$user_school_id,
				'minor_id'=>$minor_id_array[$i]);
			$result         = $this->db->insert('user_school_minor',$insert_array);
		}
	}

	public function delete_user_school_minors_by_user_school_id($user_school_id){
		$this->db->where('user_school_id',$user_school_id);
		$this->db->delete('user_school_minor');
	}

	public function get_user_school_minors_by_user_school_id($user_school_id,$language_id=1){
		$this->db->select('minor_id,description');
		$this->db->join('school_subject','school_subject.school_subject_id=user_school_minor.minor_id');
		$this->db->where('user_school_id',$user_school_id);

		$this->db->where('school_subject.display_language_id',$language_id);

		$result    = $this->db->get('user_school_minor');
		$row       = array();
		if($result->num_rows()>0){
			$row  = $result->result_array();
		}
		return $row;
	}
}
