<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_home extends CI_Model {
	public function get_datetix_members($language_id){
		$this->db->select('gender.description');
		$this->db->join('gender','gender.gender_id=user.gender_id');
		$this->db->where('user.is_featured','1');
		$this->db->where('gender.display_language_id',$language_id);
		$this->db->get('user');
	}

	public function landingPageData($language_id)
	{
		$this->load->model('model_landing_page');

		$landing_page_id      = ($this->input->get('landing_page_id')) ? $this->input->get('landing_page_id') : 1 ;
		$landing_page         = $this->model_landing_page->geLandingPage($landing_page_id, $language_id);
		$landing_page_message = $this->model_landing_page->getLandingPageMessage($landing_page_id, $language_id);

		return array(
            'banner_message'     => !(is_null($landing_page_message)) ? $landing_page_message->main_message : '',
            'banner_image'       => !(is_null($landing_page)) ? $landing_page->background_image_url : ''
            );
	}

	/**
	 * Active city list to display on change city popup
	 * @param  int $language_id
	 * @return array key/value pair of country name and city data
	 */
	public function getChangeCityData($language_id)
	{
		$sql = "SELECT `country`.`country_id`,`country`.`description`,`flag_url`
                    FROM `country`
                    WHERE `country`.`display_language_id` = '$language_id'
                GROUP BY country.country_id ORDER BY `country`.`view_order` ASC";
		$countries = $this->db->query($sql)->result();
		//echo "<pre>";print_r($countries);exit;
		$data = array();
		foreach ($countries as $country) {
			$sql = "SELECT `city`.`city_id`,`city`.`description`,`city`.`view_order`
                        FROM `city`
                        INNER JOIN `province` ON `province`.`province_id` = `city`.`province_id`
                        WHERE `province`.`display_language_id` = ".$language_id."
                        AND `city`.`is_active` = 1 
                        AND `city`.`display_language_id` = ".$language_id." 
                        AND `province`.`country_id` = ".$country->country_id."
						ORDER BY `city`.`view_order` ASC ";
			$q = $this->db->query($sql);

			if ($q->num_rows() > 0) {
				$data[$country->description]['cityData'] = $q->result();
				$data[$country->description]['flagUrl'] = $country->flag_url;
			}
		}
		//echo "<pre>";print_r($data);exit;
		return $data;
	}

	public function redirect_to_city($city_id, $return_to = '')
	{
		$this->load->model('model_city');
		$city = $this->model_city->get($city_id, 1);

		//if return to is empty then redirect to index.html
		if (!$return_to) {
			$return_to = '';
			//$return_to = 'event.html';
		}

		//if city details are not retrieved from database then set hongkong as default
		if (is_null($city)) {
			$url_path = "/hongkong/$return_to";
			$this->session->set_userdata('sess_city_id', $this->config->item('default_city'));
			$this->session->set_userdata('backgroundImageUrl','');
		}
		//if user came to this page from any 1a apply page then return user to that page again.
		else {
			$url_path = "/" . url_city_name($city->description) . "/$return_to";
			$this->session->set_userdata('sess_city_id', $city->city_id);


			$this->load->model('general_model');
			$this->general_model->set_table('landing_page');
			$where = array('city_id'=>$city->city_id);
			$landingPageDetails = $this->general_model->get('',$where);

			$imageURL = isset($landingPageDetails[0]['background_image_url']) ? $landingPageDetails[0]['background_image_url'] : '';
			$this->session->set_userdata('backgroundImageUrl',$imageURL);
		}
		
		redirect(base_url($url_path));
	}

	public function geoip_redirect()
	{
		if (!$this->uri->uri_string() || $this->uri->uri_string() == 'home') 
		{
			$this->load->library('GeoPlugin');
			$this->load->model('model_city');			
			$this->geoplugin->locate();
			if($this->geoplugin->city == 'New York')
			{
				$this->geoplugin->city = 'nyc';	
			}
			$city = $this->model_city->getByName($this->geoplugin->city);
			
			if(!$city)
			{	
				$city = new stdClass;
				$city->city_id = 0;
			}
			$this->redirect_to_city($city->city_id);
		}

	}

	public function completed_application_step($email){
		$this->db->select('user.user_id,user.completed_application_step');
		$this->db->join('user_email','user.user_id=user_email.user_id');
		$this->db->where('is_verified','1');
		$this->db->where('user_email.email_address',$email);
		$q  = $this->db->get('user');
		return ($q->num_rows() > 0) ? $q->row() : NULL ;
	}

	/**
	 * Selected datetix members
	 * @param  int $tab tab 1 - by profesion, 2 -  by school
	 * @param  int $language_id current display language id
	 * @return array $members
	 */
	public function selected_members ($tab, $language_id) {
		 
		$this->load->model('model_landing_page');

		$landing_page_id = ($this->input->get('landing_page_id')) ? $this->input->get('landing_page_id') : 1 ;
		$landing_page    = $this->model_landing_page->geLandingPage($landing_page_id, $language_id);

		$where = array();
		$where['u.approved_date IS NOT NULL'] = NULL;
		$where['u.is_featured'] = 1;

		if (isset($landing_page->city_id) && $landing_page->city_id) {
			// $where['u.current_city_id'] = $landing_page->city_id;
			$where['u.current_city_id'] = current_city_id();
		}

		if (isset($landing_page->gender_id) && $landing_page->gender_id) {
			// $where['u.gender_id'] = $landing_page->gender_id;
		}

		if (isset($landing_page->ethnicity_id) && $landing_page->ethnicity_id) {
			// $where['u.ethnicity_id'] = $landing_page->ethnicity_id;
		}

		if (isset($landing_page->career_stage_id) && $landing_page->career_stage_id) {
			// $where['u.career_stage_id'] = $landing_page->career_stage_id;
		}

		if (isset($landing_page->age_range_lower) && $landing_page->age_range_lower) {

		}

		if (isset($landing_page->age_range_upper) && $landing_page->age_range_upper) {

		}

		$selected_members = array();

		if ($tab == 1) {
			// by profession

			$this->db->select('i.industry_id,i.description, COUNT(uc.user_company_id) AS top');
			$this->db->join('industry i', 'i.industry_id = uc.industry_id', 'inner');
			$this->db->join('user u', 'u.user_id = uc.user_id', 'inner');
			$this->db->where($where);
			$this->db->group_by('uc.industry_id');
			$this->db->order_by('top', 'desc');
			$q = $this->db->get('user_company uc', 18);


			foreach ($q->result_array() as $industry) {
				$where['g.display_language_id'] = $language_id;
				$where['uc.industry_id']        = $industry['industry_id'];

				// get members
				$this->db->order_by('uc.user_company_id', 'desc');
				$this->db->order_by('', 'random');
				$this->db->group_by('u.user_id');
				$this->db->where($where);
				$this->db->join('user_company uc', 'uc.user_id = u.user_id', 'inner');
				$this->db->join('gender g', 'g.gender_id = u.gender_id', 'inner');
				$this->db->join('company c', "c.company_id = uc.company_id AND c.display_language_id = $language_id/* AND c.is_featured = 1*/", 'left');
				$this->db->select('
                    u.`birth_date`,
                    u.gender_id,
                    g.description AS `gender`,
                    uc.job_title,
                    CASE WHEN uc.company_id IS NULL THEN uc.company_name ELSE c.company_name END AS `company`
                ', FALSE);
				$members = $this->db->get('user u', 3);


				// get logos
				$this->db->where(array(
                    'uc.industry_id'        => $industry['industry_id'],
                    'c.display_language_id' => $language_id,
				// 'c.is_featured'         => 1
				));
				$this->db->join('(SELECT company_id, industry_id FROM user_company GROUP BY user_id,company_id) uc', 'uc.company_id = c.company_id', 'inner');
				$this->db->select('c.logo_url,c.company_name, COUNT(c.company_id) AS top', FALSE);
				$this->db->group_by('c.company_id');
				$this->db->order_by('top', 'desc');
				$this->db->order_by('company_name', 'asc');
				$logos = $this->db->get('company c', 4);

				if ($members->num_rows()) {
					$selected_members[] = array(
                        'industry' => $industry['description'],
                        'members'  => $members->result_array(),
                        'logos'    => $logos->result_array()
					);
				}
			}

		} else {
			// by school

			$selected_members = array(
                'international' => array(),
                'local'         => array()
			);

			/* get local schools  */
			$this->db->select("s.school_id, s.school_name, s.logo_url, COUNT(DISTINCT(us.user_id)) AS `top`, '1' AS `local`", FALSE);
			$this->db->from('user_school us');
			$this->db->join("school s", "s.school_id = us.school_id");
			$this->db->join("user u", "u.user_id = us.user_id");
			$this->db->where(array_merge(array("s.city_id" => current_city_id(), "s.display_language_id" => $language_id), $where));
			$this->db->group_by("us.school_id");
			$this->db->order_by('top', 'DESC');
			$this->db->order_by('school_name');
			$this->db->limit(9);
			$school_local = $this->db->get()->result_array();

			/* get international schools  */
			$this->db->select("s.school_id, s.school_name, s.logo_url, COUNT(DISTINCT(us.user_id)) AS `top`, '2' AS `local`", FALSE);
			$this->db->from('user_school us');
			$this->db->join("school s", "s.school_id = us.school_id");
			$this->db->join("user u", "u.user_id = us.user_id");
			$this->db->where(array_merge(array("s.city_id <>" => current_city_id(), "s.display_language_id" => $language_id), $where));
			$this->db->group_by("us.school_id");
			$this->db->order_by('top', 'DESC');
			$this->db->order_by('school_name');
			$this->db->limit(9);
			$school_international = $this->db->get()->result_array();

			foreach (array_merge($school_local, $school_international) as $school) {
				$where['g.display_language_id'] = $language_id;
				$where['us.school_id']          = $school['school_id'];

				// get members
				$this->db->join('user_school us', 'u.user_id = us.user_id', 'inner');
				$this->db->join('gender g', 'g.gender_id = u.gender_id', 'inner');
				$this->db->join('user_company uc', 'uc.user_id = u.user_id', 'left');
				$this->db->join('company c', "c.company_id = uc.company_id AND c.display_language_id = $language_id /*AND c.is_featured = 1*/", 'left');
				$this->db->join('school s', "s.school_id = us.school_id AND s.display_language_id = $language_id /*AND s.is_featured = 1*/", 'inner');
				$this->db->where($where);
				$this->db->group_by('us.user_id');
				$this->db->order_by('u.user_id', 'random');
				$this->db->select("
                    u.`birth_date`,
                    u.`gender_id`, g.`description` AS `gender`,
                    CASE
                        WHEN uc.`user_company_id` IS NOT NULL THEN uc.`job_title`
                        ELSE us.`degree_name`
                    END AS `text_1`,
                    CASE
                        WHEN uc.`company_id` IS NOT NULL THEN c.`company_name`
                        WHEN uc.`company_name` IS NOT NULL OR uc.`company_name` <> '' THEN uc.`company_name`
                        WHEN us.`school_id` IS NOT NULL THEN s.`school_name`
                        ELSE us.`school_name`
                    END AS `text_2`
                ", FALSE);
				$members = $this->db->get('user u', 3);

				if ($members->num_rows()) {
					if ($school['local'] == 1) {
						// local
						$selected_members['local'][] = array(
                            'school' => array(
                                'name' => $school['school_name'],
                                'logo' => $school['logo_url']
						),
                            'members' => $members->result_array()
						);
					} else {
						// international
						$selected_members['international'][] = array(
                            'school' => array(
                                'name' => $school['school_name'],
                                'logo' => $school['logo_url']
						),
                            'members' => $members->result_array()
						);
					}
				}
			}
		}

		return $selected_members;
	}
	public function check_email_exist($email){
		$this->db->where('email_address',$email);
		$result = $this->db->count_all_results('user_email');
		return $result;
	}


        /**
         *  get country list through domain and language
         */
        public function get_country_by_doamin($language_id){
            
                $domain=explode('.',$_SERVER['SERVER_NAME']);
                $selectedDomain=@$domain[1];  
                if($selectedDomain=='hk'){
                    $name='Hong Kong';
                }elseif ($selectedDomain=='sg') {
                    $name='Singapore';
                }elseif ($selectedDomain=='au') {
                    $name='Australia';
                }elseif ($selectedDomain=='uk') {
                    $name='United Kingdom';
                }elseif ($selectedDomain=='ca') {
                    $name='Canada';
                }elseif ($selectedDomain=='jp') {
                    $name='Japan';
                }elseif ($selectedDomain=='kr') {
                    $name='Korea';
                }else{
                    $name='';
                }
                $this->db->select('country.country_id,country.description,country.is_active');		
		$this->db->where('country.display_language_id',$language_id);
                $this->db->where('country.is_active','1');
                $this->db->where('country.description',$name);		
		$result = $this->db->get('country');
                
                if($result->num_rows() > 0){
                    $result=$result->result_array();
                    return $result[0]['country_id'];
                }else{
                    return '0';
                }
		
	}
}
