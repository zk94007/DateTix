<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Dates extends MY_Controller {

	var $language_id = '1';

	public function __construct() {

		parent::__construct();
		$this -> load -> model('model_user');
		$this -> load -> model('model_date');
		$this -> load -> model('general_model');
		if ($user_id = $this -> session -> userdata('user_id')) {
			$this -> language_id = $this -> session -> userdata('sess_language_id');
			$this -> user_id = $this -> session -> userdata('user_id');
			if (!$this -> session -> userdata('sess_city_id')) {
				$this -> session -> set_userdata('sess_city_id', $this -> config -> item('default_city'));
			}
			$this -> city_id = $this -> session -> userdata('sess_city_id');
		}
		if (!$this -> session -> userdata('user_id')) {
			redirect();
		}
	}

	public function new_date_step1() {
		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));

		$user = $user_data['0'];
		// get 7 days after day after tomorrorw list
		$dateList = array();
		for ($i = 0; $i < 14; $i++) {
			$dateKey = date("Y-m-d", strtotime('today + ' . $i . ' day'));
			$dateList[$dateKey] = date("Y-m-d", strtotime('today + ' . $i . ' day'));
		}
		$post = $this -> input -> post();
		if (!empty($post)) {
			$insertArray['date_time'] = $post['date_free_time'] . " " . $post['date_time'];
			$insertArray['requested_user_id'] = $this -> user_id;
			$insertArray['completed_step'] = '1';
			$saveDate = $this -> model_date -> save_date_step1($insertArray);
			$this -> session -> set_userdata('save_date_id', $saveDate);
			redirect(base_url() . "dates/new_date_step2");
		}
		if ($data['date_preference'] = $this -> model_date -> last_date_preference($this -> user_id)) {
			$data['prefer_date_time'] = date('H:i', strtotime($data['date_preference']['date_time']));
		} else {
			$data['prefer_date_time'] = '19:00';
		}
		sscanf($data['prefer_date_time'], "%d:%d", $hours, $minutes);
		$data['prefer_date_time_seconds'] = $hours * 60 + $minutes;

		$data['date_list'] = $dateList;
		$data['user_data'] = $user;
		$data['page_title'] = translate_phrase('New Date');
		$data['page_name'] = 'user/dates_app/new_date_step1';
		$this -> load -> view('template/editProfileTemplate', $data);
	}

	public function get_more_dates($last_date) {

		// End date
		$date = strtotime($last_date);
		$date = strtotime("+14 day", $date);
		$end_date = date('Y-m-d', $date);
		$dates = array();
		$start = strtotime("+1 day", strtotime($last_date));
		$start_date = date('Y-m-d', $start);

		$current = strtotime($start_date);
		$last = strtotime($end_date);
		$i = 0;

		while ($current <= $last) {

			$dates[$i]['key'] = date('Y-m-d', $current);
			$dates[$i]['value'] = date('l, F jS ', strtotime(date('Y-m-d', $current)));
			$current = strtotime('+1 day', $current);
			$i++;
		}
		echo json_encode($dates);
	}

	public function new_date_step2() {
		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$user = $user_data['0'];
		// get 7 days after day after tomorrorw list

		$post = $this -> input -> post();
		if (!empty($post)) {
			$date_id = $this -> session -> userdata('save_date_id');
			$updateArray['date_type_id'] = $post['date_type'];
			$updateArray['date_intention_id'] = $post['looking_for'];
			$updateArray['date_payer_id'] = $post['date_payer'];
			$updateArray['completed_step'] = '2';
			$this -> model_date -> update_date_step($date_id, $updateArray);
			redirect(base_url() . "dates/new_date_step3");
		}

		// date type
		$dateType = $this -> model_date -> get_date_type($this -> language_id);
		//relation Type
		$relationshipType = $this -> model_date -> get_relationship_type($this -> language_id);
		//date payer list
		$datePayer = $this -> model_date -> get_date_payer($this -> language_id);

		// check users hosted any date or not
		$data['last_host_date'] = $this -> model_date -> last_date_preference($this -> user_id);

		$data['date_type'] = $dateType;
		$data['relationship_type'] = $relationshipType;
		$data['date_payer'] = $datePayer;
		$data['user_data'] = $user;
		$data['page_title'] = translate_phrase('New Date');
		$data['page_name'] = 'user/dates_app/new_date_step2';

		$this -> load -> view('template/editProfileTemplate', $data);
	}
	
	public function new_date_step3() {
		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));

		$user = $user_data['0'];
		
		$post = $this -> input -> post();
		if (!empty($post)) {
			$date_id = $this -> session -> userdata('save_date_id');
			$updateArray['merchant_id'] = $post['merchant_id'];
			$updateArray['completed_step'] = '3';
			$this -> model_date -> update_date_step($date_id, $updateArray);
			//redirect( base_url() ."dates/new_date_step5");
			redirect(base_url() . "dates/new_date_step4");
		}
		$date_id = $this -> session -> userdata('save_date_id');
		$data['date_info'] = $this -> model_date -> get_date_detail_by_id($date_id);
		//$data['last_host_date']= $this -> model_date -> last_date_preference($this -> user_id);                
		//echo "<pre>";print_r($data);exit;
		
		/* Fetch citydata */
		$data['country'] = $this -> model_user -> get_country($this -> language_id);
		$neighbourhoodData = array();
		// neighbourhood list
		if ($neighbourhoodList = $this -> model_date -> get_neighbourhood($this -> language_id, $this -> city_id)) {
			foreach ($neighbourhoodList as $value) {
				$neighbourhoodData[$value['neighborhood_id']] = $value['description'];
			}
		}
		$data['neighbourhood_list'] = $neighbourhoodData;

		$this -> city_id = $user['current_city_id'];
		$current_country = $this -> model_user -> getCountryByCity($this -> city_id);
		$country_id = $current_country ? $current_country -> country_id : '0';
		$data['user_country_id'] = $country_id;
		$data['user_city_id'] = $this -> city_id;
		$data['cities'] = $this -> model_user -> get_city($this -> language_id, $country_id);

		// get cuisine
		$cuisineList = $this -> model_date -> get_cuisine_with_category($this -> language_id);
		// get merchange budget
		$budgetList = $this -> model_date -> get_budget($this -> language_id);

		$sortbyList = array('Popularity', 'Name', 'Distance');
		$data['cuisine_list'] = $cuisineList;
		$data['budget_list'] = $budgetList;
		$data['sortby_list'] = $sortbyList;

		$data['user_data'] = $user;

		$data['page_title'] = translate_phrase('New Date');
		$data['page_name'] = 'user/dates_app/new_date_step3';
		$this -> load -> view('template/editProfileTemplate', $data);
	}
	
	public function new_date_step4() {
		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$user = $user_data['0'];
		$post = $this -> input -> post();
		if (!empty($post)) {
			$date_id = $this -> session -> userdata('save_date_id');
			$updateArray['date_gender_ids'] = $post['gender'];
			$updateArray['date_age_range'] = $post['start_age'] . "-" . $post['end_age'];
			$updateArray['date_ethnicities'] = $post['ethnicity'];
			$updateArray['completed_step'] = '4';
			$this -> model_date -> update_date_step($date_id, $updateArray);
			redirect(base_url() . "dates/new_date_step6");
		}

		// date type
		$genderList = $this -> model_date -> get_gender($this -> language_id);
		// get ethnicity
		$ethnicityList = $this -> model_date -> get_ethnicity($this -> language_id);

		// user want gender
		$data['gender_want'] = $this -> db -> query("select group_concat(gender_id) as gender_id from user_want_gender where user_id='" . $this -> user_id . "'") -> row_array();
		$data['ethnicity_want'] = $this -> db -> query("select group_concat(ethnicity_id) as ethnicity_id from user_want_ethnicity where user_id='" . $this -> user_id . "'") -> row_array();
                
		$data['last_host_date'] = array();
		$checUserDate = $this -> model_date -> last_date_preference($this -> user_id);
                
		$lastDateDetail = $checUserDate;
		if (!empty($checUserDate['date_gender_ids'])) {
			$data['gender_want']['gender_id'] = $lastDateDetail['date_gender_ids'];
		}else{
        	$data['gender_want']['gender_id'] =$data['gender_want']['gender_id'];
        }
		if (!empty($checUserDate['date_ethnicities'])) {
			$data['ethnicity_want']['ethnicity_id'] = $lastDateDetail['date_ethnicities'];
		}else{
        	$data['ethnicity_want']['ethnicity_id'] =$data['ethnicity_want']['ethnicity_id'];
        }
		
        $data['gender_want']['age_range'] = $lastDateDetail['date_age_range'];
		$data['gender_list'] = $genderList;
		$data['ethnicity_list'] = $ethnicityList;
		$data['user_data'] = $user;
		$data['page_title'] = translate_phrase('New Date');
		$data['page_name'] = 'user/dates_app/new_date_step4';

		$this -> load -> view('template/editProfileTemplate', $data);
	}

	public function get_merchant_list() {
		$post = $this -> input -> post();
		$neighbourhood = ($post['neighbourhood']) ? $post['neighbourhood'] : '';
		$cuisine = (isset($post['cuisine'])) ? $post['cuisine'] : '';
		$budget_id = (isset($post['budget_id'])) ? $post['budget_id'] : '';
		$sortby = (isset($post['sort_by'])) ? $post['sort_by'] : '';
		$merchantList = $this -> model_date -> get_merchnat_list($neighbourhood, $cuisine, $budget_id, $sortby);
		$data['merchant_list'] = $merchantList;

		echo $this -> load -> view('user/dates_app/merchant_list', $data);
	}

	public function new_date_step5() {
		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$user = $user_data['0'];
		// get 7 days after day after tomorrorw list

		$post = $this -> input -> post();
		if (!empty($post)) {
			$date_id = $this -> session -> userdata('save_date_id');
			$updateArray['date_package_id'] = $post['date_package_id'];
			$updateArray['completed_step'] = '5';
			$this -> model_date -> update_date_step($date_id, $updateArray);
			redirect(base_url() . "dates/new_date_step6");
		}

		// package list
		$packageList = $this -> model_date -> get_date_package($this -> language_id);

		$data['package_list'] = $packageList;

		$data['user_data'] = $user;
		$data['page_title'] = translate_phrase('New Date');
		$data['page_name'] = 'user/dates_app/new_date_step5';
		$this -> load -> view('template/editProfileTemplate', $data);
	}

	public function new_date_step6() {
		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$user = $user_data['0'];
		// get 7 days after day after tomorrorw list
		$date_id = $this -> session -> userdata('save_date_id');

		$post = $this -> input -> post();
		if ($this -> input -> post()) {
			$date_id = $this -> session -> userdata('save_date_id');
			$updateArray['num_date_tickets'] = $post['num_date_tickets'];
			$updateArray['completed_step'] = '5';
			$this -> model_date -> update_date_step($date_id, $updateArray);
			redirect(base_url() . "dates/find_dates");
		}
		$dateDetail = $this -> model_date -> get_date_detail_by_id($date_id, $this -> language_id);
		$data['date_detail'] = $dateDetail;

		$data['user_data'] = $user;
		$data['page_title'] = translate_phrase('New Date');
		$data['page_name'] = 'user/dates_app/new_date_step6';
		$this -> load -> view('template/editProfileTemplate', $data);
	}

	public function find_dates() {

		$this -> session -> unset_userdata('save_date_id');
		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$user = $user_data['0'];

		// current user dates
		$userDates = $this -> model_date -> get_user_date_detail($this -> user_id);
		$data['user_date'] = $userDates;

		// find others dae which are not current users
		$otherDates = $this -> model_date -> get_other_date_detail($this -> user_id);
		$data['other_dates'] = $otherDates;

		$data['user_data'] = $user;
		$data['page_title'] = translate_phrase('Find Date');
		$data['page_name'] = 'user/dates_app/find_dates';
		$this -> load -> view('template/editProfileTemplate', $data);
	}

	public function get_next_date_list() {

		$post = $this -> input -> post();

		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$user = $user_data['0'];

		$last_date_id = $post['date_id'];
		$getNextDateList = $this -> model_date -> get_next_date_detail($this -> user_id, $last_date_id);

		$data['other_dates'] = $getNextDateList;
		$data['user_data'] = $user;
		echo $this -> load -> view('user/dates_app/next_date_detail_ajax', $data);
	}

	public function date_decision() {

		$post = $this -> input -> post();
		$dateid = $post['date_id'];
		$decision = $post['decision'];
		if ($decision == '1') {
			$insertArray['date_id'] = $dateid;
			$insertArray['applicant_user_id'] = $this -> user_id;
			$dateDetail = $this -> model_date -> get_date_detail_by_id($dateid, $this -> language_id);
			$insertArray['num_date_tickets'] = $dateDetail['num_date_tickets'];
			$saveDateDecision = $this -> model_date -> save_date_applicant($insertArray);
		}

		// insert into decision_viewd table
		$insertArrayDecision['date_id'] = $dateid;
		$insertArrayDecision['user_id'] = $this -> user_id;
		$insertArrayDecision['decision'] = $decision;
		$saveDateDecision = $this -> model_date -> save_decision_viewed($insertArrayDecision);
	}

	public function user_chats() {

		$user_id = $this -> user_id;
		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$user = $user_data['0'];

		// get user chat histroy
		$getChatDetail = $this -> model_date -> get_chat_history($user_id);

		$data['chat_history'] = $getChatDetail;
		$data['user_data'] = $user;
		$data['page_title'] = translate_phrase('User Chat History');
		$data['page_name'] = 'user/dates_app/user_chats';
		$this -> load -> view('template/editProfileTemplate', $data);
	}

	public function chat_history($other_user = '', $current_user = '') {
		if (!empty($other_user) && !empty($current_user)) {

			if ($this -> session -> userdata('return_url'))
				$this -> session -> unset_userdata('return_url');
			$this -> general -> set_table('user');
			$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
			$user = $user_data['0'];

			$getChatHistory = $this -> model_date -> get_chat_detail($other_user, $current_user);
			$data['chat_history'] = $getChatHistory;

			$data['user_data'] = $user;
			$data['page_title'] = translate_phrase('User Chat History');
			$data['page_name'] = 'user/dates_app/chat_history';
			$this -> load -> view('template/editProfileTemplate', $data);
		} else {
			redirect('dates/user_chats');
		}
	}

	public function get_neighborhood_by_city() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$neighbourhood_list = $this -> model_user -> get_district($language_id, $this -> input -> post('id'));
		echo form_dt_dropdown('neighbourhood', $neighbourhood_list, key($neighbourhood_list), 'class="dropdown-dt scemdowndomain" ', translate_phrase('Select  Neighbourhood'), "hiddenfield");
	}

	public function get_message() {
		$post = $this -> input -> post();
		$id = $post['id'];
		$getPoints = $this -> db -> query("select * from budget where budget_id='" . $id . "'") -> row_array();
		$message = $getPoints['description'] . " Venues will cost you " . $getPoints['num_date_tix'] . " date tickets";
		echo $message;
	}

	public function get_looking_message() {
		$post = $this -> input -> post();
		$id = $post['id'];
		$getPoints = $this -> db -> query("select * from relationship_type where relationship_type_id='" . $id . "'") -> row_array();
		$message = $getPoints['description'] . " relationship will cost you " . $getPoints['num_date_tix'] . " date tickets";
		echo $message;
	}

	public function get_selected_cuisine_list() {
		$post = $this -> input -> post();
		$cuisine_id = $post['cuisine'];

		$cuisineArray = explode(',', $cuisine_id);

		if (!empty($cuisineArray)) {
			foreach ($cuisineArray as $key => $val) {
				// get cuisinename from id
				$cuisineName = $this -> db -> query("select * from cuisine where cuisine_id='" . $val . "'") -> row_array();
				if (!empty($cuisineName)) {
					$name = (!empty($cuisineName)) ? $cuisineName['description'] : '';
					echo '<li class="Fince-But"> <a class="cuisineCkb" key="'. $val . '" href="javascript:;">'.$name.'<img src="'.base_url('assets/images/cross.png').'"></a></li>';
				}
			}
		}
	}

	/**
	 * get user dates detail
	 * @access public
	 * @author jigar oza
	 */
	public function my_dates() {
		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$user = $user_data['0'];

		//$getChatHistory = $this->model_date->get_user_date($other_user, $current_user);
		//$data['chat_history'] = $getChatHistory;
		
		$data['upcomingDates'] = $this -> model_date -> user_date($this -> user_id, 'upcoming');
		$data['pastDates'] = $this -> model_date -> user_date($this -> user_id, 'past');
		//echo "<pre>";print_r($data);exit;
		
		$data['user_data'] = $user;
		$data['page_title'] = translate_phrase('My Dates Details');
		$data['page_name'] = 'user/dates_app/my_dates';
		$this -> load -> view('template/editProfileTemplate', $data);
	}
	
	/**
	 * Edit Date
	 * @access public
	 * @author jigar oza
	 */
	public function view_applicants($date_id) {
		$data['date_info'] = $this -> model_date -> get_date_detail_by_id($date_id);
		
		$this->general->set_table('date_applicant');
		
		$data['date_applications']= $this -> model_date -> get_applicants_by_date_id($date_id);
		//echo "<pre>";print_r($data);exit;
		
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$user = $user_data['0'];
		$data['user_data'] = $user;
		$data['page_title'] = '';
		$data['page_name'] = 'user/dates_app/view_applicants';
		$this -> load -> view('template/editProfileTemplate', $data);
	}
	/**
	 * Edit Date
	 * @access public
	 * @author jigar oza
	 */
	public function edit_date($date_id) {
		$data['date_info'] = $this -> model_date -> get_date_detail_by_id($date_id);
		
		$post = $this -> input -> post();
		if (!empty($post)) {
			$updateArray['date_gender_ids'] = $post['gender'];
			$updateArray['date_age_range'] = $post['start_age'] . "-" . $post['end_age'];
			$updateArray['date_ethnicities'] = $post['ethnicity'];
			
			$this -> model_date -> update_date_step($date_id, $updateArray);
			
			$this -> session -> set_flashdata('page_msg_success', translate_phrase('Date has been udpated.'));
			redirect('dates/my_dates');
		}
		// date type
		$genderList = $this -> model_date -> get_gender($this -> language_id);
		// get ethnicity
		$ethnicityList = $this -> model_date -> get_ethnicity($this -> language_id);

		// user want gender
		$data['gender_want'] = $this -> db -> query("select group_concat(gender_id) as gender_id from user_want_gender where user_id='" . $this -> user_id . "'") -> row_array();
		$data['ethnicity_want'] = $this -> db -> query("select group_concat(ethnicity_id) as ethnicity_id from user_want_ethnicity where user_id='" . $this -> user_id . "'") -> row_array();
                
		$data['last_host_date'] = array();
		$lastDateDetail = $data['date_info'];
		if (!empty($lastDateDetail['date_gender_ids'])) {
			$data['gender_want']['gender_id'] = $lastDateDetail['date_gender_ids'];
		}else{
        	$data['gender_want']['gender_id'] =$data['gender_want']['gender_id'];
        }
		if (!empty($lastDateDetail['date_ethnicities'])) {
			$data['ethnicity_want']['ethnicity_id'] = $lastDateDetail['date_ethnicities'];
		}else{
        	$data['ethnicity_want']['ethnicity_id'] =$data['ethnicity_want']['ethnicity_id'];
        }
		
        $data['gender_want']['age_range'] = $lastDateDetail['date_age_range'];
		$data['gender_list'] = $genderList;
		$data['ethnicity_list'] = $ethnicityList;
		
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$user = $user_data['0'];
		$data['user_data'] = $user;
		$data['page_title'] = translate_phrase('Edit Date Details');
		$data['page_name'] = 'user/dates_app/edit_date';
		$this -> load -> view('template/editProfileTemplate', $data);
	}
	function view_merchant($merchant_id)
	{
		//echo $merchant_id;
		$this -> general -> set_table('merchant');
		if($merchant_info = $this -> general -> get("", array('merchant_id' => $merchant_id)))
		{
			$merchant_info = $merchant_info['0'];
			
			$this -> general -> set_table('merchant_photo');
			$merchant_info['merchant_photos'] = $this -> general -> get("", array('merchant_id' => $merchant_id));
			
			
			$this -> general -> set_table('merchant_date_type');
			$merchant_info['merchant_date_types'] = $this -> general -> get("", array('merchant_id' => $merchant_id));
			
			$fields = array('dtType.date_type_id', 'dtType.description as description');
			$from = 'merchant_date_type as mdtType';
			$joins = array('date_type as dtType' => array('mdtType.date_type_id = dtType.date_type_id', 'Inner'));
			$where['dtType.display_language_id'] = $this -> language_id;
			
			$merchant_info['merchant_date_types']  = $this -> general -> multijoins_arr($fields, $from, $joins, $where, '', 'view_order asc');
			
		}
		$data['merchant_info'] = $merchant_info;
		//echo "<pre>";print_r($data['merchant_info'] );exit;
		
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$user = $user_data['0'];
		$data['user_data'] = $user;
		$data['page_title'] = '';
		$data['page_name'] = 'user/dates_app/view_merchant';
		$this -> load -> view('template/editProfileTemplate', $data);
	}
	
	/**
	 * Cancel Date
	 * @access public
	 * @author jigar oza
	 */
	public function cancel_date() {
		$date_id=$this->input->post('date_id');
		
		$this->general->set_table('date');
		$updateData['status'] = 2;
		$updateData['cancel_date_time'] = SQL_DATETIME;
		
		$isUpdated = $this->general->update($updateData,array('date_id'=>$date_id));
		if($isUpdated)
		{
			$this->general->set_table('date_applicant');
			if($dateApplicants = $this->general->get("applicant_user_id",array('date_id'=>$date_id)))
			{
				$subject = translate_phrase('Your applied date has been cancelled');
			
				foreach($dateApplicants  as $applicant)
				{
					$user_id = $applicant['applicant_user_id'];
					$user_email_data = $this -> model_user -> get_user_email($user_id);
					
					if ($user_email_data) {
						$data['email_content'] = '';
						$data['email_title'] = translate_phrase('Your applied date has been cancelled.');
						$email_template = $this -> load -> view('email/common', $data, true);
						$this -> datetix -> mail_to_user_debug($user_email_data['email_address'], $subject, $email_template);
					}
				}
			}
			$this -> session -> set_flashdata('page_msg_success', translate_phrase('Your date has been cancelled.'));
		}
		redirect('dates/my_dates');
	}
}
?>
