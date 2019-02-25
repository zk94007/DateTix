<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class School_api_model extends CI_Model {

	public function insert_school($insert_array) {

		$this -> db -> insert('school', $insert_array);
		return $this -> db -> insert_id();
	}

	public function get_schools($language_id){
		$this->db->select('school_id,school_name');
		$this->db->where('display_language_id',$language_id);
		$this->db->where('is_active','1');
		$result = $this->db->get('school');
		$schools = array();
		if($result->num_rows()>0){
			foreach($result->result_array() as $row){
				$schools[$row['school_id']]  = ucfirst($row['school_name']);
			}
		}
		return $schools;
	}

	public function get_school_id_by_school_name($school_name, $language_id = 1){
		$this->db->select('school_id');
		$this->db->where('school_name',trim($school_name));

		if($language_id != "")
			$this->db->where('display_language_id',$language_id);

		$r       = "";
		$result  = $this->db->get('school');
		if($result->num_rows()>0){
			$row     = $result->result_array();
			$r       = $row[0]['school_id'];
		}
		return $r;
	}

	public function get_school_logo_by_school_name($school_name, $language_id = 1){
		$school_id   = $this->get_school_id_by_school_name($school_name, $language_id);
		$this->db->select('logo_url');
		$this->db->where('school_id',$school_id);
		$this->db->where('is_active','1');
		$result             = $this->db->get('school');
		$school_logo        = "";
		if($result->num_rows()>0){
			$row            = $result->row_array();
			if (file_exists("./school_logos/{$row['logo_url']}")) {
				$school_logo = $row['logo_url'];
			}
		}
		return $school_logo;
	}

	public function get_school_email_domain_by_school_name($school_name, $language_id = 1)
	{
		$school_id = $this->get_school_id_by_school_name($school_name, $language_id);
		$this->db->select('school_email_domain_id,email_domain');
		$this->db->where('school_id', $school_id);
		$result = $this->db->get('school_email_domain');

		/* Changed by Rajnish */
		$rs = $result->result_array();
		$result_arr = array();
		if ($rs) {
			foreach ($rs as $value) {
				$result_arr[$value['school_email_domain_id']] = $value['email_domain'];
			}
		} else {
			return false;
		}
		return $result_arr;

		/*
         $domain     = "";
         if($result->num_rows()>0){
            $domain      = '<select style="margin-left:0px;" nmae="school_email_domain_id" id="school_email_domain_id">';
            foreach($result->result_array() as $row){
            $domain .= '<option value="'.$row['school_email_domain_id'].'">'.$row['email_domain'].'</option>';
            }
            $domain     .= '</select>';
            }
            return $domain;
            */
	}

}
