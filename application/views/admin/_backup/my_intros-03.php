<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class My_intros extends CI_Controller {

	var $language_id = '1';
	var $user_id = '';
	public function __construct() {
		parent::__construct();
		$this -> load -> model('model_user');
		$this -> load -> model('general_model', 'general');
		set_time_limit(20000);
		if ($user_id = $this -> session -> userdata('user_id')) {
			$this -> model_user -> update_user($user_id, array('last_active_time' => SQL_DATETIME));
			$this -> user_id = $this -> session -> userdata('user_id');
			$this -> language_id = $this -> session -> userdata('sess_language_id');
		} else {
			echo '<script type="text/javascript">window.location.href = "' . base_url().url_city_name(). '/signin.html?return_url=/intros"</script>';
			exit;
		}
	}

	public function test() {
		//common SQL Query for all the intros
		$common_sql = 'SELECT user_intro.user_intro_id, max(user_intro_chat_id) as user_intro_chat_id,
				user.first_name as intro_name, 
				user.user_id
				
				FROM user_intro
				LEFT JOIN user_intro_chat on user_intro_chat.user_intro_id = user_intro.user_intro_id
				JOIN user on user.user_id = CASE 
					WHEN user_intro.user1_id = "' . $this -> user_id . '" THEN user_intro.user2_id
					WHEN user_intro.user2_id = "' . $this -> user_id . '" THEN user_intro.user1_id
				END
				WHERE (user1_id = "' . $this -> user_id . '" OR user2_id = "' . $this -> user_id . '") ';

			$query = $common_sql . 'AND DATE(intro_expiry_time) >= DATE(CURDATE()) AND DATE(intro_available_time) <= DATE(CURDATE()) group by user_intro.user_intro_id
        		ORDER BY user_intro_chat_id DESC';
			$intros_data  = $this -> general -> sql_query($query);
		echo "<pre>";print_r($intros_data);exit;
		//SQL_DATE
		
	}

	/**
	 * Index Function :: Display current user's Intro data
	 * @access public
	 * @return View data
	 * @author Rajnish Savaliya
	 */
	public function index($intro_type = 'active') {
		$user_id = $this -> session -> userdata('user_id');
		$language_id = $this -> session -> userdata('sess_language_id');

		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');

		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $user_id));
		$user = $user_data['0'];

		if ($this -> input -> post('page_no')) {
			$page_no = $this -> input -> post('page_no');
		} else {
			$page_no = 1;
		}
		
		/*
		 if ($this -> session -> userdata('redirect_intro_id')) {
				//Fetch all data redirect to particular intro
				$data['page_no'] = 0;
				$limit = '';
			} else {
				$data['page_no'] = $page_no;
				
			}
		 */ 
		 $data['page_no'] = $page_no;
		$limit = ' LIMIT ' . ($page_no - 1) * PER_PAGE . ', ' . PER_PAGE;
		//common SQL Query for all the intros
		$common_sql = 'SELECT user_intro.*, MAX(user_intro_chat.user_intro_chat_id) as user_intro_chat_id,
				user.first_name as intro_name, user.height, user.gender_id, user.ethnicity_id, user.body_type_id, user.privacy_photos,
				user.user_id, user.facebook_id, user.birth_date, user.current_city_id,
				CASE
					WHEN
						birth_date != "0000-00-00" 	
					THEN 
						TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
				END as intro_age
				
				FROM user_intro
				LEFT JOIN user_intro_chat on user_intro_chat.user_intro_id = user_intro.user_intro_id
				JOIN user on user.user_id = CASE 
					WHEN user_intro.user1_id = "' . $this -> user_id . '" THEN user_intro.user2_id
					WHEN user_intro.user2_id = "' . $this -> user_id . '" THEN user_intro.user1_id
				END
				WHERE (user1_id = "' . $this -> user_id . '" OR user2_id = "' . $this -> user_id . '") ';

		if($intro_type == 'expired')
		{
			$query = $common_sql . 'AND DATE(intro_expiry_time) < DATE(CURDATE()) group by user_intro.user_intro_id
                ORDER BY user_intro_chat_id DESC, `intro_available_time` DESC ' . $limit;
                $intros_data  = $this -> general -> sql_query($query);
				
		}
		elseif($intro_type == 'upcoming')
		{
			$query = $common_sql . 'AND DATE(intro_expiry_time) >= DATE(CURDATE()) AND DATE(intro_available_time) > DATE(CURDATE()) group by user_intro.user_intro_id
                ORDER BY user_intro_chat_id DESC, `intro_available_time` DESC ' . $limit;
			$intros_data  = $this -> general -> sql_query($query);
				
		}
		else{
			//$query = $common_sql . 'AND DATE(intro_expiry_time) >= DATE(CURDATE()) AND DATE(intro_available_time) <= DATE(CURDATE()) group by user_intro.user_intro_id
			//        		ORDER BY user_intro_chat_id DESC, `intro_available_time` DESC' . $limit;
			$query = $common_sql . 'group by user_intro.user_intro_id
									        		ORDER BY user_intro_chat_id DESC, `intro_available_time` DESC' . $limit;
			$intros_data  = $this -> general -> sql_query($query);
			//echo "<pre>";print_r($query); exit;
			//echo $query;
			
		}
		//echo "<pre>";print_r($intros_data);exit;
		//SQL_DATE

		$result = array();
		if ($intros_data) {
			foreach ($intros_data as $intro) {
				$tmp_intro = $intro;
				
				//FETCH DATE data :::
				//$this->general->set_table('user_date');
				//$tmp_intro['date_data'] = $this->general->get("",array('user_intro_id'=>$tmp_intro ['user_intro_id']));

				$query = 'SELECT user_date.*, date_type.description as date_type_desc
				FROM user_date 
                JOIN date_type on date_type.date_type_id = user_date.date_type_id
                
                WHERE 
                date_type.display_language_id = ' . $this -> language_id . '
                AND user_intro_id = ' . $tmp_intro['user_intro_id'];
				//$date_data = $this -> general -> sql_query($query);

				if ($date_data) {
					$date_data = $date_data['0'];
					$date_data['venue_dates'] = array();
					if ($date_data['venue_id']) {
						$venue_sql = ' SELECT  venue.*,  neighborhood.*, neighborhood.description as neighborhood_desc
						From venue 
						JOIN neighborhood on neighborhood.neighborhood_id = venue.neighborhood_id
		                WHERE venue.display_language_id=' . $this -> language_id . '
		                AND neighborhood.display_language_id=' . $this -> language_id . '
		                AND venue.venue_id = "' . $date_data['venue_id'] . '"
		                ORDER BY venue.view_order DESC';

						//if ($venue_row_data = $this -> general -> sql_query($venue_sql)) {
						//	$date_data = array_merge($date_data, $venue_row_data['0']);
						//}

						$query = 'SELECT * FROM venue_date_type
						JOIN date_type on date_type.date_type_id = venue_date_type.date_type_id
						WHERE date_type.display_language_id=' . $this -> language_id . '
	                	AND venue_date_type.venue_id = "' . $date_data['venue_id'] . '"
	                	Group By date_type.date_type_id
	                	ORDER BY `view_other` ASC';

						//if ($venue_date = $this -> general -> sql_query($query)) {
						//	foreach ($venue_date as $value) {
							//	$date_data['venue_dates'][] = $value['description'];
							//}
						//}
					} else {
						$date_data['name'] = '';
						$date_data['address'] = '';
						$date_data['phone_number'] = '';
						$date_data['review_url'] = '';

						if ($date_data['venue_other']) {
							preg_match('/_(.*?)_/', $date_data['venue_other'], $display);
							if ($display) {
								$date_data['name'] = $display['1'];
								$venue_sql = ' SELECT  city.*, neighborhood.description as neighborhood_desc
											From neighborhood 
											JOIN city on city.city_id = neighborhood.city_id
							                
							                WHERE city.display_language_id=' . $this -> language_id . '
							                AND neighborhood.display_language_id=' . $this -> language_id . '
							                AND neighborhood.neighborhood_id ="' . $date_data['neighborhood_id'] . '"
							                ORDER BY neighborhood.view_order DESC';

								//$venue_row_data = $this -> general -> sql_query($venue_sql);
								//if ($venue_row_data) {
								//	$date_data['address'] = $venue_row_data['0']['neighborhood_desc'] . ', ' . $venue_row_data['0']['description'];
								//}
							} else {
								$city_id = $this -> session -> userdata('sess_city_id');
								$city_data = $this -> model_user -> get_city_by_id($city_id);

								$this -> load -> library('EpiFoursquare');
								$clientId = $this -> config -> item('clientID');
								$clientSecret = $this -> config -> item('clientSecret');
								$accessToken = $this -> config -> item('accessToken');

								$fsObj = new EpiFoursquare($clientId, $clientSecret, $accessToken);
								$fsObjUnAuth = new EpiFoursquare($clientId, $clientSecret);
								$para['v'] = date('Ymd');

								$venue = $fsObjUnAuth -> get('/venues/' . $date_data['venue_other'], $para);
								//echo "<pre>";print_r($venue);exit;
								if ($venue -> response) {
									$date_data['name'] = isset($venue -> response -> venue -> name) ? $venue -> response -> venue -> name : '';
									$date_data['address'] = isset($venue -> response -> venue -> location -> address) ? $venue -> response -> venue -> location -> address : '';
									$date_data['phone_number'] = isset($venue -> response -> venue -> contact -> phone) ? $venue -> response -> venue -> contact -> phone : '';
									$date_data['review_url'] = isset($venue -> response -> venue -> canonicalUrl) ? $venue -> response -> venue -> canonicalUrl : '';
								}
							}
						}
					}
					if (isset($date_data))
						$tmp_intro = array_merge($tmp_intro, $date_data);

				}
				if ($intro['user1_id'] == $this -> user_id) {
					$intro_id = $intro['user2_id'];
				}

				if ($intro['user2_id'] == $this -> user_id) {
					$intro_id = $intro['user1_id'];
				}
				
				//Fetch Intro data...
				$tmp_intro['user_score'] = $this -> datetix -> calculate_score($this -> user_id, $intro_id);
				$tmp_intro['intro_score'] = $this -> datetix -> calculate_score($intro_id, $this -> user_id);
				$tmp_intro['intro_score']['score'] = ($tmp_intro['intro_score']['score'] + $tmp_intro['user_score']['score']) / 2;

				$intro__want_gender = $this -> datetix -> user_want($intro['user_id'], "gender");
				$intro_sexual_status = '';
				if ($intro__want_gender) {
					if ($intro__want_gender[0]['gender_id'] == $intro['gender_id']) {
						if ($intro['gender_id'] == '1') {
							$intro_sexual_status = translate_phrase('Gay');
						} else {
							$intro_sexual_status = translate_phrase('Lesbian');
						}
					} else {
						$intro_sexual_status = translate_phrase('Straight');
					}

				} else {
					$intro_sexual_status = translate_phrase('Bisexual');
				}

				$tmp_intro['sexual_status'] = $intro_sexual_status;
				$tmp_intro['gender_id'] = $intro['gender_id'];
				if ($intro['height']) {
					$inches = $intro['height'] / 2.54;
					$tmp_intro['intro_height'] = intval($inches / 12) . '´' . $inches % 12;
				}
				
				$tmp_intro['upcoming_event_attendance'] = $this->datetix->intro_attendance_upcoming_event($intro_id,$intro['gender_id']);
				
				$tmp_intro['intro_ethnicity'] = $this -> datetix -> load_data_by_id('ethnicity', $intro['ethnicity_id']);
				$tmp_intro['intro_body_type'] = $this -> datetix -> load_data_by_id('body_type', $intro['body_type_id']);
				$tmp_intro['zodiac_sign'] = getStarSign(strtotime($intro['birth_date']));
				$tmp_intro['privacy_photos'] = $intro['privacy_photos'];
				$tmp_intro['view_profile_link'] = base_url() . 'user/user_info/' . $this -> utility -> encode($intro['user_id']) . '/' . $this -> utility -> encode($this -> user_id) . '/' . $user['password'];

				/*
				 $fields = array('uj.job_title','uj.show_company_name','ind.description as industry');
				 $from = 'user_job as uj';
				 $joins = array('industry as ind'=>array('ind.industry_id = uj.industry_id','INNER'));

				 */
				unset($condition);

				$job_data = $this -> datetix -> get_my_company_data($intro_id);
				if ($job_data) {
					/*
					 if($job_data['0']['job_function_id'] && isset($job_data['0']['job_function_data']))
					 {
					 $tmp_intro['intro_works'] = $job_data['0']['job_function_data']['description'];
					 }
					 else
					 {
					 $tmp_intro['intro_works'] = $job_data['0']['job_title'];
					 }
					 */

					$tmp_intro['intro_works'] = $job_data['0']['job_title'];
					if ($job_data['0']['show_company_name']) {
						$tmp_intro['intro_works'] .= translate_phrase(' at ') . $job_data['0']['company_name'];
					} elseif ($job_data['0']['industry_id'] && isset($job_data['0']['industry_description']) && $job_data['0']['industry_description']) {
						$tmp_intro['intro_works'] .= translate_phrase(' in ') . $job_data['0']['industry_description'] . ' ' . translate_phrase('industry');
					} else {

					}
				}

				$fields = array('sc.school_id', 'sc.school_name', 'sc.logo_url', 'ct.*');
				$from = "user_school as uc";
				$joins = array('school as sc' => array('uc.school_id = sc.school_id', 'INNER'), 'city as ct' => array('ct.city_id = sc.city_id', 'INNER'));
				unset($condition);
				$condition['uc.user_id'] = $intro['user_id'];
				$condition['sc.display_language_id'] = $this -> language_id;
				$condition['ct.display_language_id'] = $this -> language_id;
				$ordersby = 'sc.school_id asc';

				$school_datas = $this -> general -> multijoins($fields, $from, $joins, $condition, $ordersby, 'array');
				if ($school_datas) {
					$school_names = array();
					foreach ($school_datas as $school) {
						$school_names[] = $school['school_name'];
					}

					$study_txt = implode(', ', $school_names);
					$tmp_intro['intro_study'] = $study_txt;
				}

				//-------------------------------------------------------------------------------------------//
				//Current Lived in
				$fields = array('ct.description as city_description', 'prvnce.description as province_description', 'cntry.description as country_description', 'cntry.country_code', 'cntry.flag_url', 'crncy.currency_id', 'crncy.description as currency_description', );

				$from = 'city as ct';
				$joins = array('province as prvnce' => array('ct.province_id = prvnce.province_id', 'LEFT'), 'country as cntry' => array('prvnce.country_id = cntry.country_id', 'LEFT'), 'currency as crncy' => array('cntry.currency_id = crncy.currency_id ', 'LEFT'));

				$temp = $this -> model_user -> multijoins($fields, $from, $joins, array('ct.city_id' => $intro['current_city_id'], 'ct.display_language_id' => $this -> language_id, 'prvnce.display_language_id' => $this -> language_id, 'cntry.display_language_id' => $this -> language_id, 'crncy.display_language_id' => $this -> language_id));

				if ($temp) {
					$tmp_intro['intro_current_location'] = $temp['0'];
					unset($temp);
				}
				unset($where);

				//User Likes
				$interestJoins = array('user_interest' => array('interest.interest_id = user_interest.interest_id', 'inner'));
				$interestCondition = array('interest.display_language_id' => $this -> language_id, 'user_interest.user_id' => $this -> user_id);
				$userInterests = $this -> general -> multijoins_arr('interest.description as interest', 'interest', $interestJoins, $interestCondition, '', 'interest.view_order asc');

				$consolidatedUsersInterest = array();
				if (!empty($userInterests)) {
					foreach ($userInterests as $key => $value) {
						$consolidatedUsersInterest[] = $value['interest'];
					}
				}

				$introCondition = array('interest.display_language_id' => $this -> language_id, 'user_interest.user_id' => $intro['user_id']);
				$introInterests = $this -> general -> multijoins_arr('interest.description as interest', 'interest', $interestJoins, $introCondition, '', 'interest.view_order asc');

				$consolidatedIntroInterest = array();
				if (!empty($introInterests)) {
					foreach ($introInterests as $key => $value) {
						$consolidatedIntroInterest[] = $value['interest'];
					}
				}
				if ($common_likes = array_intersect($consolidatedUsersInterest, $consolidatedIntroInterest)) {
					$tmp_intro['common_likes'] = $common_likes;
				}

				//Fetch Mutual Friend
				if ($user['facebook_id'] && $intro['facebook_id']) {
					if ($mutual_friends = $this -> datetix -> fb_mutual_friend($this -> user_id, $intro_id)) {
						$tmp_intro['fb_mutual_friend'] = count($mutual_friends) > 1 ? count($mutual_friends) . ' ' . translate_phrase('Mutual Friends') : count($mutual_friends) . ' ' . translate_phrase('Mutual Friend');
					}
					/*
					 try {

					 $fb_id = $this->facebook->getUser();
					 if($fb_id)
					 {
					 $mutual_friends = $this->facebook->api('me/mutualfriends/'.$intro['facebook_id']);
					 if($mutual_friends['data'])
					 {
					 $tmp_intro['fb_mutual_friend'] = count($mutual_friends['data'])>1?count($mutual_friends['data']).' '.translate_phrase('Mutual Friends'):count($mutual_friends['data']).' '.translate_phrase('Mutual Friend');
					 }
					 }
					 else
					 {
					 $this->load->view('direct_fb_login');
					 }
					 } catch (Exception $e) {}
					 */
				}
				$tmp_intro['fb_id'] = $intro['facebook_id'];

				//Find Photos..
				$this -> general -> set_table('user_photo');
				if ($primary_photo = $this -> general -> get("", array('set_primary' => '1', 'user_id' => $intro['user_id']))) {
					$tmp_intro['primary_photo'] = base_url() . 'user_photos/user_' . $intro['user_id'] . '/' . $primary_photo['0']['photo'];
				} else {
					//echo $this->db->last_query();exit;
				}
				
				$tmp_intro['is_allowed_photo'] = "YES";
				if($tmp_intro['privacy_photos'] == "HIDE")
				{
					$photo_req_condition['user_id'] = $intro_id;
					$photo_req_condition['requested_by_user_id'] = $this -> user_id;
					$photo_req_condition['status'] = "1";
					if(!$this -> general -> checkDuplicate($photo_req_condition,'user_photo_request'))
					{
						$tmp_intro['is_allowed_photo'] = "NO";		
					}
				}
	
				if ($photo_cnt = $this -> general -> count_record(array('set_primary' => '0', 'user_id' => $intro['user_id']), "user_photo")) {
					$tmp_intro['other_photos'] = $photo_cnt;
				}

				// Chat History
				$this -> general -> set_table('user_intro_chat');
				$history_order_by['user_intro_chat_id'] = 'asc';
								
				$tmp_intro['chat_history'] = $this -> general -> get("", array('user_intro_id' => $tmp_intro['user_intro_id']),$history_order_by);
				
				if($tmp_intro['recent_chat'] = $this -> general -> get("", array('user_intro_id' => $tmp_intro['user_intro_id']),array('user_intro_chat_id'=>'desc'),1))
				{
					$tmp_intro['recent_chat'] = $tmp_intro['recent_chat']['0'];
				}
				//echo "<pre>";print_r($tmp_intro);exit;
				
				//if expired or not
				//if (date("Y-m-d", strtotime($intro['intro_expiry_time'])) < SQL_DATE) {
				//	$result['expired'][] = $tmp_intro;
				//} else {
					if (date("Y-m-d", strtotime($intro['intro_available_time'])) > SQL_DATE) {
						$result['upcoming'][] = $tmp_intro;
					} else {
						$result['active'][] = $tmp_intro;
					}
				//}

			}
		}

		//echo "<pre>";print_r($result);exit;
		$data['intros_data'] = $result;
		$data['user_data'] = $user;
		$data['page_title'] = translate_phrase('My Introduction');
		$data['page_name'] = 'user/intro/my_introduction';
		$data['return_page'] = 'my-intros.html';
		
		if($this->input->is_ajax_request())
		{
			$this -> load -> view('user/intro/' . $intro_type . '_intro', $data);	
		}
		else
		{
			$this -> load -> view('template/editProfileTemplate', $data);	
		}
	}

	/**
	 * [Ajax-call] load_intro Function :: load current user's Intro data with specific ascending order
	 * @access public
	 * @return View data
	 * @author Rajnish Savaliya
	 */
	public function load_intro($intro_type = 'active') {
		$sortby = $this -> input -> post('sort_by');
		$sort_order = 'ORDER BY user_intro_chat_id DESC, `intro_available_time` DESC';

		if ($sortby == 1) {
			$sort_order = 'ORDER BY `intro_available_time` DESC';
		} elseif ($sortby == 2) {
			$sort_order = 'ORDER BY `intro_available_time` ASC';
		} elseif ($sortby == 3) {
			$sort_order = 'ORDER BY `intro_age` ASC';
		} elseif ($sortby == 4) {
			$sort_order = 'ORDER BY `intro_age` DESC';
		} elseif ($sortby == 5) {
			$sort_order = 'ORDER BY `intro_name` ASC';
		} elseif ($sortby == 6) {
			$sort_order = 'ORDER BY `intro_name` DESC';
		}

		//PAGINATION
		if ($this -> input -> post('page_no')) {
			$page_no = $this -> input -> post('page_no');
		} else {
			$page_no = 1;
		}
		$data['page_no'] = $page_no;
		$limit = ' LIMIT ' . ($page_no - 1) * PER_PAGE . ', ' . PER_PAGE;

		//common SQL Query for all the intros
		$common_sql = 'SELECT user_intro.*,   MAX(user_intro_chat.user_intro_chat_id) as user_intro_chat_id,
				user.first_name as intro_name, user.height, user.gender_id, user.ethnicity_id, user.body_type_id, user.privacy_photos,
				user.user_id, user.facebook_id, user.birth_date, user.current_city_id,
				CASE
					WHEN
						birth_date != "0000-00-00" 	
					THEN 
						TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
				END as intro_age
				FROM user_intro
				LEFT JOIN user_intro_chat on user_intro_chat.user_intro_id = user_intro.user_intro_id
				
				JOIN user on user.user_id = CASE 
					WHEN user_intro.user1_id = "' . $this -> user_id . '" THEN user_intro.user2_id
					WHEN user_intro.user2_id = "' . $this -> user_id . '" THEN user_intro.user1_id
				END
				WHERE (user1_id = "' . $this -> user_id . '" OR user2_id = "' . $this -> user_id . '") ';

		if ($intro_type == 'expired') {
			$query = $common_sql . 'AND DATE(intro_expiry_time) < DATE(CURDATE())';
		} else if ($intro_type == 'upcoming') {
			$query = $common_sql . 'AND DATE(intro_expiry_time) >= DATE(CURDATE()) AND DATE(intro_available_time) > DATE(CURDATE())';
		} else {
		//	$query = $common_sql . 'AND DATE(intro_expiry_time) >= DATE(CURDATE()) AND DATE(intro_available_time) <= DATE(CURDATE())';
			$query = $common_sql . 'AND (DATE(intro_expiry_time) < DATE(CURDATE()) OR (DATE(intro_expiry_time) >= DATE(CURDATE()) AND DATE(intro_available_time) <= DATE(CURDATE())))';
		}

		$query .= ' group by user_intro.user_intro_id '.$sort_order . '' . $limit;

		$intros_data = $this -> general -> sql_query($query);
		//print_r($intros_data);exit;
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$user = $user_data['0'];

		$result = array();
		if ($intros_data) {
			foreach ($intros_data as $intro) {
				$tmp_intro = $intro;
				//FETCH DATE data :::
				//$this->general->set_table('user_date');
				//$tmp_intro['date_data'] = $this->general->get("",array('user_intro_id'=>$tmp_intro ['user_intro_id']));

				$query = 'SELECT user_date.*, date_type.description as date_type_desc
				FROM user_date 
                JOIN date_type on date_type.date_type_id = user_date.date_type_id
                
                WHERE 
                date_type.display_language_id = ' . $this -> language_id . '
                AND user_intro_id = ' . $tmp_intro['user_intro_id'];
				$date_data = $this -> general -> sql_query($query);

				if ($date_data) {
					$date_data = $date_data['0'];
					$date_data['venue_dates'] = array();
					if ($date_data['venue_id']) {
						$venue_sql = ' SELECT  venue.*,  neighborhood.*, neighborhood.description as neighborhood_desc
						From venue 
						JOIN neighborhood on neighborhood.neighborhood_id = venue.neighborhood_id
		                
		                WHERE venue.display_language_id=' . $this -> language_id . '
		                AND neighborhood.display_language_id=' . $this -> language_id . '
		                AND venue.venue_id ="' . $date_data['venue_id'] . '"
		                ORDER BY venue.view_order DESC';

						if ($venue_row_data = $this -> general -> sql_query($venue_sql))
							$date_data = array_merge($date_data, $venue_row_data['0']);

						$query = 'SELECT * FROM venue_date_type
						JOIN date_type on date_type.date_type_id = venue_date_type.date_type_id
						WHERE date_type.display_language_id=' . $this -> language_id . '
	                	AND venue_date_type.venue_id = "' . $date_data['venue_id'] . '"
	                	Group By date_type.date_type_id
	                	ORDER BY `view_other` ASC';

						if ($venue_date = $this -> general -> sql_query($query)) {
							foreach ($venue_date as $value) {
								$date_data['venue_dates'][] = $value['description'];
							}
						}
					} else {
						$date_data['name'] = '';
						$date_data['address'] = '';
						$date_data['phone_number'] = '';
						$date_data['review_url'] = '';

						if ($date_data['venue_other']) {
							preg_match('/_(.*?)_/', $date_data['venue_other'], $display);
							if ($display) {
								$date_data['name'] = $display['1'];
								$venue_sql = ' SELECT  city.*, neighborhood.description as neighborhood_desc
											From neighborhood 
											JOIN city on city.city_id = neighborhood.city_id
							                
							                WHERE city.display_language_id=' . $this -> language_id . '
							                AND neighborhood.display_language_id=' . $this -> language_id . '
							                AND neighborhood.neighborhood_id ="' . $date_data['neighborhood_id'] . '"
							                ORDER BY neighborhood.view_order DESC';

								$venue_row_data = $this -> general -> sql_query($venue_sql);
								if ($venue_row_data) {
									$date_data['address'] = $venue_row_data['0']['neighborhood_desc'] . ', ' . $venue_row_data['0']['description'];
								}
							} else {
								$city_id = $this -> session -> userdata('sess_city_id');
								$city_data = $this -> model_user -> get_city_by_id($city_id);

								$this -> load -> library('EpiFoursquare');
								$clientId = $this -> config -> item('clientID');
								$clientSecret = $this -> config -> item('clientSecret');
								$accessToken = $this -> config -> item('accessToken');

								$fsObj = new EpiFoursquare($clientId, $clientSecret, $accessToken);
								$fsObjUnAuth = new EpiFoursquare($clientId, $clientSecret);
								$para['v'] = date('Ymd');

								$venue = $fsObjUnAuth -> get('/venues/' . $date_data['venue_other'], $para);
								//echo "<pre>";print_r($venue);exit;
								if ($venue -> response) {
									$date_data['name'] = isset($venue -> response -> venue -> name) ? $venue -> response -> venue -> name : '';
									$date_data['address'] = isset($venue -> response -> venue -> location -> address) ? $venue -> response -> venue -> location -> address : '';
									$date_data['phone_number'] = isset($venue -> response -> venue -> contact -> phone) ? $venue -> response -> venue -> contact -> phone : '';
									$date_data['review_url'] = isset($venue -> response -> venue -> canonicalUrl) ? $venue -> response -> venue -> canonicalUrl : '';
								}
							}
						}
					}
					if (isset($date_data))
						$tmp_intro = array_merge($tmp_intro, $date_data);

				}
				if ($intro['user1_id'] == $this -> user_id) {
					$intro_id = $intro['user2_id'];
				}

				if ($intro['user2_id'] == $this -> user_id) {
					$intro_id = $intro['user1_id'];
				}

				//Fetch Intro data...
				$tmp_intro['user_score'] = $this -> datetix -> calculate_score($this -> user_id, $intro_id);
				$tmp_intro['intro_score'] = $this -> datetix -> calculate_score($intro_id, $this -> user_id);
				$tmp_intro['intro_score']['score'] = ($tmp_intro['intro_score']['score'] + $tmp_intro['user_score']['score']) / 2;
				$intro__want_gender = $this -> datetix -> user_want($intro['user_id'], "gender");

				$intro_sexual_status = '';

				if ($intro__want_gender) {
					if ($intro__want_gender[0]['gender_id'] == $intro['gender_id']) {
						if ($intro['gender_id'] == '1') {
							$intro_sexual_status = translate_phrase('Gay');
						} else {
							$intro_sexual_status = translate_phrase('Lesbian');
						}
					} else {
						$intro_sexual_status = translate_phrase('Straight');
					}

				} else {
					$intro_sexual_status = translate_phrase('Bisexual');
				}

				$tmp_intro['sexual_status'] = $intro_sexual_status;
				$tmp_intro['gender_id'] = $intro['gender_id'];
				if ($intro['height']) {
					$inches = $intro['height'] / 2.54;
					$tmp_intro['intro_height'] = intval($inches / 12) . '´' . $inches % 12;
				}
				
				$tmp_intro['upcoming_event_attendance'] = $this->datetix->intro_attendance_upcoming_event($intro_id,$intro['gender_id']);
				$tmp_intro['intro_ethnicity'] = $this -> datetix -> load_data_by_id('ethnicity', $intro['ethnicity_id']);
				$tmp_intro['intro_body_type'] = $this -> datetix -> load_data_by_id('body_type', $intro['body_type_id']);
				$tmp_intro['zodiac_sign'] = getStarSign(strtotime($intro['birth_date']));
				$tmp_intro['privacy_photos'] = $intro['privacy_photos'];
				$tmp_intro['view_profile_link'] = base_url() . 'user/user_info/' . $this -> utility -> encode($intro['user_id']) . '/' . $this -> utility -> encode($this -> user_id) . '/' . $user['password'];

				$fields = array('uj.job_title', 'uj.show_company_name', 'ind.description as industry');
				$from = 'user_job as uj';
				$joins = array('industry as ind' => array('ind.industry_id = uj.industry_id', 'INNER'));
				unset($condition);

				$job_data = $this -> datetix -> get_my_company_data($intro_id);
				if ($job_data) {
					/*
					 if($job_data['0']['job_function_id'] && isset($job_data['0']['job_function_data']))
					 {
					 $tmp_intro['intro_works'] = $job_data['0']['job_function_data']['description'];
					 }
					 else
					 {
					 $tmp_intro['intro_works'] = $job_data['0']['job_title'];
					 }
					 */

					$tmp_intro['intro_works'] = $job_data['0']['job_title'];

					$tmp_intro['intro_works'] = $job_data['0']['job_title'];
					if ($job_data['0']['show_company_name']) {
						$tmp_intro['intro_works'] .= translate_phrase(' at ') . $job_data['0']['company_name'];
					} elseif ($job_data['0']['industry_id'] && isset($job_data['0']['industry_description']) && $job_data['0']['industry_description']) {
						$tmp_intro['intro_works'] .= translate_phrase(' in ') . $job_data['0']['industry_description'] . ' ' . translate_phrase('industry');
					} else {

					}
				}

				$fields = array('sc.school_id', 'sc.school_name', 'sc.logo_url', 'ct.*');
				$from = "user_school as uc";
				$joins = array('school as sc' => array('uc.school_id = sc.school_id', 'INNER'), 'city as ct' => array('ct.city_id = sc.city_id', 'INNER'));
				unset($condition);
				$condition['uc.user_id'] = $intro['user_id'];
				$condition['sc.display_language_id'] = $this -> language_id;
				$condition['ct.display_language_id'] = $this -> language_id;
				$ordersby = 'sc.school_id asc';

				$school_datas = $this -> general -> multijoins($fields, $from, $joins, $condition, $ordersby, 'array');

				if ($school_datas) {
					$school_names = array();
					foreach ($school_datas as $school) {
						$school_names[] = $school['school_name'];
					}

					$study_txt = implode(', ', $school_names);
					/*
					 if(isset($school_datas['0']['school_name']))
					 {
					 $study_txt.= $school_datas['0']['school_name'];
					 }

					 if(isset($school_datas['1']['school_name']))
					 {
					 $study_txt.= ' and '.$school_datas['1']['school_name'];
					 }
					 */
					$tmp_intro['intro_study'] = $study_txt;
				}

				//-------------------------------------------------------------------------------------------//
				//Current Lived in
				$fields = array('ct.description as city_description', 'prvnce.description as province_description', 'cntry.description as country_description', 'cntry.country_code', 'cntry.flag_url', 'crncy.currency_id', 'crncy.description as currency_description', );

				$from = 'city as ct';
				$joins = array('province as prvnce' => array('ct.province_id = prvnce.province_id', 'LEFT'), 'country as cntry' => array('prvnce.country_id = cntry.country_id', 'LEFT'), 'currency as crncy' => array('cntry.currency_id = crncy.currency_id ', 'LEFT'));

				$temp = $this -> model_user -> multijoins($fields, $from, $joins, array('ct.city_id' => $intro['current_city_id'], 'ct.display_language_id' => $this -> language_id, 'prvnce.display_language_id' => $this -> language_id, 'cntry.display_language_id' => $this -> language_id, 'crncy.display_language_id' => $this -> language_id));

				if ($temp) {
					$tmp_intro['intro_current_location'] = $temp['0'];
					unset($temp);
				}
				unset($where);

				//User Likes
				$interestJoins = array('user_interest' => array('interest.interest_id = user_interest.interest_id', 'inner'));
				$interestCondition = array('interest.display_language_id' => $this -> language_id, 'user_interest.user_id' => $this -> user_id);
				$userInterests = $this -> general -> multijoins_arr('interest.description as interest', 'interest', $interestJoins, $interestCondition, '', 'interest.view_order asc');

				$consolidatedUsersInterest = array();
				if (!empty($userInterests)) {
					foreach ($userInterests as $key => $value) {
						$consolidatedUsersInterest[] = $value['interest'];
					}
				}

				$introCondition = array('interest.display_language_id' => $this -> language_id, 'user_interest.user_id' => $intro['user_id']);
				$introInterests = $this -> general -> multijoins_arr('interest.description as interest', 'interest', $interestJoins, $introCondition, '', 'interest.view_order asc');

				$consolidatedIntroInterest = array();
				if (!empty($introInterests)) {
					foreach ($introInterests as $key => $value) {
						$consolidatedIntroInterest[] = $value['interest'];
					}
				}
				if ($common_likes = array_intersect($consolidatedUsersInterest, $consolidatedIntroInterest)) {
					$tmp_intro['common_likes'] = $common_likes;
				}

				//Fetch Mutual Friend
				if ($user['facebook_id'] && $intro['facebook_id']) {
					if ($mutual_friends = $this -> datetix -> fb_mutual_friend($this -> user_id, $intro_id)) {
						$tmp_intro['fb_mutual_friend'] = count($mutual_friends) > 1 ? count($mutual_friends) . ' ' . translate_phrase('Mutual Friends') : count($mutual_friends) . ' ' . translate_phrase('Mutual Friend');
					}

					/*
					 try {

					 $fb_id = $this->facebook->getUser();
					 if($fb_id)
					 {
					 $mutual_friends = $this->facebook->api('me/mutualfriends/'.$intro['facebook_id']);
					 if($mutual_friends['data'])
					 {
					 $tmp_intro['fb_mutual_friend'] = count($mutual_friends['data'])>1?count($mutual_friends['data']).' '.translate_phrase('Mutual Friends'):count($mutual_friends['data']).' '.translate_phrase('Mutual Friend');
					 }
					 }
					 else
					 {
					 $this->load->view('direct_fb_login');
					 }
					 } catch (Exception $e) {}
					 */
				}
				$tmp_intro['fb_id'] = $intro['facebook_id'];

				//Find Photos..
				$this -> general -> set_table('user_photo');
				if ($primary_photo = $this -> general -> get("", array('set_primary' => '1', 'user_id' => $intro['user_id']))) {
					$tmp_intro['primary_photo'] = base_url() . 'user_photos/user_' . $intro['user_id'] . '/' . $primary_photo['0']['photo'];
				}
				$tmp_intro['is_allowed_photo'] = "YES";
				if($tmp_intro['privacy_photos'] == "HIDE")
				{
					$photo_req_condition['user_id'] = $intro_id;
					$photo_req_condition['requested_by_user_id'] = $this -> user_id;
					$photo_req_condition['status'] = "1";
					if(!$this -> general -> checkDuplicate($photo_req_condition,'user_photo_request'))
					{
						$tmp_intro['is_allowed_photo'] = "NO";		
					}
				}
				if ($photo_cnt = $this -> general -> count_record(array('set_primary' => '0', 'user_id' => $intro['user_id']), "user_photo")) {
					$tmp_intro['other_photos'] = $photo_cnt;
				}
				// Chat History
				$this -> general -> set_table('user_intro_chat');
				$history_order_by['user_intro_chat_id'] = 'asc';
				$tmp_intro['chat_history'] = $this -> general -> get("", array('user_intro_id' => $tmp_intro['user_intro_id']),$history_order_by);
				if($tmp_intro['recent_chat'] = $this -> general -> get("", array('user_intro_id' => $tmp_intro['user_intro_id']),array('user_intro_chat_id'=>'desc'),1))
				{
					$tmp_intro['recent_chat'] = $tmp_intro['recent_chat']['0'];
				}
				//if expired or not
				//if (date("Y-m-d", strtotime($intro['intro_expiry_time'])) < SQL_DATE) {
				//	$result['expired'][] = $tmp_intro;
				//} else {
					if (date("Y-m-d", strtotime($intro['intro_available_time'])) > SQL_DATE) {
						$result['upcoming'][] = $tmp_intro;
					} else {
						$result['active'][] = $tmp_intro;
					}
				//}
			}
		}
		$data['user_data'] = $user;
		$data['intros_data'] = $result;
		$data['return_page'] = 'my-intros.html';
		$this -> load -> view('user/intro/' . $intro_type . '_intro', $data);
	}

	/**
	 * [Ajax-call] intro_photos :: User not interested to date
	 * @access public
	 * @param intro_id
	 * @return View data
	 * @author Rajnish Savaliya
	 */
	public function not_intrested() {
		$user_intro_cond = $this -> input -> post();

		$this -> general -> set_table('user_intro');
		$user_id = $this -> session -> userdata('user_id');
		$intro_data = $this -> general -> get("", $user_intro_cond);
		$response['flag'] = 'error';
		if ($intro_data) {
			$intro_data = $intro_data['0'];
			if ($intro_data['user1_id'] == $user_id) {
				$update_intro_data['user1_not_interested_time'] = SQL_DATETIME;

			}
			if ($intro_data['user2_id'] == $user_id) {
				$update_intro_data['user2_not_interested_time'] = SQL_DATETIME;
			}
			if ($this -> general -> update($update_intro_data, $user_intro_cond)) {
				$response['flag'] = 'success';
			}
		}
		echo json_encode($response);
	}

	/**
	 * [Ajax-call] intro_photos :: Get Introduce Now
	 * @access public
	 * @param intro_id
	 * @return View data
	 * @author Rajnish Savaliya
	 */
	public function get_introduce_now() {
		$user_intro_cond = $this -> input -> post();

		$this -> general -> set_table('user_intro');
		$user_id = $this -> session -> userdata('user_id');
		$response['flag'] = 'error';
		$update_intro_data['intro_available_time'] = SQL_DATETIME;
		if ($this -> general -> update($update_intro_data, $user_intro_cond)) {
			$intros_data = $this -> general -> get("*", array('user_intro_id' => $user_intro_cond['user_intro_id']));
			if ($intros_data) {
				$intro = $intros_data['0'];
				$user['user_id'] = $intro['user1_id'];
				$cur_user['user_id'] = $intro['user2_id'];
				$user_compitiblity = $this -> datetix -> calculate_score($cur_user['user_id'], $user['user_id']);
				$this -> datetix -> intro_mail($intro['user2_id'], $intro['user1_id'], $user_compitiblity['match_data']);

				$intro_compitiblity = $this -> datetix -> calculate_score($user['user_id'], $cur_user['user_id']);
				$this -> datetix -> intro_mail($intro['user1_id'], $intro['user2_id'], $intro_compitiblity['match_data'], 'user');
			}
			$response['flag'] = 'success';
		}
		echo json_encode($response);
	}

	/**
	 * [Popup] intro_photos :: Display user's photo
	 * @access public
	 * @param return_to [return url], user_id [photo user id]
	 * @return View data
	 * @author Rajnish Savaliya
	 */
	public function intro_photos() {
		$user_id = $this -> session -> userdata('user_id');
		$language_id = $this -> session -> userdata('sess_language_id');
		$intro_id = $this -> input -> get('user_id');
		if ($return_to = $this -> input -> get('return_to')) {
			$return_url = url_city_name() . '/' . $return_to;
		} else {
			$return_url = url_city_name() . '/index.html';
		}
		if ($data['user_photos'] = $this -> model_user -> get_photos($intro_id, "profile")) {
			$data['return_url'] = $return_url;
			$data['user_data'] = $this -> model_user -> get_user_data($user_id);
			$data['page_title'] = translate_phrase('Profile Pictures');
			$data['page_name'] = 'user/popup_intro_photos';
			$this -> load -> view('template/editProfileTemplate', $data);
		} else {
			redirect('/' . $return_url);
		}
	}

	function sort_friends($a, $b) {
		return strcasecmp($a['name'], $b['name']);
	}

	/**
	 * Mutual Friends Function :: Find out Mutual Friends between logged in user and $fb_id(search user) using Graph API.
	 * @access public
	 * @param Facebook Id
	 * @return View data
	 * @author Rajnish Savaliya
	 */
	public function mutual_friends() {
		$data['page_title'] = 'Mutual Friend Demo';
		$data['page_name'] = 'user/demo_mutual_friend';

		$fb_id = $this -> input -> get('fb_id');

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

					/*
					 *  	OLD CODE :: API
					 *
					 if($mutual_friends = $this->facebook->api("/".$fb_user_id."/mutualfriends/".$fb_id))
					 {
					 $friends = $mutual_friends['data'];
					 usort($friends, array($this,'sort_friends'));

					 $mutual_friends_app_users_cnt = 0;
					 foreach ($friends as $fb_user)
					 {
					 if($this->datetix->is_app_user($fb_user['id']))
					 {
					 $mutual_friends_app_users_cnt ++;
					 }

					 $friends = $this->facebook->api('/'.$fb_user['id'].'?fields=name,location,relationship_status');
					 if($tmp = $this->model_user->get_custom_user_data('user_id',array('facebook_id'=>$friends['id'])))
					 {
					 $friends['friend_with_datetix'] = $this->model_user->get_fb_friends_with_datetix($tmp->user_id);
					 }

					 $data['mutual_friends'][] = $friends;
					 }
					 $data['mutual_friends_app_users_cnt'] = $mutual_friends_app_users_cnt;
					 }
					 */
				} else {
					$this -> load -> view('direct_fb_login');
				}
			} catch (Exception $e) {

			}

			$data['return_url'] = $return_url;
			$data['fb_app_id'] = $this -> config -> item('appId');
			$data['fb_desc'] = translate_phrase('Apply for a free membership to datetix.com today and let us help set you up on first dates with high quality local singles. Please visit ') . base_url();

			$user_id = $this -> session -> userdata('user_id');
			$data['user_data'] = $this -> model_user -> get_user_data($user_id);
			$data['page_title'] = translate_phrase('Mutual Friend');
			$data['page_name'] = 'user/popup_mutual_friend';
			$this -> load -> view('template/editProfileTemplate', $data);
		} else {
			redirect('/' . $return_url);
		}

	}

	/**
	 * [Popup] lack_interest :: Lack of interest
	 * @access public
	 * @param return_to [return url], user_id [photo user id]
	 * @return View data
	 * @author Rajnish Savaliya
	 */
	public function lack_interest() {
		$user_id = $this -> session -> userdata('user_id');
		$language_id = $this -> session -> userdata('sess_language_id');
		$user_intro_id = $this -> utility -> decode($this -> input -> get('intro'));
		$data['user_data'] = $this -> model_user -> get_user_data($user_id);
		$data['page_title'] = translate_phrase('Confirm Lack of Interest');
		$data['page_heading'] = translate_phrase('Confirm Lack of Interest');
		$data['is_date_cancelled'] = 0;

		if ($this -> session -> userdata('return_url')) {
			$return_url = $this -> session -> userdata('return_url');
			$this -> session -> unset_userdata('return_url');
		} else {
			if ($return_to = $this -> input -> get('return_to')) {
				if ($tab = $this -> input -> get('tab')) {
					$return_to .= '#' . $tab;
				}

				$return_url = $return_to;
			} else {
				$return_url = 'index.html';
			}
			$return_url = base_url() . url_city_name() . '/' . $return_url;
		}

		$where = 'user_intro_id = ' . $user_intro_id . ' AND (user1_id = "' . $user_id . '" OR user2_id = "' . $user_id . '") ';
		$this -> general -> set_table('user_intro');
		if ($intros_data = $this -> general -> custom_get("*", $where)) {
			$intro_data = $intros_data['0'];

			$is_ticket_paid_by_user = 0;
			$is_ticket_paid_by_intro = 0;

			if ($intro_data['user1_id'] == $user_id) {
				$intro_id = $intro_data['user2_id'];
			}
			if ($intro_data['user2_id'] == $user_id) {
				$intro_id = $intro_data['user1_id'];
			}

			//FETCH DATE data :::
			$this -> general -> set_table('user_date');
			if ($user_date_data = $this -> general -> get("", array('user_intro_id' => $intro_data['user_intro_id']))) {
				$intro_data['date_data'] = $user_date_data['0'];
				if ($intro_data['date_data']['date_time'] != '0000-00-00 00:00:00' && date("Y-m-d", strtotime($intro_data['date_data']['date_time'])) >= SQL_DATE && $intro_data['date_data']['date_accepted_time'] != '0000-00-00 00:00:00') {
					$data['is_date_cancelled'] = 1;
					$data['page_title'] = translate_phrase('Confirm Date Cancellation');
					$data['page_heading'] = translate_phrase('Confirm Date Cancellation');
				}

				if ($intro_data['user1_id'] == $user_id) {
					if ($intro_data['user1_date_ticket_paid_by'] == $user_id)
						$is_ticket_paid_by_user = 1;

					if ($intro_data['user2_date_ticket_paid_by'] == $intro_id)
						$is_ticket_paid_by_intro = 1;
				}

				if ($intro_data['user2_id'] == $user_id) {
					if ($intro_data['user2_date_ticket_paid_by'] == $user_id)
						$is_ticket_paid_by_user = 1;
					if ($intro_data['user1_date_ticket_paid_by'] == $intro_id)
						$is_ticket_paid_by_intro = 1;
				}
			}

			$data['is_ticket_paid_by_user'] = $is_ticket_paid_by_user;
			$data['user_info'] = $this -> model_user -> get_user_data($intro_id);

			if ($post_data = $this -> input -> post('filters')) {
				
				if ($intro_data['user1_id'] == $user_id) {
					$update_intro_data['user1_not_interested_reason'] = $post_data;
					$update_intro_data['user1_not_interested_time'] = SQL_DATETIME;

				}
				if ($intro_data['user2_id'] == $user_id) {
					$update_intro_data['user2_not_interested_reason'] = $post_data;
					$update_intro_data['user2_not_interested_time'] = SQL_DATETIME;
				}

				$user_intro_cond['user_intro_id'] = $user_intro_id;
				$subject = translate_phrase("Sorry, but ") . $data['user_data']['first_name'] . translate_phrase(" has declined to meet you for a date");
				if ($user_date_data) {
					$cancell_date_data['date_cancelled_by_user_id'] = $this -> user_id;
					$cancell_date_data['date_cancelled_time'] = SQL_DATETIME;
					$this -> general -> set_table('user_date');
					if ($this -> general -> update($cancell_date_data, $user_intro_cond)) {
						
						//$user_noun = ($data['user_data']['gender_id'] == 1)?translate_phrase('he'):translate_phrase('she');
						$user_pro_noun = ($data['user_data']['gender_id'] == 1) ? translate_phrase('his') : translate_phrase('her');
						$subject = translate_phrase("Sorry, but ") . $data['user_data']['first_name'] . translate_phrase(" has just cancelled " . $user_pro_noun . " previously confirmed date with you");
					}
				}
				if ($is_ticket_paid_by_intro) {
						
					$update_intro_data['user1_date_ticket_paid_by'] = 0;
					$update_intro_data['user2_date_ticket_paid_by'] = 0;
						
					//Update User Tickets
					$this -> model_user -> update_user($intro_id, array('num_date_tix' => $data['user_info']['num_date_tix'] + 1));
				}
				
				$this -> general -> set_table('user_intro');
				if ($this -> general -> update($update_intro_data, $user_intro_cond)) {
					
					/* 
					 * TASK :: Don't send not interested email to the other user after user clicks "Not Interested"
					 * 
					$user_email_data = $this -> model_user -> get_user_email($intro_id);
					$data['email_content'] = '';
					$data['btn_link'] = base_url() . 'user/user_info/' . $this -> utility -> encode($data['user_data']['user_id']) . '/' . $this -> utility -> encode($intro_id) . '/' . $data['user_info']['password'];
					$data['btn_text'] = translate_phrase("View ") . $data['user_data']['first_name'] . translate_phrase("'s profile");
					$data['email_title'] = $subject . '.';
					$email_template = $this -> load -> view('email/common', $data, true);
					$this -> datetix -> mail_to_user($user_email_data['email_address'], $subject, $email_template);
					*/
					
					redirect($return_url);
				}
			}

			/*------------------get ideal_match_filters------------------*/
			$this -> general -> set_table('filter');
			unset($where);
			$where['language_id'] = $language_id;
			$result = $this -> general -> get(array('filter_id', 'description'), $where);
			foreach ($result as $key => $value) {
				$data['filters'][$value['filter_id']] = $value['description'];
			}
			$data['user_intro_id'] = $user_intro_id;

			$data['return_url'] = $return_url;

			$data['page_name'] = 'user/popup_lack_interest';
			$this -> load -> view('template/editProfileTemplate', $data);
		} else {
			redirect($return_url);
		}
	}
}
?>
