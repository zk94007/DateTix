<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class User extends MY_Controller {

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
	
	public function check_signup() {
		$this -> model_user -> is_signup_process();
		$user_id = $this -> session -> userdata('user_id');
		$this -> model_user -> is_current_signup_process($user_id);
	}
	
	/*[ajax call] load_notification() : Load user notification on panel
	* @param : null
	* @return : notification data [json]
	* @author: Rajnish	 
	*/
	public function load_notification() {
		$user_id = $this -> session -> userdata('user_id');
		
		$data['active_intro_notification'] = 0;
		//Pending introduction Notification
		$common_sql = 'SELECT count(user_intro.user_intro_id) as total_row
				FROM user_intro
				WHERE (user1_id = "'.$user_id.'" OR user2_id = "'.$user_id.'") ';
		//$query = $common_sql .'AND DATE(intro_expiry_time) >= DATE(CURDATE())
		//				ORDER BY `intro_available_time` DESC ';
		$query = $common_sql;
		if($row_data = $this->general->sql_query($query))
		{
			$data['active_intro_notification'] = $row_data['0']['total_row'];
		}
		
		$data['active_dates_notification'] = 0;
		// Active Dates  Notification //
		$common_sql = '	SELECT count(user_intro.user_intro_id) as total_row
					
					FROM user_intro
	               	JOIN user on user.user_id = CASE 
						WHEN user_intro.user1_id = "'.$user_id.'" THEN user_intro.user2_id
						WHEN user_intro.user2_id = "'.$user_id.'" THEN user_intro.user1_id
					END
				
					JOIN user_date on user_intro.user_intro_id = user_date.user_intro_id
                	JOIN date_type on date_type.date_type_id = user_date.date_type_id
	            
		            WHERE (user1_id = "'.$user_id.'" OR user2_id = "'.$user_id.'") ';
		$query = $common_sql .'
					AND DATE(date_time) >= CURDATE()
		        	AND user_intro.user1_not_interested_time = "0000-00-00 00:00:00"
					AND user_intro.user2_not_interested_time = "0000-00-00 00:00:00"
		            AND date_type.display_language_id='.$this->session->userdata('sess_language_id').'
					ORDER BY `intro_available_time` DESC ' ;
		//if($row_data = $this->general->sql_query($query))
		//{
		//	$data['active_dates_notification'] = $row_data['0']['total_row'];
		//}
		echo json_encode($data);
	}

	public function confirmation() {
		$this -> model_user -> is_signup_process();
		$user_id = $this -> session -> userdata('user_id');
		//$this -> model_user -> is_current_signup_process($user_id);
		$city_id = $this -> session -> userdata('sess_city_id');
		//$this->model_user->change_url_by_current_city($city_id,$user_id);

		$this -> general -> set_table('user');
		$user_data = $this -> general -> get("first_name", array('user_id' => $user_id));
		
		if($event_ticket_id = $this->session->userdata('event_ticket_id'))
		{
			$fields = array('rsvp.*','e.*','v.*','n.description as neighborhood_name','ct.description as city_name');
			$from = 'event_ticket as rsvp';
			$joins = array(
					'event_order as ordr' => array('rsvp.event_order_id = ordr.event_order_id', 'inner'), 
					'event as e' => array('e.event_id = ordr.event_id', 'inner'), 
					'venue as v' => array('e.venue_id = v.venue_id', 'inner'), 
					'neighborhood as n' => array('v.neighborhood_id = n.neighborhood_id', 'inner'),
					'city as ct' => array('ct.city_id = n.city_id', 'inner'),
					'province as p' => array('p.province_id = ct.province_id', 'inner'),
					);				
			if($city_id = $this -> session -> userdata('sess_city_id'))
			{
				$where['ct.city_id'] = $city_id;
			}
			else
			{
				$where['ct.city_id'] = $this->config->item('default_city');
			}
					 
			
			$where['ct.display_language_id'] = $this -> language_id;
			$where['p.display_language_id'] = $this -> language_id;
			$where['n.display_language_id'] = $this -> language_id;
			$where['e.display_language_id'] = $this -> language_id;
			$where['rsvp.event_ticket_id'] = $event_ticket_id;
			if($data['event_info'] = $this -> model_user -> multijoins($fields, $from, $joins, $where))
			{
				//echo "<pre>";print_r($data['event_info']);exit;
				$data['event_info'] = $data['event_info']['0'];
				
				$this -> general -> set_table('event_user');
				
				//delete record from user table
				$event_user_data['event_ticket_id'] = $event_ticket_id;
				$this -> general -> delete($event_user_data);
				
				$event_user_data['event_id'] = $data['event_info']['event_id'];
				$event_user_data['user_id'] = $user_id;
				
				//Newly Added fields
				$event_user_data['ad_id'] = $this -> session -> userdata('ad_id');
				$event_user_data['agent_string'] = $this->agent->agent_string();				
					
				$is_updated = 0;
				if($this -> general -> checkDuplicate(array('event_id'=>$data['event_info']['event_id'],'user_id'=>$user_id)))
				{
					$event_user_data['rsvp_time'] = SQL_DATETIME;
					$is_updated = $this -> general -> update($event_user_data,array('event_id'=>$data['event_info']['event_id'],'user_id'=>$user_id));
				}
				else
				{
					$event_user_data['rsvp_time'] = SQL_DATETIME;
					$is_updated = $this -> general -> save($event_user_data);
					$this -> general -> set_table('event_ticket');
					
					$this -> general -> update(array('user_id'=>$user_id), array('event_ticket_id' => $event_ticket_id));
				}
				
				if($is_updated)
				{
					//echo "<pre>";print_r($data['event_info']);exit;
					if($user_email_data = $this -> model_user -> get_user_email($user_id))
					{
						
						$rsvp_subject = translate_phrase("Your RSVP has been confirmed");								
						$email_data['email_title'] = '';
						$email_data['btn_link'] = base_url() . url_city_name() . '/event.html?id='.$data['event_info']['event_id'];
						$email_data['btn_text'] = translate_phrase("View Event Details");
						$email_data['email_title'] = translate_phrase('Hi ').$user_data['0']['first_name'].translate_phrase(', your RSVP for ').$data['event_info']['event_name'].translate_phrase(" on ").date(DATE_FORMATE,strtotime($data['event_info']['event_start_time'])).translate_phrase(" has been confirmed.");
						
						$email_data['email_content'] = translate_phrase('We will email you a list of your top matches 24-36 hours before the event, so keep checking your email as we get closer to the event!');
						$email_data['email_content'] .= "<br/><br/>".translate_phrase('The event starts at ').date("g:i a",strtotime($data['event_info']['event_start_time'])).translate_phrase(" on ").date(DATE_FORMATE,strtotime($data['event_info']['event_start_time'])).translate_phrase(' and takes place at').':';
						$email_data['email_content'] .= "<br/><br/>".$data['event_info']['name'];
						$email_data['email_content'] .= "<br/>".$data['event_info']['address'];
						$email_data['email_content'] .= ", ".$data['event_info']['neighborhood_name'];
						$email_data['email_content'] .= "<br/>".$data['event_info']['city_name'];
						$email_data['email_content'] .= "<br/>".$data['event_info']['phone_number']."<br/><br/>";
						$email_template = $this -> load -> view('email/common', $email_data, true);
						$this -> model_user -> send_email(INFO_EMAIL,$user_email_data['email_address'], $rsvp_subject, $email_template,"html","DateTix");
					}
				}				
				$this->session->unset_userdata('event_ticket_id');
			}
		}
		
		$data['page_title'] = translate_phrase('Apply for DateTix Membership');
		$data['page_name'] = 'user/confirmation';
		$this -> load -> view('template/default', $data);
	}

	/**
	 * Logout :: Delete all sessions variables
	 * @access public
	 * @return true or false (redirect to view)
	 * @author  by Rajnish
	 */
	public function logout() {
		parse_str($_SERVER['QUERY_STRING'], $_REQUEST);
		$this -> load -> library('Facebook');

		//detroy session
		$this -> session -> sess_destroy();
		if ($userId = $this -> facebook -> getUser()) {
			$this -> session -> unset_userdata('fb_user_data');
			$this -> facebook -> destroySession();
			$access['next'] = base_url();
			$url = $this -> facebook -> getLogoutUrl($access);
			redirect($url);
		} else {
			redirect();
		}
	}

	/**
	 * Edit Profile Function
	 * @access public
	 * @return redirect to Message:: view
	 * @author by Hannan
	 */
	public function edit_profile() {
		
		$data['scroll_to'] = $this -> input -> get('scroll_to');
		$this -> model_user -> is_signup_process();
		
		$user_id = $this -> session -> userdata('user_id');
		$data['fb_user_data'] = $this -> input -> cookie('fb_user_data');
		
		$language_id = $this -> session -> userdata('sess_language_id');

		$data['user_data'] = $this -> model_user -> get_user_data($user_id);
		$data['user_photos'] = $this -> model_user -> get_photos($user_id, "profile");

		$city_id = $data['user_data']['current_city_id'];
		// $this->session->userdata('sess_city_id');

		$current_country = $this -> model_user -> getCountryByCity($city_id);
		$city = $this -> model_user -> get_city_by_id($city_id);
		$country_id = $current_country ? $current_country -> country_id : '0';
		$data['country_name'] = $current_country ? $current_country -> description : '';
		$data['city_name'] = $city ? $city -> description : '';

		$data['user_want_gender'] = $this -> datetix -> user_want($user_id, "gender");
		$data['user_want_relationship_type'] = $this -> datetix -> user_want($user_id, "relationship_type");

		/*--------education level--------*/
		$this -> load -> model('general_model', 'general');
		$this -> general_model -> set_table("user_education_level");
		$user_education_level = $this -> general_model -> get("", array('user_id' => $user_id));
		$csvValues = '';
		foreach ($user_education_level as $key => $value) {
			$csvValues .= $value['education_level_id'] . ',';
			//$data['user_education_level'] .= $value['education_level_id'];
			//$data['user_education_level'][$value['education_level_id']] .=
		}

		$data['user_education_level'] = rtrim($csvValues, ',');
		/*--------education level END--------*/

		/*--------------------HEIGHT Calculation-----------------*/
		$useMeters = $this -> general_model -> getSingleValue('country', 'use_meters', array('country_id' => $country_id, 'display_language_id' => $language_id));
		$data['useMeters'] = $useMeters ? $useMeters : '';

		$data['feetFrom'] = '';
		$data['inchFrom'] = '';

		if ($data['useMeters'] == 0 && $data['useMeters'] !== FALSE) {
			$cmFrom = $data['user_data']['height'];
			//$cmTo = $data['usr_data']['want_height_range_upper'];

			$data['feetFrom'] = '';
			$data['inchFrom'] = '';

			if (!empty($cmFrom)) {
				$convertedValues = $this -> convertToFeet($cmFrom, '', TRUE);

				$data['feetFrom'] = $convertedValues['feetFrom'];
				$data['inchFrom'] = $convertedValues['inchFrom'];
			}
		}
		/*-------------------------------------------------------*/

		$this -> general_model -> set_table('user_descriptive_word');
		$selectedDescriptiveWords = $this -> general_model -> get('descriptive_word_id', array('user_id' => $user_id));
		//echo '<pre>';print_r($selectedDescriptiveWords);die();
		$data['user_descriptive_word'] = '';
		foreach ($selectedDescriptiveWords as $key => $value) {
			$data['user_descriptive_word'] .= $value['descriptive_word_id'] . ',';
		}
		$data['user_descriptive_word'] = rtrim($data['user_descriptive_word'], ',');

		$data['has_district'] = $this -> model_user -> check_district_exist($language_id, $city_id);

		$data['postal_code_exist'] = $this -> model_user -> check_postal_code_exist($language_id, $country_id);
		$data['ethnicity'] = $this -> model_user -> get_ethnicity($language_id, $country_id);
		$data['gender'] = $this -> model_user -> get_gender($language_id);
		$data['country'] = $this -> model_user -> get_country($language_id);
		$data['relationship_type'] = $this -> model_user -> get_relationship_type($language_id);
		$data['district'] = $this -> model_user -> get_district($language_id, $city_id);
		$data['residence_type'] = $this -> model_user -> get_residence_type($language_id);
		$data['carrier_stage'] = $this -> model_user -> get_carrier_stage($language_id);
		$data['body_type'] = $this -> model_user -> get_body_type($language_id);
		$data['looks'] = $this -> model_user -> get_looks($language_id);
		$data['eye_color'] = $this -> model_user -> get_eye_color($language_id);
		$data['hair_color'] = $this -> model_user -> get_hair_color($language_id);
		$data['hair_length'] = $this -> model_user -> get_hair_length($language_id);
		$data['skin_tone'] = $this -> model_user -> get_skin_tone($language_id);
		$data['eye_wear'] = $this -> model_user -> get_eye_wear($language_id);
		$data['relationship_status'] = $this -> model_user -> get_relationship_status($language_id);
		$data['religious_belief'] = $this -> model_user -> get_religious_belief($language_id);
		$data['spoken_language'] = $this -> model_user -> get_spoken_language($language_id);
		$data['proficiency'] = $this -> model_user -> get_proficiency($language_id);
		$data['descriptive_word'] = $this -> model_user -> get_descriptive_word($language_id);
		$data['personality'] = array();
		//$this->model_user->get_personality($language_id);
		$education_level = $this -> model_user -> get_education_level($language_id);
		foreach ($education_level as $key => $value) {
			$data['education_level'][$value['education_level_id']] = $value['description'];
		}
		$data['school_subject'] = $this -> model_user -> get_school_subject($language_id);
		$data['industry'] = $this -> model_user -> get_industry($language_id);
		$data['job_functions'] = $this -> model_user -> get_job_functions($language_id);
		$data['child_status'] = $this -> model_user -> get_child_status($language_id);
		$data['smoking_status'] = $this -> model_user -> get_smoking_status($language_id);
		$data['drinking_status'] = $this -> model_user -> get_drinking_status($language_id);
		$data['exercise_frequency'] = $this -> model_user -> get_exercise_frequency($language_id);
		$data['child_plans'] = $this -> model_user -> get_child_plans($language_id);
		$data['annual_income_range'] = $this -> model_user -> get_annual_income_range($country_id);

		$currency_detail = $this -> model_user -> get_currency_by_country($language_id, $country_id);
		$data['nationality'] = $this -> model_user -> get_country($language_id);
		$data['year'] = $this -> model_user -> get_year();
		$data['month'] = $this -> model_user -> get_month();
		$data['date'] = $this -> model_user -> get_date();
		$data['feet'] = $this -> model_user -> get_feet();
		$data['inches'] = $this -> model_user -> get_inches();
		$data['cms'] = $this -> model_user -> get_height_cm();

		$data['use_meters'] = $this -> model_user -> check_use_meters($country_id);
		$data['currency'] = (!empty($currency_detail)) ? $currency_detail['description'] : '';

		$interestsResult = $this -> model_user -> getInterests();
		$data['interests'] = ($interestsResult === false) ? '' : $interestsResult;

		$this -> general_model -> set_table('user_interest');
		$userInterests = $this -> general_model -> get('interest_id', array('user_id' => $user_id));
		$selectedInterests = '';
		if (!empty($userInterests)) {
			foreach ($userInterests as $key => $value) {
				$selectedInterests .= $value['interest_id'] . ',';
			}

		}
		$data['user_interests'] = rtrim($selectedInterests, ',');

		/*
		 $this->general_model->set_table('user_eyewear');
		 //$userEyeWear = $this->general_model->get('eyewear_id',array('user_id'=>$user_id));
		 $data['userEyeWear'] = $this->general_model->getSingleValue('user_eyewear','eyewear_id',array('user_id'=>$user_id));
		 */

		//User Eyewear
		$fields = array('usr.eyewear_id', 'ew.description as eyewear_description');
		$from = 'user_eyewear as usr';
		$joins = array('eyewear as ew' => array('usr.eyewear_id = ew.eyewear_id', 'INNER'));

		$eye_where['ew.display_language_id'] = $language_id;
		$eye_where['usr.user_id'] = $user_id;
		$temp = $this -> model_user -> multijoins($fields, $from, $joins, $eye_where, NULL, "ew.view_order asc");
		$data['userEyeWear'] = array();
		if ($temp) {
			foreach ($temp as $t) {
				$data['userEyeWear'][] = $t['eyewear_id'];
			}
		}

		/*--------------nationality--------------------*/

		$fields = array('parent.description', 'child.country_id');
		$from = "country as parent";
		$joins = array('user_nationality as child' => array('parent.country_id = child.country_id', 'inner'));
		$where['parent.display_language_id'] = $language_id;
		$where['child.user_id'] = $user_id;
		$results = $this -> general_model -> multijoins_arr($fields, $from, $joins, $where, NULL, "parent.view_order asc");

		$nationalityCSV = '';
		foreach ($results as $key => $value) {
			$data['user_nationality'][$value['country_id']] = $value['description'];
			$nationalityCSV .= $value['country_id'] . ',';
		}
		$data['nationalityCSV'] = rtrim($nationalityCSV, ',');

		$query = "SELECT parent.spoken_language_id,parent.description as language,sibling.spoken_language_level_id as fluency_id,sibling.description as fluency
                          FROM spoken_language parent
                          JOIN user_spoken_language child on child.spoken_language_id = parent.spoken_language_id
                          JOIN spoken_language_level sibling on child.spoken_language_level_id = sibling.spoken_language_level_id
                          WHERE child.user_id='" . $user_id . "' AND parent.display_language_id='" . $language_id . "' AND sibling.display_language_id ='" . $language_id . "'";
		$languageResult = $this -> db -> query($query) -> result_array();

		//generates two different arrays for language and fluency and also two variables for hidden fields.
		if (!empty($languageResult)) {
			$languageCSV = '';
			$fluencyCSV = '';
			foreach ($languageResult as $key => $value) {
				$languageArray[$value['spoken_language_id']] = $value['language'];
				$fluencyArray[$value['spoken_language_id']] = $value['fluency'];

				$languageCSV .= $value['spoken_language_id'] . ',';
				$fluencyCSV .= $value['fluency_id'] . ',';
				//$fluencyArray[$value['spoken_language_id']]  = array($value['fluency_id']=>$value['fluency']);
			}

			$data['user_spoken_language'] = $languageArray;
			$data['user_spoken_language_fluency'] = $fluencyArray;

			$data['languageCSV'] = rtrim($languageCSV, ',');
			$data['fluencyCSV'] = rtrim($fluencyCSV, ',');
		}

		/*$fields = array('country.country_id',
		 'country.description AS countryName',
		 'user_city_lived_in.city_name');

		 $from   = 'user_city_lived_in';
		 $joins  = array('country' => array('country.country_id = user_city_lived_in.country_id'));
		 unset($where);
		 $where['user_city_lived_in.user_id']               = $user_id;
		 $where['country.display_language_id']   = $language_id;
		 */
		$this -> general_model -> set_table('user_city_lived_in');
		unset($fields);
		unset($where);
		unset($joins);
		$fields = array('country.country_id', 'country.description AS countryName', 'user_city_lived_in.city_name');
		$from = 'user_city_lived_in';
		$joins = array('country' => array('country.country_id = user_city_lived_in.country_id', 'inner'));
		$where['user_city_lived_in.user_id'] = $user_id;
		$where['country.display_language_id'] = $language_id;
		$livedInCityQuery = $this -> general_model -> multijoins($fields, $from, $joins, $where, NULL, "country.view_order asc");
		$cityIds = '';
		$countryIds = '';

		foreach ($livedInCityQuery as $key => $value) {
			$cityIds .= $value -> city_name . ',';
			$countryIds .= $value -> country_id . ',';
		}
		$data['livedInCityHiddenValues'] = rtrim($cityIds, ',');
		$data['livedInCityCountryHiddenValues'] = rtrim($countryIds, ',');
		$data['user_lived_in_city'] = $livedInCityQuery;

		//echo $this->db->last_query();
		#checking city is active if it's from facebook
		if (isset($fb_user_data['location']['city']) && $fb_user_data['location']['city'])
			$this -> model_user -> is_active_city($fb_user_data['location']['city'], return_url());

		#get school count
		$data['company_count'] = $this -> model_user -> get_company_count($user_id);

		$data['user_company_id'] = array();
		//$data['user_company_id']  = $this->model_user->get_user_company_id($user_id);
		/*------------------Top ways to make your profile more attractive---------------------*/

		$priorityArray = array('user_photos' => array('divId' => 'photos', 'elementDiv' => '', 'buttonTxt' => 'Upload a Photo'), 'self_summary' => array('divId' => 'basics', 'elementDiv' => 'selfSummaryDiv', 'buttonTxt' => 'Enter a Self-Summary'), 'company_count' => array('divId' => 'career', 'elementDiv' => 'add_companies', 'buttonTxt' => 'Tell Us More About Your Career'), 'user_descriptive_word' => array('divId' => 'personality', 'elementDiv' => 'descriptiveWrodDiv', 'buttonTxt' => 'Describe Your Personality'), 'user_interests' => array('divId' => 'personality', 'elementDiv' => 'userInterestDiv', 'buttonTxt' => 'Enter a Few Interests and Hobbies'), 'smoking_status_id' => array('divId' => 'others', 'elementDiv' => 'smokignStatusDiv', 'buttonTxt' => 'Tell Us Your Smoking Habits'), 'drinking_status_id' => array('divId' => 'others', 'elementDiv' => 'drinkignStatusDiv', 'buttonTxt' => 'Tell Us Your Drinking Habits'), 'exercise_frequency_id' => array('divId' => 'others', 'elementDiv' => 'exerciseFrequencyDiv', 'buttonTxt' => 'Tell Us Your Exercise Habits'), 'residence_type' => array('divId' => 'others', 'elementDiv' => 'residenceTypeDiv', 'buttonTxt' => 'Tell Us Your Residence Type'), 'child_status_id' => array('divId' => 'others', 'elementDiv' => 'childStatusDiv', 'buttonTxt' => 'Tell Us Your Children Status'), 'child_plan_id' => array('divId' => 'others', 'elementDiv' => 'childPlanDiv', 'buttonTxt' => 'Tell Us Your Children Plans'), 'eye_color_id' => array('divId' => 'others', 'elementDiv' => 'eyeColorDiv', 'buttonTxt' => 'Tell Us Your Eye Color'), 'hair_color_id' => array('divId' => 'others', 'elementDiv' => 'hairColorDiv', 'buttonTxt' => 'Tell Us Your Hair Color'), 'hair_length_id' => array('divId' => 'others', 'elementDiv' => 'hairLengthDiv', 'buttonTxt' => 'Tell Us Your Hair Length'), 'skin_tone_id' => array('divId' => 'others', 'elementDiv' => 'skinToneDiv', 'buttonTxt' => 'Tell Us Your Sking Tone'), 'userEyeWear' => array('divId' => 'others', 'elementDiv' => 'eyeWearDiv', 'buttonTxt' => 'Tell Us If You Wear Glasses'), 'user_spoken_language' => array('divId' => 'others', 'elementDiv' => 'spokenLanguageDiv', 'buttonTxt' => 'List Languages That You Speak'), 'user_nationality' => array('divId' => 'others', 'elementDiv' => 'nationalityDiv', 'buttonTxt' => 'Tell Us Your Nationality'), 'livedInCityHiddenValues' => array('divId' => 'others', 'elementDiv' => 'livedInDiv', 'buttonTxt' => 'List Cities You Lived In'));

		//o Upload a Photo (shown if and only if user has 0 photos)
		$ways['user_photos'] = $data['user_photos'];

		//o Enter a Self-Summary
		$ways['self_summary'] = $data['user_data']['self_summary'];

		//o Tell Us More About Your Career (shown if user has 0 company record entered)
		$ways['company_count'] = $data['company_count'];

		//o Describe Your Personality
		$ways['user_descriptive_word'] = $data['user_descriptive_word'];

		//o Enter a Few Interests and Hobbies
		$ways['user_interests'] = $data['user_interests'];

		//o Tell Us Your Smoking Habits
		$ways['smoking_status_id'] = $data['user_data']['smoking_status_id'];

		//o Tell Us Your Drinking Habits
		$ways['drinking_status_id'] = $data['user_data']['drinking_status_id'];

		//o Tell Us Your Exercise Habits
		$ways['exercise_frequency_id'] = $data['user_data']['exercise_frequency_id'];

		//o Tell Us Your Residence Type
		$ways['residence_type'] = $data['user_data']['residence_type'];

		//o Tell Us Your Children Status
		$ways['child_status_id'] = $data['user_data']['child_status_id'];

		//o Tell Us Your Children Plans
		$ways['child_plan_id'] = !empty($data['user_data']['child_plan_id']) ? $data['user_data']['child_plan_id'] : '';

		//o Tell Us Your Eye Color
		$ways['eye_color_id'] = $data['user_data']['eye_color_id'];

		//o Tell Us Your Hair Color
		$ways['hair_color_id'] = $data['user_data']['hair_color_id'];

		//o Tell Us Your Hair Length
		$ways['hair_length_id'] = $data['user_data']['hair_length_id'];

		//o Tell Us Your Skin Tone
		$ways['skin_tone_id'] = $data['user_data']['skin_tone_id'];

		//o Tell Us If You Wear Glasses
		$ways['userEyeWear'] = $data['userEyeWear'];

		//o List Languages That You Speak
		$ways['user_spoken_language'] = isset($data['user_spoken_language']) ? $data['user_spoken_language'] : '';
		//check isset()

		//o Tell Us Your Nationality
		$ways['user_nationality'] = isset($data['user_nationality']) ? $data['user_nationality'] : '';
		//check isset()

		//o List Cities You Lived In
		$ways['livedInCityHiddenValues'] = isset($data['livedInCityHiddenValues']) ? $data['livedInCityHiddenValues'] : '';
		//check isset()

		$emptyFiledsCounter = 0;
		$fieldsNotHavingData = 0;
		$buttonArray = array();

		foreach ($priorityArray as $key => $value) {
			//echo $key.'---'.$emptyFiledsCounter.'----'.isset($ways[$key]).'<br>';
			if ($emptyFiledsCounter <= 2) {
				if (/*isset($ways[$key]) &&*/empty($ways[$key])) {
					//create this button.
					$buttonArray[] = $value;
					$emptyFiledsCounter++;
				}
			}

			if (empty($ways[$key])) {
				$fieldsNotHavingData++;
				$noData[] = $value;
			}

		}

		$data['waysToImproveProfile'] = (!empty($buttonArray)) ? $buttonArray : array();

		//(total number of fields in above list – unfilled fields in above list) / total number of fields in above list

		$totalFields = count($priorityArray);
		//echo '<br>';echo $emptyFiledsCounter;echo '<br>';echo $fieldsNotHavingData;die();
		$data['profileCompleteness'] = round(((($totalFields - $fieldsNotHavingData) / $totalFields) * 100), 2) . '%';

		/*------------------Top ways to make your profile more attractive-END---------------------*/

		/*------------If page is POSTED-----------------------------------------*/
		if ($this -> input -> post('submit')) {

			$this -> model_user -> updateUserProfile($user_id, $language_id, $useMeters);
			$this -> session -> set_flashdata('edit_profile_msg', translate_phrase('Your profile has been updated.'));

			$tab_id = 'basics';
			if ($tabName = $this -> input -> post('current_tab')) {
				$tab_id = str_replace('Tab', '', $tabName);
			}
			redirect(base_url() . url_city_name() . '/edit-profile.html#' . $tab_id);
			/*$this->load->library('form_validation');
			 $this->form_validation->set_error_delimiters('<div class="error_indentation error_msg">', '</div>');
			 if ($this->form_validation->run('signup') == true)
			 {
			 $this->model_user->insert_user_step1($user_id,$language_id);
			 $this->model_user->insert_user_want_personality($user_id,$data['personality'],'user_personality');
			 $this->session->unset_userdata('is_return_apply');
			 redirect(base_url().url_city_name() . '/signup-step-2.html');
			 }*/
		} else {
			#deleting school/company details if it is already exist
			//$this->model_user->remove_school_by_user($user_id);
			//$this->model_user->remove_company_by_user($user_id);
		}

		#inserting school details-from facebook
		$data['user_school_id'] = array();
		if (!empty($fb_user_data['education'])) {
			$school_details = $this -> model_user -> get_school_by_facebok($language_id, $user_id, $fb_user_data['education']);
		}
		#get school count
		$data['school_count'] = $this -> model_user -> get_school_count($user_id);
		/*---------------------------------SCHOOL SORTING------------------------------*/
		$this -> general_model -> set_table('user_school');
		$schoolCondition = array('user_id' => $user_id);
		$schoolFields = array('user_school_id', 'years_attended_end');
		$schoolOrderBy = array('years_attended_end' => 'desc', 'years_attended_start' => 'desc', 'school_name' => 'asc');
		$school = $this -> general_model -> get($schoolFields, $schoolCondition, $schoolOrderBy);
		
		$schoolIndexes = array();
		foreach ($school as $key => $value) {
			$schoolIndexes[$value['user_school_id']] = $value['user_school_id'];
		}
		//krsort($schoolIndexes);

		//$data['user_school_id']       = $this->model_user->get_user_school_id($user_id);
		$data['user_school_id'] = $schoolIndexes;

		/*-----------------------------------------------------------------------------*/

		/*------------------------------COMPANY SORTING--------------------------------*/

		$this -> general_model -> set_table('user_job');
		$companyCondition = array('user_id' => $user_id);
		$companyFields = array('user_company_id', 'company_id', 'company_name', 'years_worked_start', 'years_worked_end');
		$companyOrderBy = array('years_worked_end' => 'desc', 'years_worked_start' => 'desc', 'company_name' => 'asc');
		$company = $this -> general_model -> get($companyFields, $companyCondition, $companyOrderBy);

		if ($company) {
			$this -> general_model -> set_table('company');
			foreach ($company as $r => $cmp_val) {
				if ($cmp_val['company_id']) {
					$company_name = $this -> general_model -> get("", array('display_language_id' => $language_id, 'company_id' => $cmp_val['company_id']));
					$company[$r]['company_name'] = $company_name['0']['company_name'];
				}
			}
		}

		//die();
		$neededKey = 0;
		$newarray = array();
		if ($company) {
			foreach ($company as $key => $value) {

				if ($key == 0 || $key >= $neededKey) {
					$keyThreshold = $key;
					$outerLoopYearStart = $value['years_worked_start'];
					$outerLoopYearEnd = $value['years_worked_end'];
					$sameYearsData = array();
					foreach ($company as $k => $v) {

						if ($v['years_worked_start'] == $outerLoopYearStart && $v['years_worked_end'] == $outerLoopYearEnd) {
							$sameYearsData[] = $v;
						}
					}

					if (!empty($sameYearsData) && count($sameYearsData) > 1) {

						$companyNameArray = array();
						/*
						 foreach ($sameYearsData as $ke => $va) {
						 $companyNameArray[$ke] = $va['company_name'];
						 }
						 array_multisort($companyNameArray, SORT_STRING,$sameYearsData);
						 */

						//
						uasort($sameYearsData, array($this, 'uasort_company_name'));

						$neededKey = $keyThreshold;
						foreach ($sameYearsData as $ke => $val) {
							//$company[$neededKey] = $val;
							$newarray[$neededKey] = $val;
							$neededKey++;
						}

					} else {
						$neededKey++;
						$newarray[] = $value;
					}
				}
			}
		}
		$company = $newarray;
		$companyIndexes = array();
		foreach ($company as $key => $value) {
			$companyIndexes[$value['user_company_id']] = $value['user_company_id'];
		}

		$data['user_company_id'] = $companyIndexes;
		/*-----------------------------------------------------------------------------*/
		#inserting company details-from facebook
		//$data['user_company_id']      = array();
		if (!empty($fb_user_data['work'])) {
			$school_details = $this -> model_user -> get_company_by_facebok($language_id, $user_id, $fb_user_data['work']);
		}

		// populate i was born in field with country of city in url
		$data['born_in_country'] = $current_country ? $current_country -> country_id : '0';
		$data['country_name'] = $current_country ? $current_country -> description : '';
		$data['city_name'] = $city ? $city -> description : '';

		$user = $this -> model_user -> get_user($user_id);
		if ($user) {
			$data['is_return_apply'] = $this -> session -> userdata('is_return_apply');
			;
			$data['user_first_name'] = $user -> first_name;
		}
		$data['country_id'] = $country_id;
		$data['page_title'] = 'Edit Profile';
		$data['page_name'] = 'user/editProfile';
		$this -> load -> view('template/editProfileTemplate', $data);
	}

	private function ideal_match_filter_prefrences($user_id, $language_id) {
		/*------------------get the ideal_match_filter_prefrences-------------*/

		$fields = array('child.filter_id', 'parent.description AS filterName', 'parent.id_to_map');

		$from = 'filter as parent';
		$joins = array('user_ideal_match_filter as child' => array('child.filter_id = parent.filter_id', 'LEFT'));

		unset($where);
		$where['child.user_id'] = $user_id;

		//Hannan Munshi : supplied static because array_key_exists funciton is used to search for keys and
		//if these keys are fetched in other language from DB then any of the keys wont match and Step2 b will be rendered blank.
		//WE SHOULD BE CAREFUL REGARDING THIS SITUATION IN OTHER PART OF CODE ALSO.
		//It should be made sure that comparisions are made in same language only.
		$where['parent.language_id'] = 1;
		//$language_id;

		$selectedFilters = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'view_order asc');
		//$data['selectedFilters'] = array();
		$data = array();
		if (!empty($selectedFilters)) {
			foreach ($selectedFilters as $key => $value) {
				$data[$value['id_to_map']] = $value;
			}
		}
		return $data;
		/*------------------get the ideal_match_filter_prefrences END---------*/
	}

	/* This action is currently not in use.
	 */
	public function add_filters() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$user_id = $this -> session -> userdata('user_id');
		$dataToInsert = $this -> input -> post('idealDateAttributes');
		$this -> model_user -> insert_filters($user_id, $dataToInsert);

		/*$response = array('actionStatus'=>'ok',
		 'html' => $this->load->view('user/ajaxStep2b',$data,TRUE)
		 );*/
		$response = array('actionStatus' => 'ok');
		die(json_encode($response));
	}

	public function ideal_match() {
		$this -> model_user -> is_signup_process();
		$user_id = $this -> session -> userdata('user_id');

		$language_id = $this -> session -> userdata('sess_language_id');
		$city_id = $this -> session -> userdata('sess_city_id');
		//$this->model_user->change_url_by_current_city($city_id,$user_id);
		$current_country = $this -> model_user -> getCountryByCity($city_id);
		$country_id = $current_country ? $current_country -> country_id : '0';

		$data['user_data'] = $this -> model_user -> get_user_data($user_id);
		$useMeters = $this -> general_model -> getSingleValue('country', 'use_meters', array('country_id' => $country_id, 'display_language_id' => $language_id));
		$data['useMeters'] = $useMeters ? $useMeters : '';
		//$data['importance']        = $this->model_user->select_importance($language_id);
		$data['importance'] = array('1' => 'Very Important', '2' => 'Important', '3' => 'Not Important');

		$data['feet'] = $this -> model_user -> get_feet();
		/*---------------------change by Hannan Munshi------------------*/

		for ($i = 145; $i <= 200; $i++) {
			$data['centemeters'][$i] = $i;
		}
		/*---------------------change by Hannan Munshi------------------*/

		$data['inches'] = $this -> model_user -> get_inches();
		$data['body_type'] = $this -> model_user -> get_body_type($language_id);
		$data['looks'] = $this -> model_user -> get_looks($language_id);
		//$data['ethnicity']           = $this->model_user->get_ethnicity($language_id);
		$data['ethnicity'] = $this -> model_user -> get_ethnicity($language_id, $country_id);
		$data['hair_length'] = $this -> model_user -> get_hair_length($language_id);
		$data['skin_tone'] = $this -> model_user -> get_skin_tone($language_id);
		$data['eye_wear'] = $this -> model_user -> get_eye_wear($language_id);
		$data['descriptive_word'] = $this -> model_user -> get_descriptive_word($language_id);
		/*-----------------change by Hannan-----------------------------*/

		//modified the structure of array. change was not made in model coz the currently returned array
		//might be being used at some other page in system.
		if (!empty($data['descriptive_word'])) {
			$descriptive_word = array();
			foreach ($data['descriptive_word'] as $key => $value) {
				$descriptive_word[$value['descriptive_word_id']] = $value['description'];
			}
			$data['descriptive_word'] = $descriptive_word;
		}

		/*--------------------------------------------------------------*/
		$data['education_level'] = $this -> model_user -> get_education_level($language_id);
		/*-----------------change by Hannan-----------------------------*/

		//modified the structure of array. change was not made in model coz the currently returned array
		//might be being used at some other page in system.
		if (!empty($data['education_level'])) {
			$education_level = array();
			foreach ($data['education_level'] as $key => $value) {
				$education_level[$value['education_level_id']] = $value['description'];
			}
			$data['education_level'] = $education_level;
		}
		/*--------------------------------------------------------------*/
		/*------------------get ideal_match_filters------------------*/
		$this -> general_model -> set_table('filter');
		unset($where);
		$where['language_id'] = 1;
		$result = $this -> general_model -> get(array('filter_id', 'description'), $where, array('view_order' => 'asc'));
		foreach ($result as $key => $value) {
			$data['idealDateFilters'][$value['filter_id']] = $value['description'];
		}

		/*------------------get ideal_match_filters END------------------*/

		$data['school_subject'] = $this -> model_user -> get_school_subject($language_id);
		$data['carrier_stage'] = $this -> model_user -> get_carrier_stage($language_id);
		$data['job_functions'] = $this -> model_user -> get_job_functions($language_id);
		$data['industry'] = $this -> model_user -> get_industries($language_id);
		$data['currency'] = $this -> model_user -> get_currency($language_id);
		$data['relationship_status'] = $this -> model_user -> get_relationship_status($language_id);

		/*-----------------change by Hannan-----------------------------*/

		//modified the structure of array. change was not made in model coz the currently returned array
		//might be being used at some other page in system.
		if (!empty($data['relationship_status'])) {
			$relationship_status = array();
			foreach ($data['relationship_status'] as $key => $value) {
				$relationship_status[$value['relationship_status_id']] = $value['description'];
			}
			$data['relationship_status'] = $relationship_status;
		}
		/*--------------------------------------------------------------*/
		//removed first blank key that comes from model.Dint remove directly from model because that blank key is needed to display in edit_profile.

		$child_status = $this -> model_user -> get_child_status($language_id);
		unset($child_status['']);
		$data['child_status'] = $child_status;

		$child_plans = $this -> model_user -> get_child_plans($language_id);
		unset($child_plans['']);
		$data['child_plans'] = $child_plans;

		$data['religious_belief'] = $this -> model_user -> get_religious_belief($language_id);
		$data['smoking_status'] = $this -> model_user -> get_smoking_status($language_id);
		/*-----------------change by Hannan-----------------------------*/

		//modified the structure of array. change was not made in model coz the currently returned array
		//might be being used at some other page in system.
		if (!empty($data['smoking_status'])) {
			$smoking_status = array();
			foreach ($data['smoking_status'] as $key => $value) {
				$smoking_status[$value['smoking_status_id']] = $value['description'];
			}
			$data['smoking_status'] = $smoking_status;
		}
		/*--------------------------------------------------------------*/
		$data['drinking_status'] = $this -> model_user -> get_drinking_status($language_id);
		/*-----------------change by Hannan-----------------------------*/

		//modified the structure of array. change was not made in model coz the currently returned array
		//might be being used at some other page in system.
		if (!empty($data['drinking_status'])) {
			$drinking_status = array();
			foreach ($data['drinking_status'] as $key => $value) {
				$drinking_status[$value['drinking_status_id']] = $value['description'];
			}
			$data['drinking_status'] = $drinking_status;
		}
		/*--------------------------------------------------------------*/

		$data['exercise_frequency'] = $this -> model_user -> get_exercise_frequency($language_id);

		/*-----------------change by Hannan-----------------------------*/

		//modified the structure of array. change was not made in model coz the currently returned array
		//might be being used at some other page in system.
		if (!empty($data['exercise_frequency'])) {
			$exercise_frequency = array();
			foreach ($data['exercise_frequency'] as $key => $value) {
				$exercise_frequency[$value['exercise_frequency_id']] = $value['description'];
			}
			$data['exercise_frequency'] = $exercise_frequency;
		}
		/*--------------------------------------------------------------*/
		$residence_type = $this -> model_user -> get_residence_type($language_id);
		//removed first blank key that comes from model.Dint remove directly from model because that blank key is needed to display in edit_profile.
		unset($residence_type['']);
		$data['residence_type'] = $residence_type;

		$data['personality'] = array();
		//$this->model_user->get_personality($language_id);
		/*-----------------change by Hannan-----------------------------*/
		//modified the structure of array. change was not made in model coz the currently returned array
		//might be being used at some other page in system.
		if (!empty($data['personality'])) {
			$personality = array();
			foreach ($data['personality'] as $key => $value) {
				$personality[$value['personality_id']] = $value['description'];
			}
			$data['personality'] = $personality;
		}
		/*--------------------------------------------------------------*/
		$data['nationality'] = $this -> model_user -> get_country($language_id);
		$data['eye_color'] = $this -> model_user -> get_eye_color($language_id);
		$data['hair_color'] = $this -> model_user -> get_hair_color($language_id);
		$currency_detail = $this -> model_user -> get_currency_by_country($language_id, $country_id);
		$data['currency_id'] = (!empty($currency_detail)) ? $currency_detail['currency_id'] : '';

		/*-------changed by Hannan Munshi  --------------------*/
		$data['date_type'] = $this -> model_user -> get_date_type($language_id);
		$data['contact_method'] = $this -> model_user -> get_contact_method($language_id);
		/*-----------------change by Hannan-----------------------------*/

		//modified the structure of array. change was not made in model coz the currently returned array
		//might be being used at some other page in system.
		if (!empty($data['date_type'])) {
			$date_type = array();
			foreach ($data['date_type'] as $key => $value) {
				$date_type[$value['date_type_id']] = $value['description'];
			}
			$data['date_type'] = $date_type;
		}

		if (!empty($data['contact_method'])) {
			$contact_method = array();
			foreach ($data['contact_method'] as $key => $value) {
				$contact_method[$value['contact_method_id']] = $value['description'];
			}
			$data['contact_method'] = $contact_method;
		}
		/*--------------------------------------------------------------*/
		$data['year'] = array();
		$yearsTo = 99;
		$yearsFrom = 18;
		for ($i = $yearsFrom; $i <= $yearsTo; $i++) {
			$data['year'][$i] = $i;
		}

		$data['user_want_school'] = $this -> datetix -> user_want($user_id, "school");
		$selected_company = $this -> datetix -> user_want($user_id, "company");

		//echo $this->db->last_query();exit;
		$this -> general -> set_table('user_want_company');
		$user_custom_company_required = $this -> general -> get("user_want_company_id, user_id,CONCAT('_', `company_name`,'_') as company_id, company_name", array('user_id' => $user_id, 'company_name !=' => ''));

		$data['user_want_company'] = array_merge($selected_company, $user_custom_company_required);

		/*------------------------------------------------------*/
		//get user's selected data.
		$data['tmp_usr_data'] = $this -> model_user -> get_user_data($user_id);
		if ($data['useMeters'] == 0 && $data['useMeters'] !== FALSE) {
			$cmFrom = $data['tmp_usr_data']['want_height_range_lower'];
			$cmTo = $data['tmp_usr_data']['want_height_range_upper'];

			$data['feetFrom'] = '';
			$data['feetTo'] = '';
			$data['inchFrom'] = '';
			$data['inchTo'] = '';

			if (!empty($cmFrom) && !empty($cmTo)) {
				$convertedValues = $this -> convertToFeet($cmFrom, $cmTo);

				$data['feetFrom'] = $convertedValues['feetFrom'];
				$data['feetTo'] = $convertedValues['feetTo'];
				$data['inchFrom'] = $convertedValues['inchFrom'];
				$data['inchTo'] = $convertedValues['inchTo'];
			}

		}
		//echo '<pre>';print_r($data);die();
		//$testData = $this->datetix->user_want($user_id,"exercise_frequency");

		$tableNameArray = array('ethnicity', 'relationship_status', 'body_type', 'education_level', 'school_subject', 'school', 'company', 'job_function', 'industry', 'religious_belief', 'smoking_status', 'drinking_status', 'exercise_frequency', 'residence_type', 'child_plan', 'child_status', 'career_stage', 'descriptive_word');

		//get data(csv) for each field
		foreach ($tableNameArray as $tableName) {
			//$data['selectedValues']['want_'.$tableName] = $this->datetix->user_want($user_id,$tableName);
			$rawSelectedData = $this -> datetix -> user_want($user_id, $tableName);
			$keyName = $tableName . '_id';
			$selectedIds = '';
			if (!empty($rawSelectedData)) {
				foreach ($rawSelectedData as $key => $value) {
					$selectedIds .= $value[$keyName] . ',';

				}
				$selectedIds = rtrim($selectedIds, ',');
				$data['selectedValues']['want_' . $tableName] = $selectedIds;
			} else {
				$data['selectedValues']['want_' . $tableName] = '';
			}
		}

		$idealMatchFilter = $this -> user_want_ideal_match($user_id);

		if (!empty($idealMatchFilter)) {
			$selectedFilters = '';
			foreach ($idealMatchFilter as $key => $value) {
				$selectedFilters .= $value['filter_id'] . ',';
			}
			$selectedFilters = rtrim($selectedFilters, ',');
			$data['selectedValues']['match_filters'] = $selectedFilters;
		} else {
			$data['selectedValues']['match_filters'] = '';
		}

		$want_relationship_type = $this -> datetix -> user_want($user_id, 'relationship_type');
		if (!empty($want_relationship_type)) {
			foreach ($want_relationship_type as $key => $value) {
				$data['want_relationship_type'][$value['relationship_type_id']] = $value['description'];
			}
		}

		$this -> general_model -> set_table('user_interest');
		$interestJoins = array('user_interest' => array('interest.interest_id = user_interest.interest_id', 'inner'));
		$interestCondition = array('interest.display_language_id' => $language_id, 'user_interest.user_id' => $user_id);
		$data['user_interests'] = $this -> general_model -> multijoins_arr('interest.* ', 'interest', $interestJoins, $interestCondition, '', 'interest.view_order asc');

		//echo '<pre>';print_r($data);echo '----------------';die();
		/*----------------------------CHECK AJAX REQUEST END------------------------------------*/
		//ajax request for editIdealMatch is served by the following if condition.
		if ($this -> input -> is_ajax_request()) {
			$dataToInsert = $this -> input -> post('idealDateAttributes');
			$this -> model_user -> insert_filters($user_id, $dataToInsert);
			$data['selectedFilters'] = $this -> ideal_match_filter_prefrences($user_id, $language_id);

			$fields = array('child.filter_id', 'parent.description AS filterName', 'parent.id_to_map');
			$from = 'filter as parent';
			$joins = array('user_ideal_match_filter as child' => array('child.filter_id = parent.filter_id', 'LEFT'));
			unset($where);
			$where['child.user_id'] = $user_id;
			$where['parent.language_id'] = 1;
			//$language_id;
			$data['filters'] = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'view_order asc');

			$response = array('actionStatus' => 'ok', 'html' => $this -> load -> view('user/ajaxIdealMatch', $data, TRUE));
			die(json_encode($response));
		} else {
			$fields = array('child.filter_id', 'parent.description AS filterName', 'parent.id_to_map');
			$from = 'filter as parent';
			$joins = array('user_ideal_match_filter as child' => array('child.filter_id = parent.filter_id', 'LEFT'));
			unset($where);
			$where['child.user_id'] = $user_id;
			$where['parent.language_id'] = 1;
			//$language_id;
			$data['filters'] = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'view_order asc');

		}

		/*----------------------------CHECK AJAX REQUEST END------------------------------------*/

		if ($this -> input -> post()) {
			$this -> model_user -> updateIdealMatch($user_id, $useMeters);
			$this -> session -> set_flashdata('msg', translate_phrase('Your ideal match preferences have been updated.'));
			$this -> session -> unset_userdata('is_return_apply');
			redirect(url_city_name() . '/ideal-match.html');
		}

		$user = $this -> model_user -> get_user($user_id);
		if ($user) {
			$data['is_return_apply'] = $this -> session -> userdata('is_return_apply');
			$data['user_first_name'] = $user -> first_name;
		}

		$data['page_title'] = translate_phrase('Ideal Match ');
		$data['page_name'] = 'user/ideal_match';

		$this -> load -> view('template/editProfileTemplate', $data);
	}

	public function convertToFeet($cmFrom, $cmTo, $singleConversion = FALSE) {
		if ($singleConversion === FALSE) {
			$feetFromFloatValue = round($cmFrom * 0.0328084, 1);

			$feetToFloatValue = round($cmTo * 0.0328084, 1);

			$convertedFromValue = explode('.', $feetFromFloatValue);

			$feetFrom = $convertedFromValue[0];
			$inchFrom = isset($convertedFromValue[1]) ? round($convertedFromValue[1]) : 0;

			$convertedToValue = explode('.', $feetToFloatValue);
			$feetTo = $convertedToValue[0];
			$inchTo = isset($convertedToValue[1]) ? round($convertedToValue[1]) : 0;

			return compact('feetFrom', 'inchFrom', 'feetTo', 'inchTo');
		} else {
			$feetFromFloatValue = round($cmFrom * 0.0328084, 1);

			//$feetToFloatValue   = round($cmTo   * 0.0328084,1);

			$convertedFromValue = explode('.', $feetFromFloatValue);

			$feetFrom = $convertedFromValue[0];
			$inchFrom = isset($convertedFromValue[1]) ? round($convertedFromValue[1]) : 0;

			//$convertedToValue   = explode('.',$feetToFloatValue);
			//$feetTo  =  $convertedToValue[0];
			//$inchTo  = isset($convertedToValue[1]) ? round($convertedToValue[1]) : 0;

			return compact('feetFrom', 'inchFrom');
		}
	}

	public function check_auto_sign($view_user, $user_id, $pass) {
		$decode_view_user = $this -> utility -> decode($view_user);
		$decode_user_id = $this -> utility -> decode($user_id);

		$redirect_url = base_url() . 'user/user_info/' . $view_user;
		if ($pass != '') {
			$db_pass = $this -> model_user -> get_user_field($decode_user_id, 'password');

			if (strcasecmp($pass, $db_pass) == 0) {
				if (!$this -> session -> userdata('user_id')) {
					$this -> session -> set_userdata('user_id', $decode_user_id);
					$this -> session -> set_userdata('sign_up_id', $decode_user_id);
				}
			} else {
				//$this->sign_in_using_email($redirect_url);
				$url = base_url() . url_city_name() . '/signin.html?return_url=' . $redirect_url;
				redirect($url);
			}
		} else {
			parse_str($_SERVER['QUERY_STRING'], $_REQUEST);
			$this -> load -> library('Facebook');
			$userId = $this -> facebook -> getUser();

			$data['url'] = '';
			if ($userId == 0) {
				//user_religion_politics (remove as per client)
				$access['scope'] = 'email,user_photos,user_activities,user_about_me,user_birthday,user_education_history,user_groups,user_hometown,user_interests,user_likes,user_location,user_relationships,user_relationship_details,user_website,user_work_history,friends_about_me,friends_activities,friends_birthday,friends_education_history,friends_groups,friends_hometown,friends_interests,friends_likes,friends_relationships,friends_relationship_details,friends_religion_politics,friends_website,friends_work_history,friends_location';
				$access['redirect_uri'] = $redirect_url . '/' . $user_id;
				$fb_login_url = $this -> facebook -> getLoginUrl($access);
				redirect($fb_login_url);
			} else {
				$user = $this -> model_user -> getByFacebookId($userId);
				$this -> session -> set_userdata('user_id', $user -> user_id);
				$this -> session -> set_userdata('sign_up_id', $user -> user_id);
				$this -> session -> set_userdata('ad_id', $user->ad_id);
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
		if (!$user_intros && $user_id != $cur_user_id) {
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

				}
				
				// Chat History
				$this -> general -> set_table('user_intro_chat');
				$history_order_by['user_intro_chat_id'] = 'asc';
				$tmp_intro['chat_history'] = $this -> general -> get("", array('user_intro_id' => $tmp_intro['user_intro_id']),$history_order_by);
				$tmp_intro['upcoming_event_attendance'] = $this->datetix->intro_attendance_upcoming_event($tmp_intro['user_id'],$tmp_usr_data['gender_id']);
				$data['intro'] = $tmp_intro;
				/* END Intro data ............................................................	*/
			}

			$user_datas = array();
			//Bind view userdata.

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

				/*----------------------------------------------------*/
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
				
				//echo "<pre>";print_r($tmp_usr_data);exit;
				
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
				$data['user_info'] = $user_datas;
				if (isset($data['cur_user_info'])) {
					$data['user_data'] = $data['cur_user_info'];
				}

				$data['page_title'] = translate_phrase('User Information');
				$data['page_name'] = 'user/profile';

				$this -> load -> view('template/editProfileTemplate', $data);
			} else {
				//-nmkjWse8z6p16hPLg5OJV2xfk_SVlqgOhGrtUTjB_Q
				echo 'sorry user not found.';
			}
		}
	}
	
	
	/**
	 * Send Photo Request
	 * @access public
	 * @author Rajnish Savaliya
	 */
	 public function send_photo_view_request($requested_user_id = 1)
	 {
		$response['type'] = "0";
		$response['msg'] = translate_phrase("error occured, Please try again");
		
		$user_id = $this -> session -> userdata('user_id');
		if($user_id != $requested_user_id && $requested_user_id != "")
		{
			$this->general->set_table('user');
			$request_condition['user_id'] = $requested_user_id;
			$request_condition['privacy_photos'] = "HIDE";
			
			if($user_photo_privacy_data = $this->general->get("user_id, first_name, password, facebook_id",$request_condition))
			{
				
				$this->general->set_table('user_photo_request');
				$photo_request['requested_by_user_id'] = $user_id;
				$photo_request['user_id'] = $requested_user_id;
				if($this->general->checkDuplicate($photo_request)){
					$response['msg'] = translate_phrase("You have already sent photo request.");
				}
				else {
					$photo_request['status'] = '0';
					$photo_request['request_time'] = SQL_DATETIME;
					
					if($photo_request_id = $this->general->save($photo_request))
					{
						$data['user_data'] = $user_photo_privacy_data['0'];
						$user_email_data = $this -> model_user -> get_user_email($requested_user_id);
						
						$this->general->set_table('user');
						$intro_data = $this -> general -> get("user_id, first_name, password, facebook_id", array('user_id' => $user_id));
						$data['intro_user_data'] = $intro_data['0'];
			
						//echo $this->db->last_query();exit;
						if ($user_email_data) {
							$data['email_content'] = '';
							
							$subject = translate_phrase('You have received a new photo request from ') . $data['intro_user_data']['first_name'];
						
							$user_link = $this -> utility -> encode($data['user_data']['user_id']);
							if ($data['intro_user_data']['password']) {
								$user_link .= '/' . $data['intro_user_data']['password'];
							}
							
							$data['btn_link'] = base_url().'home/mail_action/'.$user_link.'?return_to='.url_city_name().'/setting.html&tab_name=privacy&status=1&photo_request_id='.$photo_request_id;
							$data['btn_text'] = translate_phrase('Approve');
							
							$data['btn_link2'] = base_url().'home/mail_action/'.$user_link.'?return_to='.url_city_name().'/setting.html&tab_name=privacy&status=2&photo_request_id='.$photo_request_id;
							$data['btn_text2'] = translate_phrase('Decline');
							
							$data['email_title'] = translate_phrase('You have received a new photo request from ') . $data['intro_user_data']['first_name'] . translate_phrase(' on ') . date('F j ') . translate_phrase(' at ') . date('g:ia');
							
							$email_template = $this -> load -> view('email/common', $data, true);
							//echo $email_template;exit;
							$this -> datetix -> mail_to_user($user_email_data['email_address'], $subject, $email_template);
						}
	
						$response['type'] = "1";
						$response['msg'] = translate_phrase("Your request has been sent successfully.");
					}
				}
			}
			else{
				$response['msg'] = translate_phrase("No privacy in user profile picture.");
			}
		}
		echo json_encode($response);
	}
	
	/**
	 * Function used to get user prefrences for "Important things in girlfriend."
	 * @access Private - Can be used by this controller only
	 * @return Array - Fetched data
	 * @author Hannan Munshi
	 */
	private function user_want_ideal_match($user_id) {
		$tablename = "filter";
		$fields = array('ptble.*', 'uw.*');
		//$from = 'user_want_'.$tablename.' as uw';
		$from = 'user_ideal_match_filter as uw';

		$joins = array($tablename . ' as ptble' => array('ptble.' . $tablename . '_id = uw.' . $tablename . '_id', 'LEFT'));

		$where['ptble.language_id'] = $this -> session -> userdata('sess_language_id');
		;
		$where['uw.user_id'] = $user_id;
		$temp = $this -> model_user -> multijoins($fields, $from, $joins, $where);

		if ($temp) {
			return $temp;
		} else {
			return null;
		}

	}

	public function create_captcha() {
		$this -> load -> helper('captcha');
		$vals = array('word' => $this -> model_user -> verification_code(), 'img_path' => './captcha/', 'img_url' => base_url() . '/captcha/',
		//'font_path' => './path/to/fonts/texb.ttf',
		'font_path' => './assets/fonts/MyriadPro-Semibold.ttf', 'img_width' => '150', 'img_height' => 30, 'expiration' => 7200);

		$cap = create_captcha($vals);
		return $cap;
	}

	public function autocomplete_city() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$country = $this -> input -> post('id');
		$city = $this -> model_user -> get_city($language_id, $country);
		print_r(json_encode($city));
	}

	public function upload() {
		$user_id = $this -> session -> userdata('user_id');
		$this -> load -> library('upload');
		$error = "";
		$msg = "";
		$file = $this -> uri -> segment(3);

		// set file name according to the uploaded file
		$file_ext = substr($_FILES[$file]['name'], strrpos($_FILES[$file]['name'], '.') + 1);
		if ($file == 'fileToUpload') {
			$config['file_name'] = strtotime(SQL_DATETIME) . "_profile_pic.$file_ext";
		} else if ($file == 'photo_diploma') {
			$config['file_name'] = "school_photo.$file_ext";
		} else if ($file == 'photo_business_card') {
			$config['file_name'] = "career_photo_" . $_FILES[$file]['name'];
		} else if ($file == 'photo_id_or_passport') {
			$config['file_name'] = strtotime(SQL_DATETIME) . "_card_passport.$file_ext";
		}

		//create folder
		$pathToUpload = $this -> model_user -> create_folder($user_id, $file);
		$config['upload_path'] = $pathToUpload;
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		$config['overwrite'] = true;
		$this -> upload -> initialize($config);

		$is_file_upload = $this -> model_user -> upload($file);
		if ($is_file_upload === 1) {
			$upload_data = $this -> upload -> data();
			if ($file == 'fileToUpload') {
				$photo_id = $this -> model_user -> insert_photo($upload_data, $user_id, $file);
				$this -> general_model -> set_table('user_photo');
				$condition['user_photo_id'] = $photo_id;
				$result = $this -> general_model -> get("", $condition);
				if ($result) {
					foreach ($result as $key => $value) {
						$data[$key]['id'] = $value['user_photo_id'];
						$data[$key]['set_primary'] = $value['set_primary'];
						$data[$key]['url'] = base_url() . "user_photos/user_$user_id/" . $value['photo'];
					}
					$data = $data['0'];
				}
			} else if ($file == 'photo_id_or_passport') {
				$photo_id = $this -> model_user -> insert_photo($upload_data, $user_id, $file);
				$data = $this -> model_user -> get_photo($user_id, $file, $upload_data);
			} else {
				$data = $this -> model_user -> get_photo($user_id, $file, $upload_data);
				//$this->session->set_userdata($file,$data['url']);
			}
		}

		$data['success'] = $is_file_upload;
		echo json_encode($data);
	}

	public function add_descriptive_word() {
		$user_id = $this -> session -> userdata('user_id');
		$decriptive_word_id = $this -> input -> post('decriptive_word_id');
		$result = $this -> model_user -> insert_decriptive_word($user_id, $decriptive_word_id);
	}

	public function remove_descriptive_word() {
		$user_id = $this -> session -> userdata('user_id');
		$decriptive_word_id = $this -> input -> post('decriptive_word_id');
		$result = $this -> model_user -> remove_decriptive_word($user_id, $decriptive_word_id);
	}

	public function add_school() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$school_array = array('user_school_id' => $this -> input -> post('user_school_id'), 'school_name' => $this -> input -> post('school_name'), 'school_domain' => $this -> input -> post('school_domain'), 'school_email' => $this -> input -> post('school_email'), 'degree_name' => $this -> input -> post('degree'), 'is_degree_completed' => $this -> input -> post('degree_completed'), 'years_attended_start' => $this -> input -> post('yr_start'), 'years_attended_end' => $this -> input -> post('yr_end'), 'majors' => $this -> input -> post('majors'), 'minors' => $this -> input -> post('minors'));
		$user_id = $this -> session -> userdata('user_id');

		$result = $this -> model_user -> insert_school($language_id, $user_id, $school_array);
		echo $result;
	}

	public function add_school_applyPage() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$school_array = array('user_school_id' => $this -> input -> post('user_school_id'), 'school_name' => $this -> input -> post('school_name'), 'school_domain' => $this -> input -> post('school_domain'), 'school_email' => $this -> input -> post('school_email'), 'degree_name' => $this -> input -> post('degree'), 'is_degree_completed' => $this -> input -> post('degree_completed'), 'years_attended_start' => $this -> input -> post('yr_start'), 'years_attended_end' => $this -> input -> post('yr_end'), 'majors' => $this -> input -> post('majors'), 'minors' => $this -> input -> post('minors'));
		$user_id = $this -> session -> userdata('user_id');

		$insert_result = $this -> model_user -> insert_school($language_id, $user_id, $school_array);
		if (!empty($insert_result) && $insert_result != 1) {
			/*LOGIC : ->get user_school_id and year_attended_end of this paricular user.
			 *        ->convert it into key(year) value(school_id) pairs.
			 *        ->sort this converted array.
			 *        -> follow the normal fetching process
			 *        ->return the HTML
			 *
			 */
			$list_schools = '';

			$this -> general_model -> set_table('user_school');
			$schoolCondition = array('user_id' => $user_id);
			$schoolFields = array('user_school_id', 'years_attended_end');
			$schoolOrderBy = array('years_attended_end' => 'desc', 'years_attended_start' => 'desc', 'school_name' => 'asc');
			$school = $this -> general_model -> get($schoolFields, $schoolCondition, $schoolOrderBy);
			//echo $this->db->last_query();
			$schoolIndexes = array();
			foreach ($school as $key => $value) {
				$schoolIndexes[$value['user_school_id']] = $value['user_school_id'];
			}

			$new_school = $school_array['school_name'];

			if ($school_array['school_email'] != '') {
				# school email
				if ($school_array['school_domain'] != "") {
					$school_domain = $this -> model_user -> get_school_domain_name($school_array['school_domain']);
					if ($school_array['school_email'] != "") {
						$emailHasAtSymbol = strstr(trim($school_array['school_email']), '@');
						if ($emailHasAtSymbol === FALSE) {
							$school_email = trim($school_array['school_email']) . '@' . $school_domain;
						} else {
							$school_email = trim($school_array['school_email']) . $school_domain;
						}
					} else
						$school_email = "";

				} else {
					$school_email = trim($school_array['school_email']);
				}

				$user_school['is_verified'] = '0';
				$user_school = $this -> model_user -> get_user_school_by_id($school_array['user_school_id']);

				if (!$user_school) {
					$this -> general_model -> set_table('user_school');
					$user_schoool_data = $this -> general_model -> get("*", array('school_name' => $school_array['school_name']));
					if (isset($user_schoool_data['0']))
						$user_school = $user_schoool_data['0'];
				}

				if ($user_school['is_verified'] != 1) {
					if ($user_school['verification_code'] == "") {
						$verification_code = $user_school['verification_code'] ? $user_school['verification_code'] : $this -> model_user -> verification_code();
						$send = $this -> model_user -> send_school_verification_mail($school_email, $verification_code);
						if ($send) {
							if ($last_id = $this -> session -> userdata('last_inserted_school_id')) {
								$this -> session -> userdata('last_inserted_school_id', '');
							} else {
								$last_id = $school_array['user_school_id'];
							}
							$this -> model_user -> update_user_school($last_id, array('verification_code' => $verification_code));
						} else {
							$new_school = '';
						}
					}
				} else {
					$new_school = '';
				}
			}
			//krsort($schoolIndexes);
			foreach ($schoolIndexes as $row) {
				$school_details = $this -> model_user -> get_school_details($row);
				
				$list_schools .= $this -> model_user -> list_school_details($row, $school_details, $language_id, '', $new_school);
			}
			echo $list_schools;

			die();
		} else {
			echo $insert_result;
		}
	}

	public function edit_school() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$user_school_id = $this -> input -> post('user_school_id');
		$result = $this -> model_user -> get_school_details($user_school_id);

		print_r(json_encode($result));
	}

	public function get_school_id() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$school_name = $this -> input -> post('school_name');
		$school_id = $this -> model_user -> get_school_id($school_name);

		if (!empty($school_id)) {
			die(json_encode(array('actionStatus' => 'ok', 'school_id' => $school_id)));
		} else {
			die(json_encode(array('actionStatus' => 'error', 'school_id' => FALSE)));
		}
	}

	public function remove_school() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$user_id = $this -> session -> userdata('user_id');
		$user_school_id = $this -> input -> post('user_school_id');
		$result = $this -> model_user -> remove_school($user_school_id);
		echo $school_count = $this -> model_user -> get_school_count($user_id);
	}

	public function autocomplete_school() {
		$language_id = $this -> session -> userdata('sess_language_id');
		
		//$school = $this -> model_user -> get_school($language_id);
		$school = $this -> model_user -> get_active_schools();
		//echo "<pre>";print_r($school);exit;
		print_r(json_encode($school));
	}

	public function show_school_logo() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$school_name = $this -> input -> post('school_name');
		$result = $this -> model_user -> get_school_logo($school_name);
		echo $result;
	}

	public function show_school_domain() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$school_name = $this -> input -> post('school_name');
		$result = $this -> model_user -> get_school_email_domain($school_name);

		if ($result === FALSE)
			die();

		$selected = array(key($result));
		echo form_dt_dropdown('school_email_domain_id', $result, $selected, 'id="email_domain" class="dropdown-dt"', '', "hiddenfield");
	}

	public function verify_email() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$verification_code = $this -> input -> post('verification_code');
		$id = $this -> input -> post('id');
		$value = $this -> input -> post('value');
		if ($value == 'school') {
			$user_school = $this -> model_user -> get_school_details($id);
			$email_verf_code = $user_school['0']['verification_code'];
		} else {
			$user_company = $this -> model_user -> get_company_details($id);
			$email_verf_code = $user_company['verification_code'];
		}
		if (strtolower(trim($verification_code)) == strtolower(trim($email_verf_code))) {
			$this -> model_user -> email_verification();
			echo "1";
		} else {
			echo "0";
		}
	}

	public function auto_complete_company() {
		$language_id = $this -> session -> userdata('sess_language_id');
		//$company = $this -> model_user -> get_company($language_id);
		$company = $this -> model_user -> get_active_companies();		
		print_r(json_encode($company));
	}

	public function add_company() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$user_id = $this -> session -> userdata('user_id');
		$insert_array = array('user_company_id' => $this -> input -> post('user_company_id'), 'company_name' => $this -> input -> post('company_name'), 'company_domain' => $this -> input -> post('company_domain'), 'company_email' => $this -> input -> post('company_email'), 'job_city_id' => $this -> input -> post('job_city_id'), 'show_company_name' => $this -> input -> post('show_company_name'), 'job_title' => $this -> input -> post('job_title'), 'year_work_start' => $this -> input -> post('year_work_start'), 'year_work_end' => $this -> input -> post('year_work_end'), 'industry_id' => $this -> input -> post('industry_id'), 'job_function_id' => $this -> input -> post('job_function_id'));
		$result = $this -> model_user -> insert_company($language_id, $user_id, $insert_array);
		echo $result;
	}

	public function add_company_applyPage() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$user_id = $this -> session -> userdata('user_id');
		$insert_array = array('user_company_id' => $this -> input -> post('user_company_id'), 'company_name' => $this -> input -> post('company_name'), 'company_domain' => $this -> input -> post('company_domain'), 'company_email' => $this -> input -> post('company_email'), 'job_city_id' => $this -> input -> post('job_city_id'), 'show_company_name' => $this -> input -> post('show_company_name'), 'job_title' => $this -> input -> post('job_title'), 'year_work_start' => $this -> input -> post('year_work_start'), 'year_work_end' => $this -> input -> post('year_work_end'), 'industry_id' => $this -> input -> post('industry_id'), 'job_function_id' => $this -> input -> post('job_function_id'));

		$insert_result = $this -> model_user -> insert_company($language_id, $user_id, $insert_array);
		if (!empty($insert_result) && $insert_result != 1) {

			/*LOGIC : ->get user_school_id and year_attended_end of this paricular user.
			 *        ->convert it into key(year) value(school_id) pairs.
			 *        ->sort this converted array.
			 *        -> follow the normal fetching process
			 *        ->return the HTML
			 *
			 */

			$list_companies = '';

			$this -> general_model -> set_table('user_job');
			$companyCondition = array('user_id' => $user_id);
			$companyFields = array('user_company_id', 'user_job.company_id', 'user_job.company_name', 'user_job.years_worked_start', 'user_job.years_worked_end');

			$companyOrderBy = array('years_worked_end' => 'desc', 'years_worked_start' => 'desc', 'company_name' => 'asc');

			$company = $this -> general_model -> get($companyFields, $companyCondition, $companyOrderBy);

			if ($company) {
				$this -> general_model -> set_table('company');
				foreach ($company as $r => $cmp_val) {
					if ($cmp_val['company_id']) {
						$company_name = $this -> general_model -> get("", array('display_language_id' => $language_id, 'company_id' => $cmp_val['company_id']));
						$company[$r]['company_name'] = $company_name['0']['company_name'];
					}
				}
			}
			/*==================This is newly added code for sorting=================*/
			$neededKey = 0;
			$newarray = array();
			if ($company) {
				foreach ($company as $key => $value) {

					if ($key == 0 || $key >= $neededKey) {
						$keyThreshold = $key;
						$outerLoopYearStart = $value['years_worked_start'];
						$outerLoopYearEnd = $value['years_worked_end'];
						$sameYearsData = array();
						foreach ($company as $k => $v) {

							if ($v['years_worked_start'] == $outerLoopYearStart && $v['years_worked_end'] == $outerLoopYearEnd) {
								$sameYearsData[] = $v;
							}
						}

						if (!empty($sameYearsData) && count($sameYearsData) > 1) {

							$companyNameArray = array();
							/*
							 foreach ($sameYearsData as $ke => $va) {
							 $companyNameArray[$ke] = $va['company_name'];
							 }
							 array_multisort($companyNameArray, SORT_STRING,$sameYearsData);
							 */
							uasort($sameYearsData, array($this, 'uasort_company_name'));
							$noOfElements = count($sameYearsData);
							$neededKey = $keyThreshold;
							foreach ($sameYearsData as $ke => $val) {
								//$company[$neededKey] = $val;
								$newarray[$neededKey] = $val;
								$neededKey++;
							}

						} else {
							$neededKey++;
							$newarray[] = $value;
						}
					}

				}
			}

			$company = $newarray;
			/*=======================================================================*/
			/*usort($company, function($a, $b) {
			 if ($a['company_name'] == $b['company_name']) {
			 return 0;
			 }
			 return ($a['company_name'] < $b['company_name']) ? -1 : 1;
			 }); */

			$companyIndexes = array();
			foreach ($company as $key => $value) {
				$companyIndexes[$value['user_company_id']] = $value['user_company_id'];
			}

			$new_company = $this -> input -> post('company_name');

			if ($insert_array['company_email'] != '') {
				//if($count=="0"){
				if ($insert_array['company_domain'] != "") {
					$school_domain = $this -> model_user -> get_company_domain_name($insert_array['company_domain']);
					if ($insert_array['company_email'] != "") {
						$emailHasAtSymbol = strstr(trim($insert_array['company_email']), '@');
						if ($emailHasAtSymbol === FALSE) {
							$company_email = trim($insert_array['company_email']) . '@' . $school_domain;
						} else {
							$company_email = trim($insert_array['company_email']) . $school_domain;
						}
					} else
						$company_email = "";

				} else {
					$company_email = trim($insert_array['company_email']);
				}

				$user_company = $this -> model_user -> get_user_company_by_id($insert_array['user_company_id']);
				if (!$user_company) {
					if ($last_id = $this -> session -> userdata('last_inserted_company_id')) {
						$user_company = $this -> model_user -> get_user_company_by_id($last_id);
					} else {
						$this -> general_model -> set_table('user_job');
						$user_company_data = $this -> general_model -> get("*", array('company_name' => $insert_array['company_name']));
						if (isset($user_company_data['0'])) {
							$user_company = $user_company_data['0'];
						}
					}
				}

				if ($user_company && $user_company['is_verified'] != 1) {
					if ($user_company['verification_code'] == "") {
						$verification_code = $user_company['verification_code'] ? $user_company['verification_code'] : $this -> model_user -> verification_code();
						$send = $this -> model_user -> send_company_verification_mail($company_email, $verification_code);
						if ($send) {
							if ($last_id = $this -> session -> userdata('last_inserted_company_id')) {
								$this -> session -> userdata('last_inserted_company_id', '');
							} else {
								$last_id = $insert_array['user_company_id'];
							}

							$this -> model_user -> update_user_company($last_id, array('verification_code' => $verification_code));
						} else {
							$new_company = '';
						}
					}
				} else {
					$new_company = '';
				}
			}
			foreach ($companyIndexes as $row) {
				$company_details = $this -> model_user -> get_company_details($row, $language_id);
				$list_companies .= $this -> model_user -> list_company_details($row, $company_details, $language_id, $new_company);
			}
			echo $list_companies;
			die();
		} else {
			echo $insert_result;
		}

	}

	public function show_company_industry() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$company_name = $this -> input -> post('company_name');
		$result = $this -> model_user -> get_company_industry($company_name);
		echo $result;
	}

	public function show_company_logo() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$company_name = $this -> input -> post('company_name');
		$result = $this -> model_user -> get_company_logo($company_name);
		echo $result;
	}

	public function show_company_domain() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$company_name = $this -> input -> post('company_name');
		$result = $this -> model_user -> get_company_email_domain($company_name);

		if ($result === FALSE) {
			die();
		}

		$selected = array(key($result));
		echo form_dt_dropdown('company_email_domain_id', $result, $selected, 'id="email_domain" class="dropdown-dt"', '', "hiddenfield");

		//echo  $result;
	}

	public function remove_company() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$user_id = $this -> session -> userdata('user_id');
		$user_company_id = $this -> input -> post('user_company_id');
		$result = $this -> model_user -> remove_company($user_company_id);
		echo $count = $this -> model_user -> get_company_count($user_id);
	}

	public function edit_company() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$user_company_id = $this -> input -> post('user_company_id');
		$result = $this -> model_user -> get_company_details($user_company_id);
		print_r(json_encode($result));
	}

	public function bkp_facebook() {
		/*
		 | Facebook authentication is performed through jssdk on home page.
		 | Method is called by ajax to save faceook user data.
		 | Echo out json data
		 */
		//Edite by Rajnish : No need to write Query sting in CI.
		parse_str($_SERVER['QUERY_STRING'], $_REQUEST);
		$this -> load -> library('Facebook');

		$data = array('step' => 1);

		// Try to get  user's id on facebook
		$fb_userId = $this -> facebook -> getUser();
		$data['fb_user_id'] = $fb_userId;
		if ($fb_userId) {
			// user has authenticated to connect with application
			if (($user = $this -> model_user -> getByFacebookId($fb_userId)) == false) {
				$data['user_exsist'] = 0;
			} else {
				$this -> session -> set_userdata('is_return_apply', 1);
				$data['step'] = (int)$user -> completed_application_step;
				$data['user_exsist'] = 1;
			}

			$fb_user = $this -> facebook -> api('/me');
			$this -> model_user -> setFacebookData($fb_user, $this -> input -> get('invite_id'));
		}

		echo json_encode($data);
	}

	public function ajax_get_captcha() {
		$cap = $this -> create_captcha();
		$this -> session -> set_userdata('word', $cap['word']);
		echo $cap['image'];

	}

	public function send_verification_sms() {
		$user_id = $this -> session -> userdata('user_id');
		$mobile_number = $this -> input -> post('mobile_number');
		$country_code = $this -> input -> post('country_code');
		$verification_code = $this -> model_user -> verification_code();
		$this -> model_user -> update_user($user_id, array('mobile_phone_number' => $mobile_number, 'mobile_phone_verification_code' => trim($verification_code)));

		#send sms
		$number = $country_code . $mobile_number;
		if ($this -> model_user -> send_veri_sms($number, trim($verification_code))) {
			$this -> model_user -> update_user($user_id, array('mobile_phone_verification_code_sent' => 1));
			$return_msg = translate_phrase('We have sent you a SMS with a verification code to ') . '<span class="phone-num2">(+' . $country_code . ') ' . $mobile_number . '</span>.<span>' . translate_phrase(' Please enter the verification code in the SMS into the textbox below') . ':</span>';
		} else {
			$return_msg = '<div class="error_msg" style="color:#ed217c;">' . translate_phrase("Failed to sent sms.Please try again") . '</div>';
		}
		echo $return_msg;
	}

	public function sms_verification() {
		$user_id = $this -> session -> userdata('user_id');
		$verification_code = $this -> input -> post('verification_code');
		$code = $this -> model_user -> get_user_field($user_id, 'mobile_phone_verification_code');
		$this -> model_user -> check_sms_verified($verification_code, $code, $user_id);
	}

	public function invite_friends() {
		$this -> model_user -> is_signup_process();
		$data = array();
		$user_id = $this -> session -> userdata('user_id');
		$language_id = $this -> session -> userdata('sess_language_id');
		
		$this -> model_user -> is_current_signup_process($user_id);
		
		$data['user_data'] = $this -> model_user -> get_user_data($user_id);
		
		$user = $this -> model_user -> get_user($user_id);
		if (!$user) {
			redirect('/');
		}
		$event_id = $this->utility->decode($this->input->get('event_id'));
		$fields = array('ct.description as city_name','ct.city_id','e.*');
		$from = 'event as e';
		$joins = array( 'venue as v' => array('e.venue_id = v.venue_id', 'inner'), 
					'neighborhood as n' => array('v.neighborhood_id = n.neighborhood_id', 'inner'),
					'city as ct' => array('ct.city_id = n.city_id', 'inner'));
		
		$where['ct.display_language_id'] = $language_id;
		$where['e.event_id'] = $event_id;		
		$where['n.display_language_id'] = $language_id;
		$where['e.display_language_id'] = $this -> language_id;
		if($data['event_info'] = $this -> model_user -> multijoins($fields, $from, $joins, $where,'','e.event_start_time asc'))
		{
			$data['event_info'] = $data['event_info']['0'];
			
			$redirect_link = base_url().url_city_name($data['event_info']['city_name']).'/event.html?id='.$event_id;
			$subject = translate_phrase("Hey, want to go to this event on ").date(DATE_FORMATE,strtotime($data['event_info']['event_start_time']))."?";
			
			$body = translate_phrase("Hey, want to go to the")." ";
			$body .= $data['event_info']['event_name'].translate_phrase(" event on ").date("F j",strtotime($data['event_info']['event_start_time']))."? ";
			$body .= translate_phrase("It seems like a great way to meet new people. You can find out more details and RSVP at ")."\n\r";
			$body .= $redirect_link;
			
			$data['page_title'] = translate_phrase("Invite Friends to Attend ").$data['event_info']['event_name'].translate_phrase(" on ").date(DATE_FORMATE,strtotime($data['event_info']['event_start_time']));
			$data['form_para'] = "?event_id=".$this->input->get('event_id');
		}
		else{		
			
			$subject = translate_phrase('I would like to invite you to apply for membership to DATETIX');
			
			$body = translate_phrase('Hi ') . "#Name#\n\r";
			$body .= translate_phrase('I just applied to become a member of DATETIX, an upscale dating agency in Hong Kong exclusively for graduates and students of top universities around the world.') . "\n\r";
			$body .= translate_phrase("Check it out at ") . base_url(url_city_name() . "/index.html?invite_id=$user_id") . "\n\r";
			$body .= $user -> first_name;
			$data['page_title'] = translate_phrase("Invite Friends");
			$data['form_para'] = "";
		}
		unset($where);
		
		$data['email_body'] = $body;
		$data['email_subject'] = $subject;
		
		if ($this -> input -> post('invitation')) {
			//print_r($this->input->post('invitation'));exit;
			$invites = $this -> input -> post('invitation');
			$user = $this -> model_user -> get_user($user_id);
			//$from_email = 'invite@datetix.com';
			$from_email = INFO_EMAIL;
			$success_mail = '';
			$body = $this -> input -> post('email_body');

			for ($i = 0; $i < count($invites['email']); $i++) {
				if ($invites['email'][$i] != "") {
					$invite_email = trim($invites['email'][$i]);
					if (filter_var($invite_email, FILTER_VALIDATE_EMAIL)) {
						$body = str_replace('#Name#', $invites['first_name'][$i] . ' ' . $invites['last_name'][$i], $body);
						$success_mail = $this -> model_user -> send_email($from_email, $invite_email, $this -> input -> post('email_subject'), $body);
					}
					if ($success_mail == TRUE) {
						$data['list'][] = $invites['email'][$i];
					}
				}
			}
		}

		$this -> config -> load('facebook');
		$data['facebook_user'] = 0;
		if ($user -> facebook_id && user_country_id() != FB_RESTRICTED_COUNTRY) {

			parse_str($_SERVER['QUERY_STRING'], $_REQUEST);
			$this -> load -> library('Facebook');

			$users_details = $this -> facebook -> api($user -> facebook_id . "/");

			$this -> general_model -> set_table('user_city_lived_in');
			$language_id = $this -> session -> userdata('sess_language_id');
			$fields = array('country.country_id', 'country.description AS countryName', 'user_city_lived_in.city_name');
			$from = 'user_city_lived_in';
			$joins = array('country' => array('country.country_id = user_city_lived_in.country_id', 'inner'));
			$where['user_city_lived_in.user_id'] = $user_id;
			$where['country.display_language_id'] = $language_id;
			$livedInCityQuery = $this -> general_model -> multijoins_arr($fields, $from, $joins, $where);

			$user_location = isset($users_details['location']['name']) ? $users_details['location']['name'] : NULL;
			$user_db_location = (isset($livedInCityQuery['0']['city_name'])) ? $livedInCityQuery['0']['city_name'] : strtolower(url_city_name());
			try {

				$fql = "SELECT uid,name,relationship_status, current_location
                                        FROM user 
                                        WHERE 
                                        uid in (SELECT uid2 FROM friend WHERE uid1 = me()) 
                                        AND is_app_user = 0
                                        AND relationship_status='Single' ";

				if ($friend_name = $this -> input -> post('friend_name')) {
					$fql .= " AND
					(strpos(lower(name), lower('" . $friend_name . "')) >= 0)";
					$data['facebook_user'] = 1;
					$data['fb_search'] = $friend_name;
				}
				$fql .= ' ORDER BY current_location.name ASC';

				//Create Query
				$params = array('method' => 'fql.query', 'query' => $fql, );

				if ($friends = $this -> facebook -> api($params)) {
					$data['facebook_user'] = 1;

					$same_location_friend = array();
					foreach ($friends as $key => $usr) {
						if (isset($usr['current_location']) && (in_array($user_location, $usr['current_location']) || in_array($user_db_location, $usr['current_location']))) {
							$same_location_friend[] = $usr;
							unset($friends[$key]);
						}

					}
					if ($same_location_friend) {
						$friends = array_merge($same_location_friend, $friends);
					}

					$data['fb_friends'] = $friends;
				}

				//AND ('".$user_location."' not in current_location.name OR '".$user_db_location."' not in lower(current_location.city))

				//echo "<pre>";print_r($data);exit;

				/*$fb_friends            = $this->facebook->api("me?fields=friends.fields(name,relationship_status,location)");
				 $data['fb_friends'] = array();
				 if(isset($fb_friends['friends']['data']) && $fb_friends['friends']['data'] && $user_location != NULL)
				 {
				 foreach ($fb_friends['friends']['data'] as $friend)
				 {
				 $friend_location = isset($friend['location']['name'])?$friend['location']['name']:NULL;
				 if($friend_location == $user_location &&  $friend['relationship_status'] = 'Single')
				 {
				 $friend['location_name'] = $friend_location;
				 $data['fb_friends'][] = $friend;
				 }
				 }
				 $data['facebook_user'] = 1;
				 }
				 */

			} catch (Exception $e) {
				// user is not logged in
			}
		}

		$data['fb_app_id'] = $this -> config -> item('appId');
		$data['fb_desc'] = translate_phrase('Apply for a free membership to datetix.com today and let us help set you up on first dates with high quality local singles. Please visit ') . base_url();

		$data['user_id'] = $user -> user_id;		
		$data['page_name'] = 'user/invite_friends';
		$this -> load -> view('template/editProfileTemplate', $data);
	}

	public function autocomplete_interest() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$interest = $this -> input -> get('term');
		$interest_list = $this -> model_user -> get_interest($language_id, $interest);
		echo json_encode($interest_list);
	}

	public function job_location_autocomplete() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$city = $this -> input -> get('term');
		$job_location = $this -> model_user -> get_city_list($language_id, $city);
		echo json_encode($job_location);
	}

	public function sign_in_using_email($return_url = '') {
		
		$this -> datetix -> destroy_current_session();
		if ($url = $this -> input -> get('return_url')) {
			$return_url = $url;
		}
		$highlight = '';
		if ($highlight = $this -> input -> get('highlight')) {
			$highlight = "?highlight=".$highlight;
		}
		
		//inintialize data as array
		$data = array();

		if ($this -> input -> post()) {
			//init
			$emailExists = FALSE;
			$facebookId = '';
			$approved_date = '';
			$applicationStep = '';
			$userPassword = '';
			$user_id = '';
			$is_verified = '';

			$email = trim($this -> input -> post('email'));
			//$emailExists = $this->model_home->check_email_exist($email);
			$userDetails = $this -> model_user -> get_user_by_email($email);
			if (!empty($userDetails)) {

				$emailExists = TRUE;
				$facebookId = $userDetails -> facebook_id;
				$approved_date = $userDetails -> approved_date;
				$applicationStep = $userDetails -> completed_application_step;
				$userPassword = $userDetails -> password;
				$user_id = $userDetails -> user_id;
				$is_verified = $userDetails -> is_verified;

				if ($userDetails -> last_display_language_id != '0') {

					$this -> load -> model('model_translation');
					$this -> model_translation -> setLang($userDetails -> last_display_language_id);
				}

				if ($is_verified != 1) {
					$this -> load -> library('encrypt');
					$insert_id = $this -> encrypt -> encode($user_id);
					$user_id = str_replace('/', '-', $insert_id);
					$this -> model_user -> send_verification_mail($user_id);
					$response = array('success' => 0, 'message' => translate_phrase('You must verify you email first before signing in. We have just re-sent you a verification email to your email address at ') . $email);
					die(json_encode($response));
				}
			}

			if ($emailExists === TRUE && $facebookId != "" && $approved_date == "") {
				$response = array('success' => 0, 'message' => translate_phrase('Your had registered using your Facebook account. There is no password required to sign in to your account. Simply click on the Sign in with Facebook button at the top right of this page.'));
				die(json_encode($response));
			} else if ($emailExists === TRUE && $facebookId == "" && $approved_date == "") {
				//1) if password entered is incorrect, show "Invalid email or password", or
				//2) if password entered is correct, take user back to right Apply page step based on value of "completed_application_step"
				$password = $this -> input -> post('password');
				if (!empty($password) && sha1($password) == $userPassword) {
					/*
					//$applicationStep = $applicationStep+1;
					if ($applicationStep <= 2) {
						$applicationStep = 'signup-step-' . ($applicationStep + 1) . '.html';
					} else {
						//if($is_verified == 1)
						//  $applicationStep = 'edit-profile.html';

						//else
						$applicationStep = 'signup-confirmation.html';
					}
					*/
					 
					$response = array('success' => 1, 'message' => 'Please Wait....', 'redirectUrl' => base_url() . 'user/check_signup' );
					$this -> session -> set_userdata('user_id', $user_id);
					$this -> session -> set_userdata('sign_up_id', $user_id);
					$this->session->set_userdata('ad_id', $userDetails->ad_id);
					if ($return_url != '') {
						$response['redirectUrl'] = $return_url . '/' . $this -> utility -> encode($user_id) . '/' . $userPassword;
					}

					die(json_encode($response));
				} else {
					$response = array('success' => 0, 'message' => translate_phrase('Invalid email or password'));
					die(json_encode($response));
				}
			} else if ($emailExists === TRUE && $facebookId == "" && $approved_date != "") {
				//1) if password entered is incorrect, show "Invalid email or password", or
				//2) if password entered is correct, take user back to right Apply page step based on value of "completed_application_step"
				$password = $this -> input -> post('password');
				if (!empty($password) && sha1($password) == $userPassword) {
					$response = array('success' => 1, 'message' => 'Please Wait....', 'redirectUrl' => base_url() . 'user/check_signup' );
					
					$this -> session -> set_userdata('user_id', $user_id);
					$this -> session -> set_userdata('sign_up_id', $user_id);
					$this->session->set_userdata('ad_id', $userDetails->ad_id);
					
					if ($return_url != '') {
						$response['redirectUrl'] = $return_url . '/' . $this -> utility -> encode($user_id) . '/' . $userPassword;
					}
					die(json_encode($response));

				} else {
					$response = array('success' => 0, 'message' => translate_phrase('Invalid email or password'));
					die(json_encode($response));
				}
			} else {
				$response = array('success' => 0, 'message' => translate_phrase('User Not Found'));
				die(json_encode($response));
			}
		}

		//logut from facebook.
		$this -> load -> library('Facebook');
		//detroy session
		if ($userId = $this -> facebook -> getUser()) {
			$this -> session -> unset_userdata('fb_user_data');
			$this -> facebook -> destroySession();
			if ($return_url != '') {
				$access['next'] = base_url() . url_city_name() . '/signin.html?return_url=' . $return_url . '/' . $this -> utility -> encode($user_id);
			} else {
				$access['next'] = base_url() . url_city_name() . '/signin.html'.$highlight;
			}

			$url = $this -> facebook -> getLogoutUrl($access);
			redirect($url);
		}
		
		$data['page_name'] = 'user/sign_in_using_email';
		$data['page_title'] = 'Login';
		$this -> load -> view('template/default', $data);
		//$this->load->view('user/sign_in_using_email', $data);
	}

	public function forgot_password() {

		$data = array();
		if ($this -> input -> post()) {

			$email = trim($this -> input -> post('email'));
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

				if ($this -> model_user -> check_user_email_exit($email)) {
					$fields = array('user.facebook_id', 'user.approved_date', 'user_email.email_address');
					$from = 'user';
					$joins = array('user_email' => array('user_email.user_id = user.user_id', 'LEFT'));
					$where['user_email.email_address'] = trim($this -> input -> post('email'));

					$result = $this -> model_user -> multijoins($fields, $from, $joins, $where);

					if ($result[0]['facebook_id'] != "" && $result[0]['email_address'] != "") {
						///you registered from fb;
						die(json_encode(array('success' => 0, 'message' => translate_phrase('You had registered using your Facebook account. There is no password required to sign in to your account. Simply click on the Sign in with Facebook button at the top right of this page.'))));
					} else if ($result[0]['facebook_id'] == "" && $result[0]['email_address'] != "") {
						//send reset password.
						$this -> model_user -> send_forgot_password_mail($email);
						die(json_encode(array('success' => 1, 'message' => translate_phrase('Your password reset email has been sent to ') . $email)));
					}
				} else {
					//email nto found.
					die(json_encode(array('success' => 0, 'message' => 'E-mail address not found.')));
				}
			} else {
				//invalid email
				die(json_encode(array('success' => 0, 'message' => 'Please enter a valid email address.')));
			}
		}
		// no post req so render page.
		/*if ($this->input->post()) {

		 $this->general_model->set_table('user');
		 $this->general_model->get();
		 // generate password and send to user
		 echo json_encode($this->model_user->forgot_password(trim($this->input->post('email'))));
		 return;
		 }*/

		$data['page_name'] = 'user/forgot_password';
		$data['page_title'] = 'Forgot Password';

		$this -> load -> view('template/default', $data);
	}

	public function password_reset() {
		$this -> load -> library('utility');

		$data['user_id'] = $this -> uri -> segment(3);
		$user_id = $this -> utility -> decode($data['user_id']);

		//if there is a junk string in $this->uri->segment(3) then handle it.
		if ((int)$user_id == 0) {
			$this -> session -> set_flashdata('invalidResetLink', translate_phrase('Invalid Password Reset Link.Please follow the reset password process again'));
			redirect(url_city_name() . "/forgot-password.html");
		}

		$email = $this -> model_user -> get_user_email($user_id);

		//$email will be empty if email of user with this id is not found , hence handle it.
		if (empty($email)) {
			$this -> session -> set_flashdata('invalidResetLink', translate_phrase('Invalid Password Reset Link.Please follow the reset password process again'));
			redirect(url_city_name() . "/forgot-password.html");
		}

		$data['email'] = $email['email_address'];

		//this page is not ready so when a user clicks on reset password link in email then he would be redirected to index page.
		//redirect(base_url().url_city_name().'/index.html');
		if ($this -> input -> post()) {
			$this -> form_validation -> set_rules('oldPassword', 'Old Password', 'required');
			$this -> form_validation -> set_rules('newPassword', 'New Password', 'required');
			$this -> form_validation -> set_rules('repeatNewPassword', 'Repeat New Passord', 'required|matches[newPassword]');

			$this -> form_validation -> set_message('matches', 'The two passwords you entered do not match each other.');
			$this -> form_validation -> set_error_delimiters('<div class="password_mismatch_err error_msg">', '</div>');
			if ($this -> form_validation -> run('change_password') == true) {
				#update password
				//$this->model_user->update_user($user_id,array('password'=>$this->input->post('password')));
				$this -> session -> set_flashdata('suc_msg', "Success - you've reset your password");
				redirect(url_city_name() . "/password-reset.html/1");
			}

			redirect(base_url() . url_city_name() . "/password-reset.html/1");

		}

		$data['page_title'] = 'Reset Password';
		$data['page_name'] = 'user/password_reset';
		$this -> load -> view('template/default', $data);
	}

	public function processResetPassword() {

		if ($this -> input -> post()) {
			$this -> load -> library('utility');

			//make sure both the passwords are same. i.e newpass and repeat pass.

			$newPassword = trim($this -> input -> post('newPassword'));
			$repeatNewPassword = trim($this -> input -> post('newPassword'));

			if ((!empty($newPassword) && !empty($repeatNewPassword)) && ($newPassword == $repeatNewPassword)) {
				$user_id = $this -> utility -> decode($this -> input -> post('resetText'));
				$userDetails = $this -> model_user -> get_user($user_id);

				#update password
				$this -> model_user -> update_user($user_id, array('password' => sha1($this -> input -> post('newPassword'))));
				//$this->session->set_flashdata('suc_msg',"Success - you've reset your password");
				$successMessage = "Your password has been reset successfully. <a style='color:#2097d4;' href='" . base_url() . url_city_name() . '/signin.html' . "'>Click here</a> to Sign in";
				die(json_encode(array('actionStatus' => 'ok', 'message' => $successMessage)));
			}
			die(json_encode(array('actionStatus' => 'error', 'message' => 'Passwords dont match')));
		}
	}

	public function current_living_city() {

		$this -> load -> model('model_home');
		$lang_id = $this -> session -> userdata('sess_language_id');
		if (($city_id = $this -> input -> get('city_id')) && !empty($city_id)) {
			$this -> model_home -> redirect_to_city($city_id);
		}
		$data = array();
		$data['city_data'] = $this -> model_home -> getChangeCityData($lang_id);
		$this -> load -> view('user/current_living_city', $data);
	}

	public function send_verification_email() {
		$user_id = $this -> session -> userdata('user_id');
		$user_school_id = $this -> input -> post('id');
		$email = $this -> input -> post('email');
		$mail_for = $this -> input -> post('mail_for');
		$this -> model_user -> check_verification_mail_sent($user_id, $user_school_id, $email, $mail_for);

	}

	public function delete_profile_photo() {
		$user_id = $this -> session -> userdata('user_id');
		$photo_id = $this -> input -> post('id');
		$this -> model_user -> delete_profile_photo($photo_id, $user_id);
	}

	public function uasort_company_name($a, $b) {
		return strcasecmp($a['company_name'], $b['company_name']);
	}

	public function primary_profile_photo() {
		$condition['user_id'] = $this -> session -> userdata('user_id');
		$data['set_primary'] = '0';
		$this -> general_model -> set_table('user_photo');

		//remove primary photo
		$this -> general_model -> update($data, $condition);

		$data['set_primary'] = '1';
		$condition['user_photo_id'] = $this -> input -> post('id');
		echo $this -> general_model -> update($data, $condition);
	}

	public function user_photo_privacy($opt) {

		$condition['user_id'] = $this -> session -> userdata('user_id');
		$data['privacy_photos'] = $opt;
		$this -> general_model -> set_table('user');
		//remove primary photo
		echo $this -> general_model -> update($data, $condition);
		echo $this -> db -> last_query();
	}

	public function manual_verify_sms() {
		$condition['user_id'] = $this -> session -> userdata('user_id');
		$data['mobile_phone_verification_code_sent'] = '1';
		$this -> general_model -> set_table('user');
		echo $this -> general_model -> update($data, $condition);
	}

	/**
	 * Delete educational details uploded photos, temporary files that are stored before saving to database.
	 * A folder is craeted wiht current session to store temporary image file,
	 * Here we remove the temporary folder
	 */
	public function delete_edu_temp_photo() {
		$user_id = $this -> session -> userdata('user_id');
		$folder = $this -> input -> post('id');

		$this -> model_user -> delete_edu_temp_photo($folder, $user_id);
	}

	/**
	 * Delete photo of id card or password
	 */
	public function delete_photo_id() {
		$user_id = $this -> session -> userdata('user_id');
		$resp = $this -> model_user -> delete_card_passport($user_id);
	}

	public function delete_user_by_email() {
		if ($mail = $this -> input -> post('email')) {
			$this -> general_model -> set_table('user_email');
			if ($id = $this -> general_model -> delete(array('email_address' => $mail)))
				echo 'deleted user id:' . $id;
			else
				echo 'not exist';
		}
	}
}
?>
