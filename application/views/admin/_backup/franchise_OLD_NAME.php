<?php
//if (!defined('BASEPATH'))
//exit('No direct script access allowed');
class franchise extends CI_Controller {
	var $language_id = '1';
	public function __construct() {
		parent::__construct();
		ini_set('memory_limit', '-1');
		
		$this -> load -> model('general_model', 'general');
		if (!$this -> session -> userdata('sess_language_id')) {
			$this -> session -> set_userdata('sess_language_id', '1');
		}
		
		$logged_in = $this -> session -> userdata('franchise_logged_in');
		
		if ($logged_in === FALSE) {
		
			if (!isset($_SERVER['PHP_AUTH_USER'])) {
				header('WWW-Authenticate: Basic realm="Datetix Franchise Panel"');
				header('HTTP/1.0 401 Unauthorized');
				echo("Please enter a valid username and password");
				exit();
			}
			$this -> general -> set_table('partner');
			$checkCondition['login'] = $_SERVER['PHP_AUTH_USER'];
			$checkCondition['password'] = $_SERVER['PHP_AUTH_PW'];			
		
			$CheckFranchise = $this -> general -> get('*', $checkCondition);			
			if (!empty($CheckFranchise)) {
				//$this -> session -> set_userdata(array('superadmin_logged_in' => TRUE));
				$this -> session -> set_userdata('franchise_logged_in', $CheckFranchise[0]);				
				//$this -> session -> set_userdata('sess_language_id', $CheckFranchise[0]['default_display_language_id']);
			} else {
				header('WWW-Authenticate: Basic realm="Datetix Franchise Panel"');
				header('HTTP/1.0 401 Unauthorized');
				echo("Please reload page and enter a valid username and password");
				exit();
			}
		}
	}
	public function logout() {
		$this -> session -> sess_destroy();
		$this -> session -> unset_userdata('franchise_logged_in');
		
		unset($_SERVER['PHP_AUTH_USER']);
		unset($_SERVER['PHP_AUTH_PW']);
		
		redirect('/franchise');
	}
	function index()
	{
		
		$this->view_ad_details();
	}
	/**
	 * view_ad_details : Franchise Details
	 * @author Rajnish Savaliya
	 */
	 public function view_ad_details($franchise="")
	 {
	 	
	 	/*
		 $this->session->set_userdata('franchise',$franchise);
	 	$my_franchise = array(3 => 'smartime', 7 => 'tinavip', 12 => 'mnc', 30 => 'derek', 31 => 'ellen', 24 => 'angel', 33 => 'your-mr-miss-right', 34 => 'samyin', 35 => 'carrie', 36 => 'pw');
		$ad_id = array_search($franchise, $my_franchise);
		*/
	 	$franchise_data = $this -> session -> userdata('franchise_logged_in');
		
		$franchise = $franchise_data['name'];
		$this->session->set_userdata('franchise',$franchise);
		
		$ad_id = $franchise_data['partner_id'];
		
		/*if($ad_id == 3)
		{
			$user_sql = 'SELECT u.user_id, u.first_name, u.last_name, u.gender_id, u.mobile_phone_number, ue.email_address, u.applied_date, u.completed_application_step FROM user u LEFT JOIN user_email ue ON u.user_id=ue.user_id WHERE u.website_id='.$ad_id.' ORDER BY applied_date DESC';
			$data['users'] = $this->general->sql_query($user_sql );
			
			$member_sql = 'SELECT eo.event_id, et.event_ticket_id, eo.order_amount, u.user_id, u.first_name, u.last_name, u.gender_id, u.mobile_phone_number, ue.email_address, eo.order_time FROM event_order eo JOIN event_ticket et ON (eo.event_order_id=et.event_order_id) JOIN user u ON (eo.paid_by_user_id=u.user_id) JOIN user_email ue ON (u.user_id=ue.user_id) WHERE u.website_id='.$ad_id.' ORDER BY et.event_ticket_id DESC;';
			$data['member_users'] = $this->general->sql_query($member_sql );
			
			
			$non_member_sql = 'SELECT eo.event_id, eo.order_amount, eo.order_time, eo.paid_by_first_name, eo.paid_by_last_name, eo.paid_by_email, et.event_ticket_id FROM event_order eo JOIN event_ticket et ON (eo.event_order_id=et.event_order_id) WHERE eo.paid_by_user_id is NULL AND eo.website_id='.$ad_id.' ORDER BY et.event_ticket_id DESC';
			$data['non_member_users'] = $this->general->sql_query($non_member_sql);
			
			//echo "<pre>";print_r($data['member_users']);exit;
			$data['franchise'] = $franchise;	 	
		}
		else*/
		if($ad_id )
		{
			$user_sql = 'SELECT u.user_id, u.first_name, u.last_name, u.gender_id, u.mobile_phone_number, ue.email_address, u.applied_date, u.completed_application_step FROM user u LEFT JOIN user_email ue ON u.user_id=ue.user_id WHERE u.ad_id='.$ad_id.' ORDER BY applied_date DESC';
			$data['users'] = $this->general->sql_query($user_sql );
			
			$member_sql = 'SELECT eo.event_id, et.event_ticket_id, eo.order_amount, u.user_id, u.first_name, u.last_name, u.gender_id, u.mobile_phone_number, ue.email_address, eo.order_time FROM event_order eo JOIN event_ticket et ON (eo.event_order_id=et.event_order_id) JOIN user u ON (eo.paid_by_user_id=u.user_id) JOIN user_email ue ON (u.user_id=ue.user_id) WHERE u.ad_id='.$ad_id.' ORDER BY et.event_ticket_id DESC;';
			$data['member_users'] = $this->general->sql_query($member_sql );
			
			
			$non_member_sql = 'SELECT eo.event_id, eo.order_amount, eo.order_time, eo.paid_by_name, eo.paid_by_mobile_phone_number, eo.paid_by_email, et.event_ticket_id FROM event_order eo JOIN event_ticket et ON (eo.event_order_id=et.event_order_id) WHERE eo.paid_by_user_id is NULL AND eo.ad_id='.$ad_id.' ORDER BY et.event_ticket_id DESC';
			$data['non_member_users'] = $this->general->sql_query($non_member_sql);
			
			//echo "<pre>";print_r($data['member_users']);exit;
			$data['franchise'] = $franchise;	 	
		}
		else {
			$data['franchise'] = 'Invalid Name.';	
		}
		$data['ad_id'] = $ad_id;
		$data['page_title'] = translate_phrase('Order History');
		$data['page_name'] = 'franchise/view_details';
		$this -> load -> view('template/franchise', $data);
	 }
	
