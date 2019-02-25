<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
	var $language_id = '1';
	var $admin_id = '';
	public function __construct() {
		parent::__construct();
		$this -> load -> model('general_model', 'general');
		$this -> load -> library('datetix');
		
		$logged_in = $this -> session -> userdata('superadmin_logged_in');
		if ($logged_in != TRUE) {
			$user = $this -> config -> item('superadmin_username');
			$password = $this -> config -> item('superadmin_password');
				
			if (!isset($_SERVER['PHP_AUTH_USER'])) {
				header('WWW-Authenticate: Basic realm="Datetix Super Admin Panel"');
				header('HTTP/1.0 401 Unauthorized');
				echo("Please enter a valid username and password");
				exit();
			} else if (($_SERVER['PHP_AUTH_USER'] == $user) && ($_SERVER['PHP_AUTH_PW'] == $password)) {
				$this -> session -> set_userdata(array('superadmin_logged_in' => TRUE));
			} else {
				header('WWW-Authenticate: Basic realm="Datetix Super Admin Panel"');
				header('HTTP/1.0 401 Unauthorized');
				echo("Please enter a valid username and password");
				exit();
			}
		}
	}

	/**
	 * Admin Dashboard: super admin panel
	 * @author Rajnish Savaliya
	 */
	public function index()
	{
		$data['selectedTab'] = 'review_appTAB';
		
		//Load user data
		if ($this -> input -> post('page_no')) {
			$page_no = $this -> input -> post('page_no');
		} else {
			$page_no = 1;
		}

		$data['page_no'] = $page_no;
		$limit = ' LIMIT ' . ($page_no - 1) * PER_PAGE_ADMIN . ', ' . PER_PAGE_ADMIN;

		$user_select = "user_id,completed_application_step,facebook_id,gender_id,first_name,last_name,num_date_tix,account_status_id,last_display_language_id";
		$user_application_sql = "SELECT city.description AS city_description, status.account_status_id, status.description, last_display_language_id,
					user_id,completed_application_step,facebook_id,gender_id,first_name,last_name,num_date_tix,attractiveness_level,facebook_page
					FROM user as usr
					JOIN account_status as status on usr.account_status_id = status.account_status_id					
					JOIN city on usr.current_city_id = city.city_id
					WHERE 
						usr.approved_date IS NULL AND
						status.display_language_id = ".$this->language_id." AND 
						city.display_language_id = ".$this->language_id."
					ORDER BY applied_date DESC" . $limit;
		$data['review_applications'] = $this->_make_template_data($this -> general -> sql_query($user_application_sql));
		
		$city_select = "select ct.city_id, ct.description from user 
						JOIN city as ct on ct.city_id = user.current_city_id
						WHERE approved_date IS NULL GROUP BY current_city_id ORDER BY view_order asc";
		$data['cities'] = $this -> general -> sql_query($city_select);

		/*
		//load all city data
		$this->general->set_table('city');
		$city_condition['display_language_id'] = $this->language_id;
		$city_condition['is_active'] = 1;		
		$data['cities'] = $this->general->get("city_id,description",$city_condition,array('view_order'=>'asc'));
		//echo "<pre>";print_r($data['cities']);exit;
		*/
		
		$user_select = "user_id,completed_application_step,facebook_id,gender_id,first_name,last_name,num_date_tix,account_status_id";
		$user_application_sql = "SELECT city.description AS city_description, status.account_status_id, status.description,  last_display_language_id,
					user_id,completed_application_step,facebook_id,gender_id,first_name,last_name,num_date_tix,attractiveness_level,facebook_page
					FROM user as usr
					JOIN account_status as status on usr.account_status_id = status.account_status_id					
					JOIN city on usr.current_city_id = city.city_id
					WHERE 
						usr.approved_date IS NOT NULL AND
						status.display_language_id = ".$this->language_id." AND 
						city.display_language_id = ".$this->language_id."
					ORDER BY applied_date DESC" . $limit;
		$data['approved_applications'] = $this->_make_template_data($this -> general -> sql_query($user_application_sql));
		
		
		$this -> general -> set_table('account_status');
		$account_condition['display_language_id'] = $this -> language_id;
		$data['account_data'] = $this -> general -> get("", $account_condition);

		$this -> general -> set_table('membership_option');
		$membership_condition['display_language_id'] = $this -> language_id;
		$data['membership_options'] = $this -> general -> get("", $membership_condition,array('view_order'=>'asc'));
		
		//echo $user_sql;
		//echo "<pre>";print_r($data['review_applications']);exit;
		$data['page_title'] = translate_phrase('Member Management');
		$data['page_name'] = 'admin/dashboard';
		$this -> load -> view('template/admin', $data);
	}
	
	
	/**
	 * [Ajax-call] load_users Function :: Load Users application
	 * @access public
	 * @return View data
	 * @author Rajnish Savaliya
	 */
	public function load_users($type = 'review_app') {
		if ($this -> input -> is_ajax_request()) 
		{
			$sortby = $this -> input -> post('sort_by');
			$sort_order = '';

			if ($sortby == 1) {
				$sort_order = 'ORDER BY `applied_date` DESC';
			} elseif ($sortby == 2) {
				$sort_order = 'ORDER BY `applied_date` ASC';
			} elseif ($sortby == 3) {
				$sort_order = 'ORDER BY `age` ASC';
			} elseif ($sortby == 4) {
				$sort_order = 'ORDER BY `age` DESC';
			} elseif ($sortby == 5) {
				$sort_order = 'ORDER BY `first_name` ASC';
			} elseif ($sortby == 6) {
				$sort_order = 'ORDER BY `first_name` DESC';
			}
			
			if($type == "manage_member")
			{
				if ($sortby == 7) {
				$sort_order = 'ORDER BY `last_active_time` ASC';
				} elseif ($sortby == 8) {
					$sort_order = 'ORDER BY `last_active_time` DESC';
				}elseif ($sortby == 9) {
					$sort_order = 'ORDER BY `order_time` ASC';
				} elseif ($sortby == 10) {
					$sort_order = 'ORDER BY `order_time` DESC';
				}elseif ($sortby == 11) {
					$sort_order = 'ORDER BY `order_amount` ASC';
				} elseif ($sortby == 12) {
					$sort_order = 'ORDER BY `order_amount` DESC';
				}elseif ($sortby == 13) {
					$sort_order = 'ORDER BY msg_count DESC';
				}
			}
			
			//Load user data
			if ($this -> input -> post('page_no')) {
				$page_no = $this -> input -> post('page_no');
			} else {
				$page_no = 1;
			}
			
			
			$limit = ' LIMIT ' . ($page_no - 1) * PER_PAGE_ADMIN . ', ' . PER_PAGE_ADMIN;

			$user_application_sql = "SELECT city.description as city_description, status.account_status_id, status.description,
						usr.user_id,completed_application_step,facebook_id,gender_id,first_name,last_name,num_date_tix,attractiveness_level,facebook_page,
						CASE
							WHEN
								birth_date != '0000-00-00'
								THEN 
									TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
							END as age";
			
			if($sortby==13 && $type == "manage_member")
				$user_application_sql .= " ,COUNT(user_intro_chat.user_intro_chat_id) as msg_count";
								
			$user_application_sql .= " FROM user as usr
								JOIN account_status as status on usr.account_status_id = status.account_status_id
								JOIN city on usr.current_city_id = city.city_id";
			
			if($sortby>=9 && $sortby<=12 && $type == "manage_member")
				$user_application_sql .= " JOIN user_order as usr_ord on usr.user_id = usr_ord.user_id";
			if($sortby==13 && $type == "manage_member")
				$user_application_sql .= " JOIN user_intro_chat on usr.user_id = user_intro_chat.user_id";
			
			$user_application_sql .= " WHERE status.display_language_id = ".$this->language_id." AND city.display_language_id = ".$this->language_id;
				
			if($type == 'review_app')
			{
				$user_application_sql .= " AND usr.approved_date IS NULL";
			}
			else
			{
				$user_application_sql .= " AND usr.approved_date IS NOT NULL";
			}
			
			if ($like_val = $this -> input -> post('search_txt')) {
				
				$user_application_sql .= " AND (`first_name` LIKE '%".$like_val."%' OR `last_name` LIKE '%".$like_val."%')";
			}
			
			if ($city_id = $this -> input -> post('city_id')) {
				$user_application_sql .= " AND `current_city_id` = ".$city_id."";
			}
			
			$user_application_sql .= " GROUP BY usr.user_id ".$sort_order." " .$limit;
			$users = $this->_make_template_data($this -> general -> sql_query($user_application_sql));
			$this -> general -> set_table('account_status');
			$account_condition['display_language_id'] = $this -> language_id;
			$data['account_data'] = $this -> general -> get("", $account_condition);

			$this -> general -> set_table('membership_option');
			$membership_condition['display_language_id'] = $this -> language_id;
			$data['membership_options'] = $this -> general -> get("", $membership_condition,array('view_order'=>'asc'));
			
			
			if($type == 'review_app')
			{
				$data['selectedTab'] = 'review_appTAB';
				$data['review_applications'] = $users;
				$this->load->view('admin/review_applications',$data);
			}
			else
			{
				$data['selectedTab'] = 'manage_memberTAB';
				$data['approved_applications'] =$users;
				$this->load->view('admin/load_members',$data);
			}
		}		
	}
	
	/**
	 * Formate data: super admin panel
	 * @author Rajnish Savaliya
	 */
	private function _make_template_data($user_application_data)
	{
		if($user_application_data)
		{
			foreach($user_application_data as $key=>$tmp_user)
			{
				$user_lang_id = $tmp_user['last_display_language_id'];
				
				$user_id = $tmp_user['user_id'];

				//Get Membership data
				$this->general->set_table('user_membership_option');
				$user_application_data[$key]['user_membership_option'] = $this->general->get("membership_option_id,expiry_date",array('user_id'=>$user_id));

				$user_application_data[$key]['thanks_mail_body'] = "Hi ".$tmp_user['first_name'].",";
				$user_application_data[$key]['thanks_mail_body'] .= "\n\nThanks for applying Datetix! I'm currently reviewing your profile and would like to know how you heard about Datetix?";

				$user_application_data[$key]['thanks_mail_body'] .= "\n\nMichael Ye";
				$user_application_data[$key]['thanks_mail_body'] .= "\nFounder and CEO";
				$user_application_data[$key]['thanks_mail_body'] .= "\nmichael.ye@datetix.com";
				
				switch ($user_lang_id) {
					case '3':
							$user_application_data[$key]['approve_mail_body'] = $tmp_user['first_name'].", 您好";
							//$user_application_data[$key]['approve_mail_body'] .= "\n\nAs a welcome gift, we have just given you a free 6 month premium subscription so that you may freely communicate with all of your upcoming matches!";
							$user_application_data[$key]['approve_mail_body'] .= "\n\n 恭喜您，您的DateTix会员申请审核已经正式通过！";
			
							$user_application_data[$key]['approve_mail_body'] .= "\n\n 我们已经在网站的大量高素质会员中按您的要求开始筛选合适的配对。在发现符合您要求的人选后，我们会马上和您取得联系。";
			
							$user_application_data[$key]['approve_mail_body'] .= "\n\n同时，请登陆DateTix.hk/signin 并进一步完善您的个人信息，资料越完善，我们为您找到更合适的配对的几率就越大哦！";
							$user_application_data[$key]['approve_mail_body'] .= "\n\n 大千世界，您和DateTix的相遇本身就是一种奇妙的缘分。现在，请和我们一起踏上在茫茫人海中邂逅真爱的旅程吧！";
			
							$user_application_data[$key]['approve_mail_body'] .= "\n\nMichael Ye";
							$user_application_data[$key]['approve_mail_body'] .= "\n创始人兼CEO";
							$user_application_data[$key]['approve_mail_body'] .= "\nmichael.ye@datetix.com";
						break;
					
					default:
							$user_application_data[$key]['approve_mail_body'] = "Congratulations ".$tmp_user['first_name'].", your DateTix application has just been approved!";
							//$user_application_data[$key]['approve_mail_body'] .= "\n\nAs a welcome gift, we have just given you a free 6 month premium subscription so that you may freely communicate with all of your upcoming matches!";
							$user_application_data[$key]['approve_mail_body'] .= "\n\nYou are now part of an exclusive community of single professionals.";
			
							$user_application_data[$key]['approve_mail_body'] .= "\n\nWe will now begin searching through our member database and email you whenever as we find high quality and relevant matches for you.";
			
							$user_application_data[$key]['approve_mail_body'] .= "\n\nAs you wait for your first match to arrive, please sign in to your account at http://www.DateTix.hk/signin and fill in more of your profile so that we can help you discover the most relevant matches for your profile!";
			
							$user_application_data[$key]['approve_mail_body'] .= "\n\nMichael Ye";
							$user_application_data[$key]['approve_mail_body'] .= "\nFounder and CEO";
							$user_application_data[$key]['approve_mail_body'] .= "\nmichael.ye@datetix.com";
							
						break;
				}

				//Get Photo data
				$this -> general -> set_table('user_photo');
				if ($primary_photo = $this -> general -> get("", array('set_primary' => '1', 'user_id' => $user_id))) {
					$user_application_data[$key]['primary_photo'] = base_url() . 'user_photos/user_' . $user_id. '/' . $primary_photo['0']['photo'];
				}
			}
		}
		return $user_application_data;
	}

	/**
	 * Update userdata: super admin panel
	 * @author Rajnish Savaliya
	 */
	public function go_profile($user_id = 0)
	{
		$url = $this->input->get('url');
		
		if($url !="" && $user_id)
		{
			//delete previous data
			//$this -> session -> unset_userdata('sess_city_id');
			//$this -> session -> unset_userdata('user_id');
			//$this->session->unset_userdata('sign_up_id');
			
			$this -> datetix -> destroy_current_session();
			
			$this->session->set_userdata('sign_up_id',$user_id);
			$this -> session -> set_userdata('user_id', $user_id);
			$this -> session -> set_userdata('ad_id', $user['ad_id']);
			
			if($url == "signin")
			{
				$this->model_user->is_current_signup_process($user_id);
			}
			else
			{
				redirect($url);
			}			
			//$this -> session -> set_userdata('sign_up_id', $user_id);
		}
	}
	
	/**
	 * Update userdata: super admin panel
	 * @author Rajnish Savaliya
	 */
	public function update_user()
	{
		$selectedTab = '';
		$is_updated = 0;
		if($postData = $this->input->post())
		{
			if($postData['user_id'])
			{
				if($postData['current_tab'] == 'manage_memberTAB')
				{
					$selectedTab = "#manage_member";
				}
				else
				{
					$selectedTab = "#review_app";
				}
				
				$user_membership_options = explode(',', $postData['membership_options']);
				$flag = 0;
				if ($user_membership_options ) {

					$this -> general -> set_table('user_membership_option');
					$user_membership_options_data['user_id'] = $postData['user_id'];
					
					if($user_membership_options_data)
							$this -> general -> delete($user_membership_options_data);
							
					foreach ($user_membership_options as $value) {
						
						$user_membership_options_data['membership_option_id'] = $value;
						//dynamic expiry date
						if (isset($postData['expiry_date_'.$value]))
							$user_membership_options_data['expiry_date'] = date('Y-m-d', strtotime($postData['expiry_date_'.$value]));
						
						if ($this -> general -> save($user_membership_options_data))
							$is_updated = 1;
					}
				}
				
				$user_data['account_status_id'] = $postData['account_status_id'];
				$user_data['attractiveness_level'] = $postData['attractiveness_level'];
				$user_data['num_date_tix'] = $postData['num_date_tix'];
				$this -> general -> set_table('user');

				$user_condition['user_id'] = $postData['user_id'];

				if($this -> general -> update($user_data, $user_condition))
				{
					$is_updated = 1;
				}
			}
		}
			
		if($is_updated)
		{
			$this -> session -> set_flashdata('success_msg', translate_phrase('User updated successfully!'));
		}
		else {
			$this -> session -> set_flashdata('error_msg', translate_phrase('Error occured, Please try again!'));
		}
		
		redirect('/admin'.$selectedTab);
	}
	
	/**
	 * [ajax call]Change Status of Application
	 * @author Rajnish Savaliya
	 */
	public function change_user_status($user_id = 0)
	{
		$response['type'] = 'error';
		$response['msg'] = translate_phrase('Please Try Again.');
		
		if($postData = $this->input->post())
		{
			$this -> load -> library('form_validation');
			$this -> form_validation -> set_rules('body', 'Email body', 'trim|required');
			if ($this -> form_validation -> run() == FALSE) {
					$response['msg'] = translate_phrase(validation_errors());
					$response['type'] = 'error';
			} 
			else 
			{
				if($this ->_change_account_status($user_id,$postData['status'],$postData['body']))
				{
					
					$response['flag'] = 'success';
					$response['msg'] = translate_phrase('Mail sent successfully.');
				}
			}
		}
		echo json_encode($response);
	}
	
	/**
	 * Change Status of user
	 * @author Rajnish Savaliya
	 */
	public function change_user_account_status($user_id = 3,$status=2)
	{
		$user_data = $this -> model_user -> get_user_data($user_id);
		$data['form_para'] = "/".$user_id."/".$status;
		if($postData = $this->input->post())
		{
			$this->_change_account_status($user_id,$postData['status']);
			redirect('/admin');
			
		}
		switch($status)
		{
			
			case '1': 
				$data['msg'] = translate_phrase("Please confirm you would like active ").$user_data['first_name'].translate_phrase("'s account");
				$data['page_title'] = translate_phrase('Confirm Account Active');
				$data['status'] = "approve";
				
				break;
			case '2': 
				$data['msg'] = translate_phrase("Please confirm you would like deactivate ").$user_data['first_name'].translate_phrase("'s account");
				$data['page_title'] = translate_phrase('Confirm Account Deactivate');
				$data['status'] = "suspend";
				
				break;
			case 3: 
				$data['page_title'] = translate_phrase('Confirm Account Close');
				$data['msg'] = translate_phrase("Please confirm you would like close ").$user_data['first_name'].translate_phrase("'s account");
				$data['status'] = "closed";
				break;
		}
		
		$data['extra'] = '';
		$data['page_name'] = 'admin/confirm_box';
		$this -> load -> view('template/admin', $data);		
	}
	/**
	 * Helper function to change account status and send mail to user with explict body text
	 * @author Rajnish Savaliya
	 */
	private function _change_account_status($user_id,$status,$body="")
	{
		$user_upadate_data = array();
		$user_data = $this -> model_user -> get_user_data($user_id);
		
		//Status is string while submission of application and 
		// Status numerica value while on url call
		
		switch($status)
		{
			case "approve": $user_upadate_data['approved_date'] = SQL_DATE;
							$subject = translate_phrase("Congratulations ").$user_data['first_name'].translate_phrase(", Your DateTix application has been approved");
			
							$this->_intro_with_michael($user_id);
							break;
				
			case "reject": 	$user_upadate_data['rejected_date'] = SQL_DATE;
							$subject = translate_phrase("Sorry, Your DateTix application has been declined");
							break;
			case "suspend": $user_upadate_data['suspend_date'] = SQL_DATE;
							$user_upadate_data['account_status_id'] = "2";
							$subject = translate_phrase("Sorry, Your Account has been suspended");
							break;
							
			case "closed": 	$user_upadate_data['closed_date'] = SQL_DATE;			
							$user_upadate_data['account_status_id'] = "3";
							$subject = translate_phrase("Sorry, Your Account has been closed");
							break;
			
		}
		
		if($user_data)
		{
			$this -> general -> set_table('user');
			if($this -> general -> update($user_upadate_data, array('user_id'=>$user_id)))
			{
				$user_link = $this -> utility -> encode($user_id);
				if ($user_data['password']) 
				{
					$user_link .= '/' .$user_data['password'];
				}
				
				$data['email_content'] = $body?$body:$subject;
				$data['btn_link'] = base_url() . 'home/mail_action/' . $user_link . '?return_to=' . base_url() . url_city_name() . '/edit-profile.html';
				$data['btn_text'] = translate_phrase("Edit Profile");				
				$data['email_title'] = '';
				$email_template = $this -> load -> view('email/common', $data, true);
				
				if($user_email_data = $this -> model_user -> get_user_email($user_id))
				{
					return $this -> model_user -> send_email(INFO_EMAIL,$user_email_data['email_address'], $subject, $body);
					//return $this -> model_user -> send_email(INFO_EMAIL,'mikeye27@gmail.com', $subject, $body);
				}
			}
		}
	}
	
	
	/**
	 * Simply send mail to user based on user id
	 * @author Rajnish Savaliya
	 */
	public function send_mail_to_user($user_id = 0)
	{
		$response['type'] = 'error';
		$response['msg'] = translate_phrase('Please Try Again.');
		
		if($postData = $this->input->post())
		{
			$this -> load -> library('form_validation');
			$this -> form_validation -> set_rules('subject', 'Email subject', 'trim|required');
			$this -> form_validation -> set_rules('body', 'Email body', 'trim|required');
			if ($this -> form_validation -> run() == FALSE) {
					$response['msg'] = translate_phrase(validation_errors());
					$response['type'] = 'error';
			} else {
				$body = $postData['body'];
				$subject = $postData['subject'];
				if($user_email_data = $this -> model_user -> get_user_email($user_id))
				{
					//if($this -> model_user -> send_email(CEO_EMAIL,$user_email_data['email_address'], $subject, $body))
					if($this -> model_user -> send_email(CEO_EMAIL,'mikeye27@gmail.com', $subject, $body))
					{
						$response['flag'] = 'success';
						$response['msg'] = translate_phrase('Mail sent successfully.');
					}
				}
			}
		}
		echo json_encode($response);
	}
	
	/**
	 * View user Facebook Data
	 */
	public function facebook_info($user_id = 3)
	{
		$this -> general -> set_table('user');
		if($facebook_data = $this -> general -> get("facebook_id",array('user_id'=>$user_id,'facebook_id !='=>'')))
		{
			try 
			{
				$data['fb_data'] = $this -> facebook -> api('/' . $facebook_data['0']['facebook_id']);
				//echo "<pre>";print_r($data['fb_data']);exit;
			} catch (FacebookApiException $e) {
				echo $e;
			}
		}
		$data['page_title'] = translate_phrase('Verification Information');
		$data['page_name'] = 'admin/view_facebook_data';
		$this -> load -> view('template/admin', $data);
	}
	
	/**
	 * Order History Of User
	 * @author Rajnish Savaliya
	 */
	public function order_history($user_id=3)
	{
		
		//Contact Method
		$fields = array('uo.*', 'c.description as currency_name');

		$from = 'user_order as uo';
		$joins = array('currency as c' => array('uo.order_currency_id = c.currency_id', 'inner'));

		$where['c.display_language_id'] = $this -> language_id;
		$where['uo.user_id'] = $user_id;
		
		//Ticket Order data
		$data['ticket_orders'] = array();
		$data['total_tickets'] = 0;
		$data['total_ticket_amount'] = 0;
		$data['all_ticket_currency'] = "";
		
		//Membership Option data
		$data['membership_orders'] = array();
		$data['total_membership_amount'] = 0;
		
		$data['all_membership_currency'] = "";
		
		if($user_orders = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'user_order_id asc'))
		{
			foreach($user_orders as $order)
			{
				if($order['order_num_date_tix'])
				{
					//Add ticket,amount and currency in total variable
					$data['total_tickets'] += $order['order_num_date_tix'];
					$data['total_ticket_amount'] += $order['order_amount'];
					
					$data['all_ticket_currency'][] = $order['currency_name'];
					$data['ticket_orders'][] = $order;
				}
				else
				{
					$data['total_membership_amount'] += $order['order_amount'];					
					$data['all_membership_currency'][] = $order['currency_name'];
					
					if($order['order_membership_options'])
					{
						$sql = "select GROUP_CONCAT(description) as membership_description  from membership_option where display_language_id = ".$this -> language_id." AND membership_option_id IN (".$order['order_membership_options'].")";
						if($membership_options = $this->general->sql_query($sql))
						{
							$order['membership_option_value'] = str_replace(",",", ",$membership_options['0']['membership_description']);
						}						
					}					
					$data['membership_orders'][] = $order;
				}
			}
			
			//Remove dublicate values and make csv format
			$data['all_ticket_currency'] = implode(", ",array_unique($data['all_ticket_currency']));
			$data['all_membership_currency'] = implode(", ",array_unique($data['all_membership_currency']));
			
		}
		
		$data['page_title'] = translate_phrase('Order History');
		$data['page_name'] = 'admin/view_order_history';
		$this -> load -> view('template/admin', $data);
	}
	
	/**
	 * Verification information
	 * @author Rajnish Savaliya
	 */
	public function verification_info($user_id = 3)
	{
		
		$this -> general -> set_table('user');
		if($data['user_data'] = $this -> general -> get("",array('user_id'=>$user_id)))
		{
			$data['user_data'] = $data['user_data']['0'];
			
			$this -> general -> set_table('how_you_heard_about_us');
			if($hear_about_us = $this -> general -> get("",array('display_language_id'=>$this->language_id,'how_you_heard_about_us_id'=>$data['user_data']['how_you_heard_about_us_id'])))
			{
				$data['user_data']['hear_about_us_description'] = $hear_about_us['0']['description'];
			}
			
			$this -> general -> set_table('user_photo');
			$data['user_data']['user_photos'] = $this -> general -> get("",array('user_id'=>$user_id),array('is_approved'=>'asc'));
			
			$current_country = $this -> model_user -> getCountryByCity($data['user_data']['current_city_id']);
			$data['user_data']['country_code'] = $current_country ? $current_country -> country_code : " ";
			
			//School data
			$fields = array('us.user_school_id','us.school_name as my_school_name','s.school_name','photo_diploma','us.school_id');
			$from = 'user_school as us';
			$joins = array('school as s' => array('us.school_id = s.school_id AND s.display_language_id = '.$this->language_id, 'LEFT'));
			$condition['us.user_id'] = $user_id;
			$condition['us.photo_diploma !='] = '';
			
			$data['school_data'] = $this -> model_user -> multijoins($fields, $from, $joins, $condition);
			unset($condition);
			
			//Company data
			$fields = array('uj.user_company_id','uj.company_name as my_company_name','c.company_name','photo_business_card','uj.company_id');
			$from = 'user_job as uj';
			$joins = array('company as c' => array('uj.company_id = c.company_id AND c.display_language_id = '.$this->language_id, 'LEFT'));
			$condition['uj.user_id'] = $user_id;
			$condition['uj.photo_business_card !='] = "";
			$data['company_data'] = $this -> model_user -> multijoins($fields, $from, $joins, $condition);
			//echo $this->db->last_query();
			//echo "<pre>";print_r($data['company_data']);exit;
		}
		$data['page_title'] = translate_phrase('Verification Information');
		$data['page_name'] = 'admin/varification_info';
		$this -> load -> view('template/admin', $data);
	}
	
	/**
	 * Change status : verify data
	 * @author Rajnish Savaliya
	 */
	public function verify_data($user_id)
	{	
		$reject_reason= array(
			array('reason_id'=>1,'description'=>"Photo too large"),
			array('reason_id'=>2,'description'=>"Contains nudity"),
			array('reason_id'=>3,'description'=>"Unclear photo"),
			array('reason_id'=>4,'description'=>"Other")
		);				
		
		if($postData = $this->input->post())
		{
			$this->session->set_userdata('statusData',$postData);
			if($postData['status'] == 1)
			{			
				//redirect to same function and save approve state..
				redirect('/admin/save_verify_data/'.$user_id);				
			}
			else
			{
				$data['form_para'] = "/".$user_id;
				$data['status'] = $postData['status'];
				$data['reject_reason'] = $reject_reason;
				$data['page_title'] = 'Rejection Reason';
				$data['page_name'] = 'admin/reject_reason';			
				$this -> load -> view('template/admin', $data);
			}
		}
		else
		{
			redirect('/admin/verification_info/'.$user_id);
		}		
	}
	
	public function save_verify_data($user_id)
	{
		
		$reject_reason= array(
			array('reason_id'=>1,'description'=>"Photo too large"),
			array('reason_id'=>2,'description'=>"Contains nudity"),
			array('reason_id'=>3,'description'=>"Unclear photo"),
			array('reason_id'=>4,'description'=>"Other")
		);
		
		
		if($postData = $this->session->userdata('statusData'))
		{
			$this->session->unset_userdata('statusData');
			
			//Verify data from user table
			if($postData['section'] == 'profile')
			{
				$user_upadate_data[$postData['field'].'_is_verified'] = $postData['status'];
				$this -> general -> set_table('user');
				if($this -> general -> update($user_upadate_data, array('user_id'=>$user_id)))
				{
					$response['type'] = 'success';
					$response['msg'] = translate_phrase('status changed');
				}
			}
			
			//Verify data from user table
			if($postData['section'] == 'photo')
			{
				$user_upadate_data[$postData['field']] = $postData['status'];
				$this -> general -> set_table('user_photo');
				if($this -> general -> update($user_upadate_data, array('user_photo_id'=>$postData['field_val'],'user_id'=>$user_id)))
				{
					$response['type'] = 'success';
					$response['msg'] = translate_phrase('status changed');
				}
			}
			
			
			$subject = translate_phrase("Your ").$postData['field_name'].translate_phrase(" has been ");
			$subject .= ($postData['status'] == 1)?translate_phrase('approved'):translate_phrase('rejected');
			$data['email_content'] = '';

			if($formData = $this->input->post())
			{
				
				$data['email_content'] = translate_phrase("Rejection reason :");
				if($formData['reject_reason_id'] == 4)
				{
					$data['email_content'] .= translate_phrase($formData['other_reason_txt']);
				}
				else
				{
					$selected_reason = search($reject_reason,'reason_id',$formData['reject_reason_id']);
					$data['email_content'] .= translate_phrase($selected_reason['0']['description']);				
				}
			}
		
			$data['email_title'] = $subject;
			
			$email_template = $this -> load -> view('email/common', $data, true);
			if($user_email_data = $this -> model_user -> get_user_email($user_id))
			{
				$this -> datetix -> mail_to_user($user_email_data['email_address'], $subject, $email_template);				
			}
		}
		redirect('/admin/verification_info/'.$user_id);		
	}
	
	/**
	 * Add static entry in user intro table: intro_with_michael
	 * @author Rajnish Savaliya
	 */
	private function _intro_with_michael($user_id=""){
		$user_data = $this -> model_user -> get_user_data($user_id);				
		$flag = "";
		if($user_id)
		{
			$this -> general -> set_table('user_intro');
			$intro_data['user1_id'] = '1';
			$intro_data['user2_id'] = $user_id;
			$intro_data['intro_created_time'] = SQL_DATETIME;
			$intro_data['intro_available_time'] = SQL_DATETIME;
			if ($user_data['gender_id'] == 1)
			{
				// Set intro email sent time for male users so that an intro email won't be sent to them (don't want to introduce Michael to guys)
				$intro_data['intro_email_sent_time'] = SQL_DATETIME;
			}
			$intro_data['intro_expiry_time'] = '2018-12-31';
			$this -> general -> save($intro_data);
			
			
			$this -> general -> set_table('user_membership_option');
			$membership_data['user_id'] = $user_id;
			$membership_data['membership_option_id'] = "1";
			$membership_data['expiry_date'] = "2015-12-31";			
			$this -> general -> save($membership_data);
		}
		return $flag;	
	}
	
	public function logout()
	{
		$this->session->sess_destroy();
		redirect('/');
	}
}
?>
