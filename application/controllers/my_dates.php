<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class My_dates extends CI_Controller {
	var $language_id = '1';
	var $user_id = '';

	public function __construct() {
		parent::__construct();
		$this -> load -> model('model_user');
		$this -> load -> model('general_model', 'general');
		set_time_limit(20000);
		if ($this -> user_id = $this -> session -> userdata('user_id')) {
			$this -> model_user -> update_user($this -> user_id, array('last_active_time' => SQL_DATETIME));
			$this -> user_id = $this -> session -> userdata('user_id');
			$this -> language_id = $this -> session -> userdata('sess_language_id');
		} else {
			echo '<script type="text/javascript">window.location.href = "' . base_url() . '"</script>';
		}
	}

	/**
	 * Index Function :: Display current user's Intro data
	 * @access public
	 * @return View data
	 * @author Rajnish Savaliya
	 */
	public function index() {
		//echo "<pre>";print_r($this->session->all_userdata());exit;
		if ($this -> session -> userdata('return_url'))
			$this -> session -> unset_userdata('return_url');

		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$user = $user_data['0'];

		if ($this -> input -> post('page_no')) {
			$page_no = $this -> input -> post('page_no');
		} else {
			$page_no = 1;
		}
		
		/*
		 * Load all intros
		if ($this -> session -> userdata('redirect_intro_id')) {
			$data['page_no'] = 0;
			$limit = '';
		} else {
			$data['page_no'] = $page_no;
		 	$limit = ' LIMIT ' . ($page_no - 1) * PER_PAGE . ', ' . PER_PAGE;
		}
		*/
		
		$limit = ' LIMIT ' . ($page_no - 1) * PER_PAGE . ', ' . PER_PAGE;
		
		//TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
		//common SQL Query for all the intros
		$common_sql = 'SELECT user_intro.*, user_date.*, date_type.description as date_type_desc,
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
					WHEN user_intro.user1_id = "' . $this -> user_id . '" THEN user_intro.user2_id
					WHEN user_intro.user2_id = "' . $this -> user_id . '" THEN user_intro.user1_id
				END
				
				JOIN user_date on user_intro.user_intro_id = user_date.user_intro_id
                JOIN date_type on date_type.date_type_id = user_date.date_type_id
                WHERE (user1_id = "' . $this -> user_id . '" OR user2_id = "' . $this -> user_id . '") 
                AND user_intro.user1_not_interested_time = "0000-00-00 00:00:00"
				AND user_intro.user2_not_interested_time = "0000-00-00 00:00:00"
                ';
		/*$dummy_backup = 'AND "0000-00-00 00:00:00" = CASE
		 WHEN user_intro.user1_id = "'.$this->user_id.'" THEN user_intro.user1_not_interested_time
		 WHEN user_intro.user2_id = "'.$this->user_id.'" THEN user_intro.user2_not_interested_time
		 END'
		 */

		$query = $common_sql . 'AND DATE(date_time) < CURDATE() AND date_accepted_time != "0000-00-00 00:00:00"
                AND date_type.display_language_id=' . $this -> language_id . '
                ORDER BY `date_time` DESC ' . $limit;

		$expired_intro = $this -> general -> sql_query($query);

		$query = $common_sql . 'AND DATE(date_time) >= CURDATE() AND date_accepted_time != "0000-00-00 00:00:00"
                AND date_type.display_language_id=' . $this -> language_id . '
                ORDER BY `date_time` DESC ' . $limit;
		$upcoming_intro = $this -> general -> sql_query($query);

		$query = $common_sql . 'AND DATE(date_time) >= CURDATE() AND date_accepted_time = "0000-00-00 00:00:00"
				AND date_type.display_language_id=' . $this -> language_id . '
                ORDER BY 
                CASE  
				  WHEN user_date.date_suggested_by_user_id = "' . $this -> user_id . '" THEN "`date_suggested_time` ASC"
				END
				 ,`intro_available_time` DESC ' . $limit;
		$active_intro = $this -> general -> sql_query($query);

		$sent_date_request = 0;
		$received_date_request = 0;
		if ($active_intro) {
			$pending_query = 'SELECT user_date.* FROM user_intro
				
				JOIN user on user.user_id = CASE 
					WHEN user_intro.user1_id = "' . $this -> user_id . '" THEN user_intro.user2_id
					WHEN user_intro.user2_id = "' . $this -> user_id . '" THEN user_intro.user1_id
				END
				
				JOIN user_date on user_intro.user_intro_id = user_date.user_intro_id
                JOIN date_type on date_type.date_type_id = user_date.date_type_id
                WHERE (user1_id = "' . $this -> user_id . '" OR user2_id = "' . $this -> user_id . '") 
                
                AND user_intro.user1_not_interested_time = "0000-00-00 00:00:00"
				AND user_intro.user2_not_interested_time = "0000-00-00 00:00:00"
				AND DATE(date_time) >= CURDATE()
                AND date_accepted_time = "0000-00-00 00:00:00"
        		AND date_type.display_language_id=' . $this -> language_id . '
                ORDER BY `intro_available_time` DESC ';

			$all_pending_dates = $this -> general -> sql_query($pending_query);

			if ($all_pending_dates) {
				foreach ($all_pending_dates as $intro) {
					if ($intro['date_suggested_by_user_id'] == $this -> user_id) {
						$sent_date_request++;
					}
				}
				$received_date_request = count($all_pending_dates) - $sent_date_request;
			}
		}
		$data['sent_date_request'] = $sent_date_request;
		$data['received_date_request'] = $received_date_request;

		$intros_data = array_merge($expired_intro, $upcoming_intro, $active_intro);

		$result = array();
		if ($intros_data) {
			foreach ($intros_data as $intro) {
				
				$intro['venue_dates'] = array();
				if ($intro['venue_id']) {
					$venue_sql = ' SELECT  venue.*,  neighborhood.*, neighborhood.description as neighborhood_desc
					From venue 
					JOIN neighborhood on neighborhood.neighborhood_id = venue.neighborhood_id
	                
	                WHERE venue.display_language_id=' . $this -> language_id . '
	                AND neighborhood.display_language_id=' . $this -> language_id . '
	                AND venue.venue_id ="' . $intro['venue_id'] . '"
	                ORDER BY venue.view_order DESC';

					if ($venue_row_data = $this -> general -> sql_query($venue_sql)) {
						$intro = array_merge($intro, $venue_row_data['0']);
					}
					$query = 'SELECT * FROM venue_date_type
					JOIN date_type on date_type.date_type_id = venue_date_type.date_type_id
					WHERE date_type.display_language_id=' . $this -> language_id . '
                	AND venue_date_type.venue_id = "' . $intro['venue_id'] . '"
                	Group By date_type.date_type_id
                	ORDER BY `view_other` ASC';

					if ($venue_date = $this -> general -> sql_query($query)) {
						foreach ($venue_date as $value) {
							$intro['venue_dates'][] = $value['description'];
						}
					}
				} else {
					$intro['name'] = '';
					$intro['address'] = '';
					$intro['phone_number'] = '';
					$intro['review_url'] = '';

					if ($intro['venue_other']) {
						preg_match('/_(.*?)_/', $intro['venue_other'], $display);
						if ($display) {
							$intro['name'] = $display['1'];
							$venue_sql = ' SELECT  city.*, neighborhood.description as neighborhood_desc
										From neighborhood 
										JOIN city on city.city_id = neighborhood.city_id
						                
						                WHERE city.display_language_id=' . $this -> language_id . '
						                AND neighborhood.display_language_id=' . $this -> language_id . '
						                AND neighborhood.neighborhood_id ="' . $intro['neighborhood_id'] . '"
						                ORDER BY neighborhood.view_order DESC';

							$venue_row_data = $this -> general -> sql_query($venue_sql);
							if ($venue_row_data) {
								$intro['address'] = $venue_row_data['0']['neighborhood_desc'] . ', ' . $venue_row_data['0']['description'];
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

							$venue = $fsObjUnAuth -> get('/venues/' . $intro['venue_other'], $para);
							//echo "<pre>";print_r($venue);exit;
							if ($venue -> response) {
								$intro['name'] = isset($venue -> response -> venue -> name) ? $venue -> response -> venue -> name : '';
								$intro['address'] = isset($venue -> response -> venue -> location -> address) ? $venue -> response -> venue -> location -> address : '';
								$intro['phone_number'] = isset($venue -> response -> venue -> contact -> phone) ? $venue -> response -> venue -> contact -> phone : '';
								$intro['review_url'] = isset($venue -> response -> venue -> canonicalUrl) ? $venue -> response -> venue -> canonicalUrl : '';
							}
						}
					}
				}

				$tmp_intro = $intro;
				if ($intro['user1_id'] == $this -> user_id) {
					$intro_id = $intro['user2_id'];
				}

				if ($intro['user2_id'] == $this -> user_id) {
					$intro_id = $intro['user1_id'];
				}

				$this -> general -> set_table('user_date_feedback');
				$tmp_intro['intro_feedback_data'] = $this -> general -> get("*", array('user_date_id' => $intro['user_date_id'], 'user_id' => $intro_id));

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

				$tmp_intro['intro_ethnicity'] = $this -> datetix -> load_data_by_id('ethnicity', $intro['ethnicity_id']);
				$tmp_intro['intro_body_type'] = $this -> datetix -> load_data_by_id('body_type', $intro['body_type_id']);
				$tmp_intro['zodiac_sign'] = getStarSign(strtotime($intro['birth_date']));
				$tmp_intro['privacy_photos'] = $intro['privacy_photos'];
				$tmp_intro['view_profile_link'] = base_url() . 'user/user_info/' . $this -> utility -> encode($intro['user_id']) . '/' . $this -> utility -> encode($this -> user_id) . '/' . $user['password'];

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
				if ($photo_cnt = $this -> general -> count_record(array('set_primary' => '0', 'user_id' => $intro['user_id']), "user_photo")) {
					$tmp_intro['other_photos'] = $photo_cnt;
				}

				// Chat History
				$this -> general -> set_table('user_intro_chat');
				$history_order_by['user_intro_chat_id'] = 'asc';
				$tmp_intro['chat_history'] = $this -> general -> get("", array('user_intro_id' => $tmp_intro['user_intro_id']),$history_order_by);
				
				//if expired or not
				if ($intro['date_accepted_time'] == "0000-00-00 00:00:00") {
					$result['active'][] = $tmp_intro;
				} else {
					if (date("Y-m-d", strtotime($intro['date_time'])) < SQL_DATE) {
						$feedback_data = array();
						$this -> general -> set_table('user_date_feedback');

						if ($feedback_data = $this -> general -> get("*", array('user_date_id' => $tmp_intro['user_date_id'], 'user_id' => $this -> user_id))) {
							$feedback_data = $feedback_data['0'];
							//Muliple User Descriptive Word
							$fields = array('usr.descriptive_word_id', 'dw.description as dw_description');
							$from = 'user_date_feedback_descriptive_word as usr';
							$joins = array('descriptive_word as dw' => array('dw.descriptive_word_id= usr.descriptive_word_id', 'LEFT'));
							$where['dw.display_language_id'] = $this -> language_id;
							$where['usr.user_date_feedback_id'] = $feedback_data['user_date_feedback_id'];
							$where['usr.user_id'] = $this -> user_id;

							if ($selected_descriptive_words = $this -> general -> multijoins_arr($fields, $from, $joins, $where, '', 'view_order asc')) {
								foreach ($selected_descriptive_words as $value) {
									$tmp[] = $value['descriptive_word_id'];
								}
								$feedback_data['descriptive_word_id'] = implode(',', $tmp);
							}
						}
						$tmp_intro['user_feedback'] = $feedback_data;

						$data['looks'] = $this -> model_user -> get_looks($this -> language_id);
						$data['descriptive_word'] = $this -> model_user -> get_descriptive_word($this -> language_id);
						$result['expired'][] = $tmp_intro;
					} else {
						$result['upcoming'][] = $tmp_intro;
					}
				}
			}
		}
		$data['intros_data'] = $result;
		$data['user_data'] = $user;
		$data['page_title'] = translate_phrase('My Dates');
		$data['page_name'] = 'user/dates/my_dates';
		$this -> load -> view('template/editProfileTemplate', $data);
	}

	/**
	 * [Ajax-call] load_dates Function :: Load dates by type and pagination.
	 * @access public
	 * @return View data
	 * @author Rajnish Savaliya
	 */
	public function load_dates($type = 'pending') {
		$sortby = $this -> input -> post('sort_by');
		$sort_order = 'ORDER BY date_time DESC';

		if ($sortby == 1) {
			$sort_order = 'ORDER BY
				CASE
				  WHEN user_date.date_suggested_by_user_id = "' . $this -> user_id . '" THEN "`date_suggested_time` ASC"
				END,
				`intro_available_time` DESC';
		} elseif ($sortby == 2) {
			$sort_order = 'ORDER BY
				CASE
				  WHEN user_date.date_suggested_by_user_id = "' . $this -> user_id . '" THEN "`date_suggested_time` ASC"
				END,
				`intro_available_time` ASC';
		} elseif ($sortby == 3) {
			$sort_order = 'ORDER BY
				CASE
				  WHEN user_date.date_suggested_by_user_id = "' . $this -> user_id . '" THEN "`date_suggested_time` ASC"
				END, 
				`intro_age` ASC';
		} elseif ($sortby == 4) {
			$sort_order = 'ORDER BY
				CASE
				  WHEN user_date.date_suggested_by_user_id = "' . $this -> user_id . '" THEN "`date_suggested_time` ASC"
				END,
				`intro_age` DESC';
		} elseif ($sortby == 5) {
			$sort_order = 'ORDER BY
				CASE
				  WHEN user_date.date_suggested_by_user_id = "' . $this -> user_id . '" THEN "`date_suggested_time` ASC"
				END,
				`intro_name` ASC';
		} elseif ($sortby == 6) {
			$sort_order = 'ORDER BY
				CASE
				  WHEN user_date.date_suggested_by_user_id = "' . $this -> user_id . '" THEN "`date_suggested_time` ASC"
				END,
				`intro_name` DESC';
		}

		//PAGINATION
		if ($this -> input -> post('page_no')) {
			$page_no = $this -> input -> post('page_no');
		} else {
			$page_no = 1;
		}

		$limit = ' LIMIT ' . ($page_no - 1) * PER_PAGE . ', ' . PER_PAGE;

		//common SQL Query for all the intros
		$common_sql = 'SELECT user_intro.*, user_date.*, date_type.description as date_type_desc,
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
					WHEN user_intro.user1_id = "' . $this -> user_id . '" THEN user_intro.user2_id
					WHEN user_intro.user2_id = "' . $this -> user_id . '" THEN user_intro.user1_id
				END
				
				JOIN user_date on user_intro.user_intro_id = user_date.user_intro_id
                JOIN date_type on date_type.date_type_id = user_date.date_type_id
                
                WHERE (user1_id = "' . $this -> user_id . '" OR user2_id = "' . $this -> user_id . '") 
                AND user_intro.user1_not_interested_time = "0000-00-00 00:00:00"
				AND user_intro.user2_not_interested_time = "0000-00-00 00:00:00"
                ';
		/*
		 [10:25:02] Michael Ye: if date_time < today and user has accepted, then it's past
		 [10:24:48] Michael Ye: if date_time > today and user has accepted, then it's upcoming
		 [10:24:54] Michael Ye: if date_time > today and user has not accepted, then it's pending
		 [10:25:26] Michael Ye: if date_time < today and user has not accepted, then don't show in My Dates tab
		 */

		if ($type == 'expired') {
			$query = $common_sql . 'AND DATE(date_time) < CURDATE() AND date_accepted_time != "0000-00-00 00:00:00"';
		} else if ($type == 'upcoming') {
			$query = $common_sql . 'AND DATE(date_time) >= CURDATE() AND date_accepted_time != "0000-00-00 00:00:00"';
		} else {
			$query = $common_sql . 'AND DATE(date_time) >= CURDATE() AND date_accepted_time = "0000-00-00 00:00:00"';
		}

		$query .= ' AND date_type.display_language_id=' . $this -> language_id . ' ' . $sort_order . '' . $limit;

		$intros_data = $this -> general -> sql_query($query);

		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$user = $user_data['0'];

		$result = array();
		if ($intros_data) {
			foreach ($intros_data as $intro) {
				$intro['venue_dates'] = array();
				if ($intro['venue_id']) {
					$venue_sql = ' SELECT  venue.*,  neighborhood.*, neighborhood.description as neighborhood_desc
					From venue 
					JOIN neighborhood on neighborhood.neighborhood_id = venue.neighborhood_id
	                
	                WHERE venue.display_language_id=' . $this -> language_id . '
	                AND neighborhood.display_language_id=' . $this -> language_id . '
	                AND venue.venue_id ="' . $intro['venue_id'] . '"
	                ORDER BY venue.view_order DESC';

					if ($venue_row_data = $this -> general -> sql_query($venue_sql))
						$intro = array_merge($intro, $venue_row_data['0']);

					$query = 'SELECT * FROM venue_date_type
					JOIN date_type on date_type.date_type_id = venue_date_type.date_type_id
					WHERE date_type.display_language_id=' . $this -> language_id . '
                	AND venue_date_type.venue_id = "' . $intro['venue_id'] . '"
                	Group By date_type.date_type_id
                	ORDER BY `view_other` ASC';

					if ($venue_date = $this -> general -> sql_query($query)) {
						foreach ($venue_date as $value) {
							$intro['venue_dates'][] = $value['description'];
						}
					}
				} else {
					$intro['name'] = '';
					$intro['address'] = '';
					$intro['phone_number'] = '';
					$intro['review_url'] = '';

					if ($intro['venue_other']) {
						preg_match('/_(.*?)_/', $intro['venue_other'], $display);
						if ($display) {
							$intro['name'] = $display['1'];
							$venue_sql = ' SELECT  city.*, neighborhood.description as neighborhood_desc
										From neighborhood 
										JOIN city on city.city_id = neighborhood.city_id
						                
						                WHERE city.display_language_id=' . $this -> language_id . '
						                AND neighborhood.display_language_id=' . $this -> language_id . '
						                AND neighborhood.neighborhood_id ="' . $intro['neighborhood_id'] . '"
						                ORDER BY neighborhood.view_order DESC';

							$venue_row_data = $this -> general -> sql_query($venue_sql);
							if ($venue_row_data) {
								$intro['address'] = $venue_row_data['0']['neighborhood_desc'] . ', ' . $venue_row_data['0']['description'];
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

							$venue = $fsObjUnAuth -> get('/venues/' . $intro['venue_other'], $para);
							//echo "<pre>";print_r($venue);exit;
							if ($venue -> response) {
								$intro['name'] = isset($venue -> response -> venue -> name) ? $venue -> response -> venue -> name : '';
								$intro['address'] = isset($venue -> response -> venue -> location -> address) ? $venue -> response -> venue -> location -> address : '';
								$intro['phone_number'] = isset($venue -> response -> venue -> contact -> phone) ? $venue -> response -> venue -> contact -> phone : '';
								$intro['review_url'] = isset($venue -> response -> venue -> canonicalUrl) ? $venue -> response -> venue -> canonicalUrl : '';
							}
						}
					}
				}

				$tmp_intro = $intro;

				if ($intro['user1_id'] == $this -> user_id) {
					$intro_id = $intro['user2_id'];
				}

				if ($intro['user2_id'] == $this -> user_id) {
					$intro_id = $intro['user1_id'];
				}

				//Fetch Intro data...
				$this -> general -> set_table('user_date_feedback');
				$tmp_intro['intro_feedback_data'] = $this -> general -> get("*", array('user_date_id' => $intro['user_date_id'], 'user_id' => $intro_id));

				$tmp_intro['user_score'] = $this -> datetix -> calculate_score($this -> user_id, $intro_id);
				$tmp_intro['intro_score'] = $this -> datetix -> calculate_score($intro_id, $this -> user_id);
				$tmp_intro['intro_score']['score'] = ($tmp_intro['intro_score']['score'] + $tmp_intro['user_score']['score']) / 2;

				$intro__want_gender = $this -> datetix -> user_want($intro_id, "gender");

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

				$tmp_intro['intro_ethnicity'] = $this -> datetix -> load_data_by_id('ethnicity', $intro['ethnicity_id']);
				$tmp_intro['intro_body_type'] = $this -> datetix -> load_data_by_id('body_type', $intro['body_type_id']);
				$tmp_intro['zodiac_sign'] = getStarSign(strtotime($intro['birth_date']));
				$tmp_intro['privacy_photos'] = $intro['privacy_photos'];
				$tmp_intro['user_id'] = $intro_id;
				$tmp_intro['view_profile_link'] = base_url() . 'user/user_info/' . $this -> utility -> encode($intro_id) . '/' . $this -> utility -> encode($this -> user_id) . '/' . $user['password'];

				/*
				 $fields = array('uj.job_title','uj.show_company_name','ind.description as industry');
				 $from = 'user_job as uj';
				 $joins = array('industry as ind'=>array('ind.industry_id = uj.industry_id','INNER'));
				 unset($condition);
				 $condition['ind.display_language_id'] = $this->language_id;
				 $condition['uj.user_id'] = $intro['user_id'];
				 $ordersby = 'uj.user_company_id asc';
				 $job_data = $this->general->multijoins($fields,$from,$joins,$condition,$ordersby,'array');
				 */
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
				if ($photo_cnt = $this -> general -> count_record(array('set_primary' => '0', 'user_id' => $intro['user_id']), "user_photo")) {
					$tmp_intro['other_photos'] = $photo_cnt;
				}

				// Chat History
				$this -> general -> set_table('user_intro_chat');
				$history_order_by['user_intro_chat_id'] = 'asc';
				$tmp_intro['chat_history'] = $this -> general -> get("", array('user_intro_id' => $tmp_intro['user_intro_id']),$history_order_by);
				
				//if expired or not
				if ($intro['date_accepted_time'] == "0000-00-00 00:00:00") {
					$result['active'][] = $tmp_intro;
				} else {
					if (date("Y-m-d", strtotime($intro['date_time'])) < SQL_DATE) {
						$feedback_data = array();
						$this -> general -> set_table('user_date_feedback');
						if ($feedback_data = $this -> general -> get("*", array('user_date_id' => $tmp_intro['user_date_id'], 'user_id' => $this -> user_id))) {
							$feedback_data = $feedback_data['0'];
							//Muliple User Descriptive Word
							$fields = array('usr.descriptive_word_id', 'dw.description as dw_description');
							$from = 'user_date_feedback_descriptive_word as usr';
							$joins = array('descriptive_word as dw' => array('dw.descriptive_word_id= usr.descriptive_word_id', 'LEFT'));
							$where['dw.display_language_id'] = $this -> language_id;
							$where['usr.user_date_feedback_id'] = $feedback_data['user_date_feedback_id'];
							$where['usr.user_id'] = $this -> user_id;

							if ($selected_descriptive_words = $this -> general -> multijoins_arr($fields, $from, $joins, $where, '', 'view_order asc')) {
								foreach ($selected_descriptive_words as $value) {
									$tmp[] = $value['descriptive_word_id'];
								}
								$feedback_data['descriptive_word_id'] = implode(',', $tmp);
							}
						}
						$tmp_intro['user_feedback'] = $feedback_data;
						$data['looks'] = $this -> model_user -> get_looks($this -> language_id);
						$data['descriptive_word'] = $this -> model_user -> get_descriptive_word($this -> language_id);
						$result['expired'][] = $tmp_intro;
					} else {
						$result['upcoming'][] = $tmp_intro;
					}
				}
			}
		}
		$data['user_data'] = $user;
		$data['intros_data'] = $result;
		$this -> load -> view('user/dates/load_' . $type . '_dates', $data);
	}

	/**
	 * [Ajax-call] Load More Days
	 * @access public
	 * @return return output [HTML]
	 * @author Rajnish Savaliya
	 */
	public function seven_more_days($start = 1) {
		for ($i = $start; $i < ($start + 7); $i++) {
			$strtime = strtotime('tomorrow + ' . $i . ' day');
			echo '<div><a class="rdo_div" key="' . $strtime . '" href="javascript:;" ><span class="disable-butn" >' . date("l, F j", $strtime) . '</span></a></div>';
		}

	}


	/**
	 * [Ajax-call] send_chat_message Function :: Send Chat Message
	 * @access public
	 * @return response
	 * @author Rajnish Savaliya
	 */
	public function send_chat_message($intro_id = '') {
		if ($this -> input -> is_ajax_request()) {
			if ($data = $this -> input -> post()) {
					
				$data['chat_message_time'] = SQL_DATETIME;
				$data['user_id'] = $this -> user_id;
				
				//$page_name = 'my-intros.html';
				$is_premium_intro = $this -> datetix -> is_premium_user($intro_id, PERMISSION_UNLIMITED_DATES);
				$is_premius_member = $this -> datetix -> is_premium_user($this -> user_id, PERMISSION_UNLIMITED_DATES);
				$mutual_friends_on_datetix = $this -> datetix -> datetix_mutual_friend($this -> user_id, $intro_id);
				$fb_mutual_friend_use_app = count($mutual_friends_on_datetix);
				$is_ticket_paid = 0;
				$this -> general -> set_table('user_intro');
				$intro_data = $this -> general -> get("", array('user_intro_id' => $data['user_intro_id']));
				if ($intro_data) {
					
					$intro_data = $intro_data['0'];

					$is_ticket_paid = 0;
					if ($is_premium_intro || $is_premius_member || $fb_mutual_friend_use_app > 0 || $intro_data['user1_date_ticket_paid_by'] || $intro_data['user2_date_ticket_paid_by']) {
						$is_ticket_paid = 1;
					}
				} else {
					if ($is_premium_intro || $is_premius_member || $fb_mutual_friend_use_app > 0) {
						$is_ticket_paid = 1;
					}
				}
				
				/*
				$this -> general -> set_table('user_date');
				if ($this -> general -> get("", array('user_intro_id' => $data['user_intro_id']))) {
					$page_name = 'my-date.html';
				}
				*/
				
				if ($is_ticket_paid) {
					$this -> general -> set_table('user_intro_chat');
					if ($this -> general -> save($data)) {
						if ($intro_id != '') {
							$this -> general -> set_table('user');
							
							$user_data = $this -> general -> get("user_id, first_name, password, facebook_id", array('user_id' => $this -> user_id));
							$data['user_data'] = $user_data['0'];

							$subject = translate_phrase('You have received a new message from ') . $data['user_data']['first_name'];
							$user_email_data = $this -> model_user -> get_user_email($intro_id);
							
							$intro_data = $this -> general -> get("user_id, first_name, password, facebook_id", array('user_id' => $intro_id));
							$data['intro_user_data'] = $intro_data['0'];

							//echo $this->db->last_query();exit;
							if ($user_email_data) {
								$data['email_content'] = '';
								
								$data['btn_link'] = base_url().'user/user_info/'.$this->utility->encode($data['user_data']['user_id']).'/'.$this->utility->encode($data['intro_user_data']['user_id']).'/'.$data['intro_user_data']['password'].'?redirect_intro_id='.$data['user_intro_id'];
								$data['btn_text'] = translate_phrase('View Message');
								$data['email_title'] = translate_phrase('You have received a new message from ') . $data['user_data']['first_name'] . translate_phrase(' on ') . date('F j ') . translate_phrase(' at ') . date('g:ia');
								$email_template = $this -> load -> view('email/common', $data, true);
								$this -> datetix -> mail_to_user($user_email_data['email_address'], $subject, $email_template);
							}
						}
						//http://localhost/datetix/home/mail_action/YSD9iJMrSteMp4EZm69YkzffHMhKrO0X6bOI0wi4INM?return_to=http://localhost/datetix/hongkong/my-date.html&type=message&tab_name=PastDateTab&redirect_intro_id=43
						//echo '<a href="'.$data['btn_link'].'">click to go</a>';
						
						echo '<li>
								<div class="Time-line">
									<div class="Time-section">' . date('h:i A', strtotime($data['chat_message_time'])) . '</div>
		                		</div>
								<div class="susan-chat"><span>' . translate_phrase('You') . ': </span> ' . $data['chat_message'] . '</div>
							</li>';
					}
				} else {
					echo 'permission denied';
				}
			}
		}
	}

	/**
	 * [Ajax-call] suggest_venue Function :: Fetch record from foursquare api
	 * @access public
	 * @return View data
	 * @author Rajnish Savaliya
	 */

	public function suggest_venue() {
		$city_id = $this -> session -> userdata('sess_city_id');
		$city_data = $this -> model_user -> get_city_by_id($city_id);

		$this -> load -> library('EpiFoursquare');
		$clientId = $this -> config -> item('clientID');
		$clientSecret = $this -> config -> item('clientSecret');
		$accessToken = $this -> config -> item('accessToken');

		$fsObj = new EpiFoursquare($clientId, $clientSecret, $accessToken);
		$fsObjUnAuth = new EpiFoursquare($clientId, $clientSecret);
		if ($q = $this -> input -> post('query')) {
			$para['query'] = $q;
		} else {
			$para['query'] = 'cafe';
		}

		// Set default latitude and longitude to Hong Kong
		if (!$city_data -> latitude) {
			$city_data -> latitude = 22.250000;
		}
		if (!$city_data -> longitude) {
			$city_data -> longitude = 114.166702;
		}

		$para['v'] = date('Ymd');
		$para['ll'] = $city_data -> latitude . ',' . $city_data -> longitude;
		$venue = $fsObjUnAuth -> get('/venues/search', $para);
		$vanue_data = array();
		if (isset($venue -> response -> venues)) {
			foreach ($venue->response->venues as $key => $item) {
				$vanue_data[$key]['id'] = $item -> id;
				$vanue_data[$key]['name'] = $item -> name;
				$vanue_data[$key]['address'] = isset($item -> location -> address) ? $item -> location -> address : '';
				$vanue_data[$key]['city'] = isset($item -> location -> city) ? $item -> location -> city : '';

			}
		}
		$data['venue_data'] = $vanue_data;
		echo json_encode($data);
	}

	public function use_date_ticket() {
		$response['type'] = 'error';
		$response['msg'] = 'Error occured. please try again';

		if ($post_data = $this -> input -> post()) {
			$this -> general -> set_table('user');
			$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
			$data['user_data'] = $user_data['0'];

			$user_intro_id = $post_data['user_intro_id'];
			$this -> general -> set_table('user_intro');
			$user_id = $this -> user_id;
			$intro_data = $this -> general -> get("", array('user_intro_id' => $user_intro_id));

			//FETCH DATE data :::
			if ($intro_data = $intro_data['0']) {
				$update_intro_data = array();
				if ($post_data['is_ticket_use'] == 1) {
					if ($intro_data['user1_id'] == $user_id && $intro_data['user1_date_ticket_paid_by'] == 0) {
						if ($data['user_data']['num_date_tix'] > 0) {
							$update_intro_data['user1_date_ticket_paid_by'] = $user_id;
						} else {
							$response['msg'] = translate_phrase('No Tickets');
						}
					}

					if ($intro_data['user2_id'] == $user_id && $intro_data['user2_date_ticket_paid_by'] == 0) {
						if ($data['user_data']['num_date_tix'] > 0) {
							$update_intro_data['user2_date_ticket_paid_by'] = $user_id;
						} else {
							$response['msg'] = translate_phrase('No Tickets');
						}
					}
				} else {
					if ($intro_data['user1_id'] == $user_id && $intro_data['user1_date_ticket_paid_by'] != 0) {
						$update_intro_data['user1_date_ticket_paid_by'] = 0;
					}

					if ($intro_data['user2_id'] == $user_id && $intro_data['user2_date_ticket_paid_by'] != 0) {
						$update_intro_data['user2_date_ticket_paid_by'] = 0;
					}
				}
				
				if($update_intro_data)
				{
					$this -> general -> set_table('user_intro');
					if ($this -> general -> update($update_intro_data, array('user_intro_id' => $user_intro_id))) {
						$response['type'] = 'success';
						if ($post_data['is_ticket_use'] == 1) {
							$response['msg'] = 'Thanks for using ticket';
						} else {
							$response['msg'] = 'We have deduct your ticket from this date.';
						}
					}
				}
				
			}
		}
		echo json_encode($response);
	}

	/**
	 * [HTTP Request ] accept_date Function :: Accept date - set date_time in user_date table and send email to both the users
	 * @access public
	 * @return Return to pending
	 * @author Rajnish Savaliya
	 */
	public function accept_date() {
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$data['user_data'] = $user_data['0'];

		if ($id_encoded = $this -> input -> get('date')) {
			$this -> session -> set_userdata('selected_date_id', $id_encoded);
		} else {
			$id_encoded = $this -> session -> userdata('selected_date_id');

			if (!$id_encoded) {
				echo 'in suggest date session is lost!!';
			}
		}

		$user_date_id = $this -> utility -> decode($id_encoded);
		if ($return_to = $this -> input -> get('return_to')) {
			$return_url = url_city_name() . '/' . $return_to;
		} else {
			$return_url = url_city_name() . '/index.html';
		}
		$this -> general -> set_table('user_date');
		$date_data = $this -> general -> get("", array('user_date_id' => $user_date_id));
		if ($date_data) {
			$date_data = $date_data['0'];
			$intro_id = $date_data['date_suggested_by_user_id'];

			$is_premium_intro = $this -> datetix -> is_premium_user($intro_id, PERMISSION_UNLIMITED_DATES);
			$is_premius_member = $this -> datetix -> is_premium_user($this -> user_id, PERMISSION_UNLIMITED_DATES);
			$mutual_friends_on_datetix = $this -> datetix -> datetix_mutual_friend($this -> user_id, $intro_id);
			$fb_mutual_friend_use_app = count($mutual_friends_on_datetix);

			$is_ticket_paid = 0;
			$this -> general -> set_table('user_intro');
			$intro_row_data = $this -> general -> get("", array('user_intro_id' => $date_data['user_intro_id']));
			$intro_row_data = $intro_row_data['0'];

			if ($is_premium_intro || $is_premius_member || $fb_mutual_friend_use_app > 0) {
				//echo 'free date';
				$is_ticket_paid = 1;
			} else {
				//echo 'paid date';
				$cur_is_ticket_paid = 0;
				$intro_is_ticket_paid = 0;

				$this -> general -> set_table('user');
				$data['intro_data'] = $this -> general -> get("", array('user_id' => $intro_id));
				$data['intro_data'] = $data['intro_data']['0'];

				if ($intro_row_data['user1_id'] == $this -> user_id) {
					//********* DEDUCT FROM Intro USER *************//
					if ($intro_row_data['user2_date_ticket_paid_by'] != 0) {
						//Update Intro Tickets
						if ($data['intro_data']['num_date_tix'] > 0) {
							$intro_is_ticket_paid = 1;
							$this -> model_user -> update_user($intro_id, array('num_date_tix' => $data['intro_data']['num_date_tix'] - 1));
						} else {
							$this -> session -> set_flashdata('date_error_msg', $data['intro_data']['first_name'] . translate_phrase(" hasn't enough tickets for date with you."));
						}
					}

					//********* DEDUCT FROM Current USER *************//
					if ($intro_row_data['user1_date_ticket_paid_by'] != 0) {
						//Update User Tickets
						if ($data['user_data']['num_date_tix'] > 0) {
							$cur_is_ticket_paid = 1;
							$this -> model_user -> update_user($this -> user_id, array('num_date_tix' => $data['user_data']['num_date_tix'] - 1));
						} else {
							$this -> session -> set_flashdata('date_error_msg', translate_phrase(" you haven't enough tickets for accept a date."));
						}
					}
				}

				if ($intro_row_data['user2_id'] == $this -> user_id) {
					//********* DEDUCT FROM Intro USER *************//
					if ($intro_row_data['user1_date_ticket_paid_by'] != 0) {
						//Update Intro Tickets
						if ($data['intro_data']['num_date_tix'] > 0) {
							$intro_is_ticket_paid = 1;
							$this -> model_user -> update_user($intro_id, array('num_date_tix' => $data['intro_data']['num_date_tix'] - 1));
						} else {
							$this -> session -> set_flashdata('date_error_msg', $data['intro_data']['first_name'] . translate_phrase(" hasn't enough tickets for date with you."));
						}
					}

					//********* DEDUCT FROM Current USER *************//
					if ($intro_row_data['user2_date_ticket_paid_by'] != 0) {
						//Update User Tickets
						if ($data['user_data']['num_date_tix'] > 0) {
							$cur_is_ticket_paid = 1;
							$this -> model_user -> update_user($this -> user_id, array('num_date_tix' => $data['user_data']['num_date_tix'] - 1));
						} else {
							$this -> session -> set_flashdata('date_error_msg', translate_phrase(" you haven't enough tickets for accept a date."));
						}
					}

				}

				if ($intro_row_data['user1_date_ticket_paid_by'] || $intro_row_data['user2_date_ticket_paid_by']) {
					$is_ticket_paid = 1;
				} else {
					$this -> session -> set_flashdata('date_error_msg', translate_phrase("You must use a date ticket in order to accept your date with ") . $data['intro_data']['first_name']);
				}
			}

			if ($is_ticket_paid) {
				$this -> session -> set_userdata('redirect_intro_id', $intro_row_data['user_intro_id']);
				$this -> session -> set_userdata('type', 'message');
				$this -> session -> set_userdata('redirect_tab_name', 'UpcominEasyTab');
				$return_url = url_city_name() . '/my-date.html#upcoming';

				$update_data['date_accepted_by_user_id'] = $this -> user_id;
				$update_data['date_accepted_time'] = SQL_DATETIME;

				$data['user_info'] = $this -> model_user -> get_user_data($intro_id);

				$this -> general -> set_table('user_date');
				if ($this -> general -> update($update_data, array('user_date_id' => $user_date_id))) {
					$subject = $data['user_data']['first_name'] . translate_phrase(' has accepted your date suggestion');
					$user_email_data = $this -> model_user -> get_user_email($intro_id);
					if ($user_email_data) {

						$data['email_title'] = translate_phrase("Congrates") . ' ' . $data['user_info']['first_name'] . ',';
						$data['email_content'] = $subject . '.';

						$user_link = $this -> utility -> encode($data['user_info']['user_id']);
						if ($data['user_info']['password']) {
							$user_link .= '/' . $data['user_info']['password'];
						}
						$data['btn_link'] = base_url() . 'home/mail_action/' . $user_link . '?return_to=' . base_url() . url_city_name() . '/my-date.html&type=message&redirect_intro_id=' . $intro_row_data['user_intro_id'];
						$data['btn_text'] = translate_phrase('Chat with ') . $data['user_data']['first_name'];

						$email_template = $this -> load -> view('email/common', $data, true);
						if ($this -> datetix -> mail_to_user($user_email_data['email_address'], $subject, $email_template)) {
							$subject = translate_phrase('You have accepted a date with ') . $data['user_info']['first_name'] . '!';
							$user_email_data = $this -> model_user -> get_user_email($this -> user_id);
							if ($user_email_data) {
								$data['email_content'] = $subject;
								$data['email_title'] = 'Hello ' . $data['user_data']['first_name'];
								$data['email_content'] = $subject;

								$user_link = $this -> utility -> encode($data['user_data']['user_id']);
								if ($data['user_data']['password']) {
									$user_link .= '/' . $data['user_info']['password'];
								}
								$data['btn_link'] = base_url() . 'home/mail_action/' . $user_link . '?return_to=' . base_url() . url_city_name() . '/my-date.html&type=message&redirect_intro_id=' . $intro_row_data['user_intro_id'];
								$data['btn_text'] = translate_phrase('Chat with ') . $data['user_info']['first_name'];

								$email_template = $this -> load -> view('email/common', $data, true);
								if ($this -> datetix -> mail_to_user($user_email_data['email_address'], $subject, $email_template)) {
									$data['heading_txt'] = translate_phrase('Date Confirmed');
									$data['page_title'] = translate_phrase('Date Confirmed');
									$data['content'] = '<font color=darkgreen>' . translate_phrase('Congratulations, your date with ') . $data['user_info']['first_name'] . translate_phrase(' has been confirmed! We will send you a reminder 24 hour before your date.') . '</font> ' . translate_phrase('To confirm further details of your date (e.g. how to find each other), you may now chat with each other online and potentially exchange contact info before your date!');
									$data['btn_txt'] = translate_phrase('Chat with ') . $data['user_info']['first_name'];

									$data['page_name'] = 'user/popup_date_confirmbox';
									$data['return_url'] = $return_url;
									$this -> load -> view('template/editProfileTemplate', $data);
								} else {
									redirect($return_url);
								}
							} else {
								redirect($return_url);
							}
						} else {
							redirect($return_url);
						}
					} else {
						redirect($return_url);
					}
				} else {
					redirect($return_url);
				}
			} else {
				redirect($return_url);
			}
		}
	}

	/**
	 * [POP UP]suggest_date Function :: Display Suggestion box
	 * @access public
	 * @return [date suggestion sent popup]
	 * @author Rajnish Savaliya
	 */
	public function suggest_date() {

		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$data['user_data'] = $user_data['0'];

		if ($user_intro_id_encoded = $this -> input -> get('intro')) {
			$this -> session -> set_userdata('selected_intro_id', $user_intro_id_encoded);
		} else {
			$user_intro_id_encoded = $this -> session -> userdata('selected_intro_id');
			if (!$user_intro_id_encoded) {
				//remove this - for debuggin
				echo 'in suggest date session is lost!!';
			}
		}

		$user_intro_id = $this -> utility -> decode($user_intro_id_encoded);

		if ($this -> session -> userdata('return_url')) {
			$return_url = $this -> session -> userdata('return_url');
			//$this->session->unset_userdata('return_url');
			$data['return_url'] = $return_url;
			$sent_url = 'suggestion-sent.html?return_to=my-date.html&intro=' . $user_intro_id_encoded;
		} else {
			if ($return_to = $this -> input -> get('return_to')) {
				if ($tab = $this -> input -> get('tab')) {
					$return_to .= '#' . $tab;
				}

				$return_url = $return_to;
			} else {
				$return_url = 'index.html';
			}

			$data['return_url'] = $return_url;
			$sent_url = 'suggestion-sent.html?return_to=' . $return_url . '&intro=' . $user_intro_id_encoded;
			//echo $sent_url;exit;
			$return_url = base_url() . url_city_name() . '/' . $return_url;
		}
		$this -> general -> set_table('user_intro');
		$user_id = $this -> user_id;
		$intro_data = $this -> general -> get("", array('user_intro_id' => $user_intro_id));

		if ($intro_data) {
			$intro_data = $intro_data['0'];
			if ($intro_data['user1_id'] == $user_id) {
				$intro_id = $intro_data['user2_id'];

				$update_intro_data['user1_not_interested_time'] = '0000-00-00 00:00:00';
				$update_intro_data['user1_not_interested_reason'] = '';

				$not_interested = $intro_data['user1_not_interested_time'];
				$intro_not_interested = $intro_data['user2_not_interested_time'];
				
				$data['is_ticket_paid_by_user'] = $intro_data['user1_date_ticket_paid_by'];
			}
			if ($intro_data['user2_id'] == $user_id) {
				$intro_id = $intro_data['user1_id'];

				$update_intro_data['user2_not_interested_reason'] = '';
				$update_intro_data['user2_not_interested_time'] = '0000-00-00 00:00:00';

				$not_interested = $intro_data['user2_not_interested_time'];
				$intro_not_interested = $intro_data['user1_not_interested_time'];
				
				$data['is_ticket_paid_by_user'] = $intro_data['user2_date_ticket_paid_by'];
			}

			//Intro's paid date ticket
			if ($intro_data['user1_id'] == $intro_id) {
				$data['is_ticket_paid_by_intro'] = $intro_data['user1_date_ticket_paid_by'];
			}
			if ($intro_data['user2_id'] == $intro_id) {
				$data['is_ticket_paid_by_intro'] = $intro_data['user2_date_ticket_paid_by'];
			}

			$data['user_info'] = $this -> model_user -> get_user_data($intro_id);

			//FETCH DATE data :::
			$this -> general -> set_table('user_date');
			$user_date_data = $this -> general -> get("", array('user_intro_id' => $user_intro_id));

			//set title text
			if ($user_date_data && $not_interested == '0000-00-00 00:00:00' && $intro_not_interested == '0000-00-00 00:00:00') {
				$data['heading_txt'] = translate_phrase('Change Date Idea');
				$data['page_title'] = translate_phrase('Change Date Idea');
			} else {
				$data['heading_txt'] = translate_phrase('Suggest Date Idea');
				$data['page_title'] = translate_phrase('Suggest Date Idea');
			}

			//Store DAte Data......
			if ($post_data = $this -> input -> post()) {
				//random text
				$update_data['neighborhood_id'] = $post_data['neighborhood_id'];

				$update_data['user_intro_id'] = $user_intro_id;

				$update_data['date_type_id'] = $post_data['date_type_id'];
				$update_data['date_type_other'] = $post_data['date_type_other'];
				if ($post_data['venue_other']) {
					$update_data['venue_id'] = null;
					if ($post_data['venue_other_id']) {
						$update_data['venue_other'] = $post_data['venue_other_id'];
					} else {
						//Random string..
						$update_data['venue_other'] = '_' . $post_data['venue_other'] . '_';
					}

				} else {
					$update_data['venue_other'] = '';
					if ($post_data['past_vanue_id']) {
						if (is_numeric($post_data['past_vanue_id'])) {
							$update_data['venue_id'] = $post_data['past_vanue_id'];
							$update_data['venue_other'] = null;
						} else {
							$update_data['venue_id'] = null;
							$update_data['venue_other'] = $post_data['past_vanue_id'];
						}

					} elseif (isset($post_data['venue_id'])) {
						$update_data['venue_id'] = $post_data['venue_id'];
					}
				}

				$update_data['date_suggested_by_user_id'] = $user_id;
				$update_data['date_time'] = date('Y-m-d', $post_data['prefer_date_days']) . ' ' . date('H:i:s', strtotime($post_data['prefer_date_time']));

				$update_data['date_suggested_time'] = SQL_DATETIME;
				$update_data['date_accepted_by_user_id'] = '0';
				$update_data['date_accepted_time'] = '0000-00-00 00:00:00';

				$update_data['date_cancelled_by_user_id'] = '0';
				$update_data['date_cancelled_time'] = '0000-00-00 00:00:00';

				$update_intro_data = array();
				if ($post_data['use_date_ticket']) {
					if ($intro_data['user1_id'] == $user_id && $intro_data['user1_date_ticket_paid_by'] == 0) {
						if ($data['user_data']['num_date_tix'] > 0) {
							$update_intro_data['user1_date_ticket_paid_by'] = $user_id;
						}
					}

					if ($intro_data['user2_id'] == $user_id && $intro_data['user2_date_ticket_paid_by'] == 0) {
						if ($data['user_data']['num_date_tix'] > 0) {
							$update_intro_data['user2_date_ticket_paid_by'] = $user_id;
						}
					}
				} else {
					if ($intro_data['user1_id'] == $user_id && $intro_data['user1_date_ticket_paid_by'] != 0) {
						$update_intro_data['user1_date_ticket_paid_by'] = '0';
					}

					if ($intro_data['user2_id'] == $user_id && $intro_data['user2_date_ticket_paid_by'] != 0) {
						$update_intro_data['user2_date_ticket_paid_by'] = '0';
					}
				}
				
				$is_update = 0;
				if($update_intro_data)
				{
					$this -> general -> set_table('user_intro');
					if ($this -> general -> update($update_intro_data, array('user_intro_id' => $user_intro_id)))
					{
						$is_update = 1;
						//Update User Tickets
						//$this->model_user->update_user($user_id,array('num_date_tix'=>$data['user_data']['num_date_tix'] - 1));
					}
				}
				
				if ($user_date_data) {
					$this -> general -> set_table('user_date');
					if ($this -> general -> update($update_data, array('user_intro_id' => $user_intro_id)) || $is_update) {
						$user_pro_noun = ($data['user_data']['gender_id'] == 1) ? translate_phrase('his') : translate_phrase('her');
						$subject = $data['user_data']['first_name'] . translate_phrase(' would like to re-schedule/change ' . $user_pro_noun . ' date with you');
						
						$user_email_data = $this -> model_user -> get_user_email($intro_id);
						$data['email_title'] = translate_phrase('Hello ') . $data['user_info']['first_name'];
						$data['email_content'] = $subject;

						$user_link = $this -> utility -> encode($data['user_info']['user_id']);
						if ($data['user_info']['password']) {
							$user_link .= '/' . $data['user_info']['password'];
						}
						$data['btn_link'] = base_url() . 'home/mail_action/' . $user_link . '?return_to=' . base_url() . url_city_name() . '/my-date.html&type=message&redirect_intro_id=' . $user_intro_id;
						$data['btn_text'] = translate_phrase('Chat with ') . $data['user_data']['first_name'];

						$email_template = $this -> load -> view('email/common', $data, true);
						if ($this -> datetix -> mail_to_user($user_email_data['email_address'], $subject, $email_template)) {
							$subject = ' We have informed ' . $data['user_info']['first_name'] . ' of your date re-schedule/change';
							$user_email_data = $this -> model_user -> get_user_email($this -> user_id);
							$data['email_content'] = $subject;
							$data['email_title'] = translate_phrase('Hello ') . $data['user_data']['first_name'];
							$data['email_content'] = $subject;

							$user_link = $this -> utility -> encode($data['user_data']['user_id']);
							if ($data['user_data']['password']) {
								$user_link .= '/' . $data['user_data']['password'];
							}
							$data['btn_link'] = base_url() . 'home/mail_action/' . $user_link . '?return_to=' . base_url() . url_city_name() . '/my-date.html&type=message&redirect_intro_id=' . $user_intro_id;
							$data['btn_text'] = translate_phrase('Chat with ') . $data['user_info']['first_name'];

							$email_template = $this -> load -> view('email/common', $data, true);
							if ($this -> datetix -> mail_to_user($user_email_data['email_address'], $subject, $email_template)) {

							}
						}

						$this -> general -> set_table('user_intro');
						$this -> general -> update($update_intro_data, array('user_intro_id' => $user_intro_id));

						$this -> session -> set_userdata('date_type', 'change_date');
					}
				} else {
					
					if ($this -> general -> save($update_data)) {
						$subject = $data['user_data']['first_name'] . translate_phrase(' wants to meet you for a date and has suggested a date idea!');
						$user_email_data = $this -> model_user -> get_user_email($intro_id);
						$data['email_title'] = 'Hello ' . $data['user_info']['first_name'];
						$data['email_content'] = $subject;
						$data['btn_link'] = base_url() . 'user/user_info/' . $this -> utility -> encode($data['user_data']['user_id']) . '/' . $this -> utility -> encode($intro_id) . '/' . $data['user_info']['password'];
						$data['btn_text'] = translate_phrase("View ") . $data['user_data']['first_name'] . translate_phrase("'s profile");

						$email_template = $this -> load -> view('email/common', $data, true);
						//echo $email_template;exit;
						if ($this -> datetix -> mail_to_user($user_email_data['email_address'], $subject, $email_template)) {
							$subject = translate_phrase('We have informed ' . $data['user_info']['first_name'] . ' of your date idea');
							$user_email_data = $this -> model_user -> get_user_email($this -> user_id);
							$data['email_content'] = $subject;
							$data['email_title'] = translate_phrase('Hello ') . $data['user_data']['first_name'];
							$data['email_content'] = $subject;

							$data['btn_link'] = base_url() . 'user/user_info/' . $this -> utility -> encode($data['user_info']['user_id']) . '/' . $this -> utility -> encode($user_id) . '/' . $data['user_data']['password'];
							$data['btn_text'] = translate_phrase("View ") . $data['user_info']['first_name'] . translate_phrase("'s profile");

							$email_template = $this -> load -> view('email/common', $data, true);
							if ($this -> datetix -> mail_to_user($user_email_data['email_address'], $subject, $email_template)) {

							}
						}
					}
				}
				redirect('/' . url_city_name() . '/' . $sent_url);
			}

			if (isset($user_date_data['0']['venue_other']) && $user_date_data['0']['venue_other']) {
				preg_match('/_(.*?)_/', $user_date_data['0']['venue_other'], $display);
				if ($display) {
					$user_date_data['0']['venue_other_name'] = $display['1'];
					$user_date_data['0']['venue_other'] = '';
				} else {
					$this -> load -> library('EpiFoursquare');
					$clientId = $this -> config -> item('clientID');
					$clientSecret = $this -> config -> item('clientSecret');
					$accessToken = $this -> config -> item('accessToken');

					$fsObj = new EpiFoursquare($clientId, $clientSecret, $accessToken);
					$fsObjUnAuth = new EpiFoursquare($clientId, $clientSecret);
					$para['v'] = date('Ymd');

					$tmp_data = $fsObjUnAuth -> get('/venues/' . $user_date_data['0']['venue_other'], $para);
					if ($tmp_data -> response) {
						$user_date_data['0']['venue_other_name'] = isset($tmp_data -> response -> venue -> name) ? $tmp_data -> response -> venue -> name : '';
					}
				}

			}

			$data['user_date_data'] = $user_date_data;

			//*** user_preferred_date_type -----------------------------------------//
			$data['date_type'] = $this -> model_user -> get_date_type($this -> language_id);
			$fields = array('usr.date_type_id', 'dt_type.description as date_type_description');
			$from = 'user_preferred_date_type as usr';
			$joins = array('date_type as dt_type' => array('usr.date_type_id = dt_type.date_type_id', 'LEFT'));

			$where['dt_type.display_language_id'] = $this -> language_id;
			$where['usr.user_id'] = $user_id;

			$data['intro_prefered_date_type'] = $this -> model_user -> multijoins($fields, $from, $joins, $where, NULL, 'usr.user_preferred_date_type_id asc');
			unset($where);

			//*** Neighbourhood -----------------------------------------//
			$city_id = $this -> session -> userdata('sess_city_id');
			$data['neighborhood'] = $this -> model_user -> get_district($this -> language_id, $city_id);

			$selected_venue = array();
			if (isset($user_date_data['0']['venue_id']) && $user_date_data['0']['venue_id']) {
				$this -> general -> set_table('venue');
				$venue_data = $this -> general -> get("", array('venue_id' => $user_date_data['0']['venue_id']));

				$selected_venue = $venue_data['0'];
				$data['selected_venue'] = $selected_venue;
			}

			//*** Recommonded Vanue -----------------------------------------//
			if ($data['neighborhood']) {
				if (isset($user_date_data['0']['date_type_id'])) {
					$neighbour_id = '';
					if (isset($user_date_data['0']['neighborhood_id'])) {
						$neighbour_id = $user_date_data['0']['neighborhood_id'];
					} else if ($selected_venue['neighborhood_id']) {
						$neighbour_id = $selected_venue['neighborhood_id'];
					}

					$where = 'venue.display_language_id=' . $this -> language_id;
					$where .= ' AND neighborhood_id =' . $neighbour_id;
					$where .= ' AND date_type_id = ' . $user_date_data['0']['date_type_id'];

					$query = 'SELECT venue.* From venue_date_type JOIN venue on venue.venue_id = venue_date_type.venue_id
                WHERE ' . $where . ' group by venue.venue_id  ORDER BY `view_order` ASC';
					$data['recommonded_venue'] = $this -> general -> sql_query($query);
					if ($data['recommonded_venue']) {
						foreach ($data['recommonded_venue'] as $key => $venue) {
							$query = 'SELECT * FROM venue_date_type
						JOIN date_type on date_type.date_type_id = venue_date_type.date_type_id
						WHERE date_type.display_language_id=' . $this -> language_id . '
	                	AND venue_date_type.venue_id = ' . $venue['venue_id'] . '
	                	Group By date_type.date_type_id
	                	ORDER BY `view_other` ASC';

							$venue_dates = array();
							if ($venue_date = $this -> general -> sql_query($query)) {
								foreach ($venue_date as $value) {
									$venue_dates[] = $value['description'];
								}
							}
							$data['recommonded_venue'][$key]['venue_dates'] = $venue_dates;
						}
					}
				}

				$query = 'SELECT user_date.*
				FROM user_intro
				JOIN user_date on user_intro.user_intro_id = user_date.user_intro_id
                
                WHERE (user1_id = "' . $this -> user_id . '" OR user2_id = "' . $this -> user_id . '")';
				if (isset($user_date_data['0']['user_date_id'])) {
					$query .= '  AND user_date.user_date_id != ' . $user_date_data['0']['user_date_id'];
				}
				$query .= ' ORDER BY `intro_available_time` DESC';

				$past_venue_datas = array();
				if ($user_date_datas = $this -> general -> sql_query($query)) {
					foreach ($user_date_datas as $key => $user_date) {
						if (isset($user_date['venue_other']) && $user_date['venue_other']) {
							preg_match('/_(.*?)_/', $user_date['venue_other'], $display);
							if ($display) {
								$past_venue_datas[$key]['name'] = $display['1'];
								$past_venue_datas[$key]['venue_id'] = $display['0'];
							} else {
								$this -> load -> library('EpiFoursquare');
								$clientId = $this -> config -> item('clientID');
								$clientSecret = $this -> config -> item('clientSecret');
								$accessToken = $this -> config -> item('accessToken');

								$fsObj = new EpiFoursquare($clientId, $clientSecret, $accessToken);
								$fsObjUnAuth = new EpiFoursquare($clientId, $clientSecret);
								$para['v'] = date('Ymd');

								$tmp_data = $fsObjUnAuth -> get('/venues/' . $user_date['venue_other'], $para);
								if ($tmp_data -> response) {
									$past_venue_datas[$key]['name'] = isset($tmp_data -> response -> venue -> name) ? $tmp_data -> response -> venue -> name : '';
									$past_venue_datas[$key]['venue_id'] = $user_date['venue_other'];
								}
							}
						} else if ($user_date['venue_id']) {

							$fields = array('venue_id', 'name', 'neigh.neighborhood_id', 'neigh.description as neighborhood',
							//'ct.description as city_description',
							//'prvnce.description as province_description',
							//'cntry.description as country_description',
							//'cntry.country_code','cntry.flag_url'
							);

							$from = 'venue as v';
							$joins = array('neighborhood as neigh' => array('v.neighborhood_id = neigh.neighborhood_id', 'Inner'),
							//'city as ct'=>array('ct.city_id = neigh.city_id ','Inner'),
							//'province as prvnce'=>array('ct.province_id = prvnce.province_id','LEFT'),
							//'country as cntry'=>array('prvnce.country_id = cntry.country_id','LEFT')
							);
							unset($where);
							$where['v.venue_id'] = $user_date['venue_id'];
							$where['neigh.display_language_id'] = $this -> language_id;
							//$where['ct.display_language_id'] = $this->language_id;
							//$where['prvnce.display_language_id'] = $this->language_id;
							//$where['cntry.display_language_id'] = $this->language_id;

							if ($temp_past_venue_data = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'v.name asc')) {
								$past_venue_datas[$key] = $temp_past_venue_data['0'];
							}
							/*
							 $this->general->set_table('venue');
							 if($vanue_data = $this->general->get("venue_id,name",array('display_language_id'=>$this->language_id,'venue_id'=>$user_date['venue_id'])))
							 {
							 $past_venue_datas[$key]= $vanue_data['0'];
							 }
							 */
						}
					}

				}
				if ($past_venue_datas) {
					uasort($past_venue_datas, array($this, 'uasort_by_key'));
					$data['past_venue'] = array_map("unserialize", array_unique(array_map("serialize", $past_venue_datas)));
				}

				$mutual_friends = array();
				$mutual_friends_use_app = 0;
				//Fetch Mutual Friend
				if ($data['user_data']['facebook_id'] && $data['user_info']['facebook_id']) {
					$mutual_friends_on_datetix = $this -> datetix -> datetix_mutual_friend($this -> user_id, $intro_id);
					$mutual_friends_use_app = count($mutual_friends_on_datetix);
					$data['fb_mutual_friend'] = $this -> datetix -> fb_mutual_friend($this -> user_id, $intro_id);
					/*
					 try {
					 $fb_id = $this->facebook->getUser();
					 if($fb_id)
					 {
					 $mutual_friends = $this->facebook->api('me/mutualfriends/'.$data['user_info']['facebook_id']);
					 if($mutual_friends['data'])
					 {
					 foreach ($mutual_friends['data'] as $fb_user)
					 {
					 if($this->datetix->is_app_user($fb_user['id']))
					 {
					 $mutual_friends_use_app ++;
					 }
					 }
					 }
					 }
					 else
					 {
					 $this->load->view('direct_fb_login');
					 }
					 } catch (Exception $e) {}
					 */

				}
				$data['fb_mutual_friend_use_app'] = $mutual_friends_use_app;

			}
			$data['intro_data'] = $intro_data;
		}
		$data['page_name'] = 'user/dates/suggest_dates_idea';

		$this -> load -> view('template/editProfileTemplate', $data);
	}

	/**
	 * [POP UP]suggest_date Function :: Display Suggestion box
	 * @access public
	 * @return View data
	 * @author Rajnish Savaliya
	 */
	public function suggestion_sent() {
		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$data['user_data'] = $user_data['0'];

		$user_intro_id = $this -> utility -> decode($this -> input -> get('intro'));

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

		//Intro data
		$this -> general -> set_table('user_intro');
		$user_id = $this -> user_id;
		$intro_data = $this -> general -> get("", array('user_intro_id' => $user_intro_id));

		if ($intro_data) {
			$intro_data = $intro_data['0'];
			//Date Data
			//$this -> general -> set_table('user_date');
			//$data['user_date_data'] = $this -> general -> get("", array('user_intro_id' => $user_intro_id));

			if ($intro_data['user1_id'] == $user_id) {
				$intro_id = $intro_data['user2_id'];
				$data['is_ticket_paid_by_user'] = $intro_data['user1_date_ticket_paid_by'];
			}
			if ($intro_data['user2_id'] == $user_id) {
				$intro_id = $intro_data['user1_id'];
				$data['is_ticket_paid_by_user'] = $intro_data['user2_date_ticket_paid_by'];
			}

			$data['user_info'] = $this -> model_user -> get_user_data($intro_id);

			//Fetch Mutual Friend
			$data['fb_mutual_friend'] = 0;
			$data['fb_mutual_friend_use_app'] = array();

			$mutual_friends = array();
			$mutual_friends_use_app = 0;
			//Fetch Mutual Friend
			if ($data['user_data']['facebook_id'] && $data['user_info']['facebook_id']) {
				$mutual_friends_on_datetix = $this -> datetix -> datetix_mutual_friend($this -> user_id, $intro_id);
				$data['fb_mutual_friend'] = $this -> datetix -> fb_mutual_friend($this -> user_id, $intro_id);
				$mutual_friends_use_app = count($mutual_friends_on_datetix);
				/*
				 try {
				 $fb_id = $this->facebook->getUser();
				 if($fb_id)
				 {
				 $mutual_friends = $this->facebook->api('me/mutualfriends/'.$data['user_info']['facebook_id']);
				 if($mutual_friends['data'])
				 {
				 foreach ($mutual_friends['data'] as $fb_user)
				 {
				 if($this->datetix->is_app_user($fb_user['id']))
				 {
				 $mutual_friends_use_app ++;
				 }
				 }
				 }
				 }
				 else
				 {
				 $this->load->view('direct_fb_login');
				 }
				 } catch (Exception $e) {}
				 */
			}

			$data['fb_mutual_friend_use_app'] = $mutual_friends_use_app;

			if ($this -> session -> userdata('date_type') == 'change_date') {
				$data['heading_txt'] = translate_phrase('Date Change Sent');
				$data['heading_msg'] = translate_phrase('Your date change has been sent to ');
				$data['page_title'] = translate_phrase('Date Change Sent');
			} else {
				$data['heading_txt'] = translate_phrase('Date Suggestion Sent');
				$data['heading_msg'] = translate_phrase('Your date suggestion has been sent to ');
				$data['page_title'] = translate_phrase('Date Suggestion Sent');
			}
			$data['page_name'] = 'user/dates/suggestion_sent';
			$data['return_url'] = $return_url;
			$this -> load -> view('template/editProfileTemplate', $data);
		}
	}

	/**
	 * [Ajax-call] recommendation_vanues Function :: Display Suggestion box
	 * @access public
	 * @return View data
	 * @author Rajnish Savaliya
	 */
	public function recommendation_vanues() {
		if ($data = $this -> input -> post()) {
			$where = 'venue.display_language_id=' . $this -> language_id;

			if ($data['neighborhood_id']) {
				$where .= ' AND neighborhood_id =' . $data['neighborhood_id'];
			}
			if ($data['date_type_id']) {
				$where .= ' AND date_type_id  =' . $data['date_type_id'];
			}

			$query = 'SELECT venue.* From venue_date_type JOIN venue on venue.venue_id = venue_date_type.venue_id WHERE ' . $where . ' group by venue.venue_id ORDER BY `view_order` ASC';
			$data['recommonded_venue'] = $this -> general -> sql_query($query);
			if ($data['recommonded_venue']) {
				foreach ($data['recommonded_venue'] as $key => $venue) {
					$query = 'SELECT * FROM venue_date_type
					JOIN date_type on date_type.date_type_id = venue_date_type.date_type_id
					WHERE date_type.display_language_id=' . $this -> language_id . '
                	AND venue_date_type.venue_id = ' . $venue['venue_id'] . '
                	Group By date_type.date_type_id
                	ORDER BY `view_other` ASC';

					$venue_dates = array();
					if ($venue_date = $this -> general -> sql_query($query)) {
						foreach ($venue_date as $value) {
							$venue_dates[] = $value['description'];
						}
					}
					$data['recommonded_venue'][$key]['venue_dates'] = $venue_dates;
				}
			}
			$this -> load -> view('user/dates/ajax_recommended_venues', $data);
		}
	}


	/**
	 * [Popup ] view Date Feedback
	 * @access public
	 * @return View data
	 * @author Rajnish Savaliya
	 */
	public function view_feedback() {
		$date_id = $this -> utility -> decode($this -> input -> get('date'));
		$intro_id = $this -> utility -> decode($this -> input -> get('u'));
		$data['user_info'] = $this -> model_user -> get_user_data($intro_id);
		$is_premius_member = $this -> datetix -> is_premium_user($this -> user_id, 5);
		$feedback_data = array();
		if ($is_premius_member) {
			$this -> general -> set_table('user_date_feedback');
			if ($feedback_data = $this -> general -> get("*", array('user_date_id' => $date_id, 'user_id' => $intro_id))) {
				$feedback_data = $feedback_data['0'];

				$fields = array('usr.descriptive_word_id', 'dw.description as description');
				$from = 'user_date_feedback_descriptive_word as usr';
				$joins = array('descriptive_word as dw' => array('dw.descriptive_word_id= usr.descriptive_word_id', 'LEFT'));

				$where['user_date_feedback_id'] = $feedback_data['user_date_feedback_id'];
				$where['usr.user_id'] = $intro_id;

				$where['dw.display_language_id'] = $this -> language_id;
				$where['usr.user_date_feedback_id'] = $feedback_data['user_date_feedback_id'];

				$feedback_data['descriptive_words'] = $this -> general -> multijoins_arr($fields, $from, $joins, $where, '', 'view_order asc');
			}
		}
		$data['looks'] = $this -> model_user -> get_looks($this -> language_id);
		$data['user_feedback'] = $feedback_data;
		$data['heading_txt'] = $data['user_info']['first_name'] . translate_phrase("'s Feedback of You");
		$data['page_title'] = translate_phrase('View Feedback');
		$data['page_name'] = 'user/dates/popup_view_feedback';
		$data['return_url'] = url_city_name() . '/my-date.html#past';
		;

		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("", array('user_id' => $this -> user_id));
		$data['user_data'] = $user_data['0'];

		//echo "<pre>";print_r($data);exit;
		$this -> load -> view('template/editProfileTemplate', $data);

	}

	/**
	 * [Form Request] Submit Date Feedback
	 * @access public
	 * @return View data
	 * @author Rajnish Savaliya
	 */
	public function submit_feedback() {
		$date_id = $this -> utility -> decode($this -> input -> get('date'));
		$this -> general -> set_table('user_date');
		if ($date_data = $this -> general -> get("*", array('user_date_id' => $date_id))) {
			$feedback_data = $this -> input -> post();

			$this -> general -> set_table('user');
			$user_data = $this -> general -> get("first_name,gender_id", array('user_id' => $this -> user_id));
			$intro_id = $feedback_data['intro_id'];

			$condition['user_id'] = $this -> user_id;
			$condition['user_date_id'] = $date_id;

			$descriptive_words_id = $feedback_data['descriptive_word_id'];
			unset($feedback_data['descriptive_word_id']);
			unset($feedback_data['intro_id']);
			$this -> general -> set_table('user_date_feedback');
			if ($check_data = $this -> general -> get("user_date_feedback_id,date_showed_up", $condition)) {
				if ($feedback_data['date_showed_up']) {
					$feedback_data['date_refund_request'] = "";
				} else {
					$feedback_data['date_looks'] = "0";
					$feedback_data['date_attitude'] = "0";
					$feedback_data['date_conversation'] = "0";
					$feedback_data['date_chemistry'] = "0";
					$feedback_data['date_overall'] = "0";
					$feedback_data['date_comments'] = "";
				}

				$feedback_data['feedback_time'] = SQL_DATETIME;
				$this -> general -> update($feedback_data, $condition);
				$user_date_feedback_id = isset($check_data['0']['user_date_feedback_id']) ? $check_data['0']['user_date_feedback_id'] : '';
			} else {
				$feedback_data = array_merge($feedback_data, $condition);
				$feedback_data['feedback_time'] = SQL_DATETIME;

				if ($user_date_feedback_id = $this -> general -> save($feedback_data)) {

				}
			}

			if ($user_date_feedback_id) {
				$descriptive_word_data = explode(',', $descriptive_words_id);
				if ($descriptive_word_data) {
					$save_data['user_date_feedback_id'] = $user_date_feedback_id;
					$save_data['user_id'] = $this -> user_id;

					$this -> general -> set_table('user_date_feedback_descriptive_word');
					$this -> general -> delete($save_data);

					if ($feedback_data['date_showed_up']) {
						foreach ($descriptive_word_data as $word) {
							$save_data['descriptive_word_id'] = $word;
							$this -> general -> save($save_data);
						}
					}
				}

				//Email Template
				$this -> general -> set_table('user');
				$user_info = $this -> general -> get("first_name", array('user_id' => $intro_id));
				$user_pro_noun = ($user_data['0']['gender_id'] == 1) ? translate_phrase('his') : translate_phrase('her');
				$user_pro_noun2 = ($user_data['0']['gender_id'] == 1) ? translate_phrase('him') : translate_phrase('her');

				if ($user_info) {
					if ($feedback_data['date_showed_up'] == 1) {
						$subject = 'Your feedback about your date with ' . $user_info['0']['first_name'] . ' has been recorded.';
						$subject_for_intro = $user_data['0']['first_name'] . translate_phrase(" just submitted feedback about " . $user_pro_noun . " date with you");
					} else {
						$subject = 'Your date ticket refund request has been received.';
						$subject_for_intro = $user_data['0']['first_name'] . translate_phrase(" just told us you didn't show up for your date with ") . $user_pro_noun2;
					}

					//Email to user
					$user_email_data = $this -> model_user -> get_user_email($this -> user_id);
					$data['email_title'] = 'Hello ' . $user_data['0']['first_name'] . ',';
					$data['email_content'] = $subject;
					$email_template = $this -> load -> view('email/date_suggestion', $data, true);
					$this -> datetix -> mail_to_user($user_email_data['email_address'], $subject, $email_template);

					//Email to user's intro
					$intro_email_data = $this -> model_user -> get_user_email($intro_id);
					$data['email_title'] = 'Hello ' . $user_info['0']['first_name'] . ',';
					$data['email_content'] = $subject_for_intro;
					$email_template = $this -> load -> view('email/date_suggestion', $data, true);
					$this -> datetix -> mail_to_user($intro_email_data['email_address'], $subject_for_intro, $email_template);

					//Email to DateTix admin for refund request
					$intro_email_data = $this -> model_user -> get_user_email($intro_id);
					$data['email_title'] = 'Refund Request Received';
					$data['email_content'] = 'Refund request received from ' . $user_email_data['email_address'];
					$email_template = $this -> load -> view('email/date_suggestion', $data, true);
					$this -> datetix -> mail_to_user('info@datetix.com', 'Refund Request Received', $email_template);

				}
				$this -> session -> set_flashdata('date_success_msg', translate_phrase("Your feedback has been recorded."));
			}
		}
		$return_url = '/' . url_city_name() . '/my-date.html#past';
		redirect($return_url);
	}

	public function uasort_by_key($a, $b) {
		$key = 'name';
		return strcasecmp($a[$key], $a[$key]);
	}
}
?>