	/**
	 * view_ad_details : Franchise Details
	 * @author Rajnish Savaliya
	 */
	 public function find_match($ad_id=0,$user_id=1)
	 {
	 	$data['franchise'] = $this->session->userdata('franchise');
		$data['match_url'] = base_url('franchise/find_match').'/'.$ad_id.'/'.$user_id;
		
		//Load user data
		if ($this -> input -> post('page_no')) {
			$page_no = $this -> input -> post('page_no');
		} else {
			$page_no = 1;
		}

		$order = array();
		$limit = PER_PAGE_ADMIN;
		$offset = $page_no;
		
		$data['page_no'] = $page_no;
		
		$this->general->set_table('user');
		$profile_condition['user_id'] =  $user_id;
		
		//$profile_condition['ad_id'] =  1;		
		if($user_profile = $this->general->get("*,",$profile_condition))
		{
			$user_profile = $user_profile['0'] ;
		}
		$city_name = "Hong Kong";
		
		$match_user_condition = array();
		if($user_profile['current_city_id'])
		{
			$match_user_condition['current_city_id'] = $user_profile['current_city_id'];
		}

		if($user_profile['gender_id'])
		{
			//set opposite gender
			$match_user_condition['gender_id'] = $user_profile['gender_id']=='1'?0:1;
		}
		
		$match_user_condition['user_id !='] = $user_id;
		$this -> general -> set_table('user');
		if($users = $this -> general -> get("user_id,first_name,last_name,completed_application_step,facebook_id,current_city_id,gender_id", $match_user_condition,$order,$limit,$offset))
		{
			foreach($users as $key=>$user)
			{
				
				$users[$key]['match_score'] = $this -> datetix -> calculate_score($user_id, $user['user_id']);
				$users[$key]['city_description'] = $city_name;				
			}
		}
		
		$data['users']  = $users;
			
		if($this->input->is_ajax_request())
		{
			$this->load->view('franchise/load_members',$data);
		}
		else {
			//echo "<pre>";print_r($users);exit;
			$data['page_title'] = translate_phrase('Find Match');
			$data['page_name'] = 'franchise/find_match';
			$this -> load -> view('template/franchise', $data);
		}
		
	 }
}
?>
