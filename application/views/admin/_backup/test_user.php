<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Test_user extends MY_Controller {

	var $language_id = '1';
	public function __construct() {
		
		parent::__construct();
		$this -> load -> model('model_user');
		$this -> load -> model('general_model');
		if ($user_id = $this -> session -> userdata('user_id')) {
			$this -> language_id = $this -> session -> userdata('sess_language_id');
			
			if(!$this -> session -> userdata('sess_city_id'))
			{
				$this -> session -> set_userdata('sess_city_id',$this->config->item('default_city'));
			}
		}
	}
	

	/**
	 * User Information Function :: Retrive User Information based on User id, user_id is the id of user whose profile is being viewed
	 *	cur_user_id is user id of the loged in user
	 * @access public
	 * @param User Id
	 * @return View data
	 * @author Rajnish Savaliya
	 */
	public function user_info($view_user_id, $login_user_id = '', $pass = '') {
		$this->benchmark->mark('code_start');
         $data['is_review_resricted'] = false;
         if ($access_permission = $this -> session -> userdata('LIMITED_ACCESS')) {
			$allow_permission = "review_application";
			$data['is_review_resricted'] = in_array($allow_permission, $access_permission);

		}
		$super_admin = $this->session->userdata('superadmin_logged_in');
		
		$data['return_page'] = current_url();
		
		if ($intro_row_id = $this -> input -> get('redirect_intro_id')) {
			$this -> session -> set_userdata('redirect_intro_id', $intro_row_id);
		}
		
		$user_id = $this -> utility -> decode($view_user_id);
		$language_id = $this -> session -> userdata('sess_language_id');
		$decode_logged_user_id = $this -> utility -> decode($login_user_id);
		$tmp_usr_data = $this -> model_user -> get_user_data($user_id);
		$user_intros = array();
		
		if ($login_user_id != '') {
			$uid = $this -> session -> userdata('user_id');
                        
                        
			if ($uid) {
				if ($decode_logged_user_id != $uid) {
					$this -> check_auto_sign($view_user_id, $login_user_id, $pass);
				}
			} else {
				$this -> check_auto_sign($view_user_id, $login_user_id, $pass);
			}

			$logged_in_user_id = $this -> session -> userdata('user_id');
			$this -> general_model -> set_table('user_intro');
			$user_intros = $this -> general_model -> custom_get("*", '(user1_id = "' . $user_id . '" AND user2_id="' . $logged_in_user_id . '") OR (user1_id="' . $logged_in_user_id . '" AND user2_id = "' . $user_id . '") ');
		}

		$cur_user_id = $this -> session -> userdata('user_id');
		if (!$user_intros && $user_id != $cur_user_id && !$super_admin) {
			if ($cur_user_id) {

				$data['user_data'] = $this -> model_user -> get_user_data($cur_user_id);
			} else {
				echo '<script type="text/javascript">window.location.href = "' . base_url() . '"</script>';
			}
			$data['page_title'] = 'invalid Intros';
			$data['page_name'] = 'user/message';

			$this -> load -> view('template/editProfileTemplate', $data);
		} else {
			
			
			//Intro data ............................................................
			if ($user_intros) {
				
				$update_track_data = array();
				$tmp_intro = $user_intros['0'];
				if ($tmp_intro['user1_id'] == $cur_user_id) {
					$update_track_data['profile_viewed_by_user1'] = $cur_user_id;
				}

				if ($tmp_intro['user2_id'] == $cur_user_id) {
					$update_track_data['profile_viewed_by_user2'] = $cur_user_id;
				}

				$this -> general_model -> set_table('user_intro');
				$this -> general_model -> update($update_track_data, array('user_intro_id' => $tmp_intro['user_intro_id']));

				if (!is_numeric($user_id) || !is_numeric($decode_logged_user_id)) {
					echo 'sorry.. url is not proper';
					exit ;
				}
				$return_url = base_url() . 'user/user_info/' . $view_user_id . '/' . $login_user_id . '/' . $pass;
				$this -> session -> set_userdata('return_url', $return_url);

				if ($tmp_usr_data) {
					$data['noun'] = ($tmp_usr_data['gender_id'] == 1) ? translate_phrase('he') : translate_phrase('she');
					$data['pro_noun'] = ($tmp_usr_data['gender_id'] == 1) ? translate_phrase('him') : translate_phrase('her');
					$tmp_intro['intro_name'] = $tmp_usr_data['first_name'];
					$tmp_intro['user_id'] = $tmp_usr_data['user_id'];
					
				}
				/* We have removed Date from datetix 
				 * 
				 * 
				
				$query = 'SELECT user_date.*, date_type.description as date_type_desc
				FROM user_date 
                JOIN date_type on date_type.date_type_id = user_date.date_type_id
                
                WHERE 
                date_type.display_language_id = ' . $this -> language_id . '
                AND user_intro_id = ' . $tmp_intro['user_intro_id'];
				$date_data = $this -> general -> sql_query($query);
				echo "<pre>";print_r($date_data);exit;
				
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

						$venue_row_data = $this -> general -> sql_query($venue_sql);
						$date_data = array_merge($date_data, $venue_row_data['0']);

						$query = 'SELECT * FROM venue_date_type
						JOIN date_type on date_type.date_type_id = venue_date_type.date_type_id
						WHERE date_type.display_language_id=' . $this -> language_id . '
	                	AND venue_date_type.venue_id = ' . $date_data['venue_id'] . '
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

				}*/				
				// Chat History
				$this -> general -> set_table('user_intro_chat');
				$history_order_by['user_intro_chat_id'] = 'asc';
				$tmp_intro['chat_history'] = $this -> general -> get("", array('user_intro_id' => $tmp_intro['user_intro_id']),$history_order_by);
				$tmp_intro['upcoming_event_attendance'] = $this->datetix->intro_attendance_upcoming_event($tmp_intro['user_id'],$tmp_usr_data['gender_id']);
				$data['intro'] = $tmp_intro;
				/* END Intro data ............................................................	*/
			}

			$user_datas = array();
			if ($tmp_usr_data) {
				//All Information
				$user_datas = $tmp_usr_data;
				$user_datas['user_age'] = $tmp_usr_data['age'];
				//Mutual Friends
				if ($tmp_usr_data['facebook_id'] && $user_datas['facebook_id']) {
					if ($mutual_friends = $this -> datetix -> fb_mutual_friend($user_id,$cur_user_id)) {
						
						$user_datas['fb_mutual_friend'] = count($mutual_friends) > 1 ? count($mutual_friends) . ' ' . translate_phrase('Mutual Friends') : count($mutual_friends) . ' ' . translate_phrase('Mutual Friend');
					}
				}

				/*-------------Added by Hannan Munshi-----------------*/
				$this -> db -> where('user_ideal_match_filter.user_id', $user_id);
				$this -> db -> where('filter.language_id', $language_id);
				$this -> db -> join('user_ideal_match_filter', 'filter.filter_id=user_ideal_match_filter.filter_id');
				$idealMatchFilters = $this -> db -> get('filter') -> result_array();
				$selectedIdealMatchFilters = array();
				if (!empty($idealMatchFilters)) {
					foreach ($idealMatchFilters as $key => $filters) {
						$selectedIdealMatchFilters[$filters['filter_id']] = $filters['id_to_map'];
					}
				}
				$user_datas['selectedIdealMatchFilters'] = $selectedIdealMatchFilters;

				//-------------------------------------------------------------------------------------------//
				//User data
				$user_datas['user_gender'] = $this -> datetix -> load_data_by_id('gender', $tmp_usr_data['gender_id']);
				$user_datas['user_photos'] = $this -> model_user -> get_photos($user_id, "profile");
				
				$user_datas['is_user_request_sent'] = "0";
				$user_datas['is_user_view_photo'] = "0";
				
				if($tmp_usr_data['privacy_photos'] == 'HIDE')
				{
					
					$this->general->set_table('user_photo_request');
					$photo_condition['requested_by_user_id'] = $cur_user_id;
					$photo_condition['user_id'] = $user_id;
					
					if($photo_request = $this->general->get("",$photo_condition))
					{
						$user_datas['is_user_request_sent'] = "1";
						
						if($photo_request['0']['status'] == '1')
						{
							$user_datas['is_user_view_photo'] = "1";
						}
						$user_datas['photo_request'] = $photo_request['0'];
					}
				}
				

				$user_datas['user_body_type'] = $this -> datetix -> load_data_by_id('body_type', $tmp_usr_data['body_type_id']);
				$user_datas['user_ethnicity'] = $this -> datetix -> load_data_by_id('ethnicity', $tmp_usr_data['ethnicity_id']);
				$user_datas['user_career_stage'] = $this -> datetix -> load_data_by_id('career_stage', $tmp_usr_data['career_stage_id']);
				$user_datas['user_neighborhood'] = $this -> datetix -> load_data_by_id('neighborhood', $tmp_usr_data['current_district_id']);
				//echo "<pre>";print_r($user_datas['user_neighborhood']);exit;
				/*
				 $user_datas['user_current_city'] = $this->datetix->load_data_by_id('city',$tmp_usr_data['current_city_id']);
				 $user_datas['user_current_district'] = $this->datetix->load_data_by_id('district',$tmp_usr_data['current_district_id']);
				 */

				$user_datas['user_looks'] = $this -> datetix -> load_data_by_id('looks', $tmp_usr_data['looks_id']);
				$user_datas['user_eye_color'] = $this -> datetix -> load_data_by_id('eye_color', $tmp_usr_data['eye_color_id']);
				$user_datas['user_hair_color'] = $this -> datetix -> load_data_by_id('hair_color', $tmp_usr_data['hair_color_id']);
				$user_datas['user_hair_length'] = $this -> datetix -> load_data_by_id('hair_length', $tmp_usr_data['hair_length_id']);
				$user_datas['user_skin_tone'] = $this -> datetix -> load_data_by_id('skin_tone', $tmp_usr_data['skin_tone_id']);

				$user_datas['user_relationship_status'] = $this -> datetix -> load_data_by_id('relationship_status', $tmp_usr_data['relationship_status_id']);
				$user_datas['user_religious_belief'] = $this -> datetix -> load_data_by_id('religious_belief', $tmp_usr_data['religious_belief_id']);

				$user_datas['user_child_status'] = $this -> datetix -> load_data_by_id('child_status', $tmp_usr_data['child_status_id']);
				$user_datas['user_child_plan'] = $this -> datetix -> load_data_by_id('child_plan', $tmp_usr_data['child_plan_id']);

				$user_datas['user_smoking_status'] = $this -> datetix -> load_data_by_id('smoking_status', $tmp_usr_data['smoking_status_id']);
				$user_datas['user_drinking_status'] = $this -> datetix -> load_data_by_id('drinking_status', $tmp_usr_data['drinking_status_id']);

				$user_datas['user_exercise_frequency'] = $this -> datetix -> load_data_by_id('exercise_frequency', $tmp_usr_data['exercise_frequency_id']);

				/*-----------Change by Hannan Munshi(replaced '-' with 'to')--------------------*/
				$aI = $this -> datetix -> load_data_by_id('annual_income_range', $tmp_usr_data['annual_income_range_id']);
				$user_datas['user_annual_income_range'] = !empty($aI) ? str_replace('-', ' to ', $aI) : '';
				/*-----------Change by Hannan Munshi--------------------*/

				//-------------------------------------------------------------------------------------------//

				//User wants data :: Prefered Match
				$user_datas['want_age_range_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_age_range_importance']);
				$user_datas['want_looking_for_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_looking_for_importance']);
				$user_datas['want_height_range_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_height_range_importance']);
				$user_datas['want_looks_range_higher_id_description'] = $this -> datetix -> load_data_by_id('looks', $tmp_usr_data['want_looks_range_higher_id']);
				$user_datas['want_looks_range_lower_id_description'] = $this -> datetix -> load_data_by_id('looks', $tmp_usr_data['want_looks_range_lower_id']);
				$user_datas['want_looks_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_looks_importance']);

				$user_datas['want_annual_income_currency_description'] = $this -> datetix -> load_data_by_id('currency', $tmp_usr_data['want_annual_income_currency_id']);
				$user_datas['want_annual_income_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_annual_income_importance']);

				$user_datas['user_want_relationship_type_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_looking_for_importance']);
				$user_datas['user_want_relationship_type'] = $this -> datetix -> user_want($user_id, "relationship_type");

				$user_datas['user_want_descriptive_word'] = $this -> datetix -> user_want($user_id, "descriptive_word");
				$user_datas['want_body_type_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_body_type_importance']);
				$user_datas['user_want_body_type'] = $this -> datetix -> user_want($user_id, "body_type");

				$user_datas['want_career_stage_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_career_stage_importance']);

				/* career_stage Acending */
				$fields = array('ptble.*', 'uw.*');
				$from = 'user_want_career_stage as uw';

				$joins = array('career_stage as ptble' => array('ptble.career_stage_id = uw.career_stage_id', 'LEFT'));

				$where['ptble.display_language_id'] = $this -> session -> userdata('sess_language_id');
				$where['uw.user_id'] = $user_id;
				$user_datas['user_want_career_stage'] = $this -> general_model -> multijoins_arr($fields, $from, $joins, $where, '', 'view_order asc');
				unset($where);

				//$user_datas['user_want_career_stage'] = $this->datetix->user_want($user_id,"career_stage");

				$user_datas['want_child_plan_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_child_plan_importance']);
				$user_datas['user_want_child_plan'] = $this -> datetix -> user_want($user_id, "child_plan");

				$user_datas['want_child_status_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_child_status_importance']);
				$user_datas['user_want_child_status'] = $this -> datetix -> user_want($user_id, "child_status");

				$user_datas['want_drinking_status_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_drinking_status_importance']);
				$user_datas['user_want_drinking_status'] = $this -> datetix -> user_want($user_id, "drinking_status");

				$user_datas['want_education_level_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_education_level_importance']);
				$user_datas['user_want_education_level'] = $this -> datetix -> user_want($user_id, "education_level");

				$user_datas['want_ethnicity_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_ethnicity_importance']);
				$user_datas['user_want_ethnicity'] = $this -> datetix -> user_want($user_id, "ethnicity");

				$user_datas['want_exercise_frequency_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_exercise_frequency_importance']);
				$user_datas['user_want_exercise_frequency'] = $this -> datetix -> user_want($user_id, "exercise_frequency");

				$user_datas['want_eyewear_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_eyewear_importance']);
				$user_datas['user_want_eyewear'] = $this -> datetix -> user_want($user_id, "eyewear");

				$user_datas['want_eye_color_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_eye_color_importance']);
				$user_datas['user_want_eye_color'] = $this -> datetix -> user_want($user_id, "eye_color");

				$user_datas['user_want_gender'] = $this -> datetix -> user_want($user_id, "gender");
				$user_datas['want_hair_color_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_hair_color_importance']);
				$user_datas['user_want_hair_color'] = $this -> datetix -> user_want($user_id, "hair_color");

				$user_datas['want_hair_length_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_hair_length_importance']);
				$user_datas['user_want_hair_length'] = $this -> datetix -> user_want($user_id, "hair_length");

				$user_datas['want_industry_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_industry_importance']);
				
				
				/* job_function  Acending */
				$fields = array('ptble.*', 'uw.*');
				$from = 'user_want_industry as uw';

				$joins = array('industry as ptble' => array('ptble.industry_id = uw.industry_id', 'LEFT'));

				$where['ptble.display_language_id'] = $this -> session -> userdata('sess_language_id');
				;
				$where['uw.user_id'] = $user_id;
				$user_datas['user_want_industry'] = $this -> general_model -> multijoins_arr($fields, $from, $joins, $where, '', 'description asc');
				unset($where);
				//$user_datas['user_want_industry'] = $this->datetix->user_want($user_id,"industry");

				$user_datas['want_job_function_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_job_function_importance']);

				/* job_function  Acending */
				$fields = array('ptble.*', 'uw.*');
				$from = 'user_want_job_function as uw';
				$joins = array('job_function as ptble' => array('ptble.job_function_id = uw.job_function_id', 'LEFT'));
				$where['ptble.display_language_id'] = $this -> session -> userdata('sess_language_id');
				$where['uw.user_id'] = $user_id;
				$user_datas['user_want_job_function'] = $this -> general_model -> multijoins_arr($fields, $from, $joins, $where, '', 'description asc');
				unset($where);
				//$user_datas['user_want_job_function'] = $this->datetix->user_want($user_id,"job_function");

				$user_datas['want_personality_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_personality_importance']);
				//$user_datas['user_want_personality'] = $this->datetix->user_want($user_id,"personality");

				$user_datas['want_relationship_status_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_relationship_status_importance']);
				$user_datas['user_want_relationship_status'] = $this -> datetix -> user_want($user_id, "relationship_status");

				$user_datas['want_religious_belief_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_religious_belief_importance']);
				$user_datas['user_want_religious_belief'] = $this -> datetix -> user_want($user_id, "religious_belief");

				$user_datas['want_residence_type_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_residence_type_importance']);
				$user_datas['user_want_residence_type'] = $this -> datetix -> user_want($user_id, "residence_type");

				$user_datas['want_school_subject_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_school_subject_importance']);

				/* School Subject Acending */
				$fields = array('ptble.*', 'uw.*');
				$from = 'user_want_school_subject as uw';

				$joins = array('school_subject as ptble' => array('ptble.school_subject_id = uw.school_subject_id', 'LEFT'));

				$where['ptble.display_language_id'] = $this -> session -> userdata('sess_language_id');
				;
				$where['uw.user_id'] = $user_id;
				$user_datas['user_want_school_subject'] = $this -> general_model -> multijoins_arr($fields, $from, $joins, $where, '', 'description asc');
				unset($where);
				/* End */
				//$user_datas['user_want_school_subject'] = $this->datetix->user_want($user_id,"school_subject");

				$user_datas['want_skin_tone_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_skin_tone_importance']);
				$user_datas['user_want_skin_tone'] = $this -> datetix -> user_want($user_id, "skin_tone");

				$user_datas['want_smoking_status_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_smoking_status_importance']);
				$user_datas['user_want_smoking_status'] = $this -> datetix -> user_want($user_id, "smoking_status");

				//do code for user want
				$user_datas['want_nationality_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_nationality_importance']);

				$user_datas['want_school_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_school_importance']);
				$user_datas['user_want_school'] = $this -> datetix -> user_want($user_id, "school");

				$user_datas['want_company_importance_description'] = $this -> datetix -> load_data_by_id('criteria_importance', $tmp_usr_data['want_company_importance']);

				//$user_datas['user_want_company'] = $this->datetix->user_want($user_id,"company");
				$selected_company = $this -> datetix -> user_want($user_id, "company");
				//echo $this->db->last_query();exit;
				$this -> general -> set_table('user_want_company');
				$user_custom_company_required = $this -> general -> get("user_want_company_id, user_id,CONCAT('_', `company_name`,'_') as company_id, company_name", array('user_id' => $user_id, 'company_name !=' => ''));

				$user_datas['user_want_company'] = array_merge($selected_company, $user_custom_company_required);
				//-------------------------------------------------------------------------------------------//

				$fields = array('n.*');

				$from = 'user_want_nationality as uw';
				$joins = array('country as n' => array('uw.nationality_id = n.country_id', 'LEFT'));

				$where['n.display_language_id'] = $language_id;
				$where['uw.user_id'] = $user_id;

				$temp = $this -> model_user -> multijoins($fields, $from, $joins, $where);
				if ($temp) {
					$user_datas['user_want_nationality'] = $temp;
					unset($temp);
				}

				unset($where['n.display_language_id']);
				unset($where['uw.user_id']);

				//-------------------------------------------------------------------------------------------//
				//Landing Page
				if ($tmp_usr_data['landing_page_id']) {
					$fields = array('lp.*', 'cs.description as carrer_stage_description', 'gndr.description as gender_description', 'ethnct.description as ethnicity_description', 'ct.description as city_description', 'prvnce.description as province_description', 'cntry.description as country_description', 'cntry.country_code', 'cntry.flag_url');

					$from = 'landing_page as lp';
					$joins = array('career_stage as cs' => array('lp.career_stage_id = cs.career_stage_id', 'LEFT'), 'gender as gndr' => array('lp.gender_id = gndr.gender_id', 'LEFT'), 'ethnicity as ethnct' => array('lp.ethnicity_id = ethnct.ethnicity_id', 'LEFT'), 'city as ct' => array('lp.city_id= ct.city_id', 'LEFT'), 'province as prvnce' => array('ct.province_id = prvnce.province_id', 'LEFT'), 'country as cntry' => array('prvnce.country_id = cntry.country_id', 'LEFT'));

					$where['lp.display_language_id'] = $language_id;
					$where['cs.display_language_id'] = $language_id;
					$where['gndr.display_language_id'] = $language_id;
					$where['ethnct.display_language_id'] = $language_id;
					$where['ct.display_language_id'] = $language_id;
					$where['prvnce.display_language_id'] = $language_id;
					$where['cntry.display_language_id'] = $language_id;

					$where['lp.landing_page_id'] = $tmp_usr_data['landing_page_id'];
					$temp = $this -> model_user -> multijoins($fields, $from, $joins, $where);

					if ($temp) {
						$user_datas['user_landing_page'] = $temp;
						unset($temp);
					}
					unset($where);

				}//End if: Landing Page Data

				//-------------------------------------------------------------------------------------------//
				//Current Lived in
				$fields = array('ct.description as city_description', 'prvnce.description as province_description', 'cntry.description as country_description', 'cntry.country_id', 'cntry.flag_url', 'crncy.currency_id', 'crncy.description as currency_description', );
				$from = 'city as ct';
				$joins = array('province as prvnce' => array('ct.province_id = prvnce.province_id', 'LEFT'), 'country as cntry' => array('prvnce.country_id = cntry.country_id', 'LEFT'), 'currency as crncy' => array('cntry.currency_id = crncy.currency_id ', 'LEFT'));

				$where['ct.city_id'] = $tmp_usr_data['current_city_id'];

				$where['ct.display_language_id'] = $language_id;
				$where['prvnce.display_language_id'] = $language_id;
				$where['cntry.display_language_id'] = $language_id;
				$where['crncy.display_language_id'] = $language_id;

				$temp = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'city_description asc');
				$country_id = 0;
				
				if ($temp) {
					$user_datas['user_current_location'] = $temp['0'];
					$country_id = $user_datas['user_current_location']['country_id'];				
					unset($temp);
				}
				unset($where);
				
				
				/*--------------------HEIGHT Calculation-----------------*/
				
				$useMeters = $this -> general_model -> getSingleValue('country', 'use_meters', array('country_id' => $country_id, 'display_language_id' => $language_id));
				$user_datas['useMeters'] = $useMeters ? $useMeters : '';
				$user_datas['feetFrom'] = '';
				$user_datas['inchFrom'] = '';
				
				if ($user_datas['useMeters'] == 0 && $user_datas['useMeters'] !== FALSE) {
					$cmFrom = $tmp_usr_data['height'];
					if (!empty($cmFrom)) {
						$convertedValues = $this -> convertToFeet($cmFrom, '', TRUE);
						$user_datas['feetFrom'] = $convertedValues['feetFrom'];
						$user_datas['inchFrom'] = $convertedValues['inchFrom'];
					}
				}
				/*-------------------------------------------------------*/
				
				
				/*--------Changed by Hannan Munshi---------*/
				$this -> general_model -> set_table('user_city_lived_in');
				$fieldss = array('country.country_id', 'country.description AS countryName', 'user_city_lived_in.city_name');
				$froms = 'user_city_lived_in';
				$joinss = array('country' => array('country.country_id = user_city_lived_in.country_id', 'inner'));
				$wheres['user_city_lived_in.user_id'] = $user_id;
				$wheres['country.display_language_id'] = $language_id;
				$livedInCityQuery = $this -> general_model -> multijoins($fieldss, $froms, $joinss, $wheres, 'city_name asc');

				$this -> db -> where('country.country_id', $tmp_usr_data['birth_country_id']);
				$this -> db -> where('country.display_language_id', $language_id);
				$birthCountryName = $this -> db -> get('country') -> row();

				if ($birthCountryName) {
					$birthPlaceDetails = new stdClass;
					$birthPlaceDetails -> country_id = $tmp_usr_data['birth_country_id'];
					$birthPlaceDetails -> countryName = $birthCountryName -> description;
					$birthPlaceDetails -> city_name = $tmp_usr_data['birth_city_name'];
					//$livedInCityQuery[]=$birthPlaceDetails;
					array_unshift($livedInCityQuery, $birthPlaceDetails);
				}

				$user_datas['userCityLivedIn'] = $livedInCityQuery;

				//-------------------------------------------------------------------------------------------//
				//Nationality information
				$where['usr.user_id'] = $user_id;
				$fields = array('cntry.*');
				$from = 'user_nationality as usr';
				$joins = array('country as cntry' => array('usr.country_id= cntry.country_id', 'LEFT'));

				$where['cntry.display_language_id'] = $language_id;
				$temp = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'view_order asc');

				if ($temp) {
					$user_datas['user_nationality'] = $temp;
					unset($temp);
				}
				unset($where['ntlity.display_language_id']);
				unset($where['cntry.display_language_id']);

				//-------------------------------------------------------------------------------------------//
				//Muliple User Descriptive Word
				$fields = array('usr.user_descriptive_word_id', 'dw.description as dw_description');
				$from = 'user_descriptive_word as usr';
				$joins = array('descriptive_word as dw' => array('dw.descriptive_word_id= usr.descriptive_word_id', 'LEFT'));

				$where['dw.display_language_id'] = $language_id;
				$temp = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'view_order asc');

				if ($temp) {
					$user_datas['user_descriptive_words'] = $temp;
					unset($temp);
				}
				unset($where['dw.display_language_id']);

				//-------------------------------------------------------------------------------------------//
				//User Eyewear
				$fields = array('usr.eyewear_id', 'ew.description as eyewear_description');
				$from = 'user_eyewear as usr';
				$joins = array('eyewear as ew' => array('usr.eyewear_id = ew.eyewear_id', 'LEFT'));

				$where['ew.display_language_id'] = $language_id;
				$temp = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'view_order asc');

				if ($temp) {
					$user_datas['user_eyewear'] = $temp;
					unset($temp);
				}
				unset($where['ew.display_language_id']);

				//-------------------------------------------------------------------------------------------//
				//User Interest
				$fields = array('usr.interest_id', 'intst.description as interst_description');

				$from = 'user_interest as usr';
				$joins = array('interest as intst' => array('usr.interest_id = intst.interest_id', 'LEFT'));

				$where['intst.display_language_id'] = $language_id;
				$temp = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'view_order asc');

				if ($temp) {
					$user_datas['user_interest'] = $temp;
					unset($temp);
				}
				unset($where['intst.display_language_id']);

				//-------------------------------------------------------------------------------------------//
				//Date Type
				$fields = array('usr.user_preferred_date_type_id', 'dt_type.description as date_type_description');

				$from = 'user_preferred_date_type as usr';
				$joins = array('date_type as dt_type' => array('usr.date_type_id = dt_type.date_type_id', 'LEFT'));

				$where['dt_type.display_language_id'] = $language_id;
				$temp = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'view_order asc');

				//echo $this->db->last_query();die();
				if ($temp) {
					$user_datas['user_date_type'] = $temp;
					unset($temp);
				}
				unset($where['dt_type.display_language_id']);

				//-------------------------------------------------------------------------------------------//
				//Contact Method
				$fields = array('usr.contact_method_id', 'cm.description as contact_method_description');

				$from = 'user_preferred_contact_method as usr';
				$joins = array('contact_method as cm' => array('usr.contact_method_id = cm.contact_method_id', 'LEFT'));

				$where['cm.display_language_id'] = $language_id;
				$temp = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'view_order asc');

				if ($temp) {
					$user_datas['user_contact_methods'] = $temp;
					unset($temp);
				}
				unset($where['cm.display_language_id']);

				//-------------------------------------------------------------------------------------------//
				//User Speak Languages
				$fields = array('usr.spoken_language_id', 'spkn_lang.description as spoken_lang_description', 'spkn_lang_lvl.description as spoken_lang_level_description');
				$from = 'user_spoken_language as usr';
				$joins = array('spoken_language as spkn_lang' => array('spkn_lang.spoken_language_id = usr.spoken_language_id', 'LEFT'), 'spoken_language_level as spkn_lang_lvl' => array('spkn_lang_lvl.spoken_language_level_id = usr.spoken_language_level_id', 'LEFT'));

				$where['spkn_lang.display_language_id'] = $language_id;
				$where['spkn_lang_lvl.display_language_id'] = $language_id;

				$temp = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'spkn_lang_lvl.view_order asc, spkn_lang.view_order asc');

				if ($temp) {
					$user_datas['user_spoken_languages'] = $temp;
					unset($temp);
				}
				unset($where['spkn_lang.display_language_id']);
				unset($where['spkn_lang_lvl.display_language_id']);

				//-------------------------------------------------------------------------------------------//
				//School Data
				/*$get_condition['user_id'] = $user_id;
				 $tmp_edu_data = $this->model_user->get_data('user_school',$get_condition);

				 unset($get_condition);*/
				$tmp_edu_data = array();
				$list_schools = '';

				$this -> general_model -> set_table('user_school');
				$schoolCondition = array('user_id' => $user_id);
				$schoolFields = array('user_school_id', 'years_attended_end');
				$schoolOrderBy = array('years_attended_end' => 'desc', 'years_attended_start' => 'desc', 'school_name' => 'asc');
				$school = $this -> general_model -> get($schoolFields, $schoolCondition, $schoolOrderBy);
				
				$schoolIndexes = array();
				foreach ($school as $key => $value) {
					$schoolIndexes[$value['user_school_id']] = $value['user_school_id'];
				}
				foreach ($schoolIndexes as $row) {
					$schoolsDatas = $this -> model_user -> get_school_details($row, $language_id);
					$tmp_edu_data[] = $schoolsDatas[0];
				}

				if ($tmp_edu_data) {
					foreach ($tmp_edu_data as $i_edu => $edu) {
						//If company id then fetch from db.
						if ($edu['school_id']) {
							//School City in
							$fields = array('scul.*', 'ct.description as city_description', 'prvnce.description as province_description', 'cntry.description as country_description', 'cntry.country_code', 'cntry.flag_url');

							$from = 'school as scul';
							$joins = array('city as ct' => array('scul.city_id= ct.city_id', 'LEFT'), 'province as prvnce' => array('ct.province_id = prvnce.province_id', 'LEFT'), 'country as cntry' => array('prvnce.country_id = cntry.country_id', 'LEFT'));

							$condition['ct.display_language_id'] = $language_id;
							$condition['prvnce.display_language_id'] = $language_id;
							$condition['cntry.display_language_id'] = $language_id;
							$condition['scul.display_language_id'] = $language_id;
							$condition['scul.school_id'] = $edu['school_id'];

							$temp = $this -> model_user -> multijoins($fields, $from, $joins, $condition);
							unset($condition);

							if ($temp) {
								$temp = $temp['0'];
							}

							//Minor Subject
							$condition['ssub.display_language_id'] = $language_id;
							$condition['minsub.user_school_id'] = $edu['user_school_id'];
							$temp['minor_subjects'] = $this -> model_user -> multijoins(array('ssub.*'), "user_school_minor as minsub", array('school_subject ssub' => array('ssub.school_subject_id = minsub.minor_id', 'LEFT')), $condition, '', 'view_order asc');
							unset($condition);

							//Major Subject
							$condition['ssub.display_language_id'] = $language_id;
							$condition['mjr.user_school_id'] = $edu['user_school_id'];
							$temp['major_subjects'] = $this -> model_user -> multijoins(array('ssub.*'), "user_school_major as mjr", array('school_subject ssub' => array('ssub.school_subject_id = mjr.major_id', 'LEFT')), $condition, '', 'view_order asc');
							unset($condition);

							if ($temp) {
								$tmp_edu_data[$i_edu]['school_data'] = $temp;
							}

							unset($tmp_edu_info);
							unset($get_condition);
						}
					}

					$user_datas['education_data'] = $tmp_edu_data;
					unset($tmp_edu_data);
				}
				$user_datas['company_data'] = $this -> datetix -> get_my_company_data($user_id);
				if(isset($super_admin['website_id']) && $super_admin['website_id'])
				{
					
				}
				else {
						
					if ($cur_user_id) {
						$cur_tmp_usr_data = $this -> model_user -> get_user_data($cur_user_id);
						$data['cur_user_info'] = $cur_tmp_usr_data;
	
						//=========================> :: COMPARISION :: <=====================//
						if ($cur_user_id != $user_id) {
							
							$data['cur_usr_demand_cmp'] = $this -> datetix -> calculate_score($cur_user_id, $user_id);
							$data['view_usr_demand_cmp'] = $this -> datetix -> calculate_score($user_id, $cur_user_id);
	
							/*========================For comparision box============================*/
	
							//cur_user_id is user id of the loged in user
							$consolidatedUsersInterest = array();
							$consolidatedViewersInterest = array();
	
							$this -> general_model -> set_table('user_interest');
							$interestJoins = array('user_interest' => array('interest.interest_id = user_interest.interest_id', 'inner'));
							$interestCondition = array('interest.display_language_id' => $language_id, 'user_interest.user_id' => $user_id);
							$userInterests = $this -> general_model -> multijoins_arr('interest.*,interest.description as interest', 'interest', $interestJoins, $interestCondition, '', 'interest.view_order asc');
							if (!empty($userInterests)) {
								foreach ($userInterests as $key => $value) {
									$consolidatedUsersInterest[$value['interest_id']] = $value['interest'];
								}
							}
	
							$interestCondition = array('interest.display_language_id' => $language_id, 'user_interest.user_id' => $cur_user_id);
							$viewersInterest = $this -> general_model -> multijoins_arr('interest.*,interest.description as interest', 'interest', $interestJoins, $interestCondition, '', 'interest.view_order asc');
							if (!empty($viewersInterest)) {
								foreach ($viewersInterest as $key => $value) {
									$consolidatedViewersInterest[$value['interest_id']] = $value['interest'];
								}
							}
	
							$data['commonInterests'] = array_intersect_key($consolidatedUsersInterest, $consolidatedViewersInterest);
						}
						/*========================For comparision box============================*/
					}
				}

				//echo "<pre>";print_r($user_datas);exit;
				$data['user_info'] = $user_datas;
				if (isset($data['cur_user_info'])) {
					$data['user_data'] = $data['cur_user_info'];
				}

				$this->benchmark->mark('code_end');
			echo $this->benchmark->elapsed_time('code_start', 'code_end');

				$data['page_title'] = translate_phrase('User Information');
				$data['page_name'] = 'user/profile';
				$data['click_to_action'] = $this -> input -> get('action');
                                
				if(isset($super_admin['website_id']) && $super_admin['website_id'])
				{
					$this -> load -> view('template/admin', $data);	
				}
				else
				{
					$this -> load -> view('template/editProfileTemplate', $data);
				}
			} else {
				//-nmkjWse8z6p16hPLg5OJV2xfk_SVlqgOhGrtUTjB_Q
				echo 'sorry user not found.';
			}
		}
	}
	
}
?>
