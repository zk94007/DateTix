<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Model_people extends CI_Model {

    public function get_user_preference($user_id,$type='all'){
    	$this -> general -> set_table('user');
		$user_select = "user.user_id,user.first_name, user.password,user.gender_id, user.ethnicity_id,user.last_name, user.num_date_tix, user.want_age_range_lower, user.want_age_range_upper, user.facebook_id, CASE
					WHEN
						birth_date != '0000-00-00'
					THEN 
						TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
				END as age";

		$user_data = $this -> general -> get($user_select, array('user_id' => $user_id));
		$user_info = $user_data['0'];
		if($type == 'all')
		{
			$user_info['user_want_gender'] = $this -> datetix -> user_want($user_id, "gender");
			$user_info['user_want_ethnicity'] = $this -> datetix -> user_want($user_id, "ethnicity");
		}
		//echo "<pre>";print_r($user_info);exit;
		return $user_info;
    }
	
	public function formate_prefered_match_cluase($user_info)
	{
		$condition_and = array();
		$condtion_str = array();
		if($user_info)
		{
			//1) Show only people whose gender is within people filter popup's selected genders (only apply this filter if user selected at least one gender in people filter popup)
			if ($user_info['user_want_gender']) {
				$condition = array();
				foreach ($user_info['user_want_gender'] as $value) {
					$condition_and['user.gender_id'][] = $value['gender_id'];
				}
			}
			//Show only people whose ethnicity is within people filter popup's selected ethnicity range  (only apply this filter if user selected at least one ethnicity in people filter popup)
			if ($user_info['user_want_ethnicity']) {
				$condition = array();
				foreach ($user_info['user_want_ethnicity'] as $value) {
					$condition_and['user.ethnicity_id'][] = $value['ethnicity_id'];
				}
			}
			
			//1) Show only people whose age is within people filter popup's selected age range (only apply this filter if user selected age range in people filter popup)
			if ($user_info['want_age_range_lower'] > 0) {
				$condition_and['TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE()) >'] = $user_info['want_age_range_lower'];
			}
	
			if ($user_info['want_age_range_upper'] > 0) {
				$condition_and['TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE()) <='] = $user_info['want_age_range_upper'];
			}
		}
		if ($condition_and) {
			foreach ($condition_and as $key => $value) {
				if (is_array($value)) {
					$val = "IN(" . implode(',', $value) . ")";
				} else {
					$val = $value;
				}

				$condtion_str[] = " " . $key . " " . $val;
			}

			$condtion_str = implode(' AND ', $condtion_str);
		}
		
		if(!$condtion_str)
			$condtion_str = '1=1';
		
		return $condtion_str;
		
	}

	
	public function get_prefered_matche_profiles($condtion_str,$offset,$limit=1,$select_people='',$type="all")
	{
		$user_id = $this->session->userdata('user_id');
		$exclude_user_ids[] = $user_id;
		
		$user = $this ->get_user_preference($user_id,'basic');		
		
		if($type=="all")
		{
			//New Date : Find all matches			
			if($excluded_users_ids_data = $this->general->sql_query("SELECT DISTINCT target_user_id FROM user_decision WHERE user_decision.user_id = '" . $user_id . "' AND decision IS NOT NULL"))
			{
				foreach($excluded_users_ids_data  as $value)
					$exclude_user_ids[] = $value['target_user_id'];
			}
		}
				
		$exclude_user_ids_str = implode(',', $exclude_user_ids);
				
		if($select_people=='')
		{
			$select_people = 'user.gender_id, user.ethnicity_id,user.user_id, user.facebook_id, user.birth_date, user.user_id, user.first_name, user.last_name, user.num_date_tix, user.want_age_range_lower, user.want_age_range_upper, user.last_active_time, TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE()) as age';
		}
		
		$order_by['last_active_time'] = 'desc';
		//2) Gender: Show only people where current user's gender is within their want gender ids (only apply this filter for users who have specified their gender wants)
		//2) AGE: Show only people where current user's age is within their want age range (only apply this filter for users who have specified both their upper and lower age range wants)
		//2) Ethnicity: Show only people where current user's ethnicity is within their want ethnicities (only apply this filter for users who have specified their ethnicity wants)
		 
		$sql = "SELECT " . $select_people . " FROM user
		INNER JOIN user_email as email ON email.user_id = user.user_id AND email.is_contact = 1 AND email.is_verified = 1 
		JOIN user_want_gender as uwg ON uwg.user_id = user.user_id AND uwg.gender_id = '".$user['gender_id']."'
    	LEFT JOIN user_want_ethnicity as uwe ON uwe.user_id = user.user_id AND uwe.ethnicity_id = '".$user['ethnicity_id']."'                     
		WHERE user.user_id NOT IN(".$exclude_user_ids_str.") 
		AND " . $condtion_str . " 
		AND user.want_age_range_lower <= '" . $user['age'] . "' 
        AND user.want_age_range_upper >= '" . $user['age']. "' 
        ORDER BY ";
		
		
		
		if($type=="all")
		{
			$sql .= "user.last_active_time desc";
		}
		else {
			$sql .= "RAND()";
		}
		
		if($limit){
			$sql .= " LIMIT " . $offset . ",".$limit;	
		}
		//echo $sql;exit;
		$people = $this -> general -> sql_query($sql);
		return $people ;
		
	}
}