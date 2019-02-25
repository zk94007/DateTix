<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Signup extends MY_Controller {

	var $language_id = '1';
	var $user_id = '';
	var $city_id = '';
	var $singup_name = '';
	public function __construct() {
		parent::__construct();
		$this -> load -> model('model_user');
                $this -> load -> model('model_home');
		$this -> load -> model('general_model');
		$this->singup_name = "signup-step";
		
		if($this->user_id = $this -> session -> userdata('user_id'))
		{
			
		}
		else
		{
			if($this->user_id = $this -> session -> userdata('sign_up_id'))
			{
						
			}
			else
			{
				redirect('/');
			}
		}
		
		if ($this->user_id) 
		{
			
			$this -> language_id = $this -> session -> userdata('sess_language_id');
			$this->city_id = $this -> session -> userdata('sess_city_id');
						
			if(!$this->city_id)
			{
				//$this->city_id = $this->config->item('default_city');
			}
		}
	}
	
	/**
	 * Step 1: 
	 * @access public 
	 * @return view[signup_step/step1] 
	 * @author Rajnish Savaliya
	 */
	public function step1()
	{
		/* [11/18/2014 9:40:15 PM] Michael Ye: in apply step 1, if user city id is 1007 but the URL is hongkong, then it shows user as living in Hong Kong instead of NYC
			can u make the label show the city of user.curenty_city_id instead of the URL's city?
		 */ 
		
		$this->city_id = $this -> model_user -> get_city_by_user_id($this->user_id);
		
		$city = $this -> model_user -> get_city_by_id($this->city_id);
		$current_country = $this -> model_user -> getCountryByCity($this->city_id);
		$country_id = $current_country ? $current_country -> country_id : '0';
		
		$current_country = $this -> model_user -> getCountryByCity($this->city_id);
		$data['country_name'] = $current_country ? $current_country -> description : '';
		$data['city_name'] = $city ? $city -> description : '';
		
		$data['country'] = $this -> model_user -> get_country($this -> language_id);
				
		//echo "<pre>";print_r($data['country'] );exit;
				
		$fb_user_data = $this -> session -> userdata('fb_user_data');
		$data['fb_user_data'] = $fb_user_data;
		
		//$data['district'] = $this -> model_user -> get_district($this->language_id, $this->city_id);
		$data['has_district'] = $this -> model_user -> check_district_exist($this->language_id, $this->city_id);
		
		//data used in step1
		$data['gender'] = $this -> model_user -> get_gender($this -> language_id);
		$data['relationship_type'] = $this -> model_user -> get_relationship_type($this -> language_id);
		$data['country'] = $this -> model_user -> get_country($this -> language_id);
        $data['year'] = $this -> model_user -> get_year();
		$data['month'] = $this -> model_user -> get_month();
		$data['date'] = $this -> model_user -> get_date();
		
		if ($postData = $this -> input -> post()) {
			
			$update_data['gender_id']   = $postData['gender'];
			$update_data['current_city_id']   = $postData['current_city_id'];
			
			//update session chat id
			$this -> session -> set_userdata('sess_city_id',$update_data['current_city_id']);
			
			$update_data['birth_date']   = $postData['yearId'].'-'.$postData['monthId'].'-'.$postData['dateId'];
			$update_data['completed_application_step'] = "1";
			if(isset($postData['district']) && $postData['district'])
			{
				$update_data['current_district_id'] = $postData['district'];	
			}
			$this->model_user->update_user($this->user_id,$update_data);
			$this->model_user->update_user_want_age($this->user_id);
			
			$this->model_user->clear_data($this->user_id,'user_want_gender');
			$this->model_user->insert_user_want_gender($this->user_id,$postData['want_to_date']);

			$this->model_user->clear_data($this->user_id,'user_want_relationship_type');
			$this->model_user->insert_user_want_relationship_type($this->user_id,$postData['looking_for']);
		
			if($this -> session -> userdata('is_return_apply'))
			{
				$this -> session -> unset_userdata('is_return_apply');
			}
			$this -> model_user -> is_current_signup_process($this->user_id);
		}
                
                $data['SelectedCountry']=$this->model_home->get_country_by_doamin($this -> language_id);
                
                //Checking Event_ticket in session
		if($data['event_info'] = $this->_is_event_rsvp())
		{
			$data['page_title'] = translate_phrase('RSVP for ').$data['event_info']['event_name'].(' on ').date(DATE_FORMATE,strtotime($data['event_info']['event_start_time']));
		}
		else
		{
			$data['page_title'] = translate_phrase('Apply for ').get_assets('name','DateTix').translate_phrase(' Membership');
		}
		$data['page_name'] = 'user/signup_step/step1';
		$this -> load -> view('template/default', $data);
	}
	/**
	 * Step 1: Ajax call for get city based on country_id[id] 
	 * @access public 
	 * @return dropdown HTML
	 * @author Rajnish Savaliya
	 */
	public function get_city_by_country() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$country = $this -> input -> post('id');
		$city = $this -> model_user -> get_city($language_id, $country);
		if($city)
		{
			if(count($city) == 1)
			{
				echo '<input type="hidden" id="current_city_id" name="current_city_id" value="'.key($city).'">';
                                echo form_dt_dropdown('current_city_id',$city,  key($city),'class="dropdown-dt scemdowndomain" ', translate_phrase('Select city'), "hiddenfield");	
			}
			else {
				echo form_dt_dropdown('current_city_id',$city,  key($city),'class="dropdown-dt scemdowndomain" ', translate_phrase('Select city'), "hiddenfield");	
			}	
		}
		
	}
	
	/**
	 * Step 2: 
	 * @access public 
	 * @return view[signup_step/step2] 
	 * @author Rajnish Savaliya
	 */
	public function step2()
	{
		$city = $this -> model_user -> get_city_by_id($this->city_id);
		$current_country = $this -> model_user -> getCountryByCity($this->city_id);
		$country_id = $current_country ? $current_country -> country_id : '1';
		
		$fb_user_data = $this -> session -> userdata('fb_user_data');
		$data['fb_user_data'] = $fb_user_data;
		
		//data used 
		$data['feet'] = $this -> model_user -> get_feet();
		$data['inches'] = $this -> model_user -> get_inches();
		$data['cms'] = $this -> model_user -> get_height_cm();
		$data['ethnicity'] = $this -> model_user -> get_ethnicity($this -> language_id, $country_id);
		
		$data['use_meters'] = $this -> model_user -> check_use_meters($country_id);
		//$data['relationship_status'] = $this -> model_user -> get_relationship_status($this->language_id);
		//$data['religious_belief'] = $this -> model_user -> get_religious_belief($this->language_id);
		$data['postal_code_exist'] = $this -> model_user -> check_postal_code_exist($this->language_id, $country_id);
		
		$data['user_photos'] = $this -> model_user -> get_photos($this->user_id, "profile");
		$data['country_name'] = $current_country ? $current_country -> description : '';
		$data['city_name'] = $city ? $city -> description : '';
		
		if ($postData = $this -> input -> post()) {
			

			if($data['use_meters'] == 1)
			{
				$computedHeight = $postData['height'];
			}
			else
			{
				$feet   = $postData['feet'];
				$inches = $postData['inches'];

				$totalInches = ($feet.'.'.$inches)*12;
				$computedHeight = round(($totalInches * 2.54),2);
			}
			$update_data['height']   = $computedHeight;
			$update_data['ethnicity_id']   = $postData['ethnicity'];
			$update_data['relationship_status_id']   = $postData['relationship_status'];
			$update_data['religious_belief_id']   = $postData['religiousBeliefId'];
			$update_data['current_postal_code'] = $postData['postal_code'];
			
			$update_data['completed_application_step'] = "2";
			
			$this->model_user->update_user($this->user_id,$update_data);
			if($this -> session -> userdata('is_return_apply'))
			{
				$this -> session -> unset_userdata('is_return_apply');
			}
			$this -> model_user -> is_current_signup_process($this->user_id);
		}
				
		//Checking Event_ticket in session
		if($data['event_info'] = $this->_is_event_rsvp())
		{
			$data['page_title'] = translate_phrase('RSVP for ').$data['event_info']['event_name'].(' on ').date(DATE_FORMATE,strtotime($data['event_info']['event_start_time']));
		}
		else
		{
			$data['page_title'] = translate_phrase('Apply for ').get_assets('name','DateTix').translate_phrase(' Membership');
		}
		
		$data['page_name'] = 'user/signup_step/step2';
		$this -> load -> view('template/default', $data);
	}
	
	/**
	 * Step 3: School
	 * @access public 
	 * @return view[signup_step/step2] 
	 * @author Rajnish Savaliya
	 */
	public function step3()
	{
		$city = $this -> model_user -> get_city_by_id($this->city_id);
		$current_country = $this -> model_user -> getCountryByCity($this->city_id);
		$country_id = $current_country ? $current_country -> country_id : '0';
		
		$fb_user_data = $this -> session -> userdata('fb_user_data');
		$data['fb_user_data'] = $fb_user_data;
		
		/*---------------SCHOOL sort start-----------------------------*/
		$fb_year = array();
		if (isset($fb_user_data['education']) && !empty($fb_user_data['education']))
			foreach ($fb_user_data['education'] as $key => $value) {
				if (isset($value['year']))
					$fb_year[$key] = $value['year'];
			}

		if (!empty($fb_year))
			array_multisort($fb_year, SORT_DESC, $fb_user_data['education']);

		/*---------------SCHOOL sort END-----------------------------*/
		
		$data['education_level'] = $this -> model_user -> get_education_level($this->language_id);
		
		if ($postData = $this -> input -> post()) {
			
			$this->model_user->genericUserInsert('education_level', $this->user_id, $postData['education_level']);
						
			$update_data['completed_application_step'] = "3";			
			$this->model_user->update_user($this->user_id,$update_data);
			
			if($this -> session -> userdata('is_return_apply'))
			{
				$this -> session -> unset_userdata('is_return_apply');
			}
			$this -> model_user -> is_current_signup_process($this->user_id);
		}
		else {
			//Changed By Rajnish
			$this -> model_user -> remove_school_by_user($this->user_id);		
		}
		
		#inserting school details-from facebook
		$data['user_school_id'] = array();
		if (!empty($fb_user_data['education'])) {
			$school_details = $this -> model_user -> get_school_by_facebok($this->language_id, $this->user_id, $fb_user_data['education']);
			$data['school_details'] = (!empty($school_details)) ? $school_details : array();
			$data['user_school_id'] = $this -> model_user -> get_user_school_id($this->user_id);
		}
		
		#get Count school how many school left in account after remove and add[fb]
		$data['school_count'] = $this -> model_user -> get_school_count($this->user_id);

		//Checking Event_ticket in session
		if($data['event_info'] = $this->_is_event_rsvp())
		{
			$data['page_title'] = translate_phrase('RSVP for ').$data['event_info']['event_name'].(' on ').date(DATE_FORMATE,strtotime($data['event_info']['event_start_time']));
		}
		else
		{
			$data['page_title'] = translate_phrase('Apply for ').get_assets('name','DateTix').translate_phrase(' Membership');
		}
		
		$data['page_name'] = 'user/signup_step/step3';
		$this -> load -> view('template/default', $data);
	}
	
	
	/**
	 * Step 4: Company
	 * @access public 
	 * @return view[signup_step/step2] 
	 * @author Rajnish Savaliya
	 */
	public function step4()
	{
		$city = $this -> model_user -> get_city_by_id($this->city_id);
		$current_country = $this -> model_user -> getCountryByCity($this->city_id);
		$country_id = $current_country ? $current_country -> country_id : '0';
		
		$fb_user_data = $this -> session -> userdata('fb_user_data');
		$data['fb_user_data'] = $fb_user_data;
		
		$data['annual_income_range'] = $this -> model_user -> get_annual_income_range($country_id);
		$data['industry'] = $this -> model_user -> get_industry($this->language_id);
		$data['job_functions'] = $this -> model_user -> get_job_functions($this->language_id);
		
		if ($postData = $this -> input -> post()) {
						
			$update_data['completed_application_step'] = "4";
			$update_data['annual_income_range_id'] = $postData['annual_income_range_id'];
						
			$this->model_user->update_user($this->user_id,$update_data);			
			if($this -> session -> userdata('is_return_apply'))
			{
				$this -> session -> unset_userdata('is_return_apply');
			}
			$this -> model_user -> is_current_signup_process($this->user_id);
		}
		else {
			//Changed By Rajnish
			$this -> model_user -> remove_company_by_user($this->user_id);		
		}
		
		#inserting company details-from facebook
		$data['user_company_id'] = array();
		if (!empty($fb_user_data['work'])) {
			$company_details = $this -> model_user -> get_company_by_facebok($this->language_id, $this->user_id, $fb_user_data['work']);
			$data['company_details'] = (!empty($company_details)) ? $company_details : array();
			$data['user_company_id'] = $this -> model_user -> get_user_company_id($this->user_id);
		}
		
		#get Count school how many school left in account after remove and add[fb]
		$data['company_count'] = $this -> model_user -> get_company_count($this->user_id);

		//Checking Event_ticket in session
		if($data['event_info'] = $this->_is_event_rsvp())
		{
			$data['page_title'] = translate_phrase('RSVP for ').$data['event_info']['event_name'].(' on ').date(DATE_FORMATE,strtotime($data['event_info']['event_start_time']));
		}
		else
		{
			$data['page_title'] = translate_phrase('Apply for ').get_assets('name','DateTix').translate_phrase(' Membership');
		}
		
		$data['page_name'] = 'user/signup_step/step4';
		
		$this -> load -> view('template/default', $data);
	}
	
	/**
	 * Step 5: Personality
	 * @access public 
	 * @return view[signup_step/step2] 
	 * @author Rajnish Savaliya
	 */
	public function step5()
	{
		$interestsResult = $this -> model_user -> getInterests();
		$data['interests'] = ($interestsResult === false) ? '' : $interestsResult;
		$data['descriptive_word'] = $this -> model_user -> get_descriptive_word($this->language_id);
		if ($postData = $this -> input -> post()) {
			
			$this -> model_user -> clear_data($this->user_id, 'user_descriptive_word');
			$this->model_user -> insert_user_want_descriptive_word($this->user_id,$postData['descriptive_word_id'],'user_descriptive_word');
			
			$this -> model_user -> clear_data($this->user_id, 'user_interest');
			$this->model_user -> insert_user_interest($this->user_id,$postData['interests'],$this->language_id);
			
			$looking_for = array();
			if($user_want_relationship_type = $this -> datetix -> user_want($this->user_id, "relationship_type"))
			{
				foreach($user_want_relationship_type as $val)
				{
					$looking_for[] = $val['relationship_type_id'];
				}
			}
			$critearea_for_skipp_step2 = array('1','2','3');
			
			$update_data['completed_application_step'] = "5";			
			if(!array_intersect($looking_for,$critearea_for_skipp_step2))
			{
				$update_data['completed_application_step'] = "6";
			}
			$this->model_user->update_user($this->user_id,$update_data);
			if($this -> session -> userdata('is_return_apply'))
			{
				$this -> session -> unset_userdata('is_return_apply');
			}
			$this -> model_user -> is_current_signup_process($this->user_id);
		}
				
		//Checking Event_ticket in session
		if($data['event_info'] = $this->_is_event_rsvp())
		{
			$data['page_title'] = translate_phrase('RSVP for ').$data['event_info']['event_name'].(' on ').date(DATE_FORMATE,strtotime($data['event_info']['event_start_time']));
		}
		else
		{
			$data['page_title'] = translate_phrase('Apply for ').get_assets('name','DateTix').translate_phrase(' Membership');
		}
		
		$data['page_name'] = 'user/signup_step/step5';
		$this -> load -> view('template/default', $data);
	}
	
	/**
	 * Step 6: 2 a
	 * @access public 
	 * @return view[signup_step/step2] 
	 * @author Rajnish Savaliya
	 */
	public function step6()
	{
		$default_ideal_filters = DEFAULT_IDEAL_FILTERS_ID;
		$city = $this -> model_user -> get_city_by_id($this->city_id);
		$current_country = $this -> model_user -> getCountryByCity($this->city_id);
		$country_id = $current_country ? $current_country -> country_id : '0';
		
		$data['importance'] = $this -> model_user -> select_importance($this->language_id);
		$data['feet'] = $this -> model_user -> get_feet();
		/*---------------------change by Hannan Munshi------------------*/
		for ($i = 145; $i <= 200; $i++) {
			$data['centemeters'][$i] = $i;
		}
		
		$data['inches'] = $this -> model_user -> get_inches();		
		$data['ethnicity'] = $this -> model_user -> get_ethnicity($this->language_id, $country_id);
		$data['descriptive_word'] = $this -> model_user -> get_descriptive_word($this->language_id);
		$data['education_level'] = $this -> model_user -> get_education_level($this->language_id);
		$data['industry'] = $this -> model_user -> get_industries($this->language_id);
		
		$data['year'] = array();
		$yearsTo = 99;
		$yearsFrom = 18;
		for ($i = $yearsFrom; $i <= $yearsTo; $i++) {
			$data['year'][$i] = $i;
		}

		$data['use_meters'] = '';
		if ($country_id) {
			$data['use_meters'] = $this -> general_model -> getSingleValue('country', 'use_meters', array('country_id' => $country_id, 'display_language_id' => $this->language_id));
		}
		
		
		if ($postData = $this -> input -> post()) {
			
			$this -> model_user -> insert_filters($this->user_id, $default_ideal_filters);
			if($data['use_meters'] == 0 && $data['use_meters'] !== FALSE)
			{
				#height range lower
				$feet              = $postData['feetFrom'];
				$height_lower      = NULL;
				if($postData['feetFrom']!="" && $postData['inchFrom']!="")
				{
					$inch_to_feet = $postData['inchFrom'] * 0.083333;
					$feet         = $feet+$inch_to_feet;
				}
				if( $postData['feetFrom']!=""){
					$cm           = $feet * 30.48;
					$height_lower = round($cm);
				}
				
				#height range upper
				$feet_higher       = $postData['feetTo'];
				$height_higher     = NULL;
				if($postData['feetTo']!="" && $postData['inchTo']!="")
				{
					$inch_to_feet = $postData['inchTo']* 0.083333;
					$feet_higher  = $feet_higher+$inch_to_feet;
				}
				if($postData['feetTo']!=""){
					$cm           = $feet_higher * 30.48;
					$height_higher = round($cm);
				}
			}
			else
			{
				$height_higher = $this->input->post('centemetersTo');
				$height_lower  = $this->input->post('centemetersFrom');
			}
			/*
			 
			 |---------------------------------------------------------------------------------------------------
			 * 
			 |        NO NEED Following CODE Because we have already used $postData for getting form value 
			 * 
			 |---------------------------------------------------------------------------------------------------
			 * 
			$want_age_lower              = $this->input->post('ageRangeLowerLimit')   ?$this->input->post('ageRangeLowerLimit'):NULL;
			$want_age_upper              = $this->input->post('ageRangeUpperLimit')   ?$this->input->post('ageRangeUpperLimit'):NULL;

			$want_age_range_importance            = $this->input->post('wantAgeRangeImportance')                  ?$this->input->post('wantAgeRangeImportance'):NULL;
			$want_height_range_importance         = $this->input->post('wantHeightImportance')                    ?$this->input->post('wantHeightImportance'):NULL;
			
			$want_ethnicity_importance            = $this->input->post('wantEthnicityImportance')                 ?$this->input->post('wantEthnicityImportance'):NULL;
			$want_personality_importance          = $this->input->post('wantPersonalityImportance')               ?$this->input->post('wantPersonalityImportance'):NULL;
			$want_education_level_importance      = $this->input->post('wantEducationImportance')                 ?$this->input->post('wantEducationImportance'):NULL;
			
			//removed HTML by micheal [No need for this filter]
			$want_industry_importance             = $this->input->post('wantParticularIndustryImportance')        ?$this->input->post('wantParticularIndustryImportance'):NULL;
			
			$not_want_to_date                     = $this->input->post('not_want_to_date')                        ?$this->input->post('not_want_to_date'):NULL;
			$ideal_date                           = $this->input->post('ideal_date')                              ?$this->input->post('ideal_date'):NULL;
			$looking_for_importance               = $this->input->post('wantRelationshipGoalImportance')          ?$this->input->post('wantRelationshipGoalImportance') : NULL;
			
			$ageRangeLowerLimit = $this->input->post('ageRangeLowerLimit');
			$ageRangeUpperLimit = $this->input->post('ageRangeUpperLimit' );
			 *  
			 */ 

			
			$update_data['want_age_range_lower'] = $postData['ageRangeLowerLimit'];
			$update_data['want_age_range_upper'] = $postData['ageRangeUpperLimit'];		
			$update_data['want_age_range_importance'] = $postData['wantAgeRangeImportance'];
			
			$update_data['want_height_range_lower'] = $height_lower;
			$update_data['want_height_range_upper'] = $height_higher;
			$update_data['want_height_range_importance'] = $postData['wantHeightImportance'];
			
			$update_data['want_ethnicity_importance'] = $postData['wantEthnicityImportance'];
			$update_data['want_personality_importance'] = $postData['wantPersonalityImportance'];
			$update_data['want_education_level_importance'] = $postData['wantEducationImportance'];
			
			//HTML markup is commented by micheal 
			//$update_data['want_industry_importance'] = $postData['wantParticularIndustryImportance'];
			$update_data['completed_application_step'] = "6";
			
			//echo "<pre>"; print_r($update_data);exit;
			
			$this->model_user->update_user($this->user_id,$update_data);
			
			$this->model_user->genericInsert($this->user_id,'ethnicity',$postData['ethnicityPreference']);
			$this->model_user->genericInsert($this->user_id,'descriptive_word',$postData['personalityPreference']);			
			$this->model_user->genericInsert($this->user_id,'education_level',$postData['educationPreference']);
			
			//HTML markup is commented by micheal So no need this code because no form data is submit in db.
			//$this->model_user->genericInsert($this->user_id,'body_type',$postData['bodyTypePreference']);
			//$this->model_user->genericInsert($this->user_id,'industry',$postData['industryPreference']);
			
			if($this -> session -> userdata('is_return_apply'))
			{
				$this -> session -> unset_userdata('is_return_apply');
			}
			$this -> model_user -> is_current_signup_process($this->user_id);
		}
		
		$this -> general_model -> set_table('filter');
		$default_filters = "filter_id IN (".$default_ideal_filters.") AND language_id = ".$this->language_id." ORDER BY view_order asc";
		$data['filters'] = $this -> general_model -> custom_get('*',$default_filters);
		
		
		//Checking Event_ticket in session
		if($data['event_info'] = $this->_is_event_rsvp())
		{
			$data['page_title'] = translate_phrase('RSVP for ').$data['event_info']['event_name'].(' on ').date(DATE_FORMATE,strtotime($data['event_info']['event_start_time']));
		}
		else
		{
			$data['page_title'] = translate_phrase('Apply for ').get_assets('name','DateTix').translate_phrase(' Membership');
		}
		
		$data['page_name'] = 'user/signup_step/step6';
		$this -> load -> view('template/default', $data);
	}
	
	/**
	 * Step 7: step 2 b [Contact Information] 
	 * @access public 
	 * @return view[signup_step/step2] 
	 * @author Rajnish Savaliya
	 */
	public function step7()
	{
		//Get City id from user records
		if($city_id = $this -> model_user -> get_city_by_user_id($this->user_id))
		{
			$this->city_id = $city_id;
		}
		
		$current_country = $this -> model_user -> getCountryByCity($this->city_id);
		$data['country_code'] = $current_country ? $current_country -> country_code : " ";
				
		$heared_abou_us_list = $this -> model_user -> get_heared_abou_us($this->language_id, $this->city_id);		
		foreach ($heared_abou_us_list as $key => $value) {
			$data['heardAboutUsList'][$value['how_you_heard_about_us_id']] = $value['description'];
		}
		
		$data['mobile_verified'] = $this -> model_user -> is_mobile_verified($this->user_id);
		
		if ($postData = $this -> input -> post()) {
			
			if ($postData['heared_abou_us'])
				$data['hear_about_place_holder'] = $this -> model_user -> is_hear_about_us_placeholder_exist($postData['heared_abou_us']);
			
			$update_data['how_you_heard_about_us_id'] = $postData['heared_abou_us'];			 
			$update_data['mobile_phone_number'] = $postData['mobile_phone_number'];
			$update_data['how_you_heard_about_us_other'] = $postData['heard_about_us_other'];
                        //added by jigar oza
                        $update_data['external_intros'] = $postData['external_intros'];
                        $update_data['promo_code'] = $postData['promo_code'];
			$update_data['completed_application_step'] = "5";
			
			$this->model_user->update_user($this->user_id,$update_data);
			
			$user_friends_with_datetix = $this -> model_user -> get_fb_friends_with_datetix($this->user_id);
			if ($user_friends_with_datetix) {
				foreach ($user_friends_with_datetix as $friend) {
					$this -> general -> set_table('user');
					$friend_data = $this -> general -> get("user_id,first_name,password", array('user_id' => $friend['user_id']));

					$mutual_friend_on_datetix = $this -> model_user -> get_fb_friends_with_datetix($friend_data['0']['user_id']);

					$subject = translate_phrase('One of your Facebook friends has just applied to ').get_assets('name','DateTix');
					if($user_email_data = $this -> model_user -> get_user_email($friend['user_id']))
					{
						$data['email_content'] = translate_phrase('One of your Facebook friends just applied to ').get_assets('name','DateTix').translate_phrase('. You now have ') . count($mutual_friend_on_datetix) . ' ' . translate_phrase('Facebook friends on DateTix who can introduce you to their friends for free!') . '</p>
						<p class="lead">' . translate_phrase('Invite more of your friends to apply to ').get_assets('name','DateTix').translate_phrase(' to expand your own dating pool!');

						$user_link = $this -> utility -> encode($friend_data['0']['user_id']);

						if ($friend_data['0']['password']) {
							$user_link .= '/' . $friend_data['0']['password'];
						}
						$data['btn_link'] = base_url() . 'home/mail_action/' . $user_link . '?return_to=' . base_url() . url_city_name() . '/invite-friends.html';
						$data['btn_text'] = translate_phrase('Invite More Friends');

						$data['email_title'] = ' Hi ' . $friend_data['0']['first_name'] . ',';
						$email_template = $this -> load -> view('email/common', $data, true);
						//$this -> datetix -> mail_to_user($user_email_data['email_address'], $subject, $email_template);
					}
				}
			}
			
			$this -> model_user -> is_current_signup_process($this->user_id);
		}
		//Checking Event_ticket in session
		if($data['event_info'] = $this->_is_event_rsvp())
		{
			$data['page_title'] = translate_phrase('RSVP for ').$data['event_info']['event_name'].(' on ').date(DATE_FORMATE,strtotime($data['event_info']['event_start_time']));
		}
		else
		{
			$data['page_title'] = translate_phrase('Apply for ').get_assets('name','DateTix').translate_phrase(' Membership');
		}
		
		$data['page_name'] = 'user/signup_step/step7';
		$this -> load -> view('template/default', $data);
	}
	
	
	/**
	 * [Helper Function] Check session event_ticket_id and determine wheter user from ticket rsvp or normal.
	 * @access private [not used directly] 
	 * @return event inforamtion [ if exist] 
	 * @author Rajnish Savaliya
	 */
	private function _is_event_rsvp()
	{
		if($event_ticket_id = $this->session->userdata('event_ticket_id'))
		{
			$fields = array('e.*');
			$from = 'event_ticket as rsvp';
			$joins = array(
					'event_order as ordr' => array('rsvp.event_order_id = ordr.event_order_id', 'inner'), 
					'event as e' => array('e.event_id = ordr.event_id', 'inner'), 
					);
			$where['rsvp.event_ticket_id'] = $event_ticket_id;
			$where['e.display_language_id'] = $this -> session -> userdata('sess_language_id');
			if($data['event_info'] = $this -> model_user -> multijoins($fields, $from, $joins, $where))
			{
				return $data['event_info']['0'];		
			}			
		}	
			
	}
}
?>
