<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Dates extends MY_Controller {

	var $language_id = '1';
	public function __construct() {
		ini_set('memory_limit', '-1');
		parent::__construct();
		
		$this -> load -> model('model_user');
		$this -> load -> model('model_date');
		$this -> load -> model('general_model');
		if ($user_id = $this -> session -> userdata('user_id')) {
			$this -> language_id = $this -> session -> userdata('sess_language_id');
			$this -> user_id = $this -> session -> userdata('user_id');			
		}
		else {
			$this -> user_id = 0;
			$this -> session -> set_userdata('user_id',$this -> user_id);			
			$this->is_guest_user = true;
			
		}
		if (!$this -> session -> userdata('sess_city_id')) {
			$this -> session -> set_userdata('sess_city_id', $this -> config -> item('default_city'));
		}
		$this -> city_id = $this -> session -> userdata('sess_city_id');
		
		/*if (!$this -> session -> userdata('user_id')) {
			//redirect('apply');
			$requested_url = str_replace(base_url(), '', current_url());
			redirect(url_city_name() . '/signin.html?return_url=/' . $requested_url);
		}*/
	}

	public function index() {
		$this -> find_dates();
	}

	public function new_date_step1() {
		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		
		
		// get 7 days after day after tomorrorw list
		if ($this -> session -> userdata('save_date_id')) {
			$this -> session -> unset_userdata('save_date_id');
		}
	
		$dateList = array();
		for ($i = 0; $i < 6; $i++) {
			$dateKey = date("Y-m-d", strtotime('today + ' . $i . ' day'));
			$dateList[$dateKey] = date("Y-m-d", strtotime('today + ' . $i . ' day'));
		}
		
		$data['prefer_date_time'] = '19:00';

		if($this->user_id != 0)
		{
			$this -> general -> set_table('user');
			$user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $this -> user_id));
			$user = $user_data['0'];
			
			if ($data['date_preference'] = $this -> model_date -> last_date_preference($this -> user_id)) {
				$data['prefer_date_time'] = date('H:i', strtotime($data['date_preference']['date_time']));
			}		
		}
		else {
			$user = array();
		}
		
		
		$post = $this -> input -> post();
		if (!empty($post))
		{
			
			$insertArray['date_time'] = $post['date_free_time'] . " " . $post['date_time'];
			$insertArray['requested_user_id'] = $this -> user_id;
			$insertArray['completed_step'] = '1';
			$insertArray['post_time'] = SQL_DATETIME;
			$insertArray['session_id'] = $this -> session -> userdata('session_id');
			
			$is_validate = true;
			$sql = "SELECT * FROM date WHERE requested_user_id = '".$this -> user_id."' AND `date_time` between '".date('Y-m-d H:i:s',strtotime($insertArray['date_time']))."'-INTERVAL 2 HOUR AND '".date('Y-m-d H:i:s',strtotime($insertArray['date_time']))."'+INTERVAL 2 HOUR ";
			$checkDate = $this->general->sql_query($sql );
			
			if($checkDate)
			{
				$this -> session -> set_flashdata('postData', $saveDate);
				$msg = translate_phrase("You can't host more than one date within two hours of each other!");
				$this -> session -> set_flashdata('page_msg_error',$msg);
				redirect(base_url() . "dates/new_date_step1");	
			}
			else {
				$saveDate = $this -> model_date -> save_date_step1($insertArray);	
				$this -> session -> set_userdata('save_date_id', $saveDate);
				redirect(base_url() . "dates/new_date_step2");			
				
			}			
		}
		
		sscanf($data['prefer_date_time'], "%d:%d", $hours, $minutes);
		$data['prefer_date_time_seconds'] = $hours * 60 + $minutes;
		
		//echo "<pre>";print_r($data['date_preference']['']);exit;
		
		$data['date_list'] = $dateList;
		$data['user_data'] = $user;
		$data['page_title'] = translate_phrase('New Date');
		$data['page_name'] = 'user/dates_app/new_date_step1';
		if($this->user_id != 0)
		{
			$this -> load -> view('template/editProfileTemplate', $data);
		}
		else {
			$this -> load -> view('template/default', $data);
		}
	}

	public function get_more_dates($last_date) {

		// End date
		$date = strtotime($last_date);
		$date = strtotime("+6 day", $date);
		$end_date = date('Y-m-d', $date);
		$dates = array();
		$start = strtotime("+1 day", strtotime($last_date));
		$start_date = date('Y-m-d', $start);

		$current = strtotime($start_date);
		$last = strtotime($end_date);
		$i = 0;

		while ($current <= $last) {

			$dates[$i]['key'] = date('Y-m-d', $current);
			$dates[$i]['value'] = date('D, M jS ', strtotime(date('Y-m-d', $current)));
			$current = strtotime('+1 day', $current);
			$i++;
		}
		echo json_encode($dates);
	}

	public function new_date_step2() {
		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		// get 7 days after day after tomorrorw list

		$post = $this -> input -> post();
		if (!empty($post)) {
			$date_id = $this -> session -> userdata('save_date_id');
			$updateArray['date_type_id'] = $post['date_type'];
			$updateArray['date_intention_id'] = $post['looking_for'];
			$updateArray['date_payer_id'] = $post['date_payer'];
			$updateArray['completed_step'] = '2';
			$updateArray['session_id'] = $this -> session -> userdata('session_id');
			
			$this -> model_date -> update_date_step($date_id, $updateArray);
			redirect(base_url() . "dates/new_date_step3");
		}

		// date type
		$dateType = $this -> model_date -> get_date_type($this -> language_id);
		//relation Type
		$relationshipType = $this -> model_date -> get_relationship_type($this -> language_id);
		//date payer list
		$datePayer = $this -> model_date -> get_date_payer($this -> language_id);
		
		
		$data['date_type'] = $dateType;
		$data['relationship_type'] = $relationshipType;
		$data['date_payer'] = $datePayer;
		
		$data['page_title'] = translate_phrase('New Date');
		$data['page_name'] = 'user/dates_app/new_date_step2';

		if($this->user_id != 0)
		{
			$this -> general -> set_table('user');
			$user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $this -> user_id));
			$user = $user_data['0'];
			$data['user_data'] = $user;
			
			// check users hosted any date or not
			$data['last_host_date'] = $this -> model_date -> last_date_preference($this -> user_id);
				
			
			$this -> load -> view('template/editProfileTemplate', $data);
		}
		else {
			$this -> load -> view('template/default', $data);
		}
	}

	public function new_date_step3() {
		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		
		$post = $this -> input -> post();
		if (!empty($post)) {
			$date_id = $this -> session -> userdata('save_date_id');
			$updateArray['merchant_id'] = $post['merchant_id'];
			$updateArray['completed_step'] = '3';
			$updateArray['session_id'] = $this -> session -> userdata('session_id');
			
			$this -> model_date -> update_date_step($date_id, $updateArray);
			//redirect( base_url() ."dates/new_date_step5");
			redirect(base_url() . "dates/new_date_step4");
		}
		$date_id = $this -> session -> userdata('save_date_id');
		$data['date_info'] = $this -> model_date -> get_date_detail_by_id($date_id);

		//$data['last_host_date']= $this -> model_date -> last_date_preference($this -> user_id);
		//echo "<pre>";print_r($data);exit;

		/* Fetch citydata */
		//$data['country'] = $this -> model_user -> get_country($this -> language_id);
		$neighbourhoodData = array();
		// neighbourhood list
		$neighbourhoodData = array('' => 'Any Neighborhood');
		if ($neighbourhoodList = $this -> model_date -> get_neighbourhood($this -> language_id, $this -> city_id)) {
			foreach ($neighbourhoodList as $value) {
				$neighbourhoodData[$value['neighborhood_id']] = $value['description'];
			}
		}
		$data['neighbourhood_list'] = $neighbourhoodData;

		/*		$this -> city_id = $user['current_city_id'];
		$current_country = $this -> model_user -> getCountryByCity($this -> city_id);
		$country_id = $current_country ? $current_country -> country_id : '0';
		$data['user_country_id'] = $country_id;
		$data['user_city_id'] = $this -> city_id;
		$data['cities'] = $this -> model_user -> get_city($this -> language_id, $country_id);
		 * */
		// get cuisine
		$cuisineList = $this -> model_date -> get_cuisine_with_category($this -> language_id);
		// get merchange budget
		$budgetList = $this -> model_date -> get_budget($this -> language_id);

		if ($this -> input -> post('page_no')) {
			$page_no = $this -> input -> post('page_no');
		} else {
			$page_no = 1;
		}

		$data['page_no'] = $page_no;
		$sortbyList = array('Featured', 'Name', 'Price');
		$data['cuisine_list'] = $cuisineList;
		$data['budget_list'] = $budgetList;
		$data['sortby_list'] = $sortbyList;

		
		$data['page_title'] = translate_phrase('New Date');
		$data['page_name'] = 'user/dates_app/new_date_step3';
		if($this->user_id != 0)
		{
			$this -> general -> set_table('user');
			$user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $this -> user_id));
	
			$user = $user_data['0'];
			$data['user_data'] = $user;
				
			$this -> load -> view('template/editProfileTemplate', $data);
		}
		else {
			$this -> load -> view('template/default', $data);
		}
	}

	public function new_date_step4() {
		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		
		$post = $this -> input -> post();
		if (!empty($post)) {
			$date_id = $this -> session -> userdata('save_date_id');
			$updateArray['num_date_tickets'] = $post['date_tickets'];
			$updateArray['date_gender_ids'] = $post['gender'];
			$updateArray['date_age_range'] = $post['start_age'] . "-" . $post['end_age'];
			$updateArray['date_ethnicity_ids'] = $post['ethnicity'];
			$updateArray['completed_step'] = '4';
			$updateArray['session_id'] = $this -> session -> userdata('session_id');
			
			$this -> model_date -> update_date_step($date_id, $updateArray);

			//substract from  user
			$tic = $post['date_tickets'];
			$this -> db -> query("update user set num_date_tix=num_date_tix-$tic where user_id='" . $this -> user_id . "'");

			// save date ticket log
			$insertLogArray['transaction_time'] = SQL_DATETIME;
			$insertLogArray['description'] = 'Hosted Date '.$date_id;
			$insertLogArray['user_id'] = $this -> user_id;
			$insertLogArray['num_date_tickets'] = $post['date_tickets'];
			$saveDateLog = $this -> model_date -> save_date_ticket_log($insertLogArray);
			
			// send mail to user who follow same merchant id
			$sendMailMerchantFollower = $this -> model_date -> send_mail_to_merchant_followers($date_id);
			$sendMailUserFollower = $this -> model_date -> send_mail_to_user_followers($date_id);
			if($this->user_id != 0)
			{
				redirect(base_url() . "dates/new_date_step6");
			}
			else {
				redirect(base_url() . "apply");
			}
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
		} else {
			$data['gender_want']['gender_id'] = $data['gender_want']['gender_id'];
		}
		if (!empty($checUserDate['date_ethnicity_ids'])) {
			$data['ethnicity_want']['ethnicity_id'] = $lastDateDetail['date_ethnicity_ids'];
		} else {
			$data['ethnicity_want']['ethnicity_id'] = $data['ethnicity_want']['ethnicity_id'];
		}

		$date_id = $this -> session -> userdata('save_date_id');
		$dateDetail = $this -> model_date -> get_date_detail_by_id($date_id, $this -> language_id);
		$data['date_detail'] = $dateDetail;

		$data['gender_want']['age_range'] = @$lastDateDetail['date_age_range'];
		$data['gender_list'] = $genderList;
		$data['ethnicity_list'] = $ethnicityList;
		
		$data['page_title'] = translate_phrase('New Date');
		$data['page_name'] = 'user/dates_app/new_date_step4';

		if($this->user_id != 0)
		{
			$this -> general -> set_table('user');
			$user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $this -> user_id));
			$user = $user_data['0'];
			$data['user_data'] = $user;
			$this -> load -> view('template/editProfileTemplate', $data);
		}
		else {
			
			//add tickets
			$data['user_data'] = array('num_date_tix'=>10);			
			$this -> load -> view('template/default', $data);
		}
	}

	public function get_merchant_list() {
		$post = $this -> input -> post();
		$neighbourhood = ($post['neighbourhood']) ? $post['neighbourhood'] : '';
		$cuisine = (isset($post['cuisine'])) ? $post['cuisine'] : '';
		$budget_id = (isset($post['budget_id'])) ? $post['budget_id'] : '';
		$sortby = (isset($post['sortby'])) ? $post['sortby'] : '';
		$city = (isset($post['city'])) ? $post['city'] : '';
		$date_type_id = (isset($post['date_type_id'])) ? $post['date_type_id'] : '';

		if (!empty($post['page_no'])) {
			$page_no = $post['page_no'];
		} else {
			$page_no = 1;
		}
		
		$per_page = 9;
		
		$start = ($page_no - 1) * $per_page;
		$end = $per_page;
		$merchantList = $this -> model_date -> get_merchnat_list($neighbourhood, $cuisine, $budget_id, $sortby, $start, $end, $city, $date_type_id);
		//echo $this->db->last_query();
		$data['merchant_list'] = $merchantList;

		$this -> load -> view('user/dates_app/merchant_list', $data);
	}

	public function new_date_step5() {
		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $this -> user_id));
		$user = $user_data['0'];
		// get 7 days after day after tomorrorw list

		$post = $this -> input -> post();
		if (!empty($post)) {
			$date_id = $this -> session -> userdata('save_date_id');
			$updateArray['date_package_id'] = $post['date_package_id'];
			$updateArray['completed_step'] = '5';
			$updateArray['requested_user_id'] = $this->user_id;			
			$updateArray['session_id'] = $this -> session -> userdata('session_id');
			
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
		$user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $this -> user_id));
		$user = $user_data['0'];
		// get 7 days after day after tomorrorw list
		$date_id = $this -> session -> userdata('save_date_id');

		$post = $this -> input -> post();
		if ($this -> input -> post()) {
			$date_id = $this -> session -> userdata('save_date_id');
			//$updateArray['num_date_tickets'] = $post['num_date_tickets'];
			$updateArray['completed_step'] = '5';
			$this -> model_date -> update_date_step($date_id, $updateArray);
			redirect(base_url() . "dates/my_dates");
		}
		$dateDetail = $this -> model_date -> get_date_detail_by_id($date_id, $this -> language_id);
		//echo "<pre>";print_r($dateDetail );exit;
		
		$data['date_detail'] = $dateDetail;

		$data['user_data'] = $user;
		$data['page_title'] = translate_phrase('New Date');
		$data['page_name'] = 'user/dates_app/new_date_step6';
		$this -> load -> view('template/editProfileTemplate', $data);
	}
	
	/*
	 *  Function used to find other dates
	 *  @author : Rajnish Savaliya
	 */
	public function find_dates($dateid = '') {

		$this -> session -> unset_userdata('save_date_id');
		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $this -> user_id));
		$user = $user_data['0'];
		$data['user_data'] = $user;
		$data['page_title'] = translate_phrase('Find Date');

		//$data['date_setting']=$this->model_date->get_user_date_setting($this -> user_id);
		if (!empty($dateid)) {
			// find others date which are not current users
			$data['other_dates'] = $this -> model_date -> get_invite_date($dateid);
			$inviteUserAppliedDates = $this -> db -> query("select *  from date_applicant where applicant_user_id='" . $this -> user_id . "' and date_id='" . $dateid . "'") -> row_array();
			$checkUserInvited = $this -> model_date -> checkInviteDate($this -> user_id, $dateid);
			if (!empty($checkUserInvited)) {
				redirect('dates/find_dates');
			}
			$data['applied_date'] = $inviteUserAppliedDates;
			$data['page_name'] = 'user/dates_app/invited_date';
		} else {
			$data['user_date_cnt'] = $this -> model_date -> get_user_date_detail($this -> user_id, 'count');
			// find others dae which are not current users
			$data['other_dates'] = $this -> model_date -> get_other_date_detail($this -> user_id, 1);
			$data['page_name'] = 'user/dates_app/find_dates';
		}

		$this -> load -> view('template/editProfileTemplate', $data);
	}

	/*
	 *  Function used to find other dates
	 *  @ POST params - Offset
	 *  @author : Rajnish Savaliya
	 */
	public function get_next_date_list() {

		$post = $this -> input -> post();

		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $this -> user_id));
		$user = $user_data['0'];

		$offset = $post['offset'];
		$data['other_dates'] = $this -> model_date -> get_other_date_detail($this -> user_id, 1, $offset);
		//echo "<pre>";print_r($getNextDateList);exit;
		//$data['other_dates'] = $getNextDateList;
		$data['user_data'] = $user;
		echo $this -> load -> view('user/dates_app/next_date_detail_ajax', $data);
	}

	/*
	 *  AJAX: Function used to save date decision..
	 *  @ POST params - date_id,num tickets
	 *  @author : Rajnish Savaliya
	 */
	public function date_decision() {
		$response['type'] = 'error';
		$response['msg'] = 'Error occured. please try again';

		if ($post = $this -> input -> post()) {
			$dateid = $post['date_id'];
			$decision = $post['decision'];

			$insertArrayDecision['date_id'] = $dateid;
			$insertArrayDecision['user_id'] = $this -> user_id;
			$insertArrayDecision['decision'] = $decision;
			
			if ($decision == '1') {

				$this -> general -> set_table('user');
				$user_data = $this -> general -> get("first_name,num_date_tix", array('user_id' => $this -> user_id));
				$user = $user_data['0'];
				//Update User Tickets
				$update_data['num_date_tix'] = $user['num_date_tix'] - $post['num_date_tickets'];

				if ($update_data['num_date_tix'] >= 0) {
					if ($this -> general -> update($update_data, array('user_id' => $this -> user_id))) {
						
						$insertArray['date_id'] = $dateid;
						$insertArray['applicant_user_id'] = $this -> user_id;
						$insertArray['num_date_tickets'] = $post['num_date_tickets'];						
						$saveDateDecision = $this -> model_date -> save_date_applicant($insertArray);
						
						// save date ticket log
						$insertLogArray['transaction_time'] = SQL_DATETIME;
						$insertLogArray['description'] = "Applied to Date {$dateid}";
						$insertLogArray['user_id'] = $this -> user_id;
						$insertLogArray['num_date_tickets'] = $post['num_date_tickets'];
						$this -> model_date -> save_date_ticket_log($insertLogArray);
						
						
						$this -> model_date -> save_decision_viewed($insertArrayDecision);

						$getNumTickets = $this -> db -> query("select requested_user_id,num_date_tickets from date as d where date_id='" . $dateid . "'") -> row_array();
						//send Email to user
						$this -> sendEmailTousers($getNumTickets['requested_user_id'], $dateid);

					}
					
												
					$response['type'] = 'success';
					
					$dateDetail = $this -> model_date -> get_date_detail_by_id($dateid);
					
					$msg = translate_phrase('You have successfully applied to ' . $dateDetail['date_type'] . ' @ ' . trim($dateDetail['name']) . ' on ' . print_date_daytime($dateDetail['date_time']) . ' with ') . $dateDetail['first_name'];
					
					$date_user_id = $this -> utility -> encode($getNumTickets['requested_user_id']);
					$current_user = $this -> utility -> encode($this -> user_id);					
					if(isset($dateDetail['gender_id']) && $dateDetail['gender_id'] == '1')
					{
						$pro_noun = 'him';
					}
					else {
						$pro_noun = 'her';
					}
					$chat_link = base_url() . "dates/chat_history/" . $date_user_id . "/" . $current_user;
					
					$msg.= ". <a href='".$chat_link."'>".translate_phrase("Chat with ").$dateDetail['first_name']."</a> ".translate_phrase("now to get to know ".$pro_noun." better.");					
					$this -> session -> set_flashdata('pageMsg', $msg);

					$response['msg'] = $msg;
					//translate_phrase('You have successfully applied for this date.');
				} else {

					$msg = translate_phrase("Applying to this date will cost you  ") . $post['num_date_tickets'] . translate_phrase(" date tickets, but you only have ") . $user['num_date_tix'] . translate_phrase(" date tickets left. Please get more");
					$response['msg'] = $msg;
					//translate_phrase('Please buy more tickets to apply for this date.');
					$response['redirectUrl'] = base_url() . url_city_name() . "/get-more-tickets.html";

				}
				$response['num_date_tix'] = number_format($update_data['num_date_tix']);
			} else {
				$response['type'] = 'success';
				$response['msg'] = '';
				//translate_phrase('Date has been successfully cancelled.');
				$this -> model_date -> save_decision_viewed($insertArrayDecision);
			}
		}
		echo json_encode($response);
	}

	public function user_chats() {

		$user_id = $this -> user_id;
		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $this -> user_id));
		$user = $user_data['0'];

		$getChatDetail = $this -> model_date -> get_chat_history($user_id);

		$arra = array();
		foreach ($getChatDetail as $key => $row) {
			$arra[$key] = $row['last_chat_time'];
		}

		array_multisort($arra, SORT_DESC, $getChatDetail);
		$data['chat_history'] = $getChatDetail;

		$data['user_data'] = $user;
		$data['page_title'] = translate_phrase('User Chat History');
		$data['page_name'] = 'user/dates_app/user_chats';
		//echo "<pre>";print_r($data);exit;
		$this -> load -> view('template/editProfileTemplate', $data);
	}

	public function chat_history($other_user = '', $current_user = '') {
		if (!empty($other_user) && !empty($current_user)) {
			
			if ($this -> session -> userdata('return_url'))
				$this -> session -> unset_userdata('return_url');
			
			$this -> general -> set_table('user');
			$user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $this -> user_id));
			$user = $user_data['0'];

			$getChatHistory = $this -> model_date -> get_chat_detail($other_user, $current_user);
			$data['chat_history'] = $getChatHistory;

			$getChatInteraction = $this -> model_date -> get_interaction_detail($other_user, $current_user);
			
			$data['interaction_detail'] = $getChatInteraction;

			$data['current_user'] = $current_user;
			$data['other_user'] = $other_user;
			$data['user_data'] = $user;
			$data['page_title'] = translate_phrase('User Chat History');
			$data['page_name'] = 'user/dates_app/chat_history';
			//echo "<pre>";print_r($data);exit;

			$this -> load -> view('template/editProfileTemplate', $data);
		} else {
			redirect('dates/user_chats');
		}
	}

	public function get_neighborhood_by_city() {
		$language_id = $this -> session -> userdata('sess_language_id');

		$neighbourhood_list = $this -> model_date -> get_district($language_id, $this -> input -> post('id'));
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
					echo '<li class="Fince-But"> <a lang="cuisineCkbPopup" class="cuisineCkb" key="' . $val . '" href="javascript:;">' . $name . '<img src="' . base_url('assets/images/cross.png') . '"></a></li>';
				}
			}
		}
	}

	public function get_selected_budget_list() {
		$post = $this -> input -> post();
		$budget_id = $post['budget_id'];

		$budgetArray = explode(',', $budget_id);

		if (!empty($budgetArray)) {
			foreach ($budgetArray as $key => $val) {
				// get cuisinename from id
				$budgetName = $this -> db -> query("select * from budget where budget_id='" . $val . "'") -> row_array();
				if (!empty($budgetName)) {
					$name = (!empty($budgetName)) ? $budgetName['description'] : '';
					echo '<li class="Fince-But"> <a lang="budgetCkbPopup" class="budgetCkb" key="' . $val . '" href="javascript:;">' . $name . '<img src="' . base_url('assets/images/cross.png') . '"></a></li>';
				}
			}
		}
	}

	/**
	 * get user dates detail
	 * @access public
	 * @author jigar oza
	 * @Modify Rajnish Savaliya
	 */
	public function my_dates($type="upcoming") {
		
		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');
		
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $this -> user_id));
		$user = $user_data['0'];
		$data['user_data'] = $user;
		
		if ($this->input->post('page_no')) {
            $page_no = $this->input->post('page_no');
        } else {
            $page_no = 1;
        }
		$data['page_no'] = $page_no;
		
		if($this->input->is_ajax_request())
		{
			
			//$getChatHistory = $this->model_date->get_user_date($other_user, $current_user);
			//$data['chat_history'] = $getChatHistory;
			
			$fields = array('d.date_id as date_id, d.requested_user_id as requested_user_id, d.date_time as date_time, 
								da.applicant_user_id,
		                       dt.description as date_type,
		                       rt.description as intention_type,
		                       m.merchant_id as mid, m.name, m.address, m.phone_number, m.website_url,m.review_url,
		                       rt.num_date_tix as relationship_num_date_tix');					
		                       
			$from = 'date as d';
			$joins = array(
					'date_applicant as da' => array('d.date_id = da.date_id AND da.status != 3', 'left'), 
					'date_type as dt' => array('dt.date_type_id = d.date_type_id', 'left'), 
					'relationship_type as rt' => array('rt.relationship_type_id = d.date_intention_id', 'left'), 
					'merchant as m' => array('m.merchant_id = d.merchant_id', 'left')
			);
			
			$where['dt.display_language_id'] = $this -> language_id;
			$where['rt.display_language_id'] = $this -> language_id;		
			$where['d.completed_step >='] = REQUIRED_DATE_COMPLETED_STEP;		
			
			
			if($type=="upcoming") {
				$where['d.date_time >='] = SQL_DATETIME;
			}
			if($type=="past") {
				$where['d.date_time <'] = SQL_DATETIME;				
			}
			
			if ($type == 'cancel') {
				$where['d.status'] = '-1';				
			}
			else {
				$where['d.status !='] = '-1';				
			}

			$custom_where = '(da.applicant_user_id = "' . $this->user_id . '" OR d.requested_user_id = "' . $this->user_id. '")';		
			$order_by = 'd.date_time asc';
			
			$limit = 10; // PER PAGE..
			$offset = ($page_no - 1) * $limit;
			$this -> db -> group_by('d.date_id');
			$user_dates  = $this -> general -> multijoins_arr($fields, $from, $joins, $where,$custom_where,$order_by,$limit,$offset);
			
			//echo $this->db->last_query();
			//echo "<pre>";print_r($user_dates  );exit;
			
			//echo $this->db->last_query();
			$date_results = array();
			if($user_dates)
			{
				foreach ($user_dates as $key => $date_info) {
					$photo_user_id = 0;
					if ($date_info['requested_user_id'] != $this->user_id) {
						$photo_user_id = $date_info['requested_user_id'];
						$this -> general_model -> set_table('user');
						if ($user_info = $this -> general_model -> get("first_name,last_name", array('user_id' => $date_info['requested_user_id']))) {
							$date_info['hosted_by_name'] = $user_info['0']['first_name'];
						} else {
							$date_info['hosted_by_name'] = 'Unknown';
						}
					} else {
						$photo_user_id = $date_info['applicant_user_id'];
						$date_info['hosted_by_name'] = 'You';
					}
		
					$date_info['user_photos'] = $this -> model_date->get_current_primary_photo($date_info['requested_user_id']);
		
					$this -> general_model -> set_table('date_decision');
					if ($info = $this -> general_model -> get("count(date_decision_id) as total_views", array('date_id' => $date_info['date_id']))) {
						$date_info['total_views'] = $info['0']['total_views'];
					}
					$this -> general_model -> set_table('date_applicant');
					if ($info = $this -> general_model -> get("count(date_applicant_id) as total_applications", array('date_id' => $date_info['date_id']))) {
						$date_info['total_applications'] = $info['0']['total_applications'];
					}
		
					$date_results[$key] = $date_info;
				}
			}
			if($type=="cancel")
			{
				$data['cancelDates'] = $date_results; 	
			}
			else if($type=="past") {
				$data['pastDates'] = $date_results;
			}
			else {
				$data['upcomingDates'] = $date_results;
			}
			
			$this->load->view('user/dates_app/include/date_' . $type, $data);
		}
		else {
			$data['page_title'] = '';
			$data['page_name'] = 'user/dates_app/my_dates';
			$this -> load -> view('template/editProfileTemplate', $data);			
		}
		
	}

	/**
	 * Edit Date
	 * @access public
	 * @author jigar oza
	 */
	public function view_applicants($date_id) {
		$data['date_info'] = $this -> model_date -> get_date_detail_by_id($date_id);

		$this -> general -> set_table('date_applicant');

		$data['date_applications'] = $this -> model_date -> get_applicants_by_date_id($date_id);
		//echo "<pre>";print_r($data);exit;

		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $this -> user_id));
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
		$this->db->where('requested_user_id',$this->user_id);
		if($data['date_info'] = $this -> model_date -> get_date_detail_by_id($date_id))
		{
			$post = $this -> input -> post();
			if (!empty($post)) {
				$updateArray['date_gender_ids'] = $post['gender'];
				$updateArray['date_age_range'] = $post['start_age'] . "-" . $post['end_age'];
				$updateArray['date_ethnicity_ids'] = $post['ethnicity'];
	
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
			} else {
				$data['gender_want']['gender_id'] = $data['gender_want']['gender_id'];
			}
			if (!empty($lastDateDetail['date_ethnicity_ids'])) {
				$data['ethnicity_want']['ethnicity_id'] = $lastDateDetail['date_ethnicity_ids'];
			} else {
				$data['ethnicity_want']['ethnicity_id'] = $data['ethnicity_want']['ethnicity_id'];
			}
	
			$data['gender_want']['age_range'] = $lastDateDetail['date_age_range'];
			$data['gender_list'] = $genderList;
			$data['ethnicity_list'] = $ethnicityList;	
			$data['page_name'] = 'user/dates_app/edit_date';
		}
		else {
			$data['page_name'] = 'user/dates_app/message';	
			$data['msg'] = translate_phrase('Sorry! No dates found with id : ').$date_id;	
				
		}
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $this -> user_id));
		$user = $user_data['0'];
		$data['user_data'] = $user;
		$data['page_title'] = translate_phrase('Edit Date Details');
		
		$this -> load -> view('template/editProfileTemplate', $data);
	}

	function view_merchant($merchant_id) {
		//echo $merchant_id;
		$this -> general -> set_table('merchant');
		if ($merchant_info = $this -> general -> get("", array('merchant_id' => $merchant_id))) {
			$merchant_info = $merchant_info['0'];

			$this -> general -> set_table('merchant_photo');
			$merchant_info['merchant_photos'] = $this -> general -> get("", array('merchant_id' => $merchant_id));

			$this -> general -> set_table('merchant_date_type');
			$merchant_info['merchant_date_types'] = $this -> general -> get("", array('merchant_id' => $merchant_id));

			$fields = array('dtType.date_type_id', 'dtType.description as description');
			$from = 'merchant_date_type as mdtType';
			$joins = array('date_type as dtType' => array('mdtType.date_type_id = dtType.date_type_id', 'Inner'));
			$where['dtType.display_language_id'] = $this -> language_id;

			$merchant_info['merchant_date_types'] = $this -> general -> multijoins_arr($fields, $from, $joins, $where, '', 'view_order asc', null, null, '', '', 'dtType.date_type_id');

		}

		// check already following or not
		$data['checkMerchantFollow'] = $this -> db -> query("select * from user_follow_merchant where
                                                        merchant_id='" . $merchant_id . "'
                                                        and user_id='" . $this -> user_id . "' and 
                                                        follow_time > unfollow_time") -> result_array();

		$data['merchant_info'] = $merchant_info;

		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $this -> user_id));
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
		$date_id = $this -> input -> post('date_id');

		$this -> general -> set_table('date');
		$updateData['status'] = -1;
		$updateData['cancel_date_time'] = SQL_DATETIME;

		$date_info = $this -> model_date -> get_date_detail_by_id($date_id);
		$date_type = $date_info['date_type'];
		$date_address = $date_info['name'];
		$date_time = date('l, F j,  Y', strtotime($date_info['date_time']));

		$isUpdated = $this -> general -> update($updateData, array('date_id' => $date_id));
		if ($isUpdated) {
			$this -> general -> set_table('date_applicant');
			
			if ($dateApplicants = $this -> general -> get("applicant_user_id", array('date_id' => $date_id,'status'=>1))) {
				$subject = $date_type . " @ " . $date_address . "has been cancelled";
				foreach ($dateApplicants as $applicant) {
					$user_id = $applicant['applicant_user_id'];
					$user_email_data = $this -> model_user -> get_user_email($user_id);

					if ($user_email_data) {
						//$data['email_content'] = $date_type.' @ '.$date_address.' on '.$date_time.' that you applied to has been cancelled.';
						//$data['email_title'] = $subject;
						$data['email_content'] = $date_type . ' @ ' . $date_address . 'on ' . $date_time . ' that you applied to has been cancelled.';
						$data['email_title'] = '';
						$email_template = $this -> load -> view('email/common', $data, true);
						$this -> datetix -> mail_to_user($user_email_data['email_address'], $subject, $email_template);
					}
				}
			}
			$this -> session -> set_flashdata('page_msg_success', translate_phrase('Your date has been cancelled.'));
		}
		redirect('dates/my_dates');
	}

	/**
	 * Cancel Date
	 * @access public
	 * @author jigar oza
	 */
	public function withdraw_applicant() {
		$date_id = $this -> input -> post('date_id');
		$user_id = $this -> user_id;
		$this -> general -> set_table('date_applicant');
		$updateData['status'] = 3;
		//$updateData['cancel_date_time'] = SQL_DATETIME;
		$isUpdated = $this -> general -> update($updateData, array('date_id' => $date_id, 'applicant_user_id' => $user_id));
		if ($isUpdated) {
			$this -> session -> set_flashdata('page_msg_success', translate_phrase('Your application has been withdrawn.'));
		}
		redirect('dates/my_dates');
	}

	public function date_refund() {
		$post = $this -> input -> post();
		$date_id = $post['date_id'];
		$this -> general -> set_table('date');
		$updateData['status'] = 2;
		$updateData['refund_reason'] = $post['refund_reason'];
		$isUpdated = $this -> general -> update($updateData, array('date_id' => $date_id));
		if ($isUpdated) {

			$date_info = $this -> model_date -> get_date_detail_by_id($date_id);
			$this -> general -> set_table('user');
			$user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $this -> user_id));
			$user = $user_data['0'];

			$subject = $user['first_name'] . ' has requested a date refund';
			$email_content = $user['first_name'] . ' has requested a refund for ' . $date_info['date_type'] . ' @ ' . $date_info['name'] . ' on ' . print_date_daytime($date_info['date_time']) . ' with the reason: ' . $post['refund_reason'] . '.';
			$email_content .= ' His mobile number is ' . $user['mobile_phone_number'];

			$data['email_content'] = $email_content;
			$data['email_title'] = 'Date Refund';
			$email_template = $this -> load -> view('email/common', $data, true);
			$this -> datetix -> mail_to_user(REFUND_EMAIL, $subject, $email_template);

			$this -> general -> set_table('user_email');
			$user_email_data = $this -> general -> get("", array('user_id' => $this -> user_id));
			$user_email = $user_email_data['0'];

			$subject1 = 'We have received your refund request';
			$data1['email_content'] = 'We have received your refund request and will get back to you if we have any questions.';
			$data1['email_title'] = '';
			$email_template1 = $this -> load -> view('email/common', $data1, true);
			$this -> datetix -> mail_to_user($user_email['email_address'], $subject1, $email_template1);
			$this -> session -> set_flashdata('page_msg_success', translate_phrase('<b>Your refund request has been submitted.</b>'));
		}
		redirect('dates/my_dates#past');
	}

	public function date_review() {

		$post = $this -> input -> post();
		$insertArray['date_id'] = $post['date_id'];
		$insertArray['review_by_user_id'] = $this -> user_id;
		$insertArray['rating'] = $post['score'];
		$insertArray['review'] = $post['date_comment'];
		$saveDateReview = $this -> model_date -> save_date_review($insertArray);
		redirect(base_url() . "dates/my_dates#past");
	}
	
	/*
	 * Function : Choose applicant send confirmation email to host and applicant user with confirming date data 
	 * @param : Applicant id
	 * @author : Rajnish Savaliya
	 */
	public function updateDateApplicant() {
		if($date_applicant_id = $this -> input -> post('id'))
		{
			$fields = array('d.*, da.*, dt.description as date_type, rt.description as intention_type, m.merchant_id as mid, m.name,m.neighborhood_id,m.address,m.phone_number,m.website_url, m.review_url');					
			$from = 'date_applicant as da';
			$joins = array(
					'date as d' => array('d.date_id = da.date_id', 'inner'), 
					'date_type as dt' => array('dt.date_type_id = d.date_type_id', 'left'), 
					'relationship_type as rt' => array('rt.relationship_type_id = d.date_intention_id', 'left'), 
					'merchant as m' => array('m.merchant_id = d.merchant_id', 'left')
			);
			
			$where['dt.display_language_id'] = $this -> language_id;
			$where['rt.display_language_id'] = $this -> language_id;
			$where['date_applicant_id'] = $date_applicant_id;
			$where['requested_user_id'] = $this -> user_id;
			
			if($data['date_info'] = $this -> general -> multijoins_arr($fields, $from, $joins, $where))
			{
				$data['date_info'] = $data['date_info']['0'];			
				
				//udpate applicant
				$applicant_user_id = $data['date_info']['applicant_user_id'] ;
				$udpate_data['is_chosen'] = '1';
				$updateApplicant = $this -> model_date -> update_date_applicant($date_applicant_id, $udpate_data);
				
				//Fetch host user data
				$this -> general -> set_table('user');
				$user_data = $this -> general -> get("user_id,password,first_name", array('user_id' => $this -> user_id));
				$user = $user_data['0'];
				
				//fetch choosen applicant user data
				$applicant_data = $this -> general -> get("user_id,password,first_name", array('user_id' => $applicant_user_id));
				$applicant_user = $applicant_data['0'];
				
				$date_type = $data['date_info']['date_type'];
				$date_address = $data['date_info']['name'];
				$date_time = print_date_daytime($data['date_info']['date_time']);
		
				$gender_type = ($user['gender_id'] == "1") ? "his" : "her";
				$gender_type = translate_phrase($gender_type);
				
				$email_data['email_title'] = '';
				$email_data['btn_text'] = translate_phrase('Confirm Your Date');
				
				$return_url  = base_url() . 'dates/match_date/' . $date_applicant_id;
				
				$this -> general -> set_table('user_email');
				if($host_user_email = $this -> general -> get("email_address", array('user_id' => $this -> user_id)))
				{
					//Dynamic autologin link
					$user_link = $this -> utility -> encode($user['user_id']);
					if ($user['password']) {
						$user_link .= '/' . $user['password'];
					}
					$email_data['btn_link'] = base_url() . 'home/mail_action/' . $user_link . '?return_to='.$return_url;
					
					$subject = translate_phrase('You have chosen ') . $applicant_user['first_name'] . translate_phrase(' to be your date');
					$email_data['email_content'] = translate_phrase('You have chosen ') . $applicant_user['first_name'] . translate_phrase(' to be your date for ') . $date_type . ' @ ' . $date_address . ' on ' . $date_time;
					
					$email_template = $this -> load -> view('email/common', $email_data, true);
					$this -> datetix -> mail_to_user($host_user_email[0]['email_address'], $subject, $email_template);
				}
				
				$this -> general -> set_table('user_email');
				if($applicant_user_email = $this -> general -> get("email_address", array('user_id' => $applicant_user_id)))
				{
					//Dynamic autologin link
					$user_link = $this -> utility -> encode($applicant_user['user_id']);
					if ($applicant_user['password']) {
						$user_link .= '/' . $applicant_user['password'];
					}
					$email_data['btn_link'] = base_url() . 'home/mail_action/' . $user_link . '?return_to='.$return_url;
					
					$subject = $user['first_name'] . translate_phrase(' has chosen you to be ') . $gender_type . translate_phrase(' date');
					$email_data['email_content'] = $user['first_name'] . translate_phrase(' has chosen you on ') . $gender_type . translate_phrase(' date for ') . $date_type . ' @ ' . $date_address . ' on ' . $date_time;
					
					$email_template = $this -> load -> view('email/common', $email_data, true);
					$this -> datetix -> mail_to_user($applicant_user_email[0]['email_address'], $subject, $email_template);
				}
			}	
		}
	}

	public function sendChatMessage() {
		$response['type'] = 'error';
		$response['msg'] = '';
		
		if ($this -> input -> is_ajax_request()) {
				
			$post = $this -> input -> post();
			
			$otherid = $this -> utility -> decode($post['otherid']);
			$currentid = $this->user_id;
			$msg = $post['msg'];
			$getLastMessage = $this -> db -> query("SELECT * 
	                            FROM `user_chat` 
	                            WHERE 
	                            	from_user_id='" . $currentid . "' AND 
	                            	to_user_id='" . $otherid . "' 
	                            order by chat_message_time desc
	                            limit 1") -> row_array();
			$messageTime = $getLastMessage['chat_message_time'];
	
			$insertArray['from_user_id'] = $currentid;
			$insertArray['to_user_id'] = $otherid;
			$insertArray['chat_message'] = $msg;
			$insertArray['is_read'] = '0';
			$insertArray['chat_message_time'] = date('Y-m-d H:i:s');
			$response['chat_msg'] = show_chat_txt($insertArray['chat_message']);
			
			if($this -> model_date -> save_chat($insertArray))
			{
				// check past message time btwen  these two users
				$to_time = strtotime($messageTime);
				$from_time = strtotime(date('Y-m-d H:i:s'));
				$diffMinutes = round(abs($to_time - $from_time) / 60, 2);
				// die();
				if ($diffMinutes > 30) {
					$this -> general -> set_table('user');
					$user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $currentid));
					$user = $user_data['0'];
		
					$this -> general -> set_table('user_email');
					$user_data_to = $this -> general -> get("", array('user_id' => $otherid));
					$user_to = $user_data_to['0'];
		
					//send mail to  receiver
					$data_applicant['email_content'] = 'You have received a new message from ' . $user['first_name'] . ' on ' .print_date_daytime(date('Y-m-d H:i:s'));
					$data_applicant['email_title'] = '';
					$return_url = base_url() . 'dates/chat_history/' . $this -> utility -> encode($currentid) . '/' . $this -> utility -> encode($otherid);
					
					//Dynamic autologin link
					$this -> general -> set_table('user');
					$user_data = $this -> general -> get("user_id,first_name,password", array('user_id' => $otherid));
					$user_to_profile = $user_data['0'];
					
					$user_link = $this -> utility -> encode($user_to_profile['user_id']);
					if ($user_to_profile['password']) {
						$user_link .= '/' . $user_to_profile['password'];
					}
					$data_applicant['btn_link'] = base_url() . 'home/mail_action/' . $user_link . '?return_to='.$return_url;
					
					$data_applicant['btn_text'] = 'View Message';
					$subject = 'You have received a new message from ' . $user['first_name'];
					$email_template = $this -> load -> view('email/common', $data_applicant, true);
					$this -> datetix -> mail_to_user($user_to['email_address'], $subject, $email_template);
				}

				$response['type'] = 'success';
				$response['msg'] = 'Message sent successfully.';
				
			}
		}
		echo json_encode($response);
	}

	public function edit_date_review() {
		$post = $this -> input -> post();
		$date_review_id = $post['date_review_id'];
		$data['rating'] = $post['score'];
		$data['review'] = $post['date_edit_comment'];
		$updateDateReview = $this -> model_date -> update_date_review($date_review_id, $data);
		redirect(base_url() . "dates/my_dates#past");
	}

	public function filter_date() {
		echo $this -> load -> view('user/dates_app/date_filter_popup');
	}

	public function apply_for_date() {

		$post = $this -> input -> post();
		$date_id = $post['date_id'];
		if ($date_id) {
			$getNumTickets = $this -> db -> query("select requested_user_id,num_date_tickets from date as d where date_id='" . $date_id . "'") -> row_array();
			$insertApplicants['date_id'] = $date_id;
			$insertApplicants['applicant_user_id'] = $this -> user_id;
			$insertApplicants['num_date_tickets'] = $getNumTickets['num_date_tickets'];
			$insertApplicants['is_chosen'] = '0';
			$saveApplicant=$this->model_date->saveInvitedApplicant($insertApplicants);
			
			//Save date ticket log
			$insertLogArray['transaction_time'] = SQL_DATETIME;
			$insertLogArray['description'] = "Apply to Date {$date_id}";
			$insertLogArray['user_id'] = $this -> user_id;
			$insertLogArray['num_date_tickets'] = $getNumTickets['num_date_tickets'];
			$this -> model_date -> save_date_ticket_log($insertLogArray);
			
			//send Email to user
			$this -> sendEmailTousers($getNumTickets['requested_user_id'], $date_id);

			$dateDetail = $this -> model_date -> get_date_detail_by_id($date_id);
			$msg = translate_phrase('You have successfully applied to ' . $dateDetail['date_type'] . ' @ ' . trim($dateDetail['name']) . ' on ' . print_date_daytime($dateDetail['date_time']) . ' with ') . $dateDetail['first_name'];
			
			$this -> session -> set_flashdata('pageMsg', $msg);
		}

	}

	/*
	 *  SEND EMAIL TO USERS WHILE LIKE DATE
	 *
	 */
	private function sendEmailTousers($from_user_id, $date_id) {
		if ($from_user_id && $date_id) {

			$this -> general -> set_table('user');
			$user_data = $this -> general -> get("first_name,last_name,gender_id", array('user_id' => $this -> user_id));
			$applicant_user_data = $user_data['0'];

			$this -> general -> set_table('user');
			$request_user_data = $this -> general -> get("user_id,password,first_name,last_name,gender_id", array('user_id' => $from_user_id));
			$host_user_data = $request_user_data['0'];

			$this -> general -> set_table('date_invite');
			$date_invite_condition['invite_user_id'] = $this -> user_id;
			$date_invite_condition['date_id'] = $date_id;

			
			// Send msg to applicant from host
			$insertChat['from_user_id'] = $from_user_id;
			$insertChat['to_user_id'] = $this -> user_id;
			$insertChat['chat_message'] = 'Hi ' . $applicant_user_data['first_name'] . ', thanks for apply to my date';
			$insertChat['is_read'] = '0';
			$insertChat['chat_message_time'] = SQL_DATETIME;
			$this -> model_date -> save_chat($insertChat);
			
			// Send msg to host from applicant
			$insertChat['from_user_id'] = $this -> user_id;
			$insertChat['to_user_id'] = $from_user_id;
			$insertChat['chat_message'] = 'Hi ' . $host_user_data['first_name'] . ', would be great to meet you for this date!';
			$insertChat['is_read'] = '0';
			$insertChat['chat_message_time'] = SQL_DATETIME;
			$this -> model_date -> save_chat($insertChat);
			
			
			//send email to date host user
			$is_user_invited = $this -> general -> get("", $date_invite_condition);
			if($is_user_invited)
			{
				//if invited
				$subject = $applicant_user_data['first_name'] . translate_phrase(" has responded to your invitation");
				$email_content = $applicant_user_data['first_name'] . translate_phrase(" has responded to your invitation and applied to your date! Click the button below to chat with ") . $applicant_user_data['first_name'];
			}
			else {
				$dateDetail = $this -> model_date -> get_date_detail_by_id($date_id);
				$subject = $applicant_user_data['first_name'].translate_phrase(" has applied to be your date");
				
				if($applicant_user_data['gender_id'] == 1)
				{
					$noun = 'his';
					$pro_noun = 'him';
				}
				else {
					$noun = 'her';
					$pro_noun = 'her';
				}
				$email_content = $applicant_user_data['first_name'].translate_phrase(' has applied to ' . $dateDetail['date_type'] . ' @ ' . trim($dateDetail['name']).' ' . print_date_daytime($dateDetail['date_time']). '. Click the button below to view '.$noun.' profile and chat with '.$pro_noun.':');
			}	
			
			$data['email_content'] = $email_content;
			$data['email_title'] = '';
			
			
			$host_user_id = $this -> utility -> encode($from_user_id);
			$chat_with_user_id = $this -> utility -> encode($this -> user_id);
			$data['btn_text'] = translate_phrase('Chat with ').$applicant_user_data['first_name'];
			
			$chat_link = base_url() . "dates/chat_history/" . $chat_with_user_id. "/" . $host_user_id;
			
			//Dynamic autologin link
			$user_link = $this -> utility -> encode($host_user_data['user_id']);
			if ($host_user_data['password']) {
				$user_link .= '/' . $host_user_data['password'];
			}
			$data['btn_link'] = base_url() . 'home/mail_action/' . $user_link . '?return_to='.$chat_link;
			
			$email_template = $this -> load -> view('email/common', $data, true);
			$user_email = $this -> model_user -> get_user_email($from_user_id);
			$this -> datetix -> mail_to_user($user_email['email_address'], $subject, $email_template);
		}
	}

	public function match_date($id) {
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number", array('user_id' => $this -> user_id));
		$user = $user_data['0'];
		$data['user_data'] = $user;
		
		$fields = array('d.*, 
					da.*,
                    dt.description as date_type,
                    rt.description as intention_type,
                    m.merchant_id as mid,
                    m.name,
                    m.neighborhood_id,
                    m.address,
                    m.phone_number,
                    m.website_url,
                    m.review_url');
					
		$from = 'date_applicant as da';
		$joins = array(
				'date as d' => array('d.date_id = da.date_id', 'inner'), 
				'date_type as dt' => array('dt.date_type_id = d.date_type_id', 'left'), 
				'relationship_type as rt' => array('rt.relationship_type_id = d.date_intention_id', 'left'), 
				//'date_payer as dp' => array('dp.date_payer_id = d.date_payer_id', 'left'), 							
				'merchant as m' => array('m.merchant_id = d.merchant_id', 'left')
				);
		
		$where['dt.display_language_id'] = $this -> language_id;
		$where['rt.display_language_id'] = $this -> language_id;
		$where['date_applicant_id'] = $id;
		$custom_where = "(d.requested_user_id = '".$this -> user_id."' OR da.applicant_user_id = '".$this -> user_id."')";		
		if($data['date_info'] = $this -> general -> multijoins_arr($fields, $from, $joins, $where,$custom_where))
		{
			$data['date_info'] = $data['date_info']['0'];
			$applicant_user_id = $data['date_info']['applicant_user_id'];
			$host_user_id = $data['date_info']['requested_user_id'];
			
			$data['host_photo'] = $this -> model_date -> get_current_primary_photo($host_user_id);
			$data['applicant_photo'] = $this -> model_date -> get_current_primary_photo($applicant_user_id);
			
			if($applicant_user_id == $this->user_id)
			{
				$data['applicant_user_data'] = $user;
			}
			else{
				$this -> general -> set_table('user');
				$applicant_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number", array('user_id' => $applicant_user_id));
				$data['applicant_user_data'] = $applicant_data['0'];					
			}
			
			if($host_user_id == $this->user_id)
			{
				$data['host_user_data'] = $user;
			}
			else{
				$this -> general -> set_table('user');
				$applicant_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number", array('user_id' => $host_user_id));
				$data['host_user_data'] = $applicant_data['0'];				
			}
			
			
			$data['page_name'] = 'user/dates_app/match_date';			
		}
		else {
			
			$data['page_name'] = 'user/dates_app/message';	
			$data['msg'] = translate_phrase('Sorry! Wrong Match url with id : ').$id;	
		}
		$data['page_title'] = '';
		
		$this -> load -> view('template/editProfileTemplate', $data);
	}

	public function view_date($date_id) {
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $this -> user_id));
		$user = $user_data['0'];
		$data['user_data'] = $user;
		$data['date_info'] = $this -> model_date -> get_date_detail_by_id($date_id);

		$this -> db -> order_by('set_primary', 'desc');
		$this -> db -> select('*');
		$data['merchant_photos'] = $this -> db -> get_where('merchant_photo', array('merchant_id' => $data['date_info']['mid'])) -> result_array();
		$data['merchant_neighborhood'] = $this -> db -> get_where('neighborhood', array('neighborhood_id' => $data['date_info']['neighborhood_id'])) -> row_array();
		//echo "<pre>";print_r($results);exit;

		$data['user_phots'] = $this -> model_date -> get_current_primary_photo($data['date_info']['requested_user_id']);

		$applicant_user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $data['date_info']['requested_user_id']));
		$data['applicant_user'] = $applicant_user_data['0'];

		$data['page_title'] = '';
		$data['page_name'] = 'user/dates_app/view_date';
		$this -> load -> view('template/editProfileTemplate', $data);
	}

	public function save_user_date_setting() {
			
		$response['type'] = 'error';
		$response['msg'] = '';
		
		if($post = $this -> input -> post('formData'))
		{
			$userid = $this -> user_id;
			foreach($post as $selectedParamter=>$saveData)
			{
				// save distance date setting
				if ($selectedParamter == 'date_setting_distance') {
		
				}
		
				// save setting date type
				if ($selectedParamter == 'setting_date_type') {
					// first delete all old record
					$this -> general -> set_table('user_preferred_date_type');
					$this -> general -> delete(array('user_id' => $userid));
					//  inser record
					$inserArray = explode(',', $post[$selectedParamter]);
					$data = array();
					foreach ($inserArray as $key => $val) {
						$data[] = array('date_type_id' => $val, 'user_id' => $userid);
					}
					$this -> general -> saveBatch($data);
				}
		
				// save setting date setting_date_intention
				if ($selectedParamter == 'setting_date_intention') {
					// first delete all old record
					$this -> general -> set_table('user_want_relationship_type');
					$this -> general -> delete(array('user_id' => $userid));
					//  inser record
					$inserArray = explode(',', $post[$selectedParamter]);
					$data = array();
					foreach ($inserArray as $key => $val) {
						$data[] = array('relationship_type_id' => $val, 'user_id' => $userid);
					}
					$this -> general -> saveBatch($data);
				}
		
				// save setting date gender
				if ($selectedParamter == 'setting_date_gender') {
		
					// first delete all old record
					$this -> general -> set_table('user_want_gender');
					$this -> general -> delete(array('user_id' => $userid));
					//  inser record
					$inserArray = explode(',', $post[$selectedParamter]);
					$data = array();
					foreach ($inserArray as $key => $val) {
						$data[] = array('gender_id' => $val, 'user_id' => $userid);
					}
					$this -> general -> saveBatch($data);
				}
		
				// save setting date age
				if ($selectedParamter == 'date_setting_start_age' || $selectedParamter == 'date_setting_end_age') {
					// update record
					$this -> general -> set_table('user');
					$this -> general -> update(array('want_age_range_lower' => $post['date_setting_start_age'], 'want_age_range_upper' => $post['date_setting_end_age']), array('user_id' => $userid));
				}
		
				// save setting date ethnicity
				if ($selectedParamter == 'setting_date_ethnicity') {
					// first delete all old record
					$this -> general -> set_table('user_want_ethnicity');
					$this -> general -> delete(array('user_id' => $userid));
					//  inser record
					$inserArray = explode(',', $post[$selectedParamter]);
					$data = array();
					foreach ($inserArray as $key => $val) {
						$data[] = array('ethnicity_id' => $val, 'user_id' => $userid);
					}
					$this -> general -> saveBatch($data);
				}
		
				// save setting date heifgr
				if ($selectedParamter == 'date_setting_start_height' || $selectedParamter == 'date_setting_end_height') {
					// update record
					$this -> general -> set_table('user');
					$this -> general -> update(array('want_height_range_lower' => $post['date_setting_start_height'], 'want_height_range_upper' => $post['date_setting_end_height']), array('user_id' => $userid));
				}
		
				// save setting date body type
				if ($selectedParamter == 'setting_date_bodytype') {
					// first delete all old record
		
					$this -> general -> set_table('user_want_body_type');
					$this -> general -> delete(array('user_id' => $userid));
					//  inser record
					$inserArray = explode(',', $post[$selectedParamter]);
					$data = array();
					foreach ($inserArray as $key => $val) {
						$data[] = array('body_type_id' => $val, 'user_id' => $userid);
					}
					$this -> general -> saveBatch($data);
		
				}
		
				
				// save setting date relationship status
				if ($selectedParamter == 'setting_date_relationship_status') {
					// first delete all old record
					$this -> general -> set_table('user_want_relationship_status');
					$this -> general -> delete(array('user_id' => $userid));
					//  inser record
					$inserArray = explode(',', $post[$selectedParamter]);
					$data = array();
					foreach ($inserArray as $key => $val) {
						$data[] = array('relationship_status_id' => $val, 'user_id' => $userid);
					}
					$this -> general -> saveBatch($data);
				}
		
				// save setting date religious
				if ($selectedParamter == 'setting_date_religious') {
					// first delete all old record
					$this -> general -> set_table('user_want_religious_belief');
					$this -> general -> delete(array('user_id' => $userid));
					//  inser record
					$inserArray = explode(',', $post[$selectedParamter]);
					$data = array();
					foreach ($inserArray as $key => $val) {
						$data[] = array('religious_belief_id' => $val, 'user_id' => $userid);
					}
					$this -> general -> saveBatch($data);
				}
		
				// save setting date personalities
				if ($selectedParamter == 'setting_date_personalities') {
					// first delete all old record
					$this -> general -> set_table('user_want_descriptive_word');
					$this -> general -> delete(array('user_id' => $userid));
					//  inser record
					$inserArray = explode(',', $post[$selectedParamter]);
					$data = array();
					foreach ($inserArray as $key => $val) {
						$data[] = array('descriptive_word_id' => $val, 'user_id' => $userid);
					}
					$this -> general -> saveBatch($data);
				}
		
				// save setting date smoking
				if ($selectedParamter == 'setting_date_smoking') {
					// first delete all old record
					$this -> general -> set_table('user_want_smoking_status');
					$this -> general -> delete(array('user_id' => $userid));
					//  inser record
					$inserArray = explode(',', $post[$selectedParamter]);
					$data = array();
					foreach ($inserArray as $key => $val) {
						$data[] = array('smoking_status_id' => $val, 'user_id' => $userid);
					}
					$this -> general -> saveBatch($data);
				}
		
				// save setting date smoking
				if ($selectedParamter == 'setting_date_drinking') {
					// first delete all old record
					$this -> general -> set_table('user_want_drinking_status');
					$this -> general -> delete(array('user_id' => $userid));
					//  inser record
					$inserArray = explode(',', $post[$selectedParamter]);
					$data = array();
					foreach ($inserArray as $key => $val) {
						$data[] = array('drinking_status_id' => $val, 'user_id' => $userid);
					}
					$this -> general -> saveBatch($data);
				}
		
				// save setting date excercise
				if ($selectedParamter == 'setting_date_exercise') {
					// first delete all old record
					$this -> general -> set_table('user_want_exercise_frequency');
					$this -> general -> delete(array('user_id' => $userid));
					//  inser record
					$inserArray = explode(',', $post[$selectedParamter]);
					$data = array();
					foreach ($inserArray as $key => $val) {
						$data[] = array('exercise_frequency_id' => $val, 'user_id' => $userid);
					}
					$this -> general -> saveBatch($data);
				}
				
				// save setting date language
				if ($selectedParamter == 'setting_date_language') {
		
				}
		
				// save setting date hobby
				if ($selectedParamter == 'setting_date_hobby') {
		
				}
		
				// save setting date hangouts
				if ($selectedParamter == 'setting_date_hangouts') {
		
				}
		
				// save setting date music
				if ($selectedParamter == 'setting_date_music') {
		
				}
		
				// save setting date movie
				if ($selectedParamter == 'setting_date_movie') {
		
				}
		
				// save setting date sport
				if ($selectedParamter == 'setting_date_sport') {
		
				}
		
				// save setting date sport
				if ($selectedParamter == 'setting_date_house') {
					// first delete all old record
					$this -> general -> set_table('user_want_residence_type');
					$this -> general -> delete(array('user_id' => $userid));
					//  inser record
					$inserArray = explode(',', $post[$selectedParamter]);
					$data = array();
					foreach ($inserArray as $key => $val) {
						$data[] = array('residence_type_id' => $val, 'user_id' => $userid);
					}
					$this -> general -> saveBatch($data);
				}
		
				// save setting date education
				if ($selectedParamter == 'setting_date_education_level') {
					// first delete all old record
					$this -> general -> set_table('user_want_education_level');
					$this -> general -> delete(array('user_id' => $userid));
					//  inser record
					$inserArray = explode(',', $post[$selectedParamter]);
					$data = array();
					foreach ($inserArray as $key => $val) {
						$data[] = array('education_level_id' => $val, 'user_id' => $userid);
					}
					$this -> general -> saveBatch($data);
				}
		
				// save setting date school
				if ($selectedParamter == 'setting_date_school') {
					// first delete all old record
					$this -> general -> set_table('user_want_school');
					$this -> general -> delete(array('user_id' => $userid));
					//  inser record
					$inserArray = explode(',', $post[$selectedParamter]);
					$data = array();
					foreach ($inserArray as $key => $val) {
						$data[] = array('school_id' => $val, 'user_id' => $userid);
					}
					$this -> general -> saveBatch($data);
				}
		
				// save setting date school
				if ($selectedParamter == 'setting_date_company') {
					// first delete all old record
					$this -> general -> set_table('user_want_company');
					$this -> general -> delete(array('user_id' => $userid));
					//  inser record
					$inserArray = explode(',', $post[$selectedParamter]);
					$data = array();
					foreach ($inserArray as $key => $val) {
						$data[] = array('company_id' => $val, 'user_id' => $userid);
					}
					$this -> general -> saveBatch($data);
				}		
			}
			$response['type'] = 'success';
			$msg = translate_phrase('Your setting has been successfully saved.');
			$response['msg'] = $msg;
		}
		echo json_encode($response);
	}

	public function mutual_friend($fb_id, $other_user_id) {
		$data['page_title'] = 'Mutual Friend Demo';
		$data['page_name'] = 'user/demo_mutual_friend';
		$return_to = url_city_name() . '/';
		if ($return_to .= $this -> input -> get('return_to')) {
			if ($tab = $this -> input -> get('tab')) {
				$return_to .= '#' . $tab;
			}

			$return_url = $return_to;
		} else {
			$return_url = 'index.html';
		}

		if ($data['user_info'] = $this -> model_user -> getByFacebookId($fb_id)) {
			$data['mutual_friends'] = array();

			try {
				$fb_user_id = $this -> facebook -> getUser();
				if ($fb_user_id) {
					if ($mutual_friends = $this -> datetix -> fb_mutual_friend($this -> user_id, $data['user_info'] -> user_id)) {
						$mutual_friends_app_users_cnt = 0;
						foreach ($mutual_friends as $fb_user) {
							if ($this -> datetix -> is_app_user($fb_user['facebook_id'])) {
								$mutual_friends_app_users_cnt++;
							}

							$friends = $this -> facebook -> api('/' . $fb_user['facebook_id'] . '?fields=name,location,relationship_status');
							if ($tmp = $this -> model_user -> get_custom_user_data('user_id', array('facebook_id' => $fb_user['facebook_id']))) {
								$friends['friend_with_datetix'] = $this -> model_user -> get_fb_friends_with_datetix($tmp -> user_id);
							}
							$data['mutual_friends'][] = $friends;
						}
						$data['mutual_friends_app_users_cnt'] = $mutual_friends_app_users_cnt;
					}
				} else {
					$this -> load -> view('direct_fb_login');
				}
			} catch (Exception $e) {

			}

			//$data['return_url'] = url_city_name().'/user/user_info/'.$this->utility->encode($other_user_id).'/'.$this->utility->encode($this -> user_id);
			$data['return_url'] = '';
			$data['fb_app_id'] = $this -> config -> item('appId');
			$data['fb_desc'] = translate_phrase('Apply for a free membership to datetix.com today and let us help set you up on first dates with high quality local singles. Please visit ') . base_url();

			$user_id = $this -> session -> userdata('user_id');
			$data['user_data'] = $this -> model_user -> get_user_data($user_id);
			$data['page_title'] = translate_phrase('Mutual Friend');
			$data['page_name'] = 'user/popup_mutual_friend';

			echo $this -> load -> view('user/popup_mutual_friend', $data);
		}else {
			echo "<p>No mutual friend found.</p>";
			echo '<div class="column-100">
                    <div class="Nex-mar mar-top2">
                        <input type="button" onclick="$.fancybox.close();" class="btn btn-blue disable-butn right date-btn" value="Ok">
                    </div>
                </div>';
		}
	}

	public function follow_merchant() {
		$post = $this -> input -> post();
		$merchant_id = $post['merchant_id'];
		$status = $post['type'];

		if ($status == '0') {
			$this -> db -> where('merchant_id ', $merchant_id);
			$this -> db -> where('user_id', $this -> user_id);
			$this -> db -> update('user_follow_merchant', array('unfollow_time' => SQL_DATETIME));
			$this -> session -> set_flashdata('page_msg_success', translate_phrase('<b>You have been successfully unfollowed.</b>'));
		} else {
			$this -> db -> where('merchant_id ', $merchant_id);
			$this -> db -> where('user_id', $this -> user_id);
			$updateFollowingtime = $this -> db -> update('user_follow_merchant', array('follow_time' => SQL_DATETIME));

			if ($this -> db -> affected_rows() == 0) {
				$insertArray['merchant_id'] = $merchant_id;
				$insertArray['user_id'] = $this -> user_id;
				$insertArray['follow_time'] = SQL_DATETIME;
				$saveFollow = $this -> model_date -> saveFollowMerchant($insertArray);
			}
			$this -> session -> set_flashdata('page_msg_success', translate_phrase('<b>You have been successfully followed.</b>'));
		}
	}

	public function ban_user_date() {
		$response['type'] = 'error';
		$response['msg'] = 'Error occured. please try again';
		if ($post = $this -> input -> post()) {
			$decision = '-1';
			$insertArrayDecision['date_id'] = $post['id'];
			$insertArrayDecision['user_id'] = $this -> user_id;
			$insertArrayDecision['decision'] = $decision;
			$insertArrayDecision['decision_time'] = SQL_DATETIME;
			$this -> general -> set_table('date_decision');
			$this -> general -> save($insertArrayDecision);
		}
		echo json_encode($response);
	}
}
?>
