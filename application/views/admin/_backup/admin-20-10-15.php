<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Admin extends CI_Controller {
	var $language_id = '1';
	var $admin_id = '';
	public function __construct() {
		parent::__construct();
		error_reporting(E_ALL);
		$this -> load -> model('general_model', 'general');
		$this -> load -> library('datetix');

		$logged_in = $this -> session -> userdata('superadmin_logged_in');
		//$this -> session -> set_userdata('superadmin_logged_in',false);
		//echo '<pre>';var_dump($this->session->userdata('superadmin_logged_in'));die();
		if ($logged_in === FALSE) {

			/*

			 $admin_user_username1 = $this -> config -> item('superadmin_username1');
			 $admin_user_password1 = $this -> config -> item('superadmin_password1');
			 */

			//echo "<pre>";print_r($_SERVER);exit;
			if (!isset($_SERVER['PHP_AUTH_USER'])) {
				header('WWW-Authenticate: Basic realm="Datetix Super Admin Panel"');
				header('HTTP/1.0 401 Unauthorized');
				echo("Please enter a valid username and password");
				exit();
			}

			//echo '<pre>';print_r($this -> session ->all_userdata());die();
			$user = $this -> config -> item('superadmin_username');
			$password = $this -> config -> item('superadmin_password');
			if (($_SERVER['PHP_AUTH_USER'] == $user) && ($_SERVER['PHP_AUTH_PW'] == $password)) {
				$this -> session -> set_userdata(array('superadmin_logged_in' => TRUE));
				$this -> session -> set_userdata('LIMITED_ACCESS', FALSE);
			} else {
				
				$this -> general -> set_table('website');
				$CheckAdminCondition['login'] = $_SERVER['PHP_AUTH_USER'];
				$CheckAdminCondition['password'] = $_SERVER['PHP_AUTH_PW'];
				$CheckAdmin = $this -> general -> get('*', $CheckAdminCondition);
				
				if (!empty($CheckAdmin)) {
					//$this -> session -> set_userdata(array('superadmin_logged_in' => TRUE));
					$this -> session -> set_userdata('superadmin_logged_in', $CheckAdmin[0]);
					$this -> session -> set_userdata('LIMITED_ACCESS', array('review_application'));
				} else {
					header('WWW-Authenticate: Basic realm="Datetix Super Admin Panel"');
					header('HTTP/1.0 401 Unauthorized');
					echo("Please enter a valid username and password");
					exit();
				}
			}
		}
	}

	/**
	 * Admin Dashboard: super admin panel
	 * @author Rajnish Savaliya
	 */
	public function index() {
		$LoginData = $this -> session -> userdata('superadmin_logged_in');

		$data['is_review_resricted'] = false;
                if ($access_permission = $this -> session -> userdata('LIMITED_ACCESS')) {
			$allow_permission = "review_application";
			$data['is_review_resricted'] = in_array($allow_permission, $access_permission);

		}
		$data['selectedTab'] = 'review_appTAB';

		//Load user data
		if ($this -> input -> post('page_no')) {
			$page_no = $this -> input -> post('page_no');
		} else {
			$page_no = 1;
		}

		$data['page_no'] = $page_no;

		$limit = ' LIMIT ' . ($page_no - 1) * PER_PAGE_ADMIN . ', ' . PER_PAGE_ADMIN;

		$city_select = "select ct.city_id, ct.description from user 
                            JOIN city as ct on ct.city_id = user.current_city_id
                            GROUP BY current_city_id ORDER BY ct.description";
		$data['cities'] = $this -> general -> sql_query($city_select);

		$ChekCondition['website_id'] = $LoginData['website_id'];
		$data['is_match'] = $this -> general -> checkDuplicate($ChekCondition, 'user');

		$data['seleccted_city_id'] = 260;
		if ($city_id = $this -> input -> post('city_id')) {
			$data['seleccted_city_id'] = $city_id;
		}

		$country_select = "SELECT province.country_id FROM province 
                    JOIN city on city.province_id=province.province_id
		    WHERE city.city_id=".$data['seleccted_city_id'];
		$data['country'] = $this -> general -> sql_query($country_select);

		$user_application_sql = "SELECT city.description AS city_description, status.account_status_id, status.description, last_display_language_id,
					user_id,completed_application_step,last_active_time,facebook_id,website.name as website_name,gender_id,first_name,last_name,num_date_tix,attractiveness_level,facebook_page
					FROM user as usr
					JOIN website ON website.website_id=usr.website_id
					JOIN account_status as status on usr.account_status_id = status.account_status_id					
					JOIN city on usr.current_city_id = city.city_id
					WHERE 
						usr.approved_date IS NULL AND
						status.display_language_id = " . $this -> language_id . " AND 
						city.display_language_id = " . $this -> language_id . " AND";
		
		if ($this -> session -> userdata('LIMITED_ACCESS'))
			$user_application_sql .= " usr.website_id = " . $ChekCondition['website_id'] . " AND ";
		
		$user_application_sql .=  " city.city_id = " . $data['seleccted_city_id'] ."
					ORDER BY applied_date DESC" . $limit;
					
		$data['review_applications'] = $this -> _make_template_data($this -> general -> sql_query($user_application_sql));

		if (!$this -> session -> userdata('LIMITED_ACCESS'))
		{
			$user_application_sql = "SELECT city.description AS city_description, status.account_status_id, status.description, last_display_language_id,
						user_id,completed_application_step,last_active_time,facebook_id,website.name as website_name,gender_id,first_name,last_name,num_date_tix,attractiveness_level,facebook_page
						FROM user as usr
						JOIN website ON website.website_id=usr.website_id
						JOIN account_status as status on usr.account_status_id = status.account_status_id					
						JOIN city on usr.current_city_id = city.city_id
						WHERE 
							usr.approved_date IS NOT NULL AND 			
							status.display_language_id = " . $this -> language_id . " AND 
							city.display_language_id = " . $this -> language_id . " AND
							city.city_id = " . $data['seleccted_city_id'] . "
						ORDER BY applied_date DESC" . $limit;
                        
                        $user_total_record_application_sql = "SELECT city.description AS city_description, status.account_status_id, status.description, last_display_language_id,
						user_id,completed_application_step,last_active_time,facebook_id,website.name as website_name,gender_id,first_name,last_name,num_date_tix,attractiveness_level,facebook_page
						FROM user as usr
						JOIN website ON website.website_id=usr.website_id
						JOIN account_status as status on usr.account_status_id = status.account_status_id					
						JOIN city on usr.current_city_id = city.city_id
						WHERE 
							usr.approved_date IS NOT NULL AND 			
							status.display_language_id = " . $this -> language_id . " AND 
							city.display_language_id = " . $this -> language_id . " AND
							city.city_id = " . $data['seleccted_city_id'] . "
						ORDER BY applied_date DESC" ;
		}
		else
		{
			$user_application_sql = "SELECT city.description AS city_description, status.account_status_id, status.description, last_display_language_id,
						user_id,completed_application_step,last_active_time,facebook_id,website.name as website_name,gender_id,first_name,last_name,num_date_tix,attractiveness_level,facebook_page,
						website.name
						FROM user as usr
						LEFT JOIN website on usr.website_id = website.website_id
						JOIN account_status as status on usr.account_status_id = status.account_status_id					
						JOIN city on usr.current_city_id = city.city_id
						WHERE 
							usr.approved_date IS NOT NULL AND ";
                       
			
			if ($ChekCondition['website_id'] == 1)
			{
				$user_application_sql .= "(usr.website_id = " . $ChekCondition['website_id'] . " OR usr.website_id=0) AND ";
			}
			else
			{
				$user_application_sql .= "usr.website_id = " . $ChekCondition['website_id'] . " AND ";
			}
						
			$user_application_sql .= "status.display_language_id = " . $this -> language_id . " AND 
							city.display_language_id = " . $this -> language_id . " AND
							city.city_id = " . $data['seleccted_city_id'] . "
						ORDER BY applied_date DESC" . $limit;
                        
                       
		
		}
						
		//echo $user_application_sql;exit;

		$data['approved_applications'] = $this -> _make_template_data($this -> general -> sql_query($user_application_sql));
               
		$this -> general -> set_table('account_status');
		$account_condition['display_language_id'] = $this -> language_id;
		$data['account_data'] = $this -> general -> get("", $account_condition);

		$this -> general -> set_table('membership_option');
		$membership_condition['display_language_id'] = $this -> language_id;
		$data['membership_options'] = $this -> general -> get("", $membership_condition, array('view_order' => 'asc'));

		$data['year'] = array();
		$yearsTo = 99;
		$yearsFrom = 18;
		for ($i = $yearsFrom; $i <= $yearsTo; $i++) {
			$data['year'][$i] = $i;
		}
		$data['ethnicity'] = $this -> model_user -> get_ethnicity($this -> language_id);
		$data['gender'] = $this -> model_user -> get_gender($this -> language_id);
                
                $educationLevel = $this->model_user->get_education_level($this->language_id);
                $data['education_level'] = [];
                foreach ($educationLevel as $key => $value){
                   $data['education_level'][$value['education_level_id']] = $value['description'];
                }
                
                $data['income_level'] = $this->model_user->get_annual_income_range($data['country'][0]['country_id']);
                
		$data['page_title'] = translate_phrase('Member Management');
		$data['page_name'] = 'admin/dashboard';

		$data['marketPlaceData'] = $this -> marketPlace();
		$data['requestsData'] = $this -> requests();
		$data['requestsUsers'] = $this -> loadRequestsData();
                
                
                $data['consultationsData'] = $this -> consultations();
                $data['consultationsUsers'] = $this -> loadConsultationsData();

		$data['currentTab'] = $this -> input -> post('currentTab');
		$data['param'] = $this -> input -> post('param');
                
                /* Get members to be shown in handpick dropdown in Members tab*/
                $SQL = 'SELECT user_id,first_name,last_name FROM user WHERE num_date_tix > 0 ORDER BY first_name ASC,last_name ASC limit 20';
                $data['handpickedMembers'] = $this->general->rawQuery($SQL);
		//echo '<pre>';print_r($data);die();
                
		$this -> load -> view('template/admin', $data);
	}

	/**
	 * [Ajax-call] load_users Function :: Load Users application
	 * @access public
	 * @return View data
	 * @author Rajnish Savaliya
	 */
	public function load_users($type = 'review_app') {

		$data['is_review_resricted'] = false;
                if ($access_permission = $this -> session -> userdata('LIMITED_ACCESS')) {
			$allow_permission = "review_application";
			$data['is_review_resricted'] = in_array($allow_permission, $access_permission);

		}
		
		if ($this -> input -> is_ajax_request()) {
                        
			if ($type == 'requests') {
				$data = $this -> loadRequestsData();
				echo $this -> load -> view('admin/requests', $data, TRUE);
				die();
				//because we dont want to execute the code below this point.
			}
                        if ($type == 'manage_consultations') {
				$data = $this -> loadConsultationsData();
				echo $this -> load -> view('admin/consultations', $data, TRUE);
				die();
				//because we dont want to execute the code below this point.
			}
			
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
			} elseif ($sortby == 7) {
				$sort_order = 'ORDER BY `last_active_time` DESC';
			} elseif ($sortby == 8) {
				$sort_order = 'ORDER BY `last_active_time` DESC';
			}

			if ($type == "manage_member") {
                                /* Get members to be shown in handpick dropdown in Members tab*/
                                $SQL = 'SELECT user_id,first_name,last_name FROM user WHERE num_date_tix > 0 ORDER BY first_name ASC,last_name ASC limit 20';
                                $data['handpickedMembers'] = $this->general->rawQuery($SQL);
				if ($sortby == 9) {
					$sort_order = 'ORDER BY `order_time` ASC';
				} elseif ($sortby == 10) {
					$sort_order = 'ORDER BY `order_time` DESC';
				} elseif ($sortby == 11) {
					$sort_order = 'ORDER BY `order_amount` ASC';
				} elseif ($sortby == 12) {
					$sort_order = 'ORDER BY `order_amount` DESC';
				} elseif ($sortby == 13) {
					$sort_order = 'ORDER BY msg_count DESC';
				}
			}

			if ($type == 'marketplace') {
				if ($sortby == 9) {
					$sort_order = 'ORDER BY `height` DESC';
				} elseif ($sortby == 10) {
					$sort_order = 'ORDER BY `height` ASC';
				}
			}

			//Load user data
			if ($this -> input -> post('page_no')) {
				$page_no = $this -> input -> post('page_no');
			} else {
				$page_no = 1;
			}

			$limit = ' LIMIT ' . ($page_no - 1) * PER_PAGE_ADMIN . ', ' . PER_PAGE_ADMIN;

			$user_application_sql = "SELECT city.description as city_description, status.account_status_id, status.description, last_display_language_id,
						usr.user_id,completed_application_step,last_active_time,facebook_id,website.name as website_name,usr.gender_id,first_name,last_name,num_date_tix,attractiveness_level,facebook_page,usr.credits_value,
						CASE 
							WHEN
								birth_date != '0000-00-00'
								THEN 
									TIMESTAMPDIFF(DAY,`birth_date`,CURDATE())
							END as age";

			if ($sortby == 13 && $type == "manage_member")
				$user_application_sql .= " ,COUNT(user_intro_chat.user_intro_chat_id) as msg_count";

			if ($type == 'marketplace' && $this -> session -> userdata('LIMITED_ACCESS'))
				$user_application_sql .= ", website.name";

			$user_application_sql .= " FROM user as usr
				       LEFT JOIN website ON website.website_id=usr.website_id
                                       JOIN account_status as status on usr.account_status_id = status.account_status_id
                                       JOIN city on usr.current_city_id = city.city_id";

			//if ($type == 'marketplace' && $this -> session -> userdata('LIMITED_ACCESS'))
			//	$user_application_sql .= " JOIN website on usr.website_id = website.website_id";

			if ($sortby >= 9 && $sortby <= 12 && $type == "manage_member")
				$user_application_sql .= " JOIN user_order as usr_ord on usr.user_id = usr_ord.user_id";
			if ($sortby == 13 && $type == "manage_member")
				$user_application_sql .= " JOIN user_intro_chat on usr.user_id = user_intro_chat.user_id";

                        if($type == 'manage_member'){
                            if($this->input->post('education_level')){
                                $user_application_sql .= ' JOIN user_education_level on usr.user_id = user_education_level.user_id';
                            }                                                        
                        }
                        
                        
			$user_application_sql .= " WHERE status.display_language_id = " . $this -> language_id . " AND city.display_language_id = " . $this -> language_id;

			if ($type == 'review_app') {
				$user_application_sql .= " AND usr.approved_date IS NULL";
			} else {
				$user_application_sql .= " AND usr.approved_date IS NOT NULL";
			}

			if ($like_val = $this -> input -> post('search_txt')) {

				$user_application_sql .= " AND (`first_name` LIKE '%" . $like_val . "%' OR `last_name` LIKE '%" . $like_val . "%')";
			}
			
			$city_condition = "";
                        
			if ($city_id = $this -> input -> post('city_id')) {
				$city_condition = " AND `current_city_id` = " . $city_id . "";
			}
			
			$is_find_match = $this->input->post('is_find_match');
			$find_match_of_user_id = $this->input->post('user_id');
			$find_match_of_userdata = array();
			//Common Filters
			if ($type == "manage_member" || $type == 'marketplace') {
					
				if($is_find_match && $find_match_of_user_id)
				{
					$find_match_of_userdata = $this -> model_user -> get_user_data($find_match_of_user_id);
					if($city_condition == "" && $find_match_of_userdata['current_city_id'])
					{
						$city_condition = " AND `current_city_id` = " . $find_match_of_userdata['current_city_id'] . "";
					}
					$user_application_sql .= " AND `user_id` != '" . $find_match_of_user_id . "'";					
				}
				
				if ($val = $this -> input -> post('ethnicity')) {
					$user_application_sql .= " AND `ethnicity_id` = '" . $val . "'";
				}
				else if(isset($find_match_of_userdata['user_want_ethnicity']) && $find_match_of_userdata['user_want_ethnicity'])
				{
					$user_application_sql .= " AND `ethnicity_id` = '" . $find_match_of_userdata['user_want_ethnicity'] . "'";
				}
				
				if ($val = $this -> input -> post('gender_id')) {
					$user_application_sql .= " AND `gender_id` = '" . $val . "'";
				}
				else if(isset($find_match_of_userdata['user_want_gender']) && $find_match_of_userdata['user_want_gender'])
				{
					$user_application_sql .= " AND `gender_id` = '" . $find_match_of_userdata['user_want_gender'] . "'";
				}
				
				if ($val = $this -> input -> post('age_lower')) {
					$user_application_sql .= " AND TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE()) >= '" . $val . "'";
				}
				else if(isset($find_match_of_userdata['want_age_range_lower']) && $find_match_of_userdata['want_age_range_lower'])
				{
					$user_application_sql .= " AND TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE()) >= '" . $find_match_of_userdata['want_age_range_lower'] . "'";
				}
				
				if ($val = $this -> input -> post('age_upper')) {
					$user_application_sql .= " AND TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE()) <= '" . $val . "'";
				}
				else if(isset($find_match_of_userdata['want_age_range_upper']) && $find_match_of_userdata['want_age_range_upper'])
				{
					$user_application_sql .= " AND TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE()) >= '" . $find_match_of_userdata['want_age_range_upper'] . "'";
				}

				if ($val = $this -> input -> post('height_from')) {
					$user_application_sql .= " AND `height` >= '" . $val . "'";
				}
				else if(isset($find_match_of_userdata['want_height_range_lower']) && $find_match_of_userdata['want_height_range_lower'])
				{
					$user_application_sql .= " AND `height` >= '" . $find_match_of_userdata['want_height_range_lower'] . "'";
				}
				
				if ($val = $this -> input -> post('height_to')) {
					$user_application_sql .= " AND `height` <= '" . $val . "'";
				}
				else if(isset($find_match_of_userdata['want_height_range_upper']) && $find_match_of_userdata['want_height_range_upper'])
				{
					$user_application_sql .= " AND `height` <= '" . $find_match_of_userdata['want_height_range_upper'] . "'";
				}
				
                                
				$superAdminData = $this -> session -> userdata('superadmin_logged_in');
				$websiteId = $superAdminData['website_id'];
	
				//Add tab specific filtering
				if ($type == "manage_member") { 
					if ($like_val = $this -> input -> post('first_name')) {
						$user_application_sql .= " AND `first_name` LIKE '%" . $like_val . "%'";
					}
					if ($like_val = $this -> input -> post('last_name')) {
						$user_application_sql .= " AND `last_name` LIKE '%" . $like_val . "%'";
					}	
                                        
                                        if($val = $this->input->post('education_level')){
                                                $user_application_sql .= ' AND `education_level_id` IN ('.$val.')';
                                        }

                                        if($val = $this->input->post('income_level')){
                                                $user_application_sql .= ' AND `annual_income_range_id` = '.$val;    
                                        }
                                        
                                        if($this->input->post('consultation_time')=='yes'){
                                                $user_application_sql .= ' AND consultation_time IS NOT NULL';    
                                        }
                                        if($this->input->post('consultation_time')=='no'){
                                                $user_application_sql .= ' AND consultation_time IS NULL';    
                                        }
                                       
					if ($this -> session -> userdata('LIMITED_ACCESS'))
					{
						if ($websiteId == 1)
						{
							$user_application_sql .= " AND (usr.`website_id` = " . $websiteId . " OR usr.`website_id`=0) ";
						}
						else
						{
							$user_application_sql .= " AND usr.website_id =" . $websiteId . " AND usr.website_id > 0";
						}
					}
                                         
                                        
				}
				else    if ($type == 'marketplace' && $this -> session -> userdata('LIMITED_ACCESS'))
				{
					$user_application_sql .= " AND usr.website_id !=" . $websiteId . " AND usr.website_id > 0";
					if(isset($find_match_of_userdata['website_id']) && $find_match_of_userdata['website_id'])
					{
						$user_application_sql .= " AND usr.website_id != '" . $find_match_of_userdata['website_id'] . "'";
					}
				}				
			}
			
			// Added city condtion.
			$user_application_sql .= $city_condition;
			
			
                        $userfinalQuery=$user_application_sql;
                        
                        $user_application_sql .= " GROUP BY usr.user_id " . $sort_order ;
                        $usersTotalRecordsRes = $this -> general -> sql_query($user_application_sql);
			$userstotalRecord = $this -> _make_template_data($usersTotalRecordsRes, $type);
                        $data['totalRecords']=$userstotalRecord;
                        
                        
                        $userfinalQuery .= " GROUP BY usr.user_id " . $sort_order . " " . $limit;
                        //$this -> general -> sql_query($user_application_sql);
			//echo '<pre>';print_r($this->db->last_query());die();
			//echo '<pre>'.$user_application_sql;exit;
			$usersRes = $this -> general -> sql_query($userfinalQuery);
            //echo $this->db->last_query();
			$users = $this -> _make_template_data($usersRes, $type);

			//echo '<pre>';print_r($user_application_sql);die();

			$this -> general -> set_table('account_status');
			$account_condition['display_language_id'] = $this -> language_id;
			$data['account_data'] = $this -> general -> get("", $account_condition);

			$this -> general -> set_table('membership_option');
			$membership_condition['display_language_id'] = $this -> language_id;
			$data['membership_options'] = $this -> general -> get("", $membership_condition, array('view_order' => 'asc'));

                       
			if ($type == 'review_app') {
				$data['selectedTab'] = 'review_appTAB';
				$data['review_applications'] = $users;
				$this -> load -> view('admin/review_applications', $data);
			}
			if ($type == 'marketplace') {

				/** Get members of current website **/
				$superAdminData = $this -> session -> userdata('superadmin_logged_in');
                                
				$websiteId = $superAdminData['website_id'];
				$SQL = 'SELECT user_id,first_name,last_name,gender_id FROM user WHERE website_id = "' . $websiteId . '" ORDER BY first_name ASC,last_name ASC';
				$thisWebsiteUsers = $this -> general -> sql_query($SQL);
				$data['thisWebsiteUsers'] = $thisWebsiteUsers;

				$data['selectedTab'] = 'marketplaceTAB_';
				$data['users'] = $users;
				//echo '<pre>';print_r($data);die();
				$this -> load -> view('admin/marketplace', $data);
			}else {
				$data['selectedTab'] = 'manage_memberTAB';
				$data['approved_applications'] = $users;
                                $this -> load -> view('admin/load_members', $data);
			}
		}
	}

	/**
	 * Formate data: super admin panel
	 * @author Rajnish Savaliya
	 */
	private function _make_template_data($user_application_data, $type = '') {
		if ($user_application_data) {
			foreach ($user_application_data as $key => $tmp_user) {
				$user_lang_id = $tmp_user['last_display_language_id'];

				$user_id = $tmp_user['user_id'];

				if ($type != 'marketplace') {//do the following stuff only if tab is other than marketplace
					//Get Membership data
					$this -> general -> set_table('user_membership_option');
					$user_application_data[$key]['user_membership_option'] = $this -> general -> get("membership_option_id,expiry_date", array('user_id' => $user_id));

					$user_application_data[$key]['thanks_mail_body'] = "Hi " . $tmp_user['first_name'] . ",";
					$user_application_data[$key]['thanks_mail_body'] .= "\n\nThanks for applying Datetix! I'm currently reviewing your profile and would like to know how you heard about Datetix?";

					$user_application_data[$key]['thanks_mail_body'] .= "\n\nMichael Ye";
					$user_application_data[$key]['thanks_mail_body'] .= "\nFounder and CEO";
					$user_application_data[$key]['thanks_mail_body'] .= "\nmichael.ye@datetix.com";

					switch ($user_lang_id) {
						case '2' :
							$user_application_data[$key]['approve_mail_body'] = $tmp_user['first_name'] . ", 您好！";
							$user_application_data[$key]['approve_mail_body'] .= "\n\n 恭喜您，您的DateTix會員申請審核已經正式通過！";

							$user_application_data[$key]['approve_mail_body'] .= "\n\n 我們已經在網站的大量高素質會員中按您的要求開始篩選合適的配對。在發現符合您要求的人選后，我們會馬上和您取得聯系。";

							$user_application_data[$key]['approve_mail_body'] .= "\n\n同時，請登陸DateTix.hk/signin 並進一步完善您的個人信息，資料越完善，我們為您找到更合適的配對的幾率就越大哦！";
							$user_application_data[$key]['approve_mail_body'] .= "\n\n 大千世界，您和DateTix的相遇本身就是一種奇妙的緣分。現在，請和我們一起踏上在茫茫人海中邂逅真愛的旅程吧！";

							$user_application_data[$key]['approve_mail_body'] .= "\n\nMichael";
							break;

						case '3' :
							$user_application_data[$key]['approve_mail_body'] = $tmp_user['first_name'] . ", 您好！";
							$user_application_data[$key]['approve_mail_body'] .= "\n\n 恭喜您，您的DateTix会员申请审核已经正式通过！";

							$user_application_data[$key]['approve_mail_body'] .= "\n\n 我们已经在网站的大量高素质会员中按您的要求开始筛选合适的配对。在发现符合您要求的人选后，我们会马上和您取得联系。";

							$user_application_data[$key]['approve_mail_body'] .= "\n\n同时，请登陆DateTix.hk/signin 并进一步完善您的个人信息，资料越完善，我们为您找到更合适的配对的几率就越大哦！";
							$user_application_data[$key]['approve_mail_body'] .= "\n\n 大千世界，您和DateTix的相遇本身就是一种奇妙的缘分。现在，请和我们一起踏上在茫茫人海中邂逅真爱的旅程吧！";

							$user_application_data[$key]['approve_mail_body'] .= "\n\nMichael";
							break;

						default :
							$user_application_data[$key]['approve_mail_body'] = "Congratulations " . $tmp_user['first_name'] . ", your DateTix application has just been approved!";
							//$user_application_data[$key]['approve_mail_body'] .= "\n\nAs a welcome gift, we have just given you a free 6 month premium subscription so that you may freely communicate with all of your upcoming matches!";
							$user_application_data[$key]['approve_mail_body'] .= "\n\nYou are now part of an exclusive community of single professionals.";

							$user_application_data[$key]['approve_mail_body'] .= "\n\nWe will now begin searching through our member database and email you whenever as we find high quality and relevant matches for you.";

							$user_application_data[$key]['approve_mail_body'] .= "\n\nAs you wait for your first match to arrive, please sign in to your account at http://www.DateTix.hk/signin and fill in more of your profile so that we can help you discover the most relevant matches for your profile!";

							$user_application_data[$key]['approve_mail_body'] .= "\n\nMichael";
							//$user_application_data[$key]['approve_mail_body'] .= "\n\nMichael Ye";
							//$user_application_data[$key]['approve_mail_body'] .= "\nFounder and CEO";
							//$user_application_data[$key]['approve_mail_body'] .= "\nmichael.ye@datetix.com";

							break;
					}
				}

				//Get Photo data
				$this -> general -> set_table('user_photo');
				if ($primary_photo = $this -> general -> get("", array('set_primary' => '1', 'user_id' => $user_id))) {
					$user_application_data[$key]['primary_photo'] = base_url() . 'user_photos/user_' . $user_id . '/' . $primary_photo['0']['photo'];
				}
			}
		}
		return $user_application_data;
	}

	/** Called from index() method to get data required for marketplace tab. Just to separate code
	 * and make it manageable.
	 * @author :: Hannan Munshi
	 **/
	private function marketPlace() {
		$data = array();
		$data['ageRange'] = array();
		$yearsTo = 99;
		$yearsFrom = 18;
		for ($i = $yearsFrom; $i <= $yearsTo; $i++) {
			$data['ageRange'][$i] = $i;
		}

		$data['heightRange'] = [];
		$from = 146;
		$to = 335;
		for ($i = $from; $i <= $to; $i++) {
			$data['heightRange'][$i] = $i;
		}
		return $data;
	}

	/**
	 * Called when user clicks on Request Date button from marketplace tab
	 * Inserts data in website_request_b2b
	 * @author :: Hannan Munshi
	 * **/
	public function process_date_request() {
		$post = $this -> input -> post();
		$selectedTab = '';
		if (!empty($post)) {
			if ($this -> input -> post('current_tab')) {
				$selectedTab = '#' . $this -> input -> post('current_tab');
			}

			$this -> general -> set_table('user');
			$introFee = $this -> general -> get('credits_value', array('user_id' => $this -> input -> post('requested_match_user_id')));
			if (empty($introFee)) {//this also means that user does not exist so redirect
				redirect('/admin' . $selectedTab);
			}
			$introFee = $introFee[0]['credits_value'];
                        
			$dataToInsert = array('requested_user_id' => $this -> input -> post('m_user_id'), //id of user from dropdown
			'requested_match_user_id' => $this -> input -> post('requested_match_user_id'), 'bid_amount' => $introFee, 'request_time' => date('Y-m-d H:i:s', time()), 'status' => '');

			$this -> general -> set_table('website_request_b2b');
			$lastb2bID = $this -> general -> save($dataToInsert);
			if ($lastb2bID) {
				$this -> session -> set_flashdata('msg', translate_phrase('Your date request has been submitted. We will notify you if they accept your request'));
				$selectedTab = '#requests';
			}
		}
		redirect('/admin' . $selectedTab);
	}

        
	private function requests() {
		$data = array();
		$data['statuses'] = array(translate_phrase('All'), translate_phrase('Received'), translate_phrase('Sent'), translate_phrase('Accepted'), translate_phrase('Declined'), translate_phrase('Cancelled'), );

		return $data;
	}
        
        private function consultations() {
		$data = array();
		$data['statuses'] = array(
                                        translate_phrase('Pending'),
                                        translate_phrase('Accepted'),
                                        translate_phrase('Declined'),
                                        );

		return $data;
	}

	/** Called from load_users action when current tab is requests tab**/
	private function loadRequestsData() {
		//echo '<pre>';print_r($_POST);die();
		$data = array();
		$websiteId = 0;
		$superAdminData = $this -> session -> userdata('superadmin_logged_in');
		
		//Get all the users of this website and index that array
		$SQL = 'SELECT user_id FROM user ';
		if($superAdminData && isset($superAdminData['website_id']))
		{
			$websiteId = $superAdminData['website_id'];
			$SQL .= ' WHERE website_id =' . $websiteId;
		}
		
		$res = $this -> general -> sql_query($SQL);
		$thisWebsiteUsers = array();
		foreach ($res as $key => $value) {
			$thisWebsiteUsers[$value['user_id']] = $value['user_id'];
		}

		$SQL = 'SELECT b2b.*, sent_user.credits_value, sent_user.first_name as sent_user,sent_website.name as sent_website_name,
                        received_user.first_name as received_user,received_website.name as received_website_name,
                        sent_user.matchmaker_id as sent_matchmaker_id,
                        received_user.matchmaker_id as received_matchmaker_id
               
                    FROM `website_request_b2b` as b2b
                    
                    JOIN user as sent_user ON sent_user.user_id = b2b.requested_match_user_id
                    JOIN user as received_user ON received_user.user_id = b2b.requested_user_id
		    JOIN website as sent_website on sent_user.website_id = sent_website.website_id
		    JOIN website as received_website on received_user.website_id = received_website.website_id';
		    
	      if($websiteId != 0)
		  {
		  	         $SQL .= ' WHERE (received_user.website_id =' . $websiteId . ' 
	                          OR sent_user.website_id =' . $websiteId . ')';
			
		  }              
        
		/* Filter by Status */
		if ($statusID = $this -> input -> post('status_id')) {
			switch($statusID) {
				case 1 : {//Received
					//Ned to modify whole SQL
					$SQL = 'SELECT b2b.*, sent_user.credits_value, sent_user.first_name as sent_user,sent_website.name as sent_website_name,
						received_user.first_name as received_user,received_website.name as received_website_name,
                                                sent_user.matchmaker_id as sent_matchmaker_id,
                                                received_user.matchmaker_id as received_matchmaker_id
               
                              FROM `website_request_b2b` as b2b

                              JOIN user as sent_user ON sent_user.user_id = b2b.requested_match_user_id
                              JOIN user as received_user ON received_user.user_id = b2b.requested_user_id
			      JOIN website as sent_website on sent_user.website_id = sent_website.website_id
			      JOIN website as received_website on received_user.website_id = received_website.website_id';

			if($websiteId != 0)
		  			{
                        $SQL .= '  WHERE (sent_user.website_id =' . $websiteId . ')';
                    }
					break;
				}
				case 2 : {//Sent
					//Ned to modify whole SQL
					$SQL = 'SELECT b2b.*, sent_user.credits_value, sent_user.first_name as sent_user,sent_website.name as sent_website_name,
						received_user.first_name as received_user,received_website.name as received_website_name,
                                                sent_user.matchmaker_id as sent_matchmaker_id,
                                                received_user.matchmaker_id as received_matchmaker_id
               
                              FROM `website_request_b2b` as b2b

                              JOIN user as sent_user ON sent_user.user_id = b2b.requested_match_user_id
                              JOIN user as received_user ON received_user.user_id = b2b.requested_user_id
	  		      JOIN website as sent_website on sent_user.website_id = sent_website.website_id
			      JOIN website as received_website on received_user.website_id = received_website.website_id';

                    if($websiteId != 0)
		  			{
                    	$SQL .= ' WHERE (received_user.website_id =' . $websiteId . ')';
                    }
					break;
				}
				case 3 : {//Accepted
					$SQL .= " AND (b2b.status = 'accepted')";
					break;
				}
				case 4 : {//Declined
					$SQL .= " AND (b2b.status = 'declined')";
					break;
				}
				case 5 : {//Cancelled
					$SQL .= " AND (b2b.status = 'cancelled')";
					break;
				}
			}
		}

		/* LIMIT part */
		$page_no = 1;
		if ($this -> input -> post('page_no')) {
			$page_no = $this -> input -> post('page_no');
		}

		$SQL .= ' order by b2b.request_time DESC LIMIT ' . ($page_no - 1) * PER_PAGE_ADMIN . ', ' . PER_PAGE_ADMIN;

		$res = $this -> general -> sql_query($SQL);
		//die($this->db->last_query());
		$b2bData = array();
		$this -> general -> set_table('user_photo');
		foreach ($res as $key => $value) {
			$value['type'] = 'received';
			if (array_key_exists($value['requested_user_id'], $thisWebsiteUsers) === TRUE) {
				$value['type'] = 'sent';
			}

			$SQL = 'SELECT * FROM user_photo WHERE set_primary=1 AND (user_id = ? OR user_id = ?)';
			$primary_photos = $this -> general -> rawQuery($SQL, array($value['requested_user_id'], $value['requested_match_user_id']));
                        //echo $this->db->last_query();
                        $value['sent_user_photo']='';
                        $value['received_user_photo']='';
			foreach ($primary_photos as $ke => $val) {

                                if ($value['requested_match_user_id'] == $val['user_id']) {
					$value['sent_user_photo'] = base_url() . 'user_photos/user_' . $val['user_id'] . '/' . $val['photo'];
				}
				if ($value['requested_user_id'] == $val['user_id']) {
					$value['received_user_photo'] = base_url() . 'user_photos/user_' . $val['user_id'] . '/' . $val['photo'];
				} 
			}
			$b2bData[$value['website_request_b2b_id']] = $value;

		}
		$data['b2b'] = $b2bData;
		return $data;
	}

	/** Responds to XHR request made by clicking any request action buttons in requests tab **/
	public function processRequestsAction() {
		$response = array();
		$action = $this -> input -> post('action');
		$b2bID = $this -> input -> post('b2bID');

		$dbStatuses = array('accept' => 'accepted', 'decline' => 'declined', 'cancel' => 'cancelled');

		$possibleActions = array('accept' => translate_phrase('You have accepted this bid'), 'decline' => translate_phrase('You have declined this bid'), 'cancel' => translate_phrase('Your bid has been cancelled'));

		if (!empty($b2bID) && array_key_exists($action, $possibleActions) !== FALSE) {//data is what we expected
			$dataToUpdate = array('status' => $dbStatuses[$action]);
			$this -> general -> set_table('website_request_b2b');
			$updateStastus = $this -> general -> update($dataToUpdate, array('website_request_b2b_id' => $b2bID));
			$updateStastus = TRUE;
			if ($updateStastus) {
                            
                           //Added By Jigar Oza
                            if($action=='accept'){                                
                                    // get details
                                    $totalCredit=$this -> input -> post('credits');
                                    $sentMatchMakerID=$this -> input -> post('sentMatchMakerID');
                                    $receivedMatchMakerID=$this -> input -> post('receivedMatchMakerID');
                                    
                                    // credit to matchmaker a account
                                    $this->db->query("update matchmaker set credits=credits+$totalCredit where matchmaker_id='".$receivedMatchMakerID."'");
                                    // dedcut from matchmaker b account
                                    $this->db->query("update matchmaker set credits=credits-$totalCredit where matchmaker_id='".$sentMatchMakerID."'");
                                    
                                
                            }
                                
				$response['actionStatus'] = 'ok';
				$response['msg'] = $possibleActions[$action];
			} else {
				$response['actionStatus'] = 'warning';
				$response['msg'] = translate_phrase('Not able to process the aciton');
			}
		} else {
			$response['actionStatus'] = 'warning';
			$response['msg'] = translate_phrase('Not able to process the aciton');
		}
		die(json_encode($response));
	}

	/**
	 * Update userdata: super admin panel
	 * @author Rajnish Savaliya
	 */
	public function go_profile($user_id = 0) {
		$url = $this -> input -> get('url');
        if ($url != "" && $user_id)
	{
        		
        	
			//delete previous data
			//$this -> session -> unset_userdata('sess_city_id');
			//$this -> session -> unset_userdata('user_id');
			//$this->session->unset_userdata('sign_up_id');
			
			$limitedAccess = $this->session->userdata('LIMITED_ACCESS');  
			
            if($limitedAccess === FALSE){
               //$this -> datetix -> destroy_current_session();
				
               $this -> session -> set_userdata('sign_up_id', $user_id);
               $this -> session -> set_userdata('user_id', $user_id);
			   
               //$this -> session -> set_userdata('ad_id', $user['ad_id']); 
            }
			//echo "<pre>";print_r($this->session->all_userdata());exit;
			
		if ($url == "signin"){
			$this -> model_user -> is_current_signup_process($user_id);
		}else{
			//echo $url;exit;
	        redirect($url);
		}
		//$this -> session -> set_userdata('sign_up_id', $user_id);
        }
	else
	{
            redirect(base_url('admin'));
        }
	}

	/**
	 * Update userdata: super admin panel
	 * @author Rajnish Savaliya
	 */
	public function update_user() {
		$selectedTab = '';
		$is_updated = 0;
		if ($postData = $this -> input -> post()) {
			if ($postData['user_id']) {
				if ($postData['current_tab'] == 'manage_memberTAB') {
					$selectedTab = "#manage_member";
				} else {
					$selectedTab = "#review_app";
				}

				$user_membership_options = explode(',', $postData['membership_options']);
				$flag = 0;
				if ($user_membership_options) {

					$this -> general -> set_table('user_membership_option');
					$user_membership_options_data['user_id'] = $postData['user_id'];

					if ($user_membership_options_data)
						$this -> general -> delete($user_membership_options_data);

					foreach ($user_membership_options as $value) {

						$user_membership_options_data['membership_option_id'] = $value;
						//dynamic expiry date
						if (isset($postData['expiry_date_' . $value]))
							$user_membership_options_data['expiry_date'] = date('Y-m-d', strtotime($postData['expiry_date_' . $value]));

						if ($this -> general -> save($user_membership_options_data))
							$is_updated = 1;
					}
				}

				$user_data['account_status_id'] = $postData['account_status_id'];
				$user_data['attractiveness_level'] = $postData['attractiveness_level'];
				$user_data['num_date_tix'] = $postData['num_date_tix'];
				$this -> general -> set_table('user');

				$user_condition['user_id'] = $postData['user_id'];

				if ($this -> general -> update($user_data, $user_condition)) {
					$is_updated = 1;
				}
			}
		}

		if ($is_updated) {
			$this -> session -> set_flashdata('success_msg', translate_phrase('User updated successfully!'));
		} else {
			$this -> session -> set_flashdata('error_msg', translate_phrase('Error occured, Please try again!'));
		}

		redirect('/admin' . $selectedTab);
	}

	/**
	 * [ajax call]Change Status of Application
	 * @author Rajnish Savaliya
	 */
	public function change_user_status($user_id = 0) {
		$response['type'] = 'error';
		$response['msg'] = translate_phrase('Please Try Again.');

		if ($postData = $this -> input -> post()) {
			$this -> load -> library('form_validation');
			$this -> form_validation -> set_rules('body', 'Email body', 'trim|required');
			if ($this -> form_validation -> run() == FALSE) {
				$response['msg'] = translate_phrase(validation_errors());
				$response['type'] = 'error';
			} else {
				if ($this -> _change_account_status($user_id, $postData['status'], $postData['body'])) {

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
	public function change_user_account_status($user_id = 3, $status = 2) {
		$user_data = $this -> model_user -> get_user_data($user_id);
		$data['form_para'] = "/" . $user_id . "/" . $status;
		if ($postData = $this -> input -> post()) {
			$this -> _change_account_status($user_id, $postData['status']);
			redirect('/admin');

		}
		switch($status) {

			case '1' :
				$data['msg'] = translate_phrase("Please confirm you would like active ") . $user_data['first_name'] . translate_phrase("'s account");
				$data['page_title'] = translate_phrase('Confirm Account Active');
				$data['status'] = "approve";

				break;
			case '2' :
				$data['msg'] = translate_phrase("Please confirm you would like deactivate ") . $user_data['first_name'] . translate_phrase("'s account");
				$data['page_title'] = translate_phrase('Confirm Account Deactivate');
				$data['status'] = "suspend";

				break;
			case 3 :
				$data['page_title'] = translate_phrase('Confirm Account Close');
				$data['msg'] = translate_phrase("Please confirm you would like close ") . $user_data['first_name'] . translate_phrase("'s account");
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
	private function _change_account_status($user_id, $status, $body = "") {
		$user_upadate_data = array();
		$user_data = $this -> model_user -> get_user_data($user_id);

		//Status is string while submission of application and
		// Status numerica value while on url call

		switch($status) {
			case "approve" :
				$user_upadate_data['approved_date'] = SQL_DATETIME;
				$subject = translate_phrase("Congratulations ") . $user_data['first_name'] . translate_phrase(", Your DateTix application has been approved");

				$this -> _intro_with_michael($user_id);
				$this -> _send_welcome_notes($user_id);
				break;

			case "reject" :
				$user_upadate_data['rejected_date'] = SQL_DATE;
				$subject = translate_phrase("Sorry, Your DateTix application has been declined");
				break;
			case "suspend" :
				$user_upadate_data['suspend_date'] = SQL_DATE;
				$user_upadate_data['account_status_id'] = "2";
				$subject = translate_phrase("Sorry, Your Account has been suspended");
				break;

			case "closed" :
				$user_upadate_data['closed_date'] = SQL_DATE;
				$user_upadate_data['account_status_id'] = "3";
				$subject = translate_phrase("Sorry, Your Account has been closed");
				break;
		}

		if ($user_data) {
			$this -> general -> set_table('user');
			if ($this -> general -> update($user_upadate_data, array('user_id' => $user_id))) {
				$user_link = $this -> utility -> encode($user_id);
				if ($user_data['password']) {
					$user_link .= '/' . $user_data['password'];
				}

				$data['email_content'] = $body ? $body : $subject;
				$data['btn_link'] = base_url() . 'home/mail_action/' . $user_link . '?return_to=' . base_url() . url_city_name() . '/edit-profile.html';
				$data['btn_text'] = translate_phrase("Edit Profile");
				$data['email_title'] = '';
				$email_template = $this -> load -> view('email/common', $data, true);
				//echo $email_template;exit;
				if ($user_email_data = $this -> model_user -> get_user_email($user_id)) {
					return $this -> model_user -> send_email(INFO_EMAIL, $user_email_data['email_address'], $subject, $body);
					//return $this -> model_user -> send_email(INFO_EMAIL,'mikeye27@gmail.com', $subject, $body);
				}
			}
		}
	}

	/**
	 * Simply send mail to user based on user id
	 * @author Rajnish Savaliya
	 */
	public function send_mail_to_user($user_id = 0) {
		$response['type'] = 'error';
		$response['msg'] = translate_phrase('Please Try Again.');

		if ($postData = $this -> input -> post()) {
			$this -> load -> library('form_validation');
			$this -> form_validation -> set_rules('subject', 'Email subject', 'trim|required');
			$this -> form_validation -> set_rules('body', 'Email body', 'trim|required');
			if ($this -> form_validation -> run() == FALSE) {
				$response['msg'] = translate_phrase(validation_errors());
				$response['type'] = 'error';
			} else {
				$body = $postData['body'];
				$subject = $postData['subject'];
				if ($user_email_data = $this -> model_user -> get_user_email($user_id)) {
					//if($this -> model_user -> send_email(CEO_EMAIL,$user_email_data['email_address'], $subject, $body))
					if ($this -> model_user -> send_email(CEO_EMAIL, 'mikeye27@gmail.com', $subject, $body)) {
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
	public function facebook_info($user_id = 3) {
		$this -> general -> set_table('user');
		if ($facebook_data = $this -> general -> get("facebook_id", array('user_id' => $user_id, 'facebook_id !=' => ''))) {
			try {
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
	public function order_history($user_id = 3) {

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

		if ($user_orders = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'user_order_id asc')) {
			foreach ($user_orders as $order) {
				if ($order['order_num_date_tix']) {
					//Add ticket,amount and currency in total variable
					$data['total_tickets'] += $order['order_num_date_tix'];
					$data['total_ticket_amount'] += $order['order_amount'];

					$data['all_ticket_currency'][] = $order['currency_name'];
					$data['ticket_orders'][] = $order;
				} else {
					$data['total_membership_amount'] += $order['order_amount'];
					$data['all_membership_currency'][] = $order['currency_name'];

					if ($order['order_membership_options']) {
						$sql = "select GROUP_CONCAT(description) as membership_description  from membership_option where display_language_id = " . $this -> language_id . " AND membership_option_id IN (" . $order['order_membership_options'] . ")";
						if ($membership_options = $this -> general -> sql_query($sql)) {
							$order['membership_option_value'] = str_replace(",", ", ", $membership_options['0']['membership_description']);
						}
					}
					$data['membership_orders'][] = $order;
				}
			}

			//Remove dublicate values and make csv format
			$data['all_ticket_currency'] = implode(", ", array_unique($data['all_ticket_currency']));
			$data['all_membership_currency'] = implode(", ", array_unique($data['all_membership_currency']));

		}

		$data['page_title'] = translate_phrase('Order History');
		$data['page_name'] = 'admin/view_order_history';
		$this -> load -> view('template/admin', $data);
	}

	/**
	 * Verification information
	 * @author Rajnish Savaliya
	 */
	public function verification_info($user_id = 3) {

		$this -> general -> set_table('user');
		if ($data['user_data'] = $this -> general -> get("", array('user_id' => $user_id))) {
			$data['user_data'] = $data['user_data']['0'];

			$this -> general -> set_table('how_you_heard_about_us');
			if ($hear_about_us = $this -> general -> get("", array('display_language_id' => $this -> language_id, 'how_you_heard_about_us_id' => $data['user_data']['how_you_heard_about_us_id']))) {
				$data['user_data']['hear_about_us_description'] = $hear_about_us['0']['description'];
			}

			$this -> general -> set_table('user_photo');
			$data['user_data']['user_photos'] = $this -> general -> get("", array('user_id' => $user_id), array('is_approved' => 'asc'));

			$current_country = $this -> model_user -> getCountryByCity($data['user_data']['current_city_id']);
			$data['user_data']['country_code'] = $current_country ? $current_country -> country_code : " ";

			//School data
			$fields = array('us.user_school_id', 'us.school_name as my_school_name', 's.school_name', 'photo_diploma', 'us.school_id');
			$from = 'user_school as us';
			$joins = array('school as s' => array('us.school_id = s.school_id AND s.display_language_id = ' . $this -> language_id, 'LEFT'));
			$condition['us.user_id'] = $user_id;
			$condition['us.photo_diploma !='] = '';

			$data['school_data'] = $this -> model_user -> multijoins($fields, $from, $joins, $condition);
			unset($condition);

			//Company data
			$fields = array('uj.user_company_id', 'uj.company_name as my_company_name', 'c.company_name', 'photo_business_card', 'uj.company_id');
			$from = 'user_job as uj';
			$joins = array('company as c' => array('uj.company_id = c.company_id AND c.display_language_id = ' . $this -> language_id, 'LEFT'));
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
	public function verify_data($user_id) {
		$reject_reason = array( array('reason_id' => 1, 'description' => "Photo too large"), array('reason_id' => 2, 'description' => "Contains nudity"), array('reason_id' => 3, 'description' => "Unclear photo"), array('reason_id' => 4, 'description' => "Other"));

		if ($postData = $this -> input -> post()) {
			$this -> session -> set_userdata('statusData', $postData);
			if ($postData['status'] == 1) {
				//redirect to same function and save approve state..
				redirect('/admin/save_verify_data/' . $user_id);
			} else {
				$data['form_para'] = "/" . $user_id;
				$data['status'] = $postData['status'];
				$data['reject_reason'] = $reject_reason;
				$data['page_title'] = 'Rejection Reason';
				$data['page_name'] = 'admin/reject_reason';
				$this -> load -> view('template/admin', $data);
			}
		} else {
			redirect('/admin/verification_info/' . $user_id);
		}
	}

        
        
	public function save_verify_data($user_id) {

		$reject_reason = array( array('reason_id' => 1, 'description' => "Photo too large"), array('reason_id' => 2, 'description' => "Contains nudity"), array('reason_id' => 3, 'description' => "Unclear photo"), array('reason_id' => 4, 'description' => "Other"));

		if ($postData = $this -> session -> userdata('statusData')) {
			$this -> session -> unset_userdata('statusData');

			//Verify data from user table
			if ($postData['section'] == 'profile') {
				$user_upadate_data[$postData['field'] . '_is_verified'] = $postData['status'];
				$this -> general -> set_table('user');
				if ($this -> general -> update($user_upadate_data, array('user_id' => $user_id))) {
					$response['type'] = 'success';
					$response['msg'] = translate_phrase('status changed');
				}
			}

			//Verify data from user table
			if ($postData['section'] == 'photo') {
				$user_upadate_data[$postData['field']] = $postData['status'];
				$this -> general -> set_table('user_photo');
				if ($this -> general -> update($user_upadate_data, array('user_photo_id' => $postData['field_val'], 'user_id' => $user_id))) {
					$response['type'] = 'success';
					$response['msg'] = translate_phrase('status changed');
				}
			}

			$subject = translate_phrase("Your ") . $postData['field_name'] . translate_phrase(" has been ");
			$subject .= ($postData['status'] == 1) ? translate_phrase('approved') : translate_phrase('rejected');
			$data['email_content'] = '';

			if ($formData = $this -> input -> post()) {

				$data['email_content'] = translate_phrase("Rejection reason :");
				if ($formData['reject_reason_id'] == 4) {
					$data['email_content'] .= translate_phrase($formData['other_reason_txt']);
				} else {
					$selected_reason = search($reject_reason, 'reason_id', $formData['reject_reason_id']);
					$data['email_content'] .= translate_phrase($selected_reason['0']['description']);
				}
			}

			$data['email_title'] = $subject;

			$email_template = $this -> load -> view('email/common', $data, true);
			if ($user_email_data = $this -> model_user -> get_user_email($user_id)) {
				$this -> datetix -> mail_to_user($user_email_data['email_address'], $subject, $email_template);
			}
		}
		redirect('/admin/verification_info/' . $user_id);
	}

        
        
	private function _send_welcome_notes($user_id = "") {

		if ($user_data = $this -> model_user -> get_user_data($user_id)) {
			$welcome_message = '';
			if ($user_email_data = $this -> model_user -> get_user_email($user_id)) {

				$user_lang_id = $user_data['last_display_language_id'];
				switch ($user_lang_id) {
					case '2' :
						$welcome_message = $user_data['last_name'] . $user_data['first_name'] . "您好，歡迎來到DateTix！我們會開始搜索我們數據庫中與你最相配的會員介紹給你。如果有任何問題歡迎隨時與我聯繫。";
						break;

					case '3' :
						$welcome_message = $user_data['last_name'] . $user_data['first_name'] . "您好，欢迎来到DateTix！我们会开始搜索我们数据库中与你最相配的会员介绍给你。如果有任何问题欢迎随时与我联系。";
						break;

					default :
						$welcome_message = "Hi " . $user_data['first_name'] . ", welcome to DateTix! We will now start to search our database and introduce you to your top matches. Let me know here anytime if you have any questions.";
						break;
				}

				//Send Message
				$admin_id = '1';
				$this -> general -> set_table('user_intro');
				$condition = "(user1_id='" . $user_id . "' AND user2_id='" . $admin_id . "') OR (user1_id='" . $admin_id . "' AND user2_id='" . $user_id . "')";
				if ($user_intros = $this -> general -> custom_get("user_intro_id,user1_id,user2_id", $condition)) {

					$intro = $user_intros['0'];

					$chat_data['chat_message_time'] = SQL_DATETIME;
					$chat_data['user_id'] = 1;
					$chat_data['chat_message'] = $welcome_message;
					$chat_data['user_intro_id'] = $intro['user_intro_id'];

					$this -> general -> set_table('user_intro_chat');

					$this -> general -> save($chat_data);

					$this -> general -> set_table('user');
					$intro_data = $this -> general -> get("user_id, first_name, password, facebook_id", array('user_id' => $admin_id));
					$data['intro_user_data'] = $intro_data['0'];

					$data['email_content'] = '';
					$data['btn_link'] = base_url() . 'user/user_info/' . $this -> utility -> encode($user_data['user_id']) . '/' . $this -> utility -> encode($data['intro_user_data']['user_id']) . '/' . $data['intro_user_data']['password'] . '?redirect_intro_id=' . $intro['user_intro_id'];
					$data['btn_text'] = translate_phrase('View Message');
					$data['email_title'] = translate_phrase('You have received a new message from Michael') . translate_phrase(' on ') . date('F j ') . translate_phrase(' at ') . date('g:ia');
					$subject = translate_phrase('You have received a new message from Michael');
					$email_template = $this -> load -> view('email/common', $data, true);

					$this -> datetix -> mail_to_user($user_email_data['email_address'], $subject, $email_template);
				}

			}
		}
	}

	/**
	 * Add static entry in user intro table: intro_with_michael
	 * @author Rajnish Savaliya
	 */
	private function _intro_with_michael($user_id = "") {
		$user_data = $this -> model_user -> get_user_data($user_id);
		$flag = "";
		if ($user_id) {
			$this -> general -> set_table('user_intro');
			$intro_data['user1_id'] = '1';
			$intro_data['user2_id'] = $user_id;
			$intro_data['intro_created_time'] = SQL_DATETIME;
			$intro_data['intro_available_time'] = SQL_DATETIME;
			if ($user_data['gender_id'] == 1) {
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
        
        public function get_credits(){
            $data = [];
            $data['is_review_resricted'] = false;
            if ($access_permission = $this -> session -> userdata('LIMITED_ACCESS')) {
                    $allow_permission = "review_application";
                    $data['is_review_resricted'] = in_array($allow_permission, $access_permission);

            }
            $data['page_name'] = 'admin/get_credits';
            $this -> load -> view('template/admin', $data);
            //$this->load->view('admin/get_credits',array());
        }
        
        /** Processes the request when user clicks on Handpick Match button in Members tab **/
        public function processHandpickMatch(){
           if($this->input->is_ajax_request()){
              $response = []; 
              $userID = $this->input->post('userID');
              $handpickedUserID = $this->input->post('handpickedUserID'); 
              
              //Check if this user intro already exists or not.If not then only insert
              // modify by jigar oza
              $SQL = 'SELECT * FROM user_intro WHERE ((user1_id=? OR user1_id = ?) AND (user2_id=? OR user2_id = ?)) AND handpicked_time IS NOT null AND handpicked_time !="0000-00-00 00:00:00"';
              //$SQL = 'SELECT user_intro_id FROM user_intro WHERE (user1_id=? OR user1_id = ?) AND (user2_id=? OR user2_id = ?)';
              //echo $SQL;die();
              $res = $this->general->rawQuery($SQL,[$userID,$handpickedUserID,$userID,$handpickedUserID]);
              
              //die();
              /** Get names of the users 
               *  @todo :: This query can be done away with, in view file we already have both the names**/
              $SQL = 'SELECT user_id,first_name FROM user where user_id = ? OR user_id = ?';
              $usersNamesRes = $this->general->rawQuery($SQL,[$userID,$handpickedUserID]);
              $usersNames = [];
			  
              foreach ($usersNamesRes as $key => $value) {
                 $usersNames[$value['user_id']] = $value;                 
              }
             
			//print_r($handpickedUserID);exit;
              if(!empty($res)){
                 $response['actionStatus'] = 'warning';
                 $response['msg'] = $usersNames[$userID]['first_name'].translate_phrase(' is already in ').$usersNames[$handpickedUserID]['first_name'].'\'s '.translate_phrase('intro list');
                 die(json_encode($response));
              }
              
              $TS = time();
              $createdTime = date('Y-m-d- H:i:s',$TS);
              $introExpiryTime = date('Y-m-d- H:i:s',($TS + (86400*90)));
              $dataToInsert = [
                  'user1_id' => $userID,
                  'user2_id' => $handpickedUserID,
                  'intro_created_time' => $createdTime,
                  'intro_available_time' => $createdTime,
                  'handpicked_time' => $createdTime,
                  'intro_expiry_time' => $introExpiryTime,
                  'intro_email_sent_time'=>$createdTime,// added by Jigar oza
              ];
              $this->general->set_table('user_intro');
              $lastID = $this->general->save($dataToInsert);
              if($lastID){//handpickedID has been added to userID intro list
                  
                    // added by jigar oza to send mail
                    $this -> datetix -> intro_mail($handpickedUserID, $userID);
                    $this -> datetix -> intro_mail($userID,$handpickedUserID);

                  $response['actionStatus'] = 'ok';
                  //$response['msg'] = $usersNames[$handpickedUserID]['first_name'].translate_phrase(' has been added to ').$usersNames[$userID]['first_name'].'\'s '.translate_phrase('intro list');
                  $response['msg'] = $usersNames[$userID]['first_name'].translate_phrase(' has been added to ').$usersNames[$handpickedUserID]['first_name'].'\'s '.translate_phrase('intro list');
              }else{
                  $response['actionStatus'] = 'warning';
                  $response['msg'] = translate_phrase('Failed to introduct');
              }              
              die(json_encode($response));
           }
           redirect(base_url('admin'));
        }

	public function logout() {
		$this -> session -> sess_destroy();
		unset($_SERVER['PHP_AUTH_USER']);
		unset($_SERVER['PHP_AUTH_PW']);

		redirect('/');
	}
        
        /**
         * added by jigar oza
         */
        /** Called from load_users action when current tab is requests tab**/
	private function loadConsultationsData() {
		//echo '<pre>';print_r($_POST);die();
		$data = array();
		$websiteId = 0;
                
		$superAdminData = $this -> session -> userdata('superadmin_logged_in');
		
		//Get all the users of this website and index that array
		$superAdminData = $this -> session -> userdata('superadmin_logged_in');
                $websiteId = $superAdminData['website_id'];
           
		
               $SQL = "Select 
                        ud.*,
                        user.user_id as user_id,
                        user.website_id as website_id,
                        sent_user.first_name as sent_first_name, 
                        sent_user.user_id as sent_user_id, 
                        sent_user.last_name as sent_last_name,
                        received_user.first_name as received_first_name,
                        received_user.last_name as received_last_name,
                        received_user.user_id as received_user_id
                        from user_date as ud
                        INNER JOIN user_intro ON user_intro.user_intro_id=ud.user_intro_id
                        INNER JOIN user AS sent_user on sent_user.user_id=  
                            CASE WHEN user_intro.user1_id = ud.date_suggested_by_user_id 
                                 THEN user_intro.user1_id 
                                 WHEN user_intro.user2_id = ud.date_suggested_by_user_id THEN user_intro.user2_id 
                            END 
                        INNER JOIN user AS received_user on received_user.user_id=  
                                 CASE WHEN user_intro.user1_id = ud.date_suggested_by_user_id 
                                    THEN user_intro.user2_id 
                                     WHEN user_intro.user2_id = ud.date_suggested_by_user_id THEN user_intro.user1_id 
                                END                                     
                        INNER JOIN user on user.user_id=ud.date_suggested_by_user_id                        
                        WHERE ";
				if($websiteId > 0)
				{
					$SQL .= " user.website_id='".$websiteId."'";
				}
				else {
					$SQL .= " 1=1 ";
				}
                
                if($this->input->post('status_id')=='0'){
                    $SQL .= " AND (ud.date_accepted_time is null or ud.date_accepted_time='0000-00-00 00:00:00')
                              AND (ud.date_declined_time is null or ud.date_declined_time='0000-00-00 00:00:00')";
                }
                if($this->input->post('status_id')=='1'){                    
                    $SQL .= " AND ud.date_accepted_time is not null AND ud.date_accepted_time!='0000-00-00 00:00:00'";
                }
                if($this->input->post('status_id')=='2'){                    
                    $SQL .= " AND ud.date_declined_time is not null AND ud.date_declined_time!='0000-00-00 00:00:00'";
                }
	      

		/* LIMIT part */
		$page_no = 1;
		if ($this -> input -> post('page_no')) {
			$page_no = $this -> input -> post('page_no');
		}

		$SQL .= ' order by ud.user_date_id DESC LIMIT ' . ($page_no - 1) * PER_PAGE_ADMIN . ', ' . PER_PAGE_ADMIN;
//echo $SQL;
		$res = $this -> general -> sql_query($SQL);
               
		//echo ($this->db->last_query());exit;
                $b2bData = array();
		$this -> general -> set_table('user_photo');
		foreach ($res as $key => $value) {
			
                        
                        $sentUserId=$value['sent_user_id'];
                        $receivedUserId=$value['received_user_id'];
                        
                        
                        $getSentUserDetail=$this->db->query("select u.first_name as first_name,
                                                                        u.mobile_phone_number as mobile_phone_number,
                                                                        u.current_city_id as current_city_id,
                                                                        co.country_code as country_code
                                                               from user as u 
                                                               inner join city as c on u.current_city_id=c.city_id
                                                               inner join province as p on  c.province_id=p.province_id
                                                               inner join country as co on p.country_id=co.country_id
                                                               where u.user_id='".$sentUserId."'")->row_array();
                        
                          $getReceivedUserDetail=$this->db->query("select u.first_name as first_name,
                                                                        u.mobile_phone_number as mobile_phone_number,
                                                                        u.current_city_id as current_city_id,
                                                                        co.country_code as country_code
                                                               from user as u 
                                                               inner join city as c on u.current_city_id=c.city_id
                                                               inner join province as p on  c.province_id=p.province_id
                                                               inner join country as co on p.country_id=co.country_id
                                                               where u.user_id='".$receivedUserId."'")->row_array();           
                        
			$SQL = 'SELECT * FROM user_photo WHERE set_primary=1 AND (user_id = ? OR user_id = ?)';
			$primary_photos = $this -> general -> rawQuery($SQL, array($sentUserId,$receivedUserId));
                        //echo $this->db->last_query();
                        $value['sent_user_photo']='';
                        $value['received_user_photo']='';
                        $value['sent_country_code'] = $getSentUserDetail['country_code'];
                        $value['sent_user'] = $getSentUserDetail['first_name'];
                        $value['sent_user_mobile'] = $getSentUserDetail['mobile_phone_number'];
                        $value['received_country_code'] = $getReceivedUserDetail['country_code'];
                        $value['received_user'] = $getReceivedUserDetail['first_name'];
                        $value['received_user_mobile'] = $getReceivedUserDetail['mobile_phone_number'];
                        
			foreach ($primary_photos as $ke => $val) {

                                if ($sentUserId == $val['user_id']) {
					$value['sent_user_photo'] = base_url() . 'user_photos/user_' . $val['user_id'] . '/' . $val['photo'];
				}
				if ($receivedUserId == $val['user_id']) {
					$value['received_user_photo'] = base_url() . 'user_photos/user_' . $val['user_id'] . '/' . $val['photo'];
				} 
                                
			}
			$b2bData[$value['user_date_id']] = $value;

		}
		$data['b2b'] = $b2bData; 
               
                return $data;
	}
        
        
        public function processConsultationsAction() {
		$response = array();
		$action = $this -> input -> post('action');
		$userDateID = $this -> input -> post('user_date_id');

		$dbStatuses = array('accept' => 'accepted', 'decline' => 'declined', 'cancel' => 'cancelled');

		$possibleActions = array('accept' => translate_phrase('You have accepted this bid'), 
                                    'decline' => translate_phrase('You have declined this bid'), 
                                    'cancel' => translate_phrase('Your bid has been cancelled'));

		if (!empty($userDateID) && array_key_exists($action, $possibleActions) !== FALSE) {//data is what we expected
			$updateStastus = TRUE;			                            
                           //Added By Jigar Oza
                            if($action=='accept'){                                                                                                    
                                    // credit to matchmaker a account
                                    $this->db->query("update user_date set date_accepted_time='".SQL_DATETIME."',date_declined_time=''
                                                        where user_date_id='".$userDateID."'");         
                                    
                                    $this->general->set_table('user_date');
                                    $getUserDateDetail=$this->general->get('*',array('user_date_id'=>$userDateID));
                                    
                                    $getUserDetail=$this->db->query("select u.first_name as first_name,
                                                                        u.mobile_phone_number as mobile_phone_number,
                                                                        u.current_city_id as current_city_id,
                                                                        co.country_code as country_code
                                                               from user as u 
                                                               inner join city as c on u.current_city_id=c.city_id
                                                               inner join province as p on  c.province_id=p.province_id
                                                               inner join country as co on p.country_id=co.country_id
                                                               where u.user_id='".$getUserDateDetail[0]['date_suggested_by_user_id']."'")->row_array();
                                    
                                    $name=$getUserDetail['first_name'];
                                    $phone=$getUserDetail['mobile_phone_number'];
                                    $code=$getUserDetail['country_code'];
                                    
                                    $response['actionStatus'] = 'accept';
                                    $response['msg'] = translate_phrase('You have accepted this consultation request. You may now call '.$name.' at (+'.$code.") ".$phone.' to schedule a consultation time.');
                                    
                            }elseif($action=='decline'){                                
                                    $this->db->query("update user_date set date_declined_time='".SQL_DATETIME."',date_accepted_time=''
                                                        where user_date_id='".$userDateID."'");                                    
                                    $response['actionStatus'] = 'decline';
                                    $response['msg'] = translate_phrase('You have declined this consultations request.');                    				                                
                                    
                            }else{
                                    $response['actionStatus'] = 'warning';
                                    $response['msg'] = translate_phrase('Not able to process the aciton');
                            }
		} else {
			$response['actionStatus'] = 'warning';
			$response['msg'] = translate_phrase('Not able to process the aciton');
		}
		die(json_encode($response));
	}
        
}
?>
