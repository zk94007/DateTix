<?php
//if (!defined('BASEPATH'))
//exit('No direct script access allowed');
class Cron extends CI_Controller {

	public function __construct() {
		parent::__construct();
		ini_set('memory_limit', '-1');
		$this -> load -> model('model_user');
		$this -> load -> model('model_city');
		$this -> load -> model('general_model', 'general');
		$this -> load -> library('datetix');

		if (!$this -> session -> userdata('sess_language_id')) {
			$this -> session -> set_userdata('sess_language_id', '1');
		}

		
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
	/*
	 *  
	- For male, set user.want_age_range_lower = user's age - 5 (but minimum is 18) and user.want_age_range_upper = user's age
	- For female, set user.want_age_range_lower = user's age and user.want_age_range_upper = user's age + 5
	- Get all users from user table with user.want_age_range_lower = NULL and then set value based on above rules for male and female
	- Get all users from user table with user.want_age_range_upper= NULL and then set value based on above rules for male and female
	 * 
	 */ 
	public function test_signup() {
		
		$this -> general -> set_table('user');
		$user_select = "user_id,first_name,last_name,gender_id, want_age_range_lower,want_age_range_upper,
		
				CASE
					WHEN
						birth_date != '0000-00-00'
					THEN 
						TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
				END as age
		";
		$user_condition['want_age_range_lower'] = null;
		$user_condition['want_age_range_upper'] = null;
		//$this->db->or_where($user_condition);
		
		$users = $this -> general -> get($user_select,$user_condition);
		//echo $this->db->last_query();
		//echo "<pre>";print_r($users);exit;
		if($users)
		{
			foreach($users  as $user)
			{
				if($user['age'])
				{
					$udpate_user_data = array();
				
					//For Male
					if($user['gender_id'] == 1)
					{
						$user_age = $user['age']-5;
						$user_age = $user_age >= 18?$user_age : 18;
						$udpate_user_data['want_age_range_lower'] = $user_age;
						$udpate_user_data['want_age_range_upper'] = $user['age'];					
					}
					else {
						//For Female
						$udpate_user_data['want_age_range_lower'] = $user['age'];
						$udpate_user_data['want_age_range_upper'] = $user['age']+5;					
					}
					
					$this -> general -> update($udpate_user_data,array('user_id'=>$user['user_id']));
					
					
					echo $user['first_name'].' '.$user['last_name'];
					echo ' => ['.$udpate_user_data['want_age_range_lower'].' - '.$udpate_user_data['want_age_range_upper'].']'.'<br/>';					
				}
				
			}
		}
		
	}
	public function how_score($user_id = 1, $intro_id = 3) {
		$data = $this -> datetix -> calculate_score_debug($user_id, $intro_id);
		echo "<pre>";
		print_r($data);
		exit ;
	}

	public function test_geoip($ip = '122.170.119.109') {
		$this -> load -> library('GeoPlugin');
		$this -> load -> model('model_city');
		$this -> geoplugin -> locate($ip);
		echo "<pre>";
		print_r($this -> geoplugin);

		echo '</pre><br/> Now Fetch record from database (if city found then redirect works otherwise redirect to hongkong city): <br/>';
		$city = $this -> model_city -> getByName($this -> geoplugin -> city);
		echo $this -> db -> last_query();
		echo "<pre>";
		print_r($city);
		exit ;
	}
	public function decode_id($decode="_acCmn2LnDIusHt_rAjAyqnFttedK6AWKGWmyhyAWPE")
	{
		//http://datetix.com/user/user_info/XkdidwPsQDGZXu75SZlavMJ2VRav6dOA6HsMWG9WGvo/N6QW1U6lwG-s2ZAKUNH5-WOGXarXbohv_JxfMV_c1rk/ecc92703e8c212215ff4bb71209a4636f0cdbf3c
		echo $this->utility->decode($decode);
	}

	public function test_mail($user_id = 435, $intro_id = 535,$user_id2 = 0, $intro_id2 = 0) {
		
		$this -> datetix -> intro_mail_debug($intro_id, $user_id);
		$this -> datetix -> intro_mail_debug($user_id,$intro_id);
		
		if($intro_id2 && $intro_id2)
		{
			$this -> datetix -> intro_mail_debug($intro_id2, $user_id2);
			$this -> datetix -> intro_mail_debug($user_id2,$intro_id2);
		}
		
	}

	public function fetch() {
		//$this -> general -> set_table('website');
		//$users = $this -> general -> get("*");
		//echo $this->db->last_query();
		echo "<pre>";
		//print_r($users);
		exit ;
	}

	public function index() {
		$is_premi = $this -> datetix -> is_premium_user(3, 2);
		echo var_dump($is_premi);
	}

	public function execute_query() {
       $sql = "ALTER TABLE `date` ADD `is_refunded` TINYINT( 1 ) NOT NULL DEFAULT '0'";
       $query = $this -> db -> query($sql);
       echo '<pre>';print_r($query);die();
	}

	/**
	 * expire_intro_reminder: Send mail to both user before 24 hours for expiring intro .
	 * @access public
	 * @return
	 * @author Rajnish Savaliya
	 */
	public function expire_intro_reminder()
	{
		$this->session->set_userdata('type','expire_intro_reminder');
		$email_where = "intro_expiry_time >= NOW() AND intro_expiry_time <  DATE_ADD(NOW(), INTERVAL 24 HOUR)";
		$this -> general -> set_table('user_intro');
		$intros_data = $this -> general -> custom_get("*", $email_where);
		if ($intros_data) {
			foreach ($intros_data as $intro) {
				$user['user_id'] = $intro['user1_id'];
				$cur_user['user_id'] = $intro['user2_id'];
				$user_compitiblity = $this -> datetix -> calculate_score($cur_user['user_id'], $user['user_id']);
				$this -> datetix -> intro_mail_debug($intro['user2_id'], $intro['user1_id'], $user_compitiblity['match_data']);

				$intro_compitiblity = $this -> datetix -> calculate_score($user['user_id'], $cur_user['user_id']);
				$this -> datetix -> intro_mail_debug($intro['user1_id'], $intro['user2_id'], $intro_compitiblity['match_data'], 'user');
			}
		} else {
			echo 'No Expire intro found.';
		}
		$this->session->unset_userdata('type');
	}
	
	/**
	 * rsvp_event_reminder: Send RSVP reminder emails to all rows in event_ticket with: 
	 * 1) invite_email_address != NULL, 
	 * 2) user_id=NULL OR user.completed_application_step<7, 
	 * 3) NOW() - last_reminder_sent >= 48 hours, 4) NOW() < event.event_start_time
	 * @access public
	 * @return
	 * @author Rajnish Savaliya
	 */
	public function rsvp_event_reminder()
	{
		$language_id = $this -> session -> userdata('sess_language_id');
		$sql_query = "
					SELECT e.event_id, el.event_name, e.event_start_time,rsvp.*, v.*, 
					n.description as neighborhood_name, ct.description as city_name, 
					p.description as province_name, c.description as country_name ,
					DATE_SUB(NOW(), INTERVAL 48 HOUR) as date_compare
					FROM (event_ticket as rsvp) 
					INNER JOIN event_order as ordr ON rsvp.event_order_id = ordr.event_order_id 
					INNER JOIN event as e ON e.event_id = ordr.event_id 
					INNER JOIN event_language as el ON el.event_id = e.event_id 
					INNER JOIN venue as v ON e.venue_id = v.venue_id 
					INNER JOIN neighborhood as n ON v.neighborhood_id = n.neighborhood_id 
					INNER JOIN city as ct ON ct.city_id = n.city_id 
					INNER JOIN province as p ON p.province_id = ct.province_id 
					LEFT JOIN country as c ON p.country_id = c.country_id					
					LEFT JOIN user on user.user_id = CASE 
						WHEN rsvp.user_id IS NOT NULL THEN rsvp.user_id
					END
					
					WHERE rsvp.last_reminder_sent < DATE_SUB(NOW(), INTERVAL 48 HOUR) 
					AND (rsvp.user_id IS NULL OR user.completed_application_step < 7) 
					AND e.event_start_time >= NOW()
					AND rsvp.invite_email_address IS NOT NULL					
					AND el.display_language_id = '".$language_id."'
					AND ct.display_language_id = '".$language_id."'
					AND n.display_language_id = '".$language_id."'
					AND p.display_language_id = '".$language_id."'
					AND c.display_language_id = '".$language_id."'";
		
		$event_tickets = $this -> general -> sql_query($sql_query);
		
		if($event_tickets)
		{
			foreach($event_tickets as $ticket_info)
			{
				
				$data['btn_link'] = base_url() . url_city_name() . '/apply.html?event_ticket_id='.$this->utility->encode($ticket_info['event_ticket_id']);
				$data['email_title'] = translate_phrase('Hi');
				//Generate AutoLogin Link
				$this->general->set_table("user");
				if($user_profile_data = $this -> general -> get("user_id,first_name,password,facebook_id", array('user_id' => $ticket_info['user_id'])))
				{
					$user_link = $this -> utility -> encode($ticket_info['user_id']);
					if ($user_profile_data['0']['password']) {
						$user_link .= '/' . $user_profile_data['0']['password'];
					}
					$data['btn_link'] = base_url() . 'home/mail_action/' . $user_link . '?ticket_id='.$this->utility->encode($ticket_info['event_ticket_id']);
					$data['email_title'] .= " ".$user_profile_data['0']['first_name'];
				}
				$subject = translate_phrase("Reminder: You still need to RSVP for ").$ticket_info['event_name'];
				$data['btn_text'] = translate_phrase("RSVP Now");
				$data['email_title'] .= translate_phrase(', you still need to complete your RSVP for ').$ticket_info['event_name'].translate_phrase(" on ").date(DATE_FORMATE,strtotime($ticket_info['event_start_time']));
				$data['email_content'] = translate_phrase('The event starts at ').date("g:i a",strtotime($ticket_info['event_start_time'])).translate_phrase(' and takes place at').':';
				$data['email_content'] .= "<br/>".$ticket_info['name'];
				$data['email_content'] .= "<br/>".$ticket_info['address'];
				$data['email_content'] .= ", ".$ticket_info['neighborhood_name'];
				$data['email_content'] .= "<br/>".$ticket_info['city_name'];
				$data['email_content'] .= "<br/>".$ticket_info['phone_number'];
				echo $email_template = $this -> load -> view('email/common', $data, true);
				
			}
		}
		else
		{
			echo "No rsvp user found.";
		}
	}

	/**
	 * event_match_reminder:  Send event match emails to all rows in event_user 
	 * for current event_id with: 1) event_matches_sent=0, 
	 * 2) event.event_start_time - NOW() <= 48 hours, 
	 * 3) NOW() < event.event_start_time.
	 * @access public
	 * @return
	 * @author Rajnish Savaliya
	 */
	public function event_match_reminder() {
		$language_id = $this -> session -> userdata('sess_language_id');

		$condition = "event_start_time >= NOW() AND display_language_id = $language_id AND event_start_time <  DATE_ADD(NOW(), INTERVAL 48 HOUR)";
		$this -> general -> set_table('event');
		$upcoming_event = $this -> general -> custom_get("*", $condition);
		
		$this -> general -> set_table('event_user');
		if ($upcoming_event) {
			
			foreach ($upcoming_event as $event) {
				
				//Attending users
				$attend_user_condition['rsvp_time !='] = '0000-00-00 00:00:00';
				$attend_user_condition['event_matches_sent'] = '0';				
				$attend_user_condition['event_id'] = $event['event_id'];				
				if($event_users = $this -> general -> get("",$attend_user_condition))
				{
					foreach($event_users as $user)
					{
						//Send Mail to current user
						if($user_email_data = $this -> model_user -> get_user_email($user['user_id']))
						{
							$suggestion_list_sql = 'SELECT user_intro.*,
								user.first_name as intro_name, user.height, user.gender_id, user.ethnicity_id, user.body_type_id, user.privacy_photos,
								user.user_id, user.facebook_id, user.birth_date, user.current_city_id,
								CASE
									WHEN
										birth_date != "0000-00-00" 	
									THEN 
										TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
								END as intro_age
								
								FROM user_intro
								
								JOIN user on user.user_id = CASE 
									WHEN user_intro.user1_id = "' . $user['user_id'] . '" THEN user_intro.user2_id
									WHEN user_intro.user2_id = "' . $user['user_id']. '" THEN user_intro.user1_id
								END
								WHERE 
								user_intro.event_id = '.$event['event_id'].' AND  
								(user1_id = "' . $user['user_id'] . '" OR user2_id = "' . $user['user_id'] . '") ';
							$query = $suggestion_list_sql . ' ORDER BY `intro_available_time` DESC ';
							$user_intro = $this -> general -> sql_query($query);
							$data['email_content'] = "";
							if($user_intro)
							{
								$data['email_content'] = "<ol>";
								foreach($user_intro as $intro)
								{
									$subject_line = $intro['intro_name'];		
									//$subject_line .= $intro['gender_id']==1?', Male':', Female';
									$subject_line .= ', '.$intro['intro_age'].' years old';
									$fields = array('sc.school_id','sc.school_name','sc.logo_url','ct.*');
									$from = "user_school as uc";
									$joins = array('school as sc'=>array('uc.school_id = sc.school_id','INNER'),'city as ct'=>array('ct.city_id = sc.city_id','INNER'));
									unset($condition);
									$condition['uc.user_id'] = $intro['user_id'];
									$condition['sc.display_language_id'] = $language_id;
									$condition['ct.display_language_id'] = $language_id;
									$ordersby = 'sc.school_id asc';
									$school_datas = $this->general->multijoins($fields,$from,$joins,$condition,$ordersby,'array',NULL,2,NULL,'where','school_id');
									if($school_datas)
									{
										$school_arr = array();
										foreach ($school_datas as $value) {
											$school_arr[] = $value['school_name'];
										}
										$subject_line .= 'studied at '.implode(' and ', $school_arr);
									}

									$job_data = $this->datetix->get_my_company_data($intro['user_id']);
									if($job_data)
									{
										$data['intro_works'] = translate_phrase('Works as ');
										if($job_data['0']['job_function_id'] && isset($job_data['0']['job_function_data']))
										{
											$data['intro_works'] .= $job_data['0']['job_function_data']['description'];
										}
										else
										{
											$data['intro_works'] .= $job_data['0']['job_title'];
										}
										
										if($job_data['0']['show_company_name'])
										{
											$data['intro_works'] .= translate_phrase(' at ').$job_data['0']['company_name'];
										}
										elseif($job_data['0']['industry_id'] && isset($job_data['0']['industry_description']) && $job_data['0']['industry_description'])
										{
											$data['intro_works'] .= translate_phrase(' in ').$job_data['0']['industry_description'].' '.translate_phrase('industry');
										}
										$subject_line .= ', '.$data['intro_works'];
									}
									$data['email_content'] .= "<li>".$subject_line."</li>";
								}
								$data['email_content'] .= "</ol>";
							}
							
							
							$user_info = $this -> model_user -> get_user_data($user['user_id']);
							$data['user_data'] = $this -> model_user -> get_user_data($user['user_id']);
							$data['email_title'] = 'Hi ' . $user_info['first_name'].', '.translate_phrase('here is your personalized list of "People to Meet" for ').$event['event_name'].' on '.date(DATE_FORMATE,strtotime($event['event_start_time']));
							$data['email_content'] .= '<p class="callout">'.translate_phrase('Be sure to look for them at the event (each attendee will be asked to wear a name tag)').'.</p>';					
							$data['btn_link'] = base_url().url_city_name().'/my-intros.html';
							$data['btn_text'] = translate_phrase("View Their Profiles");					
							echo $email_template = $this -> load -> view('email/common', $data, true);
							$subject ="Here are your matches for ".$event['event_name']." on ".date(DATE_FORMATE,strtotime($event['event_start_time'])); 
							if($this -> datetix -> mail_to_user_debug($user_email_data['email_address'],$subject, $email_template))
							{
								$update_attend_user_condition['event_id'] = $event['event_id'];
								$update_attend_user_condition['user_id'] = $user['user_id'];
								$this -> general -> set_table('event_user');
								$event_users = $this -> general -> update(array('event_matches_sent'=>'1'),$update_attend_user_condition);
								unset($update_attend_user_condition);
							}
						}
					}
				}
				else
				{
					echo "No event_user remaining for sending mail in ".$event['event_name']." on ".date(DATE_FORMATE,strtotime($event['event_start_time']));
				}
			}
		} else {
			echo 'no upcoming event in 48 hour';
		}

	}
	
	/**
	 * event_people_to_meet :: Simple HTML Page with all event user with his matches
	 * @access public
	 * @return
	 * @author Rajnish Savaliya
	 */
	public function event_people_to_meet($event_id = 1)
	{
		$language_id = $this -> session -> userdata('sess_language_id');
		
		$this -> general -> set_table('event_user');
		$attend_user_condition['event_id'] = $event_id;				
		
		if($event_users = $this -> general -> get("DISTINCT(user_id)",$attend_user_condition))
		{
			$results = array();
			foreach($event_users as $key=>$user)
			{
				$results[$key] = $user;
				
				//Send Mail to current user
				if($user_email_data = $this -> model_user -> get_user_email($user['user_id']))
				{
					$suggestion_list_sql = 'SELECT user_intro.*,
						user.first_name as intro_name, user.height, user.gender_id, user.ethnicity_id, user.body_type_id, user.privacy_photos,
						user.user_id, user.facebook_id, user.birth_date, user.current_city_id,
						CASE
							WHEN
								birth_date != "0000-00-00" 	
							THEN 
								TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
						END as intro_age
						
						FROM user_intro
						
						JOIN user on user.user_id = CASE 
							WHEN user_intro.user1_id = "' . $user['user_id'] . '" THEN user_intro.user2_id
							WHEN user_intro.user2_id = "' . $user['user_id']. '" THEN user_intro.user1_id
						END
						WHERE 
						user_intro.event_id = '.$event_id.' AND  
						(user1_id = "' . $user['user_id'] . '" OR user2_id = "' . $user['user_id'] . '") ';
					$query = $suggestion_list_sql . ' ORDER BY `intro_available_time` DESC ';
					$user_intro = $this -> general -> sql_query($query);
					$data['email_content'] = "";
					$results[$key]['matches'] = array();
					if($user_intro)
					{
						foreach($user_intro as $i=>$intro)
						{
							$results[$key]['matches'][$i]['name'] = $intro['intro_name'];
							$subject_line = '';
							//$subject_line .= $intro['gender_id']==1?', Male':', Female';
							//$subject_line .= ', '.$intro['intro_age'].' years old';
							$fields = array('sc.school_id','sc.school_name','sc.logo_url','ct.*');
							$from = "user_school as uc";
							$joins = array('school as sc'=>array('uc.school_id = sc.school_id','INNER'),'city as ct'=>array('ct.city_id = sc.city_id','INNER'));
							unset($condition);
							$condition['uc.user_id'] = $intro['user_id'];
							$condition['sc.display_language_id'] = $language_id;
							$condition['ct.display_language_id'] = $language_id;
							$ordersby = 'sc.school_id asc';
							$school_datas = $this->general->multijoins($fields,$from,$joins,$condition,$ordersby,'array',NULL,2,NULL,'where','school_id');
							if($school_datas)
							{
								$school_arr = array();
								foreach ($school_datas as $value) {
									$school_arr[] = $value['school_name'];
								}
								$subject_line .= ', '.implode(' and ', $school_arr);
							}

							$job_data = $this->datetix->get_my_company_data($intro['user_id']);
							if($job_data)
							{
								//$data['intro_works'] = translate_phrase('Works as ');
								$data['intro_works'] = '';
								if($job_data['0']['job_function_id'] && isset($job_data['0']['job_function_data']))
								{
									//$data['intro_works'] .= $job_data['0']['job_function_data']['description'];
								}
								else
								{
									//$data['intro_works'] .= $job_data['0']['job_title'];
								}
								
								if($job_data['0']['show_company_name'])
								{
									//$data['intro_works'] .= translate_phrase(' at ').$job_data['0']['company_name'];
									$data['intro_works'] .= $job_data['0']['company_name'];
								}
								elseif($job_data['0']['industry_id'] && isset($job_data['0']['industry_description']) && $job_data['0']['industry_description'])
								{
									//$data['intro_works'] .= translate_phrase(' in ').$job_data['0']['industry_description'].' '.translate_phrase('industry');
									$data['intro_works'] .= $job_data['0']['industry_description'].' '.translate_phrase('industry');
								}
								$subject_line .= ', '.$data['intro_works'];
							}
							$results[$key]['matches'][$i]['text'] = $subject_line;
							$results[$key]['matches'][$i]['intro_data'] = $intro;
						}
					}
					$results[$key]['user_info'] = $this -> model_user -> get_user_data($user['user_id']);
				}
			}
			$data['event_user_data'] = $results;
			$email_template = $this -> load -> view('event/print_matches_to_meet', $data);
		}		
	}
	
	
	/**
	 * upcoming_date_reminder: Send mail to both user before 24 hours.
	 * @access public
	 * @return
	 * @author Rajnish Savaliya
	 */
	public function upcoming_date_reminder() {
		$language_id = $this -> session -> userdata('sess_language_id');

		$common_sql = 'SELECT user_intro.*, user_date.*, date_time as time_cond, DATE_ADD(NOW(), INTERVAL 24 HOUR) as tommorow FROM user_intro
				JOIN user_date on user_intro.user_intro_id = user_date.user_intro_id
                JOIN date_type on date_type.date_type_id = user_date.date_type_id
                AND user_intro.user1_not_interested_time = "0000-00-00 00:00:00"
				AND user_intro.user2_not_interested_time = "0000-00-00 00:00:00"
                ';
		$query = $common_sql . '
				AND date_time <  DATE_ADD(NOW(), INTERVAL 24 HOUR)
				AND date_time >= NOW() 
		 		AND date_accepted_by_user_id > 0
                AND date_type.display_language_id=' . $language_id . '
                ORDER BY `intro_available_time` DESC ';
		$upcoming_intro = $this -> general -> sql_query($query);

		if ($upcoming_intro) {
			foreach ($upcoming_intro as $intro) {
				$user_id = $intro['user1_id'];
				$intro_id = $intro['user2_id'];

				$data['user_info'] = $this -> model_user -> get_user_data($intro_id);
				$user_email_data = $this -> model_user -> get_user_email($intro_id);

				$data['user_data'] = $this -> model_user -> get_user_data($user_id);
				$data['email_title'] = 'Hello ' . $data['user_info']['first_name'];
				$data['email_content'] = 'You have a date with ' . $data['user_data']['first_name'] . ' on ' . date('l, F j @ ga', strtotime($intro['date_time']));

				$email_template = $this -> load -> view('email/date_suggestion', $data, true);

				if ($this -> datetix -> mail_to_user($user_email_data['email_address'], 'Reminder: ' . $data['email_content'], $email_template)) {
					echo '<br/> Upcoming Email Sent to ' . $data['user_info']['first_name'];
					echo $email_template;

					$user_email_data = $this -> model_user -> get_user_email($user_id);
					$data['email_title'] = 'Hello ' . $data['user_data']['first_name'];
					$data['email_content'] = 'You have a date with ' . $data['user_info']['first_name'] . ' on ' . date('l, F j @ ga', strtotime($intro['date_time']));
					$email_template = $this -> load -> view('email/date_suggestion', $data, true);
					if ($this -> datetix -> mail_to_user($user_email_data['email_address'], 'Reminder: ' . $data['email_content'], $email_template)) {
						echo '<br/> Upcoming Email Sent to ' . $data['user_data']['first_name'];
						echo $email_template;
					}
				}
			}
		} else {
			echo '<br/><br/><h1>no upcoming intros in 24 hour</h1>';
		}

	}
	
	/**
	 * - For dates that expired with 2 or less applicants and where user didn't choose any applicant, and which have date.is_refunded=0, 
	 * 	add back date tickets to user.num_date_tix and set date.is_refunded=1
	 * @access public
	 * @return : NULL
	 * @author Rajnish Savaliya
	 */
	 function refund_date_tickets()
	 {
	 	$sql = 'SELECT d.*
	 			FROM date as d 
	 			WHERE d.date_time < NOW()
				AND d.completed_step >= '.REQUIRED_DATE_COMPLETED_STEP.'
				AND (SELECT COUNT(date_applicant_id) FROM date_applicant as da WHERE da.date_id = d.date_id AND is_chosen = "1") < 1
				AND (SELECT COUNT(date_applicant_id) FROM date_applicant as da WHERE da.date_id = d.date_id) <= 2
				AND d.status = "1"
				AND d.is_refunded = "0"';
		
		if($refundable_dates = $this -> general -> sql_query($sql))
		{
			//echo "<pre>";print_r($refundable_dates);exit;				
			foreach($refundable_dates  as $date)
			{
				
				$user_id = $date['requested_user_id'];
				
				//fetch user account data
				$this->general->set_table('user');
            	$user_data = $this->general->get("num_date_tix",array('user_id'=>$user_id));
				
				echo $this->db->last_query().'<br>';
				
				//udpate user account
				$data['num_date_tix']= $user_data['0']['num_date_tix']+$date['num_date_tickets'];
                $this->model_user->update_user($user_id,$data);
				echo $this->db->last_query().'<br>';
				
				//set is_refunded flag to date tale
				$this->general->set_table('date');
				$date_update['is_refunded'] = "1";
				$date_update['status'] = "2";
				$date_update['refund_reason'] = "Less than 3 applicants";
				
            	$this->general->update($date_update,array('date_id'=>$date['date_id']));
				echo $this->db->last_query().'<br>';				
				exit;				
			}
		}		
		//echo "<pre>";print_r($refundable_dates);exit;
		
	 }
	 
	/**
	 * save_fb_friends: Store Facebook friends in database
	 * @access public
	 * @return
	 * @author Rajnish Savaliya
	 */
	public function save_fb_friends() {
		$this -> general -> set_table('user');
		$users = $this -> general -> get("user_id,first_name,last_name,facebook_id", array('facebook_id != ' => ''));
		if ($users) {
                    
                    
                        // added by jigar oza
                        $this->general->set_table('user');
                        $updateData['matchmaker_id']='0';
                        $this->general->update($updateData,array('matchmaker_id'=>'1'));
                        
                        $SQL='UPDATE user set matchmaker_id=1
                                WHERE mobile_phone_number IS NOT NULL
                                AND approved_date is NOT NULL
                                AND matchmaker_id=0 
                                AND current_city_id=260
                                AND user_id in (SELECT distinct (user_id) from user_intro_chat)
                                AND user_id in (SELECT distinct (user_id) from user_photo)
                                AND user_id in (SELECT distinct (user_id) from user_school)
                                AND user_id in (SELECT distinct (user_id) from user_job)';
                        $this -> db -> query($SQL)->result_array();	
                        
                        
                        foreach ($users as $user) {
				try {
					$friends = $this -> facebook -> api('/' . $user['facebook_id'] . '/friends');
					if (!empty($friends["data"])) {
						echo "<br/><strong> -->" . $user['first_name'] . " " . $user['last_name'] . "'s new inserted friends <-- </strong>";

						foreach ($friends["data"] as $friend) {
							$fb_friend_data["user_id"] = $user['user_id'];
							$fb_friend_data["facebook_id"] = $friend["id"];
							$this -> general -> set_table('user_fb_friend');
							if (!$this -> general -> checkDuplicate($fb_friend_data)) {
								if ($this -> general -> save($fb_friend_data)) {
									echo '<br/><a target="_blank" href="http://www.facebook.com/' . $friend['id'] . '">' . $friend['name'] . '</a>';
								}
							}
						}
						echo "</hr>";
					}
				} catch (FacebookApiException $e) {
				}
			}
		}
	}

	/**
	 * Import user data 
	 * @access public
	 * @return
	 * @author Rajnish Savaliya
	 */
	public function import_fb_data() {
		$this -> general -> set_table('user');
		$users = $this -> general -> get("user_id,first_name,last_name,facebook_id,applied_date", array(),array('applied_date'=>'desc'),50);
		echo "<pre>";print_r($users);exit;
		
		if ($users) {
			foreach ($users as $user) {
				try {
					$fb_user = $this -> facebook -> api('/' . $user['facebook_id']);
					
					
					echo "<pre>";print_r($fb_user);
					
					
					
				} catch (FacebookApiException $e) {
				}
			}
		}
	}

	/**
	 * user_intros: Cron Job is based on given pseudo code.
	 * param: $is_event = "event" then use event_user table.
	 * @access public
	 * @return Mail.
	 * @author Rajnish Savaliya
	 */
	public function user_intros($event_id = "") {
		
		$this -> send_intro_mail();

		//Auto-Reminder cron job
		$this -> upcoming_date_reminder();

		//$this->db->empty_table('user_intro');
		
		//echo $this->db->last_query();
		$language_id = $this -> session -> userdata('sess_language_id');
		/*

		 For each user in database who:
		 1) has signed in at least once in past 12 months,
		 2) has a response rate of at least 10% after 10 intros (either suggested/accepted a date or clicked “Not Interested”)
		 3) has <=3 (free users) or <=10 (premium users) upcoming intros in user_intro table [NEW] free users) or <=10 (premium users) upcoming intros (intro_available_time > now()) in user_intro table
		 */
		 
		$select_user_field = "user.user_id, first_name, last_name, gender_id, password, current_city_id, facebook_id, applied_date, approved_date, last_intro_generated";
		//$usr_condition = 'last_active_time >= ' . date("Y-m-d", strtotime("-2 month", time())) . ' AND approved_date IS NOT NULL AND completed_application_step>=6';
		$usr_condition = 'last_active_time >= now()-interval 12 month AND approved_date IS NOT NULL AND completed_application_step>=6 AND is_contact=1';
		$user_limit_and_order = " ORDER BY last_intro_generated ASC LIMIT ".CRON_USER_LIMIT;
		//$usr_condition .= ' AND user_id IN(119,557)';
		/*
		 *  last introduce 100 users and compare with all database users and update last_intro_generated date to at the end.
		 */ 
		
		if($event_id != "")
		{
			$event_user_sql = "SELECT event_user.*, ".$select_user_field." FROM user 
					JOIN event_user on event_user.user_id = user.user_id 
					WHERE ".$usr_condition." AND event_user.event_id = ".$event_id;
			
			$compare_with_users = $this -> general -> sql_query($event_user_sql);
			$users = $this -> general -> sql_query($event_user_sql."".$user_limit_and_order);
		}
		else
		{
			$this -> general -> set_table('user');
			//$compare_with_users = $this -> general -> custom_get($select_user_field, $usr_condition);
			//$users = $this -> general -> custom_get("user_id, ".$select_user_field, $usr_condition."".$user_limit_and_order);
                        // compare with other user not require here - jigar
			//$compare_with_users = $this -> general -> sql_query("SELECT ".$select_user_field." FROM user JOIN user_email ON user_email.user_id=user.user_id WHERE ".$usr_condition);
			$users = $this -> general -> sql_query("SELECT ".$select_user_field." FROM user JOIN user_email ON user_email.user_id=user.user_id WHERE ".$usr_condition."".$user_limit_and_order);
		}
		
		if ($users) {
			foreach ($users as $user) {
                                
                                if($event_id != "")
                                {
                                        // not need for event
                                }
                                else
                                {
                                        $this -> general -> set_table('user');
                                        $usr_condition .= " AND (website_id='".$user['website_id']."' OR external_intros=1)";
                                        $compare_with_users = $this -> general -> sql_query("SELECT ".$select_user_field." FROM user JOIN user_email ON user_email.user_id=user.user_id WHERE ".$usr_condition);
                                        
                                }
                            
				//2nd condition in psuedo
				$where = '(user1_id = "' . $user['user_id'] . '" OR user2_id = "' . $user['user_id'] . '") ';
				$this -> general -> set_table('user_intro');
				$user_intros = $this -> general -> custom_get("user_intro_id,user1_id,user2_id,intro_available_time,user1_not_interested_time,user2_not_interested_time", $where);
				$total_user_intros = count($user_intros);
				$req_intro_response = $total_user_intros * 10 / 100;
				$user_city = $this -> model_city -> get($user['current_city_id'], $language_id);

				if ($user_intros) {
					foreach ($user_intros as $key => $intro) {
						//2nd condition in psuedo :: Remove User intros which is not interested by user
						if ($intro['user1_id'] == $user['user_id'] && $intro['user1_not_interested_time'] != '0000-00-00 00:00:00') {
							unset($user_intros[$key]);
						}

						//2nd condition in psuedo :: Remove User intros which is not interested by user
						if ($intro['user2_id'] == $user['user_id'] && $intro['user2_not_interested_time'] != '0000-00-00 00:00:00') {
							unset($user_intros[$key]);
						}
					}

					/////   3rd Condition in pseudo: has <=3 (free users) or <=10 (premium users) upcoming intros (intro_available_time > now()) in user_intro table//
					$up_where = 'DATE(intro_available_time) > CURDATE()  AND (user1_id = "' . $user['user_id'] . '" OR user2_id = "' . $user['user_id'] . '") ';
					$this -> general -> set_table('user_intro');
					$user_upcoming_intros = $this -> general -> custom_get("user_intro_id", $up_where);
					if($event_id == "")
					{
						if ($this -> datetix -> is_premium_user($user['user_id'], PERMISSION_MORE_INTRODUCTIONS)) {
							if (count($user_upcoming_intros) > MAX_PAID_UPCOMING_INTROS) {
								echo '<br/>' . $user['first_name'] . ' ' . $user['last_name'] . ' has reached 10 upcoming intro limit for a premium user';
								continue;
							}
						} else {
							if (count($user_upcoming_intros) > MAX_FREE_UPCOMING_INTROS) {
								echo '<br/>' . $user['first_name'] . ' ' . $user['last_name'] . ' has reached 3 upcoming intro limit for a free user';
								continue;
							}
						}
					}
					
					///// UPCOMING INTRO COMPLETE ////////////////

					$intro_response = count($user_intros);
					//count response after 10 intros && check response is minimum 10%?
					//if ($total_user_intros > 10 && $intro_response < $req_intro_response) {
					//	echo '<br/>' . $user['first_name'] . ' ' . $user['last_name'] . ' has reached 10% intro limit';
					//	continue;
					//}

				}

				$user_intro_score = array();
				$user_intro_score_data = array();
				$intro_with_user_score_data = array();
				$total_intro_score = 0;
				
				foreach ($compare_with_users as $cur_user) {

					$is_friend = 0;
					//Check is friend or not [If not friends (0) then satisfy our condition and go ahead]
					if ($user['facebook_id'] && $cur_user['facebook_id']) {						
						try{
								
							$friends = $this -> facebook -> api('/' . $user['facebook_id'] . '/friends?uid=' . $cur_user['facebook_id']);
							if ($friends['data']) {
								$is_friend = 1;
							}
						}
						catch(exception $e){
							echo  "FB_Exeption".$e."<br/>";
						}
					}
					//END CONDITION
					///////////////////           Gender Preference ////////////////
					$user_want_gender = $this -> datetix -> user_want($user['user_id'], "gender");

					$is_user_gender_match = 0;
					if ($user_want_gender) {
						foreach ($user_want_gender as $gender) {
							if ($gender['gender_id'] == $cur_user['gender_id']) {
								$is_user_gender_match = 1;
							}
						}
					}

					$cur_user_want_gender = $this -> datetix -> user_want($cur_user['user_id'], "gender");

					$is_cur_user_gender_match = 0;
					if ($cur_user_want_gender) {
						foreach ($cur_user_want_gender as $gender) {
							if ($gender['gender_id'] == $user['gender_id']) {
								$is_cur_user_gender_match = 1;
							}
						}
					}

					if (!$is_user_gender_match || !$is_cur_user_gender_match) {
						echo '<br/>' . $user['first_name'] . ' ' . $user['last_name'] . ' not gender match with ' . $cur_user['first_name'] . ' ' . $cur_user['last_name'];
						continue;
					}

					if (($user['user_id'] != $cur_user['user_id']) && ($is_friend == 0) && ($user['current_city_id'] && ($user['current_city_id'] == $cur_user['current_city_id']))) {
						$get_intro_cond = array();
						
						$cur_user_where = '(user1_id = "' . $user['user_id'] . '" AND user2_id = "' . $cur_user['user_id'] . '") OR (user1_id = "' . $cur_user['user_id'] . '" AND user2_id = "' . $user['user_id'] . '")';						
						$this -> general -> set_table('user_intro');
						$user_match_intros = $this -> general -> custom_get("user_intro_id", $cur_user_where);
						
						if ($user_match_intros && $event_id == "") {
							echo '<br/>' . $user['first_name'] . ' ' . $user['last_name'] . ' is already introduced with ' . $cur_user['first_name'] . ' ' . $cur_user['last_name'];
							continue;
						} else {
							
							//--- 3 Condition as above ----/
							//2nd condition in psuedo
							$where = '(user1_id = "' . $cur_user['user_id'] . '" OR user2_id = "' . $cur_user['user_id'] . '") ';
							$this -> general -> set_table('user_intro');
							$cur_user_intros = $this -> general -> custom_get("user_intro_id,user1_id,user2_id,intro_available_time,user1_not_interested_time,user2_not_interested_time", $where);
							
							$total_cur_user_intros = count($cur_user_intros);
							$req_cur_intro_response = $total_cur_user_intros * 10 / 100;
							if ($cur_user_intros) {
								foreach ($cur_user_intros as $key => $intro) {
									//2nd condition in psuedo :: Remove User intros which is not interested by user
									if ($intro['user1_id'] == $user['user_id'] && $intro['user1_not_interested_time'] != '0000-00-00 00:00:00') {
										unset($cur_user_intros[$key]);
									}
									//2nd condition in psuedo :: Remove User intros which is not interested by user
									if ($intro['user2_id'] == $user['user_id'] && $intro['user2_not_interested_time'] != '0000-00-00 00:00:00') {
										unset($cur_user_intros[$key]);
									}
								}

								$all_cur_users = array();
								/////   3rd Condition in pseudo: has <=3 (free users) or <=10 (premium users) upcoming intros (DATE(intro_available_time) <= CURDATE() ) in user_intro table//
								$up_where = ' DATE(intro_available_time) > CURDATE() AND ';
								$up_where .= '(user1_id = "' . $cur_user['user_id'] . '" OR user2_id = "' . $cur_user['user_id'] . '") ';
								$this -> general -> set_table('user_intro');
								$cur_user_upcoming_intros = $this -> general -> custom_get("user_intro_id", $up_where);

								if($event_id == "")
								{					
									if ($this -> datetix -> is_premium_user($cur_user['user_id'], PERMISSION_MORE_INTRODUCTIONS)) {
										if (count($cur_user_upcoming_intros) > MAX_PAID_UPCOMING_INTROS) {
											echo '<br/>' . $cur_user['first_name'] . ' ' . $cur_user['last_name'] . ' has reached 10 upcoming intro limit for a premium user';
											continue;
										}
									} else {
										if (count($cur_user_upcoming_intros) > MAX_FREE_UPCOMING_INTROS) {
											echo '<br/>' . $cur_user['first_name'] . ' ' . $cur_user['last_name'] . ' has reached 3 upcoming intro limit for a free user';
											continue;
										}
									}
								}
								
								////////////////// END UPCOMING INTRO OF CURRENT USER ////
								$cur_intro_response = count($cur_user_intros);
								//Live in same city

								//count response after 10 intros && check response is minimum 10%?
								if (($total_cur_user_intros > 10 && $cur_intro_response < $req_cur_intro_response)) {
									echo '<br/>' . $cur_user['first_name'] . ' ' . $cur_user['last_name'] . ' has not replied to more than 10% of his/her intros';
									continue;
								}
							}

							echo '<br/> <h3>' . $user['first_name'] . ' ' . $user['last_name'] . ' intro starting with ' . $cur_user['first_name'] . ' ' . $cur_user['last_name'] . '</h3>';
							$user_compitiblity = $this -> datetix -> calculate_score($cur_user['user_id'], $user['user_id']);

							echo '<br/> <h3> NOW INTRO USER ' . $cur_user['first_name'] . ' ' . $cur_user['last_name'] . ' Compare with ' . $user['first_name'] . ' ' . $user['last_name'] . '</h3>';
							$intro_compitiblity = $this -> datetix -> calculate_score($user['user_id'], $cur_user['user_id']);

							if ($intro_compitiblity['score'] >= 1 && $user_compitiblity['score'] >= 1) {
								$user_intro_score[$cur_user['user_id']] = $intro_compitiblity['score'] + $user_compitiblity['score'];
								$user_intro_score_data[$cur_user['user_id']] = $user_compitiblity['match_data'];
								$intro_with_user_score_data[$cur_user['user_id']] = $user_compitiblity['match_data'];

								echo '<br/> <h2>Intro Successed :: ' . $intro_compitiblity['score'] . ' &&  ' . $user_compitiblity['score'] . '</h2>';
							} else {
								echo '<br/> <h2>Score is not useful for sending mail.' . $intro_compitiblity['score'] . ' &&  ' . $user_compitiblity['score'] . '</h2>';
							}

						}//not same user in loop

					}//Not yet intro $user_match_intros
					else {
						echo '<br/>' . $user['first_name'] . ' ' . $user['last_name'] . ' have not match crite area city, fb etc with ' . $cur_user['first_name'] . ' ' . $cur_user['last_name'];
					}
				}
				
				
				if ($user_intro_score) {
					$max_score = max($user_intro_score);
					// > 0
					if ($max_score) {
						echo '<br/> <h3>Eligibled Score will be save in database: ' . $max_score . '</h3>';
						//Now First Mail to user regarding intro user
						$intro_id = array_search($max_score, $user_intro_score);
						//$match_criteara = $user_intro_score_data[$intro_id];

						
						$this -> general -> set_table('user');
						if($intros_data = $this -> general -> get("user_id", array('user_id' => $intro_id)))
						{
							$intro_user = $intros_data['0'];

							$event_debug_msg = "";
							if($event_id != "")
							{
								//No need to pass language id in event condition.
								$this -> general -> set_table('event');
								if($event_data = $this->general->get("event_id,event_name,event_start_time",array('event_id'=>$user['event_id'])))
								{
									$event_info = $event_data['0'];
									//$set_intros_data['intro_available_time'] = $event_info['event_start_time'];
									$set_intros_data['intro_available_time'] = calculate_current_time($user_city -> time_zone_val);
									$set_intros_data['event_id'] = $user['event_id'];
									
									$event_debug_msg = "<br/><h1 style='color:red'> EVENT NAME : ".$event_info['event_name']."</h1>";
								}
								else
								{
									$event_debug_msg = "<br/><h1 style='color:red'> OMG!! Wrong entry in event_user with event_id : ".$user['event_id']."</h1>";
								}
							}
							else
							{
								//If at least one of the two users matched is a premium user, then set intro_available_time to today
								//Logic has now been changed so that even intros for users with Instant Introductions upgrade are not availble immediately. But premium users can choose to make any upcoming intro available immediately.
								if ($this -> datetix -> is_premium_user($intro_user['user_id'], PERMISSION_INSTANT_INTRO) || $this -> datetix -> is_premium_user($user['user_id'], PERMISSION_INSTANT_INTRO)) {
									//Set intro_available_time = intro_created_time + 7 even for users with Instant Introductions upgrade
									$set_intros_data['intro_available_time'] = calculate_current_time($user_city -> time_zone_val, '+2 day');
								} else {
									$set_intros_data['intro_available_time'] = calculate_current_time($user_city -> time_zone_val, '+2 day');
								}
							}
							
							$this -> general -> set_table('user_intro');
							if($is_intro_already_exist = $this -> general -> custom_get("user_intro_id", '(user1_id = "' . $user['user_id'] . '" AND user2_id = "' . $intro_user['user_id'] . '") OR (user1_id = "' . $intro_user['user_id'] . '" AND user2_id = "' . $user['user_id'] . '")'))
							{
								$set_intros_data['user1_not_interested_time'] = '0000-00-00 00:00:00';
								$set_intros_data['user2_not_interested_time'] = '0000-00-00 00:00:00';
								$set_intros_data['user1_not_interested_reason'] = '';
								$set_intros_data['user2_not_interested_reason'] = '';
								
								echo $event_debug_msg ;
								if ($user_intro_id = $this -> general -> update($set_intros_data,array('user_intro_id'=>$is_intro_already_exist['0']['user_intro_id']))) {
									echo "<br/>Intro updated successfully.<br/><br/>";			
								}
								else
								{
									echo "<br/>No Change in record.<br/><br/>";
								}
								print_r($set_intros_data);
							}
							else
							{
								$set_intros_data['intro_created_time'] = calculate_current_time($user_city -> time_zone_val);
								$set_intros_data['intro_expiry_time'] = calculate_current_time($user_city -> time_zone_val, "+200 day");
								$set_intros_data['user1_id'] = $user['user_id'];
								$set_intros_data['user2_id'] = $intro_user['user_id'];
								//$set_intros_data['intro_email_sent_time'] = NULL;
								
								if ($user_intro_id = $this -> general -> save($set_intros_data)) {
									echo $event_debug_msg ;
									echo "<h1 style='color:red'>Intro inserted successfully</h1>";
									print_r($set_intros_data);
								}
							}
						}
						else
						{
							echo '<br/> No such intro exist with id='.$intro_id;
						}
											
					}
				}
				
				//update last intro sent
				$this -> general -> set_table('user');
				$this -> general -> update(array('last_intro_generated'=>SQL_DATETIME),array('user_id'=>$user['user_id']));

			}//End users loop
		}//End users if
	}

	/**
	 * Sending a Mail to past intros where email is not sent.
	 * @access public
	 * @return Mail
	 * @author Rajnish Savaliya
	 */
	function send_intro_mail() {
		
		//CLIENT COMMENT :: DEBUG :: NEED TO CHANGE '<=' CURDATE()
		$email_where = 'DATE(intro_available_time) <= CURDATE() AND (handpicked_time IS NULL OR handpicked_time = "0000-00-00 00:00:00") AND intro_email_sent_time < "2000-01-01"';
		$this -> general -> set_table('user_intro');
		$intros_data = $this -> general -> custom_get("*", $email_where);
		
		if ($intros_data)
		{			
			$user_where = "(last_intro_mail_sent IS NULL OR last_intro_mail_sent <  DATE_SUB(NOW(), INTERVAL 6 HOUR) OR user_id=1)";
			foreach ($intros_data as $intro) {
				$this -> general -> set_table('user');	
				$tmp_users = $this -> general -> custom_get("user_id", $user_where.' AND user_id = '.$intro['user1_id']);
				$tmp_users2 = $this -> general -> custom_get("user_id", $user_where.' AND user_id = '.$intro['user2_id']);
				//$tmp_users2 = $this -> general -> custom_get("DATE_SUB(NOW(), INTERVAL 24 HOUR) as compare_date, user_id, last_intro_mail_sent", $user_where.' AND user_id = '.$intro['user1_id']);
								
				if($tmp_users && $tmp_users2)
				{
					$this -> datetix -> intro_mail($intro['user2_id'], $intro['user1_id']);
					$this -> datetix -> intro_mail($intro['user1_id'], $intro['user2_id']);
					//$this -> datetix -> intro_mail($intro['user1_id'], $intro['user2_id'], array(), 'user');	
				}
			}
		} else {
			echo '<br/>No intros exist in database.';
		}
	}	
}
?>
