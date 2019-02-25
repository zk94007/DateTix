<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Model_date extends CI_Model {

	public function save_date_step1($insert_array) {
		//$new_array['requested_user_id'] = date('Y-m-d H:i:s');
		//$insert_array   = array_merge($insert_array, $new_array);
		$this -> db -> insert('date', $insert_array);
		return $this -> db -> insert_id();
	}

	public function get_date_type($language_id = '1') {
		$this -> db -> select('date_type_id,description,view_order');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> where('is_active', 1);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('date_type');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_relationship_type($language_id = '1') {
		$this -> db -> select('relationship_type_id,description,view_order');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('relationship_type');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_date_payer($language_id = '1') {
		$this -> db -> select('date_payer_id,description,view_order');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('date_payer');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function update_date_step($date_id, $data) {
		$this -> db -> where('date_id', $date_id);
		
		$this -> db -> update('date', $data);
	}

	public function get_gender($language_id = '1') {
		$this -> db -> select('gender_id,description,view_order');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('gender');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_ethnicity($language_id = '1') {
		$this -> db -> select('ethnicity_id,description,view_order');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('ethnicity');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_neighbourhood($language_id, $city_id) {
		$this -> db -> select('neighborhood_id,description,view_order');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> where('city_id', $city_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('neighborhood');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_cuisine($language_id = '1') {
		$this -> db -> select('cuisine_id,description,view_order');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('cuisine');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_cuisine_with_category($language_id = '1') {

		$this -> db -> select('cuisine_category_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('cuisine_category');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
			foreach ($results as $key => $value) {
				$this -> db -> select('cuisine_id,description');
				$this -> db -> where('display_language_id', $language_id);
				$this -> db -> where('cuisine_category_id', $value['cuisine_category_id']);
				$this -> db -> order_by('view_order', 'ASC');
				$query = $this -> db -> get('cuisine');
				if ($query -> num_rows() > 0) {
					$results[$key]['list'] = $query -> result_array();
				}
			}
		}
		return $results;
	}

	public function get_budget($language_id = '1') {
		$this -> db -> select('budget_id,description,view_order');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('budget');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_merchnat_list($neighbourhood = '', $cuisine = '', $budget_id = '', $sortby = '', $start = '', $end = '', $city = '', $date_type_id = '') {

		$this -> db -> select('merchant.price_range,merchant.merchant_id,merchant.name,merchant.address,merchant.phone_number,merchant.website_url,merchant.review_url,merchant_photo.photo_url');
		$this -> db -> join('merchant_photo', 'merchant_photo.merchant_id = merchant.merchant_id', 'left');
		$this -> db -> join('merchant_cuisine', 'merchant_cuisine.merchant_id = merchant.merchant_id', 'left');
		$this -> db -> join('merchant_date_type', 'merchant_date_type.merchant_id = merchant.merchant_id', 'left');

		if (!empty($neighbourhood)) {
			$this -> db -> where_in('neighborhood_id ', explode(',', $neighbourhood));
		} else {
			$neighbourhood1 = $this -> db -> query("select group_concat(distinct neighborhood_id) as neighborhood_id from neighborhood where city_id='" . $city . "'") -> row_array();
			if($neighbourhood1['neighborhood_id'])
			{
				$this -> db -> where_in('neighborhood_id ', explode(',', $neighbourhood1['neighborhood_id']));	
			}
		}
		if (!empty($cuisine)) {
			$this -> db -> where_in('merchant_cuisine.cuisine_id ', explode(',', $cuisine));
		}
		if ($budget_id) {
			$this -> db -> where('budget_id', $budget_id);
		}
		if ($date_type_id) {
			//$this -> db -> where('merchant_date_type.date_type_id', $date_type_id);
		}

		$this -> db -> where('merchant.is_active', '1');
		$this -> db -> group_by('merchant.merchant_id');
		$sort_option = 'DESC';
		
		if($sortOpt = $this->input->post('sortby_option'))
		{
			$sort_option = $sortOpt;
		}
		
		if ($sortby == 'Name') {
			$this -> db -> order_by('merchant.name', $sort_option);
		} elseif ($sortby == 'Featured') {
			$this -> db -> order_by('merchant.is_featured', 'DESC');
			$this -> db -> order_by('merchant.name', 'ASC');
		} elseif ($sortby == 'Price') {
			$this -> db -> order_by('merchant.price_range', $sort_option);
		} else {
			$this -> db -> order_by('merchant.view_order', 'ASC');
		}

		$this -> db -> limit($end, $start);
		$result = $this -> db -> get('merchant');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_date_package($language_id = '1') {
		$this -> db -> select('budget_id,description,view_order');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('budget');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function get_date_detail_by_id($date_id, $language_id = '1') {

		$this -> db -> select('d.*, 
                    dt.description as date_type,
                    rt.description as intention_type,
                    dp.description as date_payer,
                    g.description as gender,
                    e.description as ethnicity,
                    m.merchant_id as mid,
                    m.name,
                    m.neighborhood_id,
                    m.address,
                    m.phone_number,
                    m.website_url,
                    m.review_url,
                    rt.num_date_tix as relationship_num_date_tix,
                    mb.num_date_tix as budget_num_date_tix,
                    mb.description as venue,
                    u.user_id as user_id,
                    u.first_name as first_name,
                    u.last_name as last_name,
                    u.gender_id as gender_id,
                    u.birth_date,
                    u.facebook_id,
                    u.last_active_time as user_last_active_time,
                    CASE
					WHEN
						u.birth_date != "0000-00-00" 	
					THEN 
						TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
					END as age,
                    up.photo as user_photo');

		$this -> db -> join('date_type as dt', 'dt.date_type_id = d.date_type_id', 'left');
		$this -> db -> join('relationship_type as rt', 'rt.relationship_type_id = d.date_relationship_type_id', 'left');
		$this -> db -> join('date_payer as dp', 'dp.date_payer_id = d.date_payer_id', 'left');
		$this -> db -> join('gender as g', 'FIND_IN_SET(g.gender_id,d.date_gender_ids)', 'left');
		$this -> db -> join('ethnicity as e', 'FIND_IN_SET(e.ethnicity_id,d.date_ethnicity_ids)', 'left');
		
		$this -> db -> join('merchant as m', 'm.merchant_id = d.merchant_id', 'left');
		$this -> db -> join('budget as mb', 'mb.budget_id = m.budget_id', 'left');
		$this -> db -> join('user as u', 'u.user_id=d.requested_user_id', 'left');
		$this -> db -> join('user_photo as up', 'up.user_id=u.user_id', 'left');

		$this -> db -> where('date_id', $date_id);
		$result = $this -> db -> get('date as d');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> row_array();
		}
		return $results;
	}
	
	
	public function get_detail_by_id($date_id) {
		$user_id = $this->session->userdata('user_id');
		$language_id = $this -> session -> userdata('sess_language_id');
		$this -> db -> select('d.*, 
                    dt.description as date_type,
                    rt.description as intention_type,
                    m.name,
                    m.address,
                    m.phone_number,
                    m.website_url,
                    m.review_url,
                    rt.num_date_tix as relationship_num_date_tix,
                    u.user_id as user_id,
                    u.first_name as first_name,
                    u.last_name as last_name,
                    u.gender_id as gender_id,
                    u.facebook_id
                    ');

		$this -> db -> join('date_type as dt', 'dt.date_type_id = d.date_type_id', 'left');
		$this -> db -> join('relationship_type as rt', 'rt.relationship_type_id = d.date_relationship_type_id', 'left');
		$this -> db -> join('merchant as m', 'm.merchant_id = d.merchant_id', 'left');
		$this -> db -> join('user as u', 'u.user_id=d.requested_user_id', 'left');
		
		$this -> db -> where('date_id', $date_id);
		
		//$this -> db -> where('dt.display_language_id', $language_id);
		//$this -> db -> where('rt.display_language_id', $language_id);		
		
		$this -> db -> where('d.requested_user_id', $user_id);
		$this -> db -> limit(1);		
		$result = $this -> db -> get('date as d');
		
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> row_array();
		}
		return $results;
		
	}

	public function get_user_date_detail($user_id, $type = "") {
		if ($type == "count") {
			$this -> db -> select('d.date_id');
		} else {
			$this -> db -> select('d.*, 
                    dt.description as date_type,
                    rt.description as intention_type,
                    dp.description as date_payer,
                    g.description as gender,
                    e.description as ethnicity,
                    m.merchant_id as mid,
                    m.name,
                    m.address,
                    m.phone_number,
                    m.website_url,
                    m.review_url,
                    rt.num_date_tix as relationship_num_date_tix,
                    mb.num_date_tix as budget_num_date_tix,
                    mb.description as venue');
		}

		$this -> db -> join('date_type as dt', 'dt.date_type_id = d.date_type_id', 'left');
		$this -> db -> join('relationship_type as rt', 'rt.relationship_type_id = d.date_relationship_type_id', 'left');
		
		$this -> db -> join('date_payer as dp', 'dp.date_payer_id = d.date_payer_id', 'inner');
		$this -> db -> join('gender as g', 'FIND_IN_SET(g.gender_id,d.date_gender_ids)', 'inner');
		$this -> db -> join('ethnicity as e', 'FIND_IN_SET(e.ethnicity_id,d.date_ethnicity_ids)', 'inner');
		
		$this -> db -> join('merchant as m', 'm.merchant_id = d.merchant_id', 'left');
		$this -> db -> join('budget as mb', 'mb.budget_id = m.budget_id', 'left');

		$this -> db -> where('d.requested_user_id', $user_id);
		$this -> db -> group_by('d.date_id');
		$result = $this -> db -> get('date as d');
		//echo $this->db->last_query();exit;
		$results = array();
		if ($type == "count") {
			$results = 0;
		}

		if ($result -> num_rows() > 0) {
			if ($type == "count") {
				$results = $result -> num_rows();
			} else {
				$results = $result -> result_array();
			}
		}
		return $results;
	}

	public function get_other_date_detail($user_id, $limit = NULL, $offset = NULL,$type="all") {
		
		$this -> db -> select(' DISTINCT d.date_id');

		$this -> db -> join('user as u', 'u.user_id=d.requested_user_id', 'left');
		$this -> db -> join('user_want_gender as h_want_gender', 'h_want_gender.user_id = u.user_id', 'left');
		
		//$this -> db -> join('date_type as dt', 'dt.date_type_id = d.date_type_id', 'left');
		//$this -> db -> join('relationship_type as rt', 'rt.relationship_type_id = d.date_relationship_type_id', 'left');
		//$this -> db -> join('date_payer as dp', 'dp.date_payer_id = d.date_payer_id', 'left');
		//$this -> db -> join('merchant as m', 'm.merchant_id = d.merchant_id', 'left');
		//$this -> db -> join('budget as mb', 'mb.budget_id = m.budget_id', 'left');
		//$this -> db -> join('user_photo as up', 'up.user_id=u.user_id', 'left');		
		//$this -> db -> join('gender as g', 'FIND_IN_SET(g.gender_id,d.date_gender_ids)', 'inner');
		//$this -> db -> join('ethnicity as e', 'FIND_IN_SET(e.ethnicity_id,d.date_ethnicity_ids)', 'inner');
		
		//Current user want* match with host User
		$date_setting = $this -> get_user_date_setting($user_id);
		
		
		//Show only dates by hosts whose gender is within date filter popup's selected genders
		if (!empty($date_setting['gender_id'])) {
			$this -> db -> where_in('u.gender_id', explode(',',$date_setting['gender_id']));
		}
		
		//how only dates where current user's gender is within date.gender_ids
		if (!empty($date_setting['user_gender'])) {
			//$this -> db -> where('h_want_gender.gender_id', $date_setting['user_gender']);
			$this -> db -> where("d.date_gender_ids REGEXP '[[:<:]]".$date_setting['user_gender']."[[:>:]]'");		
		}
		
		if(!empty($date_setting['want_age_range_lower']) && !empty($date_setting['want_age_range_upper']))
		{
		  	// Show only dates by hosts whose age is within date filter popup's selected age range
		 	$tmp = "TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE()) >= ".$date_setting['want_age_range_lower']."";
			$this -> db -> where($tmp);
			$tmp = "TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE()) <= ".$date_setting['want_age_range_upper']."";
		  	$this -> db -> where($tmp);
			
		}
		//2) Show only dates where current user's age is within date.age_range_lower and date.age_range_upper
		if(!empty($date_setting['user_age']))
		{
			//(temporarily comment out)
			/*
				$tmp = "d.age_range_lower <= ".$date_setting['user_age'];
				$this -> db -> where($tmp);
				$tmp = "d.age_range_upper >=".$date_setting['user_age'];
				$this -> db -> where($tmp);			
			*/ 
		}
		
		
		if (!empty($date_setting['ethnicity_id'])) {
			//Show only dates by hosts whose ethnicity is within date filter popup's selected ethnicity range
			$this -> db -> where_in('u.ethnicity_id', explode(',',$date_setting['ethnicity_id']));
		}
		//Show only dates where current user's ethnicity is within date.ethnicity_ids
		if (!empty($date_setting['user_ethnicity'])) {
			//(temporarily comment out)	
			//$this -> db -> where("d.date_gender_ids REGEXP '[[:<:]]".$date_setting['user_ethnicity']."[[:>:]]'");		
		}
		
		//Show only dates with date type within date filter popup's selected date types
		if (!empty($date_setting['date_type'])) {
			$tmp = "FIND_IN_SET (d.date_type_id, '" . $date_setting['date_type'] . "')";
			$this -> db -> where($tmp);			
		}
		
		//Show only dates with date intention within date filter popup's selected date intentions
		if (!empty($date_setting['relationship_type'])) {			
			$tmp = "FIND_IN_SET (d.date_relationship_type_id, '" . $date_setting['relationship_type'] . "')";
			$this -> db -> where($tmp);			
		}
		
		$this -> db -> where('d.requested_user_id !=', $user_id);
		$this -> db -> where('d.date_time >', date("Y-m-d H:i:s", strtotime("+15 minutes")));
		
		$this -> db -> where('d.completed_step >=', REQUIRED_DATE_COMPLETED_STEP);
		$this -> db -> where('d.status >=',1);
		
		$this -> db -> order_by('d.date_time', 'ASC');
		if ($limit) {
			$this -> db -> limit($limit, $offset);
		}

		$allDateid = $this -> db -> query("select dd.date_id from date_decision as dd where decision IS NOT NULL AND dd.user_id='" . $user_id. "'") -> result_array();
		
		if (!empty($allDateid)) {
				
			$not_include_date_ids = array();			
			foreach($allDateid as $tmp)
			{
				$not_include_date_ids[] = $tmp['date_id'];
			}
			
			$this -> db -> where_not_in('d.date_id', $not_include_date_ids);
		}
		$allBanUsers = $this -> db -> query("select d.requested_user_id
                         from date_decision as dd 
                         inner join date as d on dd.date_id=d.date_id 
                         where dd.user_id='" . $user_id . "' and decision='-1'
                         GROUP BY d.date_id
                         ") -> result_array();
        
		if (!empty($allBanUsers)) {
				
			$ban_users_id = array();			
			foreach($allBanUsers as $tmp)
			{
				$ban_users_id[] = $tmp['requested_user_id'];
			}
			$this -> db -> where_not_in('d.requested_user_id',$ban_users_id);
		}

		$result = $this -> db -> get('date as d');
		//echo $this->db->last_query();
		$return_data = 0;
		
		if ($result -> num_rows() > 0) {
		
			if($type=="all")
			{
				$results = $result -> result_array();
					
				//echo "<pre>";print_r($results);exit;
				if ($results) {
					foreach ($results as $key => $result) {
						$results[$key] = $this->get_date_detail_by_id($result['date_id']);
						
						$insertArrayDecision['date_id'] = $result['date_id'];
						$insertArrayDecision['user_id'] = $this -> user_id;
						$insertArrayDecision['decision'] = NULL;
						$this -> save_decision_viewed($insertArrayDecision);	
						
						
						$this -> general -> set_table('date_decision');
						if ($info = $this -> general -> get("count(date_decision_id) as total_views", array('date_id' => $result['date_id']))) {
							$results[$key]['total_views'] = $info['0']['total_views'];
						}
						
						
						$this -> general -> set_table('date_applicant');
						if ($info = $this -> general -> get("count(date_applicant_id) as total_applications", array('date_id' => $result['date_id'],'status !='=>3))) {
							$results[$key]['total_applications'] = $info['0']['total_applications'];
						}
						/*
						 REMOVED AS per Michael : - Comment out code for merchant photo below button bar 
						 * 
						 **/
						$this -> db -> order_by('set_primary', 'desc');
						$this -> db -> select('photo_url as merchant_photo_url')->limit(1);
						
						if($merchant_photo = $this -> db -> get_where('merchant_photo', array('merchant_id' => $results[$key]['merchant_id'])) -> row_array())
						{
							$results[$key] = array_merge($results[$key],$merchant_photo );
						}
						
						//echo "<pre>";print_r($results[$key]);exit;
						//$results[$key]['merchant_neighborhood'] = $this -> db -> get_where('neighborhood', array('neighborhood_id' => $results[$key]['neighborhood_id'])) -> row_array();						
					}
				}
				$return_data = $results;		
			}
			else {
				$return_data  = $result -> num_rows();
			}
		}
		return $return_data;
	}

	public function get_next_date_detail($user_id, $date_id) {
		$this -> db -> select('d.*, 
                    dt.description as date_type,
                    rt.description as intention_type,
                    dp.description as date_payer,
                    g.description as gender,
                    e.description as ethnicity,
                    m.merchant_id as mid,
                    m.name,
                    m.address,
                    m.phone_number,
                    m.website_url,
                    m.review_url,
                    rt.num_date_tix as relationship_num_date_tix,
                    mb.num_date_tix as budget_num_date_tix,
                    mb.description as venue,
                    u.user_id as user_id,
                    u.first_name as first_name,
                    u.last_name as last_name,
                    u.gender_id as user_gender,
                    u.birth_date as birth_date,
                    u.facebook_id,
                    up.photo as user_photo
                ');

		$this -> db -> join('date_type as dt', 'dt.date_type_id = d.date_type_id', 'left');
		$this -> db -> join('relationship_type as rt', 'rt.relationship_type_id = d.date_relationship_type_id', 'left');
		$this -> db -> join('date_payer as dp', 'dp.date_payer_id = d.date_payer_id', 'left');
		$this -> db -> join('gender as g', 'FIND_IN_SET(g.gender_id,d.date_gender_ids)', 'inner');
		$this -> db -> join('ethnicity as e', 'FIND_IN_SET(e.ethnicity_id,d.date_ethnicity_ids)', 'inner');
		$this -> db -> join('merchant as m', 'm.merchant_id = d.merchant_id', 'left');
		$this -> db -> join('budget as mb', 'mb.budget_id = m.budget_id', 'left');
		$this -> db -> join('user as u', 'u.user_id=d.requested_user_id', 'left');
		$this -> db -> join('user_photo as up', 'up.user_id=u.user_id and set_primary=0', 'left');

		//$allDateid = $this -> db -> query("select group_concat(date_id) as date_id from date_decision as dd where dd.user_id='" . $user_id . "'") -> row_array();
		$allDateid = $this -> db -> query("select dd.date_id from date_decision as dd where dd.user_id='" . $user_id. "'") -> result_array();
		
		if (!empty($allDateid)) {
				
			$not_include_date_ids = array();			
			foreach($allDateid as $tmp)
			{
				$not_include_date_ids[] = $tmp['date_id'];
			}
			
			$this -> db -> where_not_in('d.date_id', $not_include_date_ids);
		}

		$date_setting = $this -> get_user_date_setting($user_id);
		$where = "(";
		if (!empty($date_setting['date_type'])) {
			//$this -> db -> or_where_in('d.date_type_id', explode(',',$date_setting['date_type']));
			//explode(',',$date_setting['date_type']);
			$where .= " d.date_type_id  IN (" . $date_setting['date_type'] . ") OR";

		}
		if (!empty($date_setting['relationship_type'])) {
			//$this -> db -> or_where_in('d.date_relationship_type_id', explode(',',$date_setting['relationship_type']));
			$where .= " d.date_relationship_type_id  IN (" . $date_setting['relationship_type'] . ") OR";
		}
		if (!empty($date_setting['gender_id'])) {
			// $this -> db -> or_where_in('d.date_gender_ids', explode(',',$date_setting['gender_id']));
			$where .= "  d.date_gender_ids  IN (" . $date_setting['gender_id'] . ") OR";
		}
		if (!empty($date_setting['ethnicity_id'])) {
			// $this -> db -> or_where_in('d.date_ethnicity_ids', explode(',',$date_setting['ethnicity_id']));
			$where .= "  d.date_ethnicity_ids  IN (" . $date_setting['ethnicity_id'] . ")";
		}
		/* if(!empty($date_setting['body_type_id'])){
		 $this -> db -> join('user_want_body_type as ubt', 'ubt.user_id=d.requested_user_id', 'left');
		 $this -> db -> or_where_in('ubt.body_type_id', explode(',',$date_setting['body_type_id']));
		 }
		 if(!empty($date_setting['relationship_status'])){
		 $this -> db -> join('user_want_relationship_status as urs', 'urs.user_id=d.requested_user_id', 'left');
		 $this -> db -> or_where_in('urs.relationship_status_id', explode(',',$date_setting['relationship_status']));
		 }
		 if(!empty($date_setting['religious_belief'])){
		 $this -> db -> join('user_want_religious_belief as urb', 'urb.user_id=d.requested_user_id', 'left');
		 $this -> db -> or_where_in('urb.religious_belief_id', explode(',',$date_setting['religious_belief']));
		 }
		 if(!empty($date_setting['descriptive_word'])){
		 $this -> db -> join('user_want_descriptive_word as udw', 'udw.user_id=d.requested_user_id', 'left');
		 $this -> db -> or_where_in('udw.descriptive_word_id', explode(',',$date_setting['descriptive_word']));
		 }
		 if(!empty($date_setting['smoking_status'])){
		 $this -> db -> join('user_want_smoking_status as uss', 'uss.user_id=d.requested_user_id', 'left');
		 $this -> db -> or_where_in('uss.smoking_status_id', explode(',',$date_setting['smoking_status']));
		 }
		 if(!empty($date_setting['drinking_status'])){
		 $this -> db -> join('user_want_drinking_status as uds', 'uds.user_id=d.requested_user_id', 'left');
		 $this -> db -> or_where_in('uds.drinking_status_id', explode(',',$date_setting['drinking_status']));
		 }
		 if(!empty($date_setting['exercise_frequency'])){
		 $this -> db -> join('user_want_exercise_frequency as uef', 'uef.user_id=d.requested_user_id', 'left');
		 $this -> db -> or_where_in('uef.exercise_frequency_id', explode(',',$date_setting['excercise_status']));
		 }
		 if(!empty($date_setting['residence_type'])){
		 $this -> db -> join('user_want_residence_type as urt', 'urt.user_id=d.requested_user_id', 'left');
		 $this -> db -> or_where_in('urt.residence_type_id', explode(',',$date_setting['residence_type']));
		 }
		 if(!empty($date_setting['education_level'])){
		 $this -> db -> join('user_want_education_level as uel', 'uel.user_id=d.requested_user_id', 'left');
		 $this -> db -> or_where_in('uel.education_level_id', explode(',',$date_setting['education_level']));
		 }
		 if(!empty($date_setting['school'])){
		 $this -> db -> join('user_want_school as us', 'us.user_id=d.requested_user_id', 'left');
		 $this -> db -> or_where_in('us.school_id', explode(',',$date_setting['school']));
		 }
		 if(!empty($date_setting['company'])){
		 $this -> db -> join('user_want_company as uc', 'uc.user_id=d.requested_user_id', 'left');
		 $this -> db -> or_where_in('uc.company_id', explode(',',$date_setting['company']));
		 }

		 $this->db->where("u.height >=","(select want_height_range_lower from user where user_id=".$user_id.")");
		 $this->db->where("u.height <=","(select want_height_range_upper from user where user_id=".$user_id.")");
		 */
		$lastwo = substr($where, -3);
		if (trim($lastwo) == 'OR') {
			$customWhere = substr($where, 0, -2);
		} else {
			$customWhere = $where;
		}

		$customWhere .= ")";
		$this -> db -> where($customWhere);

		$this -> db -> where('d.requested_user_id !=', $user_id);
		$this -> db -> where('d.date_id > ', $date_id);
		$this -> db -> where('DATE(d.date_time) >', date("Y-m-d H:i:s", strtotime("+30 minutes")));
		$this -> db -> order_by('d.date_time', 'ASC');
		$this -> db -> group_by('d.date_id');
		$result = $this -> db -> get('date as d');

		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> row_array();
		}
		return $results;
	}

	public function save_decision_viewed($insert_array) {
		
		$insert_array['decision_time'] = SQL_DATETIME;
		if(isset($insert_array['date_id']) && isset($insert_array['user_id']))
		{
			$this -> db -> where('date_id', $insert_array['date_id']);
			$this -> db -> where('user_id', $insert_array['user_id']);
			
			$result = $this -> db -> get('date_decision');
			
			if ($result -> num_rows() > 0) {
				
				$this -> db -> where('date_id', $insert_array['date_id']);
				$this -> db -> where('user_id', $insert_array['user_id']);
				
				unset($insert_array['date_id']);
				unset($insert_array['user_id']);
				
				return $this -> db -> update('date_decision', $insert_array);
			}
			else{
				$this -> db -> insert('date_decision', $insert_array);
				return $this -> db -> insert_id();	
			}
		}
	}

	public function save_date_applicant($insert_array_decision) {
		$insert_array_decision['applied_time'] = SQL_DATETIME;
		if(isset($insert_array_decision['date_id']) && isset($insert_array_decision['applicant_user_id']))
		{
			$this -> db -> where('date_id', $insert_array_decision['date_id']);
			$this -> db -> where('applicant_user_id', $insert_array_decision['applicant_user_id']);
			
			$result = $this -> db -> get('date_applicant');
			//echo $this->db->last_query();
			if ($result -> num_rows() > 0) {
				
				$this -> db -> where('date_id', $insert_array_decision['date_id']);
				$this -> db -> where('applicant_user_id', $insert_array_decision['applicant_user_id']);
				
				unset($insert_array_decision['date_id']);
				unset($insert_array_decision['applicant_user_id']);
				
				return $this -> db -> update('date_applicant', $insert_array_decision);
			}
			else{
				$this -> db -> insert('date_applicant', $insert_array_decision);
				return $this -> db -> insert_id();	
			}
		}
		
	}
	/*
	 * Function send counter of total unread msg of particular user
	 */ 
	public function total_unread_msg($user_id=""){
		if($user_id != "")
		{
			$data = $this -> db -> query("SELECT count(*) as total_unread FROM `user_chat`  WHERE to_user_id='" . $user_id. "' AND is_read='0'") -> row_array();
			return $data['total_unread'];			
		}
		else {
			return 0;
		}
		
	}
	
	public function get_chat_history($userid,$page_no=1) {
		$limit = 20;
		if($page_no==1)
		{
			$offset = 0;
		}
		else {
			$offset = $page_no * $limit;
		}
		
		$results = $this -> db -> query("
                        SELECT * FROM(
	                        SELECT distinct(to_user_id) as other_user_id, from_user_id as main_user
	                        FROM user_chat where from_user_id='" . $userid . "'
	                        UNION
	                        SELECT distinct(from_user_id) as other_user_id,to_user_id as main_user
	                        FROM user_chat where to_user_id='" . $userid . "'
                        ) AS qry
                        WHERE other_user_id != main_user
                        ORDER BY qry.other_user_id
                        ") -> result_array();
		/*
		 $chat_lines = "SELECT DISTINCT from_user_id, to_user_id						
						FROM user_chat 
						WHERE
						from_user_id='" . $userid . "' OR to_user_id='" . $userid . "'
						GROUP BY to_user_id
                        ORDER BY is_read DESC, chat_message_time DESC";
		
		$chat_lines = "SELECT from_user_id, to_user_id						
						FROM user_chat 
						WHERE
						from_user_id != to_user_id 
						AND
						(
							(from_user_id ='" . $userid . "' AND to_user_id !='" . $userid . "') 
							OR
							(to_user_id='" . $userid . "' AND from_user_id !='" . $userid . "')
						)
						GROUP BY from_user_id
                        ORDER BY is_read DESC, chat_message_time DESC";
		
		$results = $this -> db -> query($chat_lines) -> result_array();
         
		  * 
		 *  */
		//echo "<pre>";print_r($results);exit;
		
		foreach ($results as $key => $val) {
					
			/* Execute with second query	
			if($userid == $val['from_user_id'])
			{
				$val['main_user'] = $val['from_user_id'];
				$val['other_user_id'] = $val['to_user_id'];				
				
				
			}
			else {
				$val['main_user'] = $val['to_user_id'];
				$val['other_user_id'] = $val['from_user_id'];
			}
			$results[$key] = $val;
			*/
			// get other user detail
			$getuserDetail = $this -> db -> query("select
                       u.first_name as first_name,
                       u.last_name as last_name,
                       u.birth_date as birth_date,
                       u.facebook_id,
                       up.photo as user_photo,
                       g.description as user_gender,
                       CASE
                       	WHEN
                            u.birth_date != '0000-00-00' 	
                       	THEN 
                       		TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
                       END as user_age
                       FROM user as u
                       LEFT join user_photo as up on up.user_id=u.user_id  and set_primary='1'
                       LEFT join gender as g on g.gender_id=u.gender_id 
                       WHERE u.user_id='" . $val['other_user_id'] . "'
                       ") -> row_array();
			$results[$key]['first_name'] = $getuserDetail['first_name'];
			$results[$key]['last_name'] = $getuserDetail['last_name'];
			$results[$key]['photo'] = $getuserDetail['user_photo'];
			$results[$key]['user_gender'] = $getuserDetail['user_gender'];
			$results[$key]['birth_date'] = $getuserDetail['birth_date'];
			$results[$key]['user_age'] = $getuserDetail['user_age'];
			
			// get last conversation messsage for display
			$getLastMessage = $this -> db -> query("SELECT * 
                                                FROM `user_chat` 
                                                where (from_user_id='" . $userid . "' or to_user_id='" . $userid . "')
                                                and
                                                (from_user_id='" . $val['other_user_id'] . "' or to_user_id='" . $val['other_user_id'] . "')
                                                order by chat_message_time desc
                                                limit 1") -> row_array();

			$results[$key]['last_chat_message'] = $getLastMessage['chat_message'];
			$results[$key]['is_read'] = $getLastMessage['is_read'];
			$results[$key]['last_chat_time'] = $getLastMessage['chat_message_time'];

			// get total unread message count
			$getUnreadMessage = $this -> db -> query("SELECT count(*) as total_unread
                                                FROM `user_chat` 
                                                where to_user_id='" . $userid . "' AND from_user_id='" . $val['other_user_id'] . "' AND is_read='0'") -> row_array();
			$results[$key]['total_unread'] = $getUnreadMessage['total_unread'];

			// get expire date
			$dateExpired = $this -> db -> query("select d.date_time as date_time
                                from date as d 
                                left join date_applicant as da on da.date_id = d.date_id
                                where d.completed_step >= ".REQUIRED_DATE_COMPLETED_STEP."
                                and (da.applicant_user_id = '" . $userid . "' OR d.requested_user_id = '" . $userid . "')
                                and (da.applicant_user_id = '" . $val['other_user_id'] . "' OR d.requested_user_id = '" . $val['other_user_id'] . "')
                                order by d.date_time desc limit 1") -> row_array();
			//and d.date_time >=  NOW()
			$now = time();
			// or your date as well
			$results[$key]['chat_expired_date'] = $dateExpired?$dateExpired['date_time']:NULL;
			
			$datetime1 = new DateTime('now');
			$datetime2 = new DateTime($results[$key]['chat_expired_date']);
			
			$interval = $datetime1->diff($datetime2);
			$results[$key]['expiry_days'] = $interval->format('%R%a');
			
			/*
			$your_date = strtotime(@$dateExpired['date_time']);
			$datediff = $your_date - $now;
			$results[$key]['expiry_days'] = floor($datediff / (60 * 60 * 24));
			*/
			
			 
			 /**/
			// first connected date
			// get expire date
			$dateConnected = $this -> db -> query("select da.applied_time as applied_time
                                FROM date_applicant as da
                                JOIN date as d on da.date_id = d.date_id
                                where d.completed_step >= ".REQUIRED_DATE_COMPLETED_STEP."
                                AND d.date_time >= DATE(NOW()) - INTERVAL 7 DAY
                                AND (da.applicant_user_id = '" . $userid . "' OR d.requested_user_id = '" . $userid . "')
                                AND (da.applicant_user_id = '" . $val['other_user_id'] . "' OR d.requested_user_id = '" . $val['other_user_id'] . "')
                                order by da.applied_time asc limit 1")->row_array();
			
			$now = time(); // or your date as well
			$your_date = strtotime(@$dateConnected['applied_time']);
		    $datediff = $now-$your_date;
        	$results[$key]['first_connected_date'] = floor($datediff/(60*60*24));            
        }
		//echo "<pre>";print_r($results);exit;
		return $results;
	}
	
	public function get_chat_history_pagination($userid,$page_no=1) {
		$limit = 20;
		if($page_no==1)
		{
			$offset = 0;
		}
		else {
			$offset = ($page_no-1) * $limit;
		}
		
		$results = $this -> db -> query("SELECT * FROM((
	                        SELECT DISTINCT(to_user_id) as other_user_id
	                        FROM user_chat where from_user_id='" . $userid . "' GROUP BY to_user_id order by user_chat_id asc)
	                        UNION
	                        (SELECT DISTINCT(from_user_id) as other_user_id
	                        FROM user_chat where to_user_id='" . $userid . "' GROUP BY from_user_id order by user_chat_id asc)
                        ) AS qry
                        WHERE other_user_id != '" . $userid . "'
                        LIMIT " . $offset . ",".$limit) -> result_array();
		
		echo "SELECT * FROM((
	                        SELECT DISTINCT(to_user_id) as other_user_id
	                        FROM user_chat where from_user_id='" . $userid . "' GROUP BY to_user_id order by user_chat_id asc)
	                        UNION
	                        (SELECT DISTINCT(from_user_id) as other_user_id
	                        FROM user_chat where to_user_id='" . $userid . "' GROUP BY from_user_id order by user_chat_id asc)
                        ) AS qry
                        WHERE other_user_id != '" . $userid . "'
                        LIMIT " . $offset . ",".$limit;exit;
                        
		foreach ($results as $key => $val) {
			$val['main_user'] = $userid;	
				
			// get other user detail
			$getuserDetail = $this -> db -> query("select
                       u.first_name as first_name,
                       u.last_name as last_name,
                       u.birth_date as birth_date,
                       up.photo as user_photo,
                       u.facebook_id,
                       g.description as user_gender,
                       CASE
                       	WHEN
                            u.birth_date != '0000-00-00' 	
                       	THEN 
                       		TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
                       END as user_age
                       FROM user as u
                       LEFT join user_photo as up on up.user_id=u.user_id  and set_primary='1'
                       LEFT join gender as g on g.gender_id=u.gender_id 
                       WHERE u.user_id='" . $val['other_user_id'] . "'
                       ") -> row_array();
			$results[$key]['first_name'] = $getuserDetail['first_name'];
			$results[$key]['last_name'] = $getuserDetail['last_name'];
			$results[$key]['photo'] = $getuserDetail['user_photo'];
			$results[$key]['user_gender'] = $getuserDetail['user_gender'];
			$results[$key]['birth_date'] = $getuserDetail['birth_date'];
			$results[$key]['user_age'] = $getuserDetail['user_age'];
			
			// get last conversation messsage for display
			$getLastMessage = $this -> db -> query("SELECT * 
                                                FROM `user_chat` 
                                                where (from_user_id='" . $userid . "' or to_user_id='" . $userid . "')
                                                and
                                                (from_user_id='" . $val['other_user_id'] . "' or to_user_id='" . $val['other_user_id'] . "')
                                                order by chat_message_time desc
                                                limit 1") -> row_array();

			$results[$key]['last_chat_message'] = $getLastMessage['chat_message'];
			$results[$key]['is_read'] = $getLastMessage['is_read'];
			$results[$key]['last_chat_time'] = $getLastMessage['chat_message_time'];

			// get total unread message count
			$getUnreadMessage = $this -> db -> query("SELECT count(*) as total_unread
                                                FROM `user_chat` 
                                                where to_user_id='" . $userid . "' AND from_user_id='" . $val['other_user_id'] . "' AND is_read='0'") -> row_array();
			$results[$key]['total_unread'] = $getUnreadMessage['total_unread'];

			// get expire date
			$dateExpired = $this -> db -> query("select d.date_time as date_time
                                from date as d 
                                left join date_applicant as da on da.date_id = d.date_id
                                where d.completed_step >= ".REQUIRED_DATE_COMPLETED_STEP."
                                and (da.applicant_user_id = '" . $userid . "' OR d.requested_user_id = '" . $userid . "')
                                and (da.applicant_user_id = '" . $val['other_user_id'] . "' OR d.requested_user_id = '" . $val['other_user_id'] . "')
                                and d.date_time >=  NOW()
                                order by d.date_time desc limit 1") -> row_array();

			$now = time();
			// or your date as well
			$your_date = strtotime(@$dateExpired['date_time']);
			$datediff = $your_date - $now;
			$results[$key]['expiry_days'] = floor($datediff / (60 * 60 * 24));
			
			/**/
			// first connected date
			// get expire date
			$dateConnected = $this -> db -> query("select da.applied_time as applied_time
                                FROM date_applicant as da
                                JOIN date as d on da.date_id = d.date_id
                                where d.completed_step >= ".REQUIRED_DATE_COMPLETED_STEP."
                                AND d.date_time >= DATE(NOW()) - INTERVAL 7 DAY
                                AND (da.applicant_user_id = '" . $userid . "' OR d.requested_user_id = '" . $userid . "')
                                AND (da.applicant_user_id = '" . $val['other_user_id'] . "' OR d.requested_user_id = '" . $val['other_user_id'] . "')
                                order by da.applied_time asc limit 1")->row_array();
			
			$now = time(); // or your date as well
			$your_date = strtotime(@$dateConnected['applied_time']);
		    $datediff = $now-$your_date;
        	$results[$key]['first_connected_date'] = floor($datediff/(60*60*24));            
        }
		//echo "<pre>";print_r($results);exit;
		return $results;
	}

	public function get_chat_detail($other_user_id = '', $current_user = '') {

		$other_user_id = $this -> utility -> decode($other_user_id);
		$current_user = $this -> utility -> decode($current_user);
		$results['chat_detail'] = $this -> db -> query("SELECT * 
                                                FROM `user_chat` 
                                                where (from_user_id='" . $current_user . "' or to_user_id='" . $current_user . "')
                                                and
                                                (from_user_id='" . $other_user_id . "' or to_user_id='" . $other_user_id . "')
                                                order by chat_message_time asc
                                                ") -> result_array();
		$getuserDetail = $this -> db -> query("select
                                               u.user_id as user_id,
                                               u.first_name as first_name,
                                               u.facebook_id,
                                               u.last_name as last_name,
                                               u.birth_date as birth_date,
                                               u.gender_id as user_gender,
                                               CASE
												WHEN
													birth_date != '0000-00-00' 	
												THEN 
													TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
												END as user_age
                                               from user as u
                                               where u.user_id='" . $other_user_id . "'
                                               ") -> row_array();
		$results['other_user_id'] = $other_user_id;		
		$results['other_first_name'] = $getuserDetail['first_name'];
		$results['other_last_name'] = $getuserDetail['last_name'];
		$results['other_photo'] = $this->model_user->get_photos($other_user_id,'profile');

		$results['user_gender'] = $getuserDetail['user_gender'];
		$results['birth_date'] = $getuserDetail['birth_date'];
		$results['user_age'] = $getuserDetail['user_age'];
		//echo "<pre>";print_r($results);exit;
		
		// current_user_detail
		$getcurrentuserDetail = $this -> db -> query("select
                                               u.first_name as first_name,
                                               u.last_name as last_name,
                                               u.facebook_id
                                               from user as u
                                               where u.user_id='" . $current_user . "'
                                               ") -> row_array();

		$results['current_first_name'] = $getcurrentuserDetail['first_name'];
		$results['current_last_name'] = $getcurrentuserDetail['last_name'];
		$results['current_photo'] = $this->model_user->get_photos($current_user,'profile'); 

		$dateExpired = $this -> db -> query("select d.date_time as date_time
                                from date as d 
                                left join date_applicant as da on da.date_id = d.date_id
                                where d.completed_step >= ".REQUIRED_DATE_COMPLETED_STEP."
                                and (da.applicant_user_id = '" . $current_user . "' OR d.requested_user_id = '" . $current_user . "')
                                and (da.applicant_user_id = '" . $other_user_id . "' OR d.requested_user_id = '" . $other_user_id . "')
                                order by d.date_time desc limit 1
                                ") -> row_array();
		//and d.date_time >=  NOW()
		
		$results['chat_expired_date'] = $dateExpired?$dateExpired['date_time']:NULL;
		$datetime1 = new DateTime('now');
		$datetime2 = new DateTime($results['chat_expired_date']);
		
		$interval = $datetime1->diff($datetime2);
		$results['expiry_days'] = $interval->format('%R%a');
		
		return $results;
	}

	public function last_date_preference($user_id) {
		/*$this -> db -> select('*');
		 $this -> db -> where('requested_user_id', $user_id);
		 $this -> db -> where('completed_step', 5);
		 $this -> db -> order_by('date_time', 'desc');

		 $this -> db -> limit(1);
		 $result = $this -> db -> get('date');

		 $results = array();
		 if ($result -> num_rows() > 0) {
		 $results = $result -> result_array();
		 $results = $results['0'];
		 }*/
		$results = $this -> db -> query("select * from date where requested_user_id='" . $user_id . "' and completed_step >='".REQUIRED_DATE_COMPLETED_STEP."' order by date_id desc limit 1") -> row_array();
		return $results;
	}

	public function user_date($userid, $dateStatus = 'upcoming') {
		$this -> db -> select('d.status as status,d.date_id as date_id, d.requested_user_id as requested_user_id, d.date_time as date_time, 
							da.applicant_user_id,
	                       dt.description as date_type,
	                       rt.description as intention_type,
	                       dp.description as date_payer,
	                       g.description as gender,
	                       e.description as ethnicity,
	                       m.merchant_id as mid, m.name, m.address, m.phone_number, m.website_url,m.review_url,
	                       rt.num_date_tix as relationship_num_date_tix,
	                       mb.num_date_tix as budget_num_date_tix,mb.description as venue');
		$this -> db -> join('date_applicant as da', 'da.date_id = d.date_id', 'left');
		$this -> db -> join('date_type as dt', 'dt.date_type_id = d.date_type_id', 'left');
		$this -> db -> join('relationship_type as rt', 'rt.relationship_type_id = d.date_relationship_type_id', 'left');
		$this -> db -> join('date_payer as dp', 'dp.date_payer_id = d.date_payer_id', 'left');
		$this -> db -> join('gender as g', 'FIND_IN_SET(g.gender_id,d.date_gender_ids)', 'inner');
		$this -> db -> join('ethnicity as e', 'FIND_IN_SET(e.ethnicity_id,d.date_ethnicity_ids)', 'inner');
		$this -> db -> join('merchant as m', 'm.merchant_id = d.merchant_id', 'left');
		$this -> db -> join('budget as mb', 'mb.budget_id = m.budget_id', 'left');

		$this -> db -> where('d.completed_step >=', REQUIRED_DATE_COMPLETED_STEP);
		$this -> db -> where('(da.applicant_user_id = "' . $userid . '" OR d.requested_user_id = "' . $userid . '")');
		
		$this -> db -> where('da.status !=', '3');
		
		if ($dateStatus == 'upcoming') {
			$this -> db -> where('d.date_time >=', date('Y-m-d H:i:s'));
			$this -> db -> where('d.status !=', '-1');
			$this -> db -> order_by('d.date_time', 'asc');
		}
		if ($dateStatus == 'past') {
			$this -> db -> where('d.date_time <', date('Y-m-d H:i:s'));
			$this -> db -> where('d.status !=', '-1');
			$this -> db -> order_by('d.date_time', 'asc');
		}
		if ($dateStatus == 'cancel') {
			$this -> db -> where('d.status', '-1');
			$this -> db -> order_by('d.date_time', 'asc');
		}

		$this -> db -> group_by('d.date_id');
		$date_results = array();
		$result = $this -> db -> get('date as d');
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
			foreach ($results as $key => $date_info) {

				$photo_user_id = 0;
				if ($date_info['requested_user_id'] != $userid) {
					$photo_user_id = $date_info['requested_user_id'];
					$this -> general -> set_table('user');
					if ($user_info = $this -> general -> get("first_name,last_name", array('user_id' => $date_info['requested_user_id']))) {
						$date_info['hosted_by_name'] = $user_info['0']['first_name'];
					} else {
						$date_info['hosted_by_name'] = 'Unknown';
					}
				} else {
					$photo_user_id = $date_info['applicant_user_id'];
					$date_info['hosted_by_name'] = 'You';
				}

				$date_info['user_photos'] = $this -> get_current_primary_photo($date_info['requested_user_id']);

				$this -> general -> set_table('date_decision');
				if ($info = $this -> general -> get("count(date_decision_id) as total_views", array('date_id' => $date_info['date_id']))) {
					$date_info['total_views'] = $info['0']['total_views'];
				}
				$this -> general -> set_table('date_applicant');
				if ($info = $this -> general -> get("count(date_applicant_id) as total_applications", array('date_id' => $date_info['date_id'],'status !='=>3))) {
					$date_info['total_applications'] = $info['0']['total_applications'];
				}

				$date_results[$key] = $date_info;
			}
		}
		return $date_results;
	}
	public function user_upcoming_dates($userid, $type = 'count') {
		$select = 'd.*';
		if($type == 'count')
		{
			$select = 'd.date_id';	
		}
		$this -> db -> select($select);		
	
		$this -> db -> where('d.completed_step >=', REQUIRED_DATE_COMPLETED_STEP);
		$this -> db -> where('d.requested_user_id',$userid);
		$this -> db -> where('d.date_time >=', date('Y-m-d H:i:s'));
		$this -> db -> where('d.status !=', '-1');
	
		$this -> db -> order_by('d.date_time', 'asc');
	
		$result = $this -> db -> get('date as d');
		if($type == 'count')
		{
			return $result -> num_rows() ;	
		}
		else {
			return $result -> result_array();	
		}
	}
	
	public function get_applicants_by_date_id($date_id) {
		$this -> db -> where('date_id', $date_id);
		
		$this -> db -> order_by('num_date_tickets', 'desc');
		$this -> db -> order_by('applied_time', 'desc');
		
		$date_results = array();
		$result = $this -> db -> get_where('date_applicant',array('status != '=>3));
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
			foreach ($results as $key => $date_info) {

				$photo_user_id = 0;

				$photo_user_id = $date_info['applicant_user_id'];
				$this -> general -> set_table('user');
				$select = 'first_name,last_name, gender_id, CASE
					WHEN
						birth_date != "0000-00-00" 	
					THEN 
						TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
				END as age';
				if ($user_info = $this -> general -> get($select, array('user_id' => $photo_user_id))) {
					$date_info['applicant_by_name'] = $user_info['0']['first_name'];
					$date_info['age'] = $user_info['0']['age'];
					$date_info['gender_id'] = $user_info['0']['gender_id'];
					

				} else {
					$date_info['applicant_by_name'] = 'Unknown';
					$date_info['age'] = 0;
					$date_info['gender_id'] = '0';

				}
				$date_info['user_photos'] = $this -> model_date -> get_current_primary_photo($photo_user_id);
				$date_results[$key] = $date_info;
			}
		}
		return $date_results;
	}

	public function save_date_review($insert_array) {
		$this -> db -> insert('date_review', $insert_array);
		return $this -> db -> insert_id();
	}

	public function get_district($language_id, $city_id = '') {
		//TABLE RENAME

		$this -> db -> select('neighborhood_id,description');
		//$this->db->select('district_id,description');

		$this -> db -> where('display_language_id', $language_id);
		if ($city_id)
			$this -> db -> where('city_id', $city_id);
		//TABLE RENAME
		$result = $this -> db -> get('neighborhood');
		//$result = $this->db->get('district');

		$result = $result -> result_array();
		$filter_arr = array();
		$filter_arr = array('' => 'Any Neighborhood');
		if ($result) {
			foreach ($result as $value) {
				//TABLE RENAME
				$filter_arr[$value['neighborhood_id']] = $value['description'];

				//$filter_arr[$value['district_id']] = $value['description'];
			}
		}
		return $filter_arr;

		//changed by rajnish
		/*
		 $district = '<option value="">'.translate_phrase('Select district').'</option>';
		 if($result->num_rows()>0){
		 foreach($result->result_array() as $row){
		 $district .= '<option value="'.$row['district_id'].'">'.ucfirst($row['description']).'</option>';
		 }
		 }
		 //$district .='</select>';
		 return $district;

		 */
	}

	public function get_photos($user_id) {

		$image_data = array();

		$type = 'profile';

		$this -> db -> where('user_id', $user_id);
		$this -> db -> order_by('set_primary', 'desc');
		$result = $this -> db -> get('user_photo');
		$result = $result -> result_array();
		if ($result) {
			foreach ($result as $key => $value) {
				$image_data[$key] = $value;
				$image_data[$key]['url'] = base_url() . "user_photos/user_$user_id/" . $value['photo'];
			}
		}

		return $image_data;
	}

	public function get_current_primary_photo($user_id) {
		$this -> db -> select('*');
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('set_primary', '1');
		$r = "";
		$result = $this -> db -> get('user_photo');
		if ($result -> num_rows() > 0) {
			$row = $result -> row_array();
			$r = $row['user_photo_id'];
		} else {
			$row = array();
		}
		return $row;
	}

	public function update_date_applicant($date_applicant_id, $data) {
		$this -> db -> where('date_applicant_id', $date_applicant_id);
		$this -> db -> update('date_applicant', $data);
	}

	public function save_chat($insert_array) {
		$this -> db -> insert('user_chat', $insert_array);
		return $this -> db -> insert_id();
	}

	public function get_interaction_detail($other_user, $current_user) {

		$userid = $this -> utility -> decode($other_user);

		$current_user_id = $this -> utility -> decode($current_user);
		$this -> db -> select('d.date_id as date_id, d.requested_user_id as requested_user_id, d.date_time as date_time, 
							da.applicant_user_id,
	                       dt.description as date_type,
	                       rt.description as intention_type,
	                       dp.description as date_payer,
	                       g.description as gender,
	                       e.description as ethnicity,
	                       m.merchant_id as mid,
                               m.name, m.address, m.phone_number, m.website_url,m.review_url,
	                       rt.num_date_tix as relationship_num_date_tix,
	                       mb.num_date_tix as budget_num_date_tix,mb.description as venue');
		$this -> db -> join('date_applicant as da', 'da.date_id = d.date_id', 'left');
		$this -> db -> join('date_type as dt', 'dt.date_type_id = d.date_type_id', 'left');
		$this -> db -> join('relationship_type as rt', 'rt.relationship_type_id = d.date_relationship_type_id', 'left');
		$this -> db -> join('date_payer as dp', 'dp.date_payer_id = d.date_payer_id', 'left');
		$this -> db -> join('gender as g', 'FIND_IN_SET(g.gender_id,d.date_gender_ids)', 'left');
		$this -> db -> join('ethnicity as e', 'FIND_IN_SET(e.ethnicity_id,d.date_ethnicity_ids)', 'left');
		$this -> db -> join('merchant as m', 'm.merchant_id = d.merchant_id', 'left');
		$this -> db -> join('budget as mb', 'mb.budget_id = m.budget_id', 'left');

		$this -> db -> where('d.completed_step >=', REQUIRED_DATE_COMPLETED_STEP);
		$this -> db -> where('d.status', '1');
		$this -> db -> where('((da.applicant_user_id = "' . $current_user_id . '" OR d.requested_user_id = "' . $current_user_id . '") AND (da.applicant_user_id = "' . $userid . '" OR d.requested_user_id = "' . $userid . '"))');

		$this -> db -> order_by('d.date_time', 'asc');
		$this -> db -> group_by('d.date_id');
		$date_results = array();
		$result = $this -> db -> get('date as d');
		//echo $this->db->last_query();
		$results = $result -> row_array();
		return $results;
	}

	public function save_date_ticket_log($insert_array) {
		$this -> db -> insert('log_user_date_ticket', $insert_array);
		return $this -> db -> insert_id();
	}

	public function check_rating_for_date($date_id, $user_id) {
		$this -> db -> select('*');
		$this -> db -> where('date_id', $date_id);
		$this -> db -> where('review_by_user_id', $user_id);
		$result = $this -> db -> get('date_review');
		$results = $result -> row_array();
		return $results;
	}

	public function update_date_review($date_review_id, $data) {
		$this -> db -> where('date_review_id', $date_review_id);
		$this -> db -> update('date_review', $data);
	}

	public function get_body_type($language_id) {
		$this -> db -> select('body_type_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('body_type');
		//$body_type  = array(''=>translate_phrase('Select body type'));
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$body_type[$row['body_type_id']] = ucfirst($row['description']);
			}
		}
		return $body_type;
	}

	public function get_looks($language_id) {
		$this -> db -> select('looks_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('looks');
		$looks = array('' => translate_phrase('Select looks'));
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$looks[$row['looks_id']] = ucfirst($row['description']);
			}
		}
		return $looks;
	}

	public function get_eye_color($language_id) {
		$this -> db -> select('eye_color_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('eye_color');
		$eye_color = array('' => translate_phrase('Select eye color'));
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$eye_color[$row['eye_color_id']] = ucfirst($row['description']);
			}
		}
		return $eye_color;
	}

	public function get_hair_color($language_id) {
		$this -> db -> select('hair_color_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('hair_color');
		$hair_colorr = array('' => translate_phrase('Select hair color'));
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$hair_colorr[$row['hair_color_id']] = ucfirst($row['description']);
			}
		}
		return $hair_colorr;
	}

	public function get_hair_length($language_id) {
		$this -> db -> select('hair_length_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('hair_length');
		$hair_length = array('' => translate_phrase('Select hair length'));
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$hair_length[$row['hair_length_id']] = ucfirst($row['description']);
			}
		}
		return $hair_length;
	}

	public function get_skin_tone($language_id) {
		$this -> db -> select('skin_tone_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('skin_tone');
		$skin_tone = array('' => translate_phrase('Select skin tone'));
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$skin_tone[$row['skin_tone_id']] = ucfirst($row['description']);
			}
		}
		return $skin_tone;
	}

	public function get_relationship_status($language_id) {
		$this -> db -> select('relationship_status_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('relationship_status');
		$results = array();
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$relationship_status[$row['relationship_status_id']] = ucfirst($row['description']);
			}
		}
		return $relationship_status;
	}

	public function get_religious_belief($language_id) {
		$this -> db -> select('religious_belief_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('religious_belief');
		//$religious_belief   = array(''=>translate_phrase('Select religious belief'));
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$religious_belief[$row['religious_belief_id']] = ucfirst($row['description']);
			}
		}
		return $religious_belief;
	}

	public function get_spoken_language($language_id) {
		$this -> db -> select('spoken_language_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('spoken_language');
		$spoken_language = array('' => translate_phrase('Select language'));
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$spoken_language[$row['spoken_language_id']] = ucfirst($row['description']);
			}
		}
		return $spoken_language;
	}

	public function get_proficiency($language_id) {
		$this -> db -> select('spoken_language_level_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('spoken_language_level');
		$proficiency = array('' => translate_phrase('Select proficiency'));
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$proficiency[$row['spoken_language_level_id']] = ucfirst($row['description']);
			}
		}
		return $proficiency;
	}

	public function get_descriptive_word($language_id) {
		$this -> db -> select('descriptive_word_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('descriptive_word');
		$results = array();
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$descriptive_word[$row['descriptive_word_id']] = ucfirst($row['description']);
			}
		}
		return $descriptive_word;
	}

	public function get_drinking_status($language_id) {
		$this -> db -> select('drinking_status_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('drinking_status');
		$results = array();
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$drinking[$row['drinking_status_id']] = ucfirst($row['description']);
			}
		}
		return $drinking;
	}

	public function get_smoking_status($language_id) {
		$this -> db -> select('smoking_status_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('smoking_status');
		$results = array();
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$smoking[$row['smoking_status_id']] = ucfirst($row['description']);
			}
		}
		return $smoking;
	}

	public function get_exercise_frequency($language_id) {
		$this -> db -> select('exercise_frequency_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('exercise_frequency');
		$results = array();
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$exercise[$row['exercise_frequency_id']] = ucfirst($row['description']);
			}
		}
		return $exercise;
	}

	public function get_residence_type($language_id) {
		$this -> db -> select('residence_type_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('residence_type');
		$residence = array('' => translate_phrase('Select residence type'));

		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$residence[$row['residence_type_id']] = ucfirst($row['description']);
			}
		}
		return $residence;
	}

	public function get_education_level($language_id) {
		$this -> db -> select('education_level_id,description');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('education_level');
		$results = array();
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$education[$row['education_level_id']] = ucfirst($row['description']);
			}
		}
		return $education;
	}

	public function get_school($language_id) {
		$this -> db -> select('school_id,school_name');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> where('is_active', '1');
		$result = $this -> db -> get('school');
		$school = array();
		if ($result -> num_rows() > 0) {
			foreach ($result->result_array() as $row) {
				$school[$row['school_id']] = ucfirst($row['school_name']);
			}
		}
		return $school;
	}

	public function get_company($language_id = "") {
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

	public function getInterests($languageId = '') {
		//if language id is not supplied. default to english.
		$languageId = (empty($languageId)) ? $this -> session -> userdata('sess_language_id') : $languageId;

		$instance = get_instance();
		//$query = "SELECT interest_category_id,description FROM interest_category WHERE display_language_id ='".$languageId."'";
		$query = "SELECT a.interest_category_id,a.description as parentDescription, b.description, b.interest_id
                      FROM interest_category a
                      JOIN interest b ON a.interest_category_id = b.interest_category_id
                      AND a.display_language_id = b.display_language_id
                      WHERE a.display_language_id ='" . $languageId . "'";
		$result = $instance -> db -> query($query) -> result();
		if (!empty($result)) {
			$finalAray = array();
			$parentKeys = array();

			//create a seperate array of parent cat id and name.
			foreach ($result as $key => $categoryDetails) {
				if (!array_key_exists($categoryDetails -> interest_category_id, $parentKeys)) {
					$parentKeys[$categoryDetails -> interest_category_id] = $categoryDetails -> parentDescription;
				}
			}

			//create a tree structure.
			foreach ($parentKeys as $id => $categoryName) {
				$finalAray[$id] = array();
				foreach ($result as $key => $value) {
					if ($id == $value -> interest_category_id) {
						array_push($finalAray[$id], $value);
					}
				}
			}

			return array('parentDetails' => $parentKeys, 'childDetails' => $finalAray);
		}

		//return false if resultSet from database was empty.
		return false;
	}

	public function get_invite_date($date_id) {
		$results = array();
		$result['date_id'] = $date_id;
		$key = 0;
		$results[$key] = $this->get_date_detail_by_id($result['date_id']);
		
		$this -> general -> set_table('date_decision');
		if ($info = $this -> general -> get("count(date_decision_id) as total_views", array('date_id' => $result['date_id']))) {
			$results[$key]['total_views'] = $info['0']['total_views'];
		}
		$this -> general -> set_table('date_applicant');
		if ($info = $this -> general -> get("count(date_applicant_id) as total_applications", array('date_id' => $result['date_id'],'status !='=>3))) {
			$results[$key]['total_applications'] = $info['0']['total_applications'];
		}
		
		/*$this -> db -> order_by('set_primary', 'desc');
		$this -> db -> select('photo_url');
		$results[$key]['merchant_photos'] = $this -> db -> get_where('merchant_photo', array('merchant_id' => $results[$key]['merchant_id'])) -> result_array();
		$results[$key]['merchant_neighborhood'] = $this -> db -> get_where('neighborhood', array('neighborhood_id' => $results[$key]['neighborhood_id'])) -> row_array();
		*/
		$this -> db -> order_by('set_primary', 'desc');
		$this -> db -> select('photo_url as merchant_photo_url')->limit(1);
		
		if($merchant_photo = $this -> db -> get_where('merchant_photo', array('merchant_id' => $results[$key]['merchant_id'])) -> row_array())
		{
			$results[$key] = array_merge($results[$key],$merchant_photo );
		}
		
		//echo "<pre>";print_r($results);exit;
		return  $results;
	}

	public function saveInvitedApplicant($insert_array) {
		$this->save_date_applicant($insert_array);
		/*		$this -> db -> insert('date_applicant', $insert_array);
		return $this -> db -> insert_id();*/
	}

	public function checkInviteDate($user_id, $date_id) {
		$checkUser = $this -> db -> query("select * from date_invite where date_id='" . $date_id . "' and invite_user_id='" . $user_id . "'") -> result_array();
		return $checkUser;
	}

	public function get_user_date_setting($userid) {

		$settingArray = array();

		// get date type
		$user_datas = $this -> db -> query("SELECT user_id,gender_id as user_gender, ethnicity_id as user_ethnicity, want_age_range_lower,want_age_range_upper,CASE
                       	WHEN
                            birth_date != '0000-00-00' 	
                       	THEN 
                       		TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
                       END as user_age FROM user where user_id='" . $userid . "'") -> row_array();
		
		$user_preferred_date_type = $this -> db -> query("select group_concat(date_type_id) as date_type from user_preferred_date_type where user_id='" . $userid . "'") -> row_array();
		$user_want_relationship_type = $this -> db -> query("select group_concat(relationship_type_id) as relationship_type from user_want_relationship_type where user_id='" . $userid . "'") -> row_array();
		$user_want_gender = $this -> db -> query("select group_concat(gender_id) as gender_id from user_want_gender where user_id='" . $userid . "'") -> row_array();
		//echo "<pre>"; print_r($user_want_gender );exit;
		
		$user_want_ethnicity = $this -> db -> query("select group_concat(ethnicity_id) as ethnicity_id from user_want_ethnicity where user_id='" . $userid . "'") -> row_array();
		
		
		/* $user_want_body_type=$this->db->query("select group_concat(body_type_id) as body_type_id from user_want_body_type where user_id='".$userid."'")->row_array();
		 $user_want_relationship_status=$this->db->query("select group_concat(relationship_status_id) as relationship_status from user_want_relationship_status where user_id='".$userid."'")->row_array();
		 $user_want_religious_belief=$this->db->query("select group_concat(religious_belief_id) as religious_belief from user_want_religious_belief where user_id='".$userid."'")->row_array();
		 $user_want_descriptive_word=$this->db->query("select group_concat(descriptive_word_id) as descriptive_word from user_want_descriptive_word where user_id='".$userid."'")->row_array();
		 $user_want_smoking_status=$this->db->query("select group_concat(smoking_status_id) as smoking_status from user_want_smoking_status where user_id='".$userid."'")->row_array();
		 $user_want_drinking_status=$this->db->query("select group_concat(drinking_status_id) as drinking_status from user_want_drinking_status where user_id='".$userid."'")->row_array();
		 $user_want_exercise_frequency=$this->db->query("select group_concat(exercise_frequency_id) as exercise_frequency from user_want_exercise_frequency where user_id='".$userid."'")->row_array();
		 $user_want_residence_type=$this->db->query("select group_concat(residence_type_id) as residence_type from user_want_residence_type where user_id='".$userid."'")->row_array();
		 $user_want_education_level=$this->db->query("select group_concat(education_level_id) as education_level from user_want_education_level where user_id='".$userid."'")->row_array();
		 $user_want_school=$this->db->query("select group_concat(school_id) as school from user_want_school where user_id='".$userid."'")->row_array();
		 $user_want_company=$this->db->query("select group_concat(company_id) as company from user_want_company where user_id='".$userid."'")->row_array();

		 return array_merge($user_preferred_date_type,
		 $user_want_relationship_type,
		 $user_want_gender,
		 $user_want_ethnicity,
		 $user_want_body_type,
		 $user_want_relationship_status,
		 $user_want_religious_belief,
		 $user_want_descriptive_word,
		 $user_want_smoking_status,
		 $user_want_drinking_status,
		 $user_want_exercise_frequency,
		 $user_want_residence_type,
		 $user_want_education_level,
		 $user_want_school,
		 $user_want_company);*/
		return array_merge($user_datas,$user_preferred_date_type, $user_want_relationship_type, $user_want_gender, $user_want_ethnicity);

	}

	public function commonInterest($otheruser, $currentuser) {
		$consolidatedUsersInterest = array();
		$consolidatedViewersInterest = array();
		$this -> general -> set_table('user_interest');
		$interestJoins = array('user_interest' => array('interest.interest_id = user_interest.interest_id', 'inner'));
		$interestCondition = array('interest.display_language_id' => $this -> language_id, 'user_interest.user_id' => $otheruser);
		$userInterests = $this -> general -> multijoins_arr('interest.*,interest.description as interest', 'interest', $interestJoins, $interestCondition, '', 'interest.view_order asc');
		if (!empty($userInterests)) {
			foreach ($userInterests as $key => $value) {
				$consolidatedUsersInterest[$value['interest_id']] = $value['interest'];
			}
		}

		$interestCondition = array('interest.display_language_id' => $this -> language_id, 'user_interest.user_id' => $currentuser);
		$viewersInterest = $this -> general -> multijoins_arr('interest.*,interest.description as interest', 'interest', $interestJoins, $interestCondition, '', 'interest.view_order asc');
		if (!empty($viewersInterest)) {
			foreach ($viewersInterest as $key => $value) {
				$consolidatedViewersInterest[$value['interest_id']] = $value['interest'];
			}
		}

		$commonInterests = array_intersect_key($consolidatedUsersInterest, $consolidatedViewersInterest);
		return $commonInterests;
	}

	public function saveFollowMerchant($insert_array) {
		$this -> db -> insert('user_follow_merchant', $insert_array);
		return $this -> db -> insert_id();
	}
	public function send_date_invitation($invite_user_id,$dateDetail,$decision=1,$ref="")
	{
		
		$date_id = $dateDetail['date_id'];
		$user_id = $this->session->userdata('user_id');
			
		//Save invite detail
		$insertArrayInvite['date_id'] = $date_id;
		$insertArrayInvite['invite_user_id'] = $invite_user_id;		
		$this -> general -> set_table('date_invite');
		if($this -> general -> checkDuplicate($insertArrayInvite))
		{
			$update_data['invite_time'] = SQL_DATETIME;
			$update_data['status'] = '0';
			$this -> general -> update($update_data,$insertArrayInvite);
		}
		else {
			$insertArrayInvite['invite_time'] = SQL_DATETIME;
			$insertArrayInvite['status'] = '0';
			$this -> general -> save($insertArrayInvite);
		}
		
		if($ref != "new_dates")
		{
			$insertArrayDecision['target_user_id'] = $invite_user_id;
			$insertArrayDecision['user_id'] = $user_id;
			$this -> general -> set_table('user_decision');
			if($this -> general -> checkDuplicate($insertArrayDecision))
			{
				$decision_data['decision'] = $decision;
				$decision_data['decision_time'] = SQL_DATETIME;
				$this -> general -> update($decision_data,$insertArrayDecision);
			}
			else {
				$insertArrayDecision['decision'] = $decision;
				$insertArrayDecision['decision_time'] = SQL_DATETIME;
				$this -> general -> save($insertArrayDecision);				
			}	
		}
		
		$gender_type = ($dateDetail['gender_id'] == "1") ? "him" : "her";
		
		// get invite user detail
		$inviteUserDetail = $this -> db -> query("SELECT u.user_id, u.first_name,u.password, ue.email_address 
							FROM user as u 
							LEFT JOIN user_email as ue on ue.user_id=u.user_id 
							WHERE u.user_id='" . $invite_user_id . "'") -> row_array();
		$datas = $this->model_user->get_user_email($invite_user_id);
		if($inviteUserDetail['email_address'])
		{
			$subject = $dateDetail['first_name'] . translate_phrase(" has invited you to date ") . $gender_type . translate_phrase(" for ") . $dateDetail['date_type'] . " @ " . $dateDetail['name'] . " " . print_date_daytime($dateDetail['date_time']);				
			$email_content = $dateDetail['first_name'] . translate_phrase(" has invited you to date ") . $gender_type . translate_phrase(" for ") . $dateDetail['date_type'] . " @ " . $dateDetail['name'] . " " . print_date_daytime($dateDetail['date_time']) . translate_phrase(" Click on the button below to meet ") . $gender_type . " for the date:";
			$data['email_content'] = $email_content;
			$data['btn_text'] = translate_phrase('Apply Date');
			$return_url = base_url() . "dates/find_dates/" . $date_id;
			
			$user_link = $this -> utility -> encode($inviteUserDetail['user_id']);
			if ($inviteUserDetail['password']) {
				$user_link .= '/' . $inviteUserDetail['password'];
			}
			$data['btn_link'] = base_url() . 'home/mail_action/' . $user_link . '?return_to='.$return_url;
			
			
			$data['email_title'] = '';
			$email_template = $this -> load -> view('email/common', $data, true);
			
			$this -> datetix -> mail_to_user($inviteUserDetail['email_address'], $subject, $email_template);
			return $inviteUserDetail['first_name'];
		}
		else {
			return translate_phrase('Sorry, No User email found for ').$invite_user_id;
		}
	}

	public function get_date_info_by_id($date_id) {

		$this -> db -> select('d.date_id, d.date_time,
							d.date_gender_ids, d.date_ethnicity_ids,
		                    dt.description as date_type,
		                    rt.description as intention_type,
		                    m.merchant_id as mid,
		                    m.name,
		                    m.address'
						);

		$this -> db -> join('date_type as dt', 'dt.date_type_id = d.date_type_id', 'left');
		$this -> db -> join('relationship_type as rt', 'rt.relationship_type_id = d.date_relationship_type_id', 'left');
		$this -> db -> join('merchant as m', 'm.merchant_id = d.merchant_id', 'left');

		$this -> db -> where('date_id', $date_id);
		$result = $this -> db -> get('date as d');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> row_array();
		}
		return $results;
	}
	public function send_mail_to_merchant_followers($date_id) {
		
		$date_info = $this -> get_date_info_by_id($date_id);	
		
		$date_type = $date_info['date_type'];
		$date_address = $date_info['name'];
		$merchant_id = $date_info['mid'];
		$date_time = print_date_daytime($date_info['date_time']);
		
		
		$user_preference = $this -> model_people->get_user_preference($this->user_id);
		if($date_info['date_gender_ids'])
		{
			$arr = explode(',', $date_info['date_gender_ids']);
			foreach($arr as $value)
			{
				if($value)
					$user_preference['user_want_gender'][]['gender_id'] = $value;	
			}
		}
		
		if($date_info['date_ethnicity_ids'])
		{
			$arr = explode(',', $date_info['date_ethnicity_ids']);
			foreach($arr as $value)
			{
				if($value)
					$user_preference['user_want_ethnicity'][]['ethnicity_id'] = $value;	
			}
		}
		/*
		if($date_info['date_age_range'])
		{
			$arr = explode('-', $date_info['date_age_range']);
			$user_preference['want_age_range_lower'] = $arr['0'];
			$user_preference['want_age_range_upper'] = $arr['1'];			
		}
		*/
		if($date_info['age_range_lower'] && $date_info['age_range_upper'])
		{
			$user_preference['want_age_range_lower'] = $date_info['age_range_lower'];
			$user_preference['want_age_range_upper'] = $date_info['age_range_upper'];			
		}
		
		$prefered_match_cluase = $this -> model_people->formate_prefered_match_cluase($user_preference);
		
		$user = $user_preference;
		
		$gender_type = ($user['gender_id'] == "1") ? "him" : "her";
		$subject = 'Someone has hosted a date at ' . $date_address . ' for ' . $date_time;
		$data['email_content'] = 'Someone has hosted ' . $date_type . ' @ ' . $date_address . ' at ' . $date_time . '. Click on the button below to find out who and apply for the date:';
		
		$data['email_title'] = '';
		$return_url = base_url() . 'dates/find_dates/' . $date_id;
		
		
		$sql = "SELECT um.user_id, um.email_address ,  user.password,user.first_name
					FROM user
					JOIN user_follow_merchant as ufm ON ufm.user_id = user.user_id 
                    JOIN user_email as um ON ufm.user_id=um.user_id AND is_contact=1 AND is_verified = 1
                    
                    JOIN user_want_gender as uwg ON uwg.user_id = user.user_id AND uwg.gender_id = '".$user['gender_id']."'
                    JOIN user_want_ethnicity as uwe ON uwe.user_id = user.user_id AND uwe.ethnicity_id = '".$user['ethnicity_id']."'
                    WHERE um.user_id != '" . $user['user_id'] . "'
                    AND ufm.merchant_id='" . $merchant_id . "'
                    
                    AND user.want_age_range_lower <= '" . $user['age'] . "'
                    AND user.want_age_range_upper >= '" . $user['age']. "'
                    
                    AND ".$prefered_match_cluase."
                    GROUP BY user.user_id";
		$getAllemail = $this -> db -> query($sql) -> result_array();
		if($getAllemail)
		{
			foreach ($getAllemail as $key => $val) {
					//Dynamic autologin link
				$user_link = $this -> utility -> encode($val['user_id']);
				if ($val['password']) {
					$user_link .= '/' . $val['password'];
				}
				$data['btn_link'] = base_url() . 'home/mail_action/' . $user_link . '?return_to='.$return_url;
				$data['btn_text'] = translate_phrase('View Date');
				$email_template = $this -> load -> view('email/common', $data, true);
		
				
				$this -> datetix -> mail_to_user($val['email_address'], $subject, $email_template);
			} 	
		}
	}

	public function send_mail_to_user_followers($date_id) {
		$date_info = $this -> get_date_detail_by_id($date_id);
		$date_type = $date_info['date_type'];
		$date_address = $date_info['name'];
		$merchant_id = $date_info['mid'];
		$date_time = print_date_daytime($date_info['date_time']);

		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$user = $user_data['0'];
		$gender_type = ($user['gender_id'] == "1") ? "him" : "her";

		//$subject=$user['first_name'].' has hosted a date  @ '.$date_address.' for '.$date_time;
		//$subject = $user['first_name'] . ' wants to date someone  @ ' . $date_address . ' at ' . $date_time;
		//$data['email_content']=$user['first_name'].' has hosted '.$date_type.' @ '.$date_address.' at '.$date_time.'. Click on the button below to apply and meet '.$gender_type.' for the date:';
		//$data['email_content'] = $user['first_name'] . ' wants to date someone for date at  ' . $date_time . '. Click on the button below to find out where and apply to meet ' . $gender_type . ' for the date:';
		$data['email_title'] = '';
		
		
		$subject = $user['first_name'] . translate_phrase(' wants to date someone').' @ '. $date_address . ' at ' . $date_time;
		$data['email_content'] = $user['first_name'] . translate_phrase(' wants to date someone for date at ') . $date_time . '. '.translate_phrase('Click on the button below to find out where and apply to meet ') . $gender_type . translate_phrase(' for the date');
		
		//Dynamic autologin link
		$return_url = base_url() . 'dates/find_dates/' . $date_id;
		
		$data['btn_text'] = translate_phrase('View Date');
		$getAllemail = $this -> general -> sql_query(
				"SELECT um.user_id, user.password,user.first_name, um.email_address FROM user_follow_user as ufm
                 INNER JOIN user on user.user_id=ufm.user_id 
                 INNER JOIN user_email as um on um.user_id = user.user_id 
                 WHERE 
                 user.user_id !='" . $this -> user_id. "' 
                 AND ufm.follow_user_id ='" . $this -> user_id . "'
                 AND ufm.unfollow_time ='0000-00-00 00:00:00'
                 GROUP BY user.user_id");
		//echo "<pre>";print_r($getAllemail);exit;
		foreach ($getAllemail as $key => $val) {
			
			$user_link = $this -> utility -> encode($val['user_id']);
			if ($val['password']) {
				$user_link .= '/' . $val['password'];
			}
			$data['btn_link'] = base_url() . 'home/mail_action/' . $user_link . '?return_to='.$return_url;			
			$email_template = $this -> load -> view('email/common', $data, true);
			$this -> datetix -> mail_to_user($val['email_address'], $subject, $email_template);
		}
	}

}
?>
