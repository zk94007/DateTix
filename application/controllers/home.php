<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Home extends MY_Controller {

	//Used tables
	public $tblcountry = 'country';
	public $tblprovince = 'province';
	public $tblschool = 'school';
	public $tblcity = 'city';
	public $tbluserschool = 'user_school';
	public $tbluser = 'user';
	public $tblgender = 'gender';
	public $tbluser_company = 'user_company';
	public $tblcompany = 'company';
	public $tblindustry = 'industry';
	public $tbluser_email = 'user_email';
	public $tbluserjob = 'user_job';
	public $tbljob_function = 'job_function';

	public function __construct() {
		parent::__construct();
		$this -> load -> library('form_validation');

		$this -> load -> model('model_user');
		$this -> load -> model('model_home');
		$this -> load -> model('model_city');
		$this -> load -> model("general_model", 'general');
		$this -> load -> model("model_landing_page", 'landingpage');
	}

	/**
	 * Landing Page.Get School and professional data
	 * @access public
	 * @return Landing Page.
	 * @author Rajnish
	 */

	public function index() {

		$this -> model_home -> geoip_redirect();

		$language_id = $this -> session -> userdata('sess_language_id');
		$this -> session -> unset_userdata('sign_up_id');
		$this -> session -> unset_userdata('succ_email_verify');
		$this -> session -> unset_userdata('fb_user_data');
		$this -> session -> unset_userdata('is_return_apply');
		$data = $this -> model_home -> landingPageData($language_id);

		if ($data['invite_id'] = $this -> input -> get('invite_id')) {
			$this -> session -> set_userdata('invite_id', $data['invite_id']);
		}

		$data['page_title'] = 'Homepage';
		$data['page_name'] = 'home/index';

		/* Get Landing Page Details */
		$landing = $this -> input -> get('landing_page_id');
		$landing_page_id = ($landing) ? $landing : 1;
		$landing_page = $this -> landingpage -> geLandingPage($landing_page_id, $language_id);

		/*Get Country Data by City */
		$fields = array('con.country_id');
		$from = $this -> tblcity . ' as c';

		$joins = array($this -> tblprovince . ' as pro' => array('pro.province_id = c.province_id', 'INNER'), $this -> tblcountry . ' as con' => array('con.country_id = pro.country_id', 'INNER'));
		$contry_condition['c.display_language_id'] = $language_id;
		$contry_condition['con.display_language_id'] = $language_id;
		$contry_condition['pro.display_language_id'] = $language_id;
		$contry_condition['c.city_id'] = current_city_id();

		$ordersby = 'c.city_id asc';

		$country_datas = $this -> general -> multijoins($fields, $from, $joins, $contry_condition, $ordersby, 'array');

		$country_id = isset($country_datas[0]['country_id']) ? $country_datas[0]['country_id'] : '';

		/*Get school information  */

		$this -> general -> set_table($this -> tblcity);
		$fields = array('sc.school_id', 'sc.school_name', 'sc.logo_url', 'sc.city_id AS city_id', 'ct.description AS city_name', 'con.country_id', 'con.description AS country_name');
		/* $fields = array('sc.school_id','sc.school_name','sc.logo_url','sc.city_id AS city_id',
		 'ct.description AS city_name');*/
		$from = $this -> tblschool . ' as sc';

		//CLIENT's Comment :I don't think there is a need to SELECT country_name? Please remove the JOINs to the province and country tables.
		$joins = array($this -> tblcity . ' as ct' => array('ct.city_id = sc.city_id', 'INNER'), $this -> tblprovince . ' as pro' => array('pro.province_id = ct.province_id', 'INNER'), $this -> tblcountry . ' as con' => array('con.country_id = pro.country_id', 'INNER'), $this -> tbluserschool . ' as us' => array('us.school_id = sc.school_id', 'INNER'), $this -> tbluser . ' as u' => array('u.user_id = us.user_id', 'INNER'), $this -> tbluserjob . ' as uj' => array('uj.user_id = us.user_id', 'INNER'), $this -> tblindustry . ' as ind' => array('ind.industry_id = uj.industry_id', 'INNER'), $this -> tblgender . ' as g' => array('g.gender_id = u.gender_id', 'INNER'));
		$condition['ind.display_language_id'] = $language_id;
		$condition['sc.display_language_id'] = $language_id;
		$condition['ct.display_language_id'] = $language_id;
		$condition['pro.display_language_id'] = $language_id;
		$condition['con.display_language_id'] = $language_id;
		$condition['u.is_featured'] = 1;
		$condition['u.approved_date IS NOT NULL'] = NULL;
		$condition['u.current_city_id'] = current_city_id();

		$condition['sc.is_active'] = 1;
		$condition['sc.is_featured'] = 1;

		$ordersby = 'sc.school_name asc';
		$school_datas = $this -> general -> multijoins($fields, $from, $joins, $condition, $ordersby, 'array', $likes = NULL, $num = NULL, $offset = NULL, $wheretype = 'where', $groupby = 'sc.school_id');
		$school_results = array();

		unset($condition);
		unset($fields);
		unset($from);
		unset($joins);
		foreach ($school_datas as $key => $school) {
			$fields = array('u.user_id', 'CONCAT(u.first_name," ",u.last_name) AS user_name', 'u.current_city_id', 'u.gender_id', 'birth_date', 'CASE
											WHEN
												birth_date != "0000-00-00" 	
											THEN 
												TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
											END as age', 'g.description AS gender', 'us.degree_name AS degree', 'uj.job_title', 'ind.description as industry');
			$from = $this -> tbluserschool . ' as us';
			$joins = array($this -> tbluser . ' as u' => array('u.user_id = us.user_id', 'INNER'), $this -> tblgender . ' as g' => array('g.gender_id = u.gender_id', 'INNER'), $this -> tbluserjob . ' as uj' => array('uj.user_id = us.user_id', 'INNER'), $this -> tblindustry . ' as ind' => array('ind.industry_id = uj.industry_id', 'INNER'));

			$condition['ind.display_language_id'] = $language_id;
			$condition['us.school_id'] = $school['school_id'];

			$condition['u.is_featured'] = 1;
			$condition['u.approved_date IS NOT NULL'] = NULL;
			$condition['u.current_city_id'] = current_city_id();

			//$condition['uj.is_featured'] = 1;

			$condition['g.display_language_id'] = $language_id;
			$ordersby = 'u.applied_date DESC';
			$tmp_students = $this -> general -> multijoins($fields, $from, $joins, $condition, $ordersby, 'array', NULL, 3, NULL, $wheretype = 'where', $groupby = 'u.user_id');
			//$tmp_students;
			$tmp_array = array();
			$tmp_array['info'] = $school;
			$tmp_array['students'] = $tmp_students;
			if ($country_id != $school['country_id']) {
				$school_results['international'][] = $tmp_array;
			} else {
				$school_results['local'][] = $tmp_array;
			}
			unset($tmp_array);
		}
		$data['school_datas'] = $school_results;
		$fields = array('i.*');
		$from = $this -> tblindustry . ' as i';

		$joins = array($this -> tblcompany . ' as c' => array('c.industry_id = i.industry_id ', 'INNER'), $this -> tbluserjob . ' as uj' => array('uj.company_id = c.company_id', 'INNER'), $this -> tbluser . ' as u' => array('u.user_id = uj.user_id', 'INNER'), $this -> tblgender . ' as g' => array('g.gender_id = u.gender_id', 'INNER'), $this -> tbluserschool . ' as us' => array('us.user_id = u.user_id', 'INNER'), $this -> tblschool . ' as sc' => array('us.school_id = sc.school_id', 'INNER'));
		$cond['i.display_language_id'] = $language_id;
		$cond['c.display_language_id'] = $language_id;
		$cond['u.is_featured'] = '1';
		$cond['u.approved_date IS NOT NULL'] = NULL;
		$cond['u.current_city_id'] = current_city_id();
		$cond['uj.is_featured'] = 1;
		$cond['c.is_active'] = 1;
		$cond['c.is_featured'] = 1;
		$cond['g.display_language_id'] = $language_id;
		// $company_cond['jf.display_language_id'] = $language_id;
		//$cond['el.display_language_id'] = $language_id;
		$cond['sc.display_language_id'] = $language_id;

		$ordersby = 'uj.user_company_id asc';
		//$industry_datails  = $this->general->multijoins_groupby($fields,$from,$joins,$cond,$ordersby,'array','i.industry_id');
		$industry_datails = $this -> general -> multijoins($fields, $from, $joins, $cond, $ordersby, 'array', NULL, NULL, NULL, $wheretype = 'where', $groupby = 'i.industry_id');

		$proffesional_data = array();
		if ($industry_datails) {
			unset($cond);
			foreach ($industry_datails as $cnt => $industry) {
				$proffesional_data[$cnt] = $industry;

				$fields = array('c.*');
				$from = $this -> tblcompany . ' as c';

				$joins = array($this -> tbluserjob . ' as uj' => array('uj.company_id = c.company_id', 'INNER'), $this -> tbluser . ' as u' => array('u.user_id = uj.user_id', 'INNER'));
				$cond['c.display_language_id'] = $language_id;
				//$cond['uj.job_function_id !='] = '';
				$cond['u.is_featured'] = '1';
				$cond['u.approved_date IS NOT NULL'] = NULL;
				$cond['u.current_city_id'] = current_city_id();
				$cond['uj.is_featured'] = 1;
				$cond['c.is_active'] = 1;
				$cond['c.industry_id'] = $industry['industry_id'];
				$cond['c.is_featured'] = 1;
				$ordersby = 'c.company_id asc';
				$proffesional_data[$cnt]['company_datails'] = $this -> general -> multijoins_groupby($fields, $from, $joins, $cond, $ordersby, 'array', 'c.company_id');
				if ($proffesional_data[$cnt]['company_datails']) {
					//unset($cond);
					foreach ($proffesional_data[$cnt]['company_datails'] AS $key => $companies_datas) {
						$company_ids[] = $companies_datas['company_id'];
					}

					$sql = '
							SELECT u.user_id, uj.job_title, 
							CONCAT(u.first_name," ",u.last_name) AS user_name, birth_date, 
							CASE
 							WHEN  birth_date != "0000-00-00" 
							THEN TIMESTAMPDIFF(YEAR,`birth_date`,CURDATE())
 							END as age, 
 							g.gender_id, g.description AS gender, 
 							sc.school_id, sc.school_name, sc.logo_url
							
 							FROM (user as u)
							
							INNER JOIN user_job as uj ON uj.user_id = u.user_id
							INNER JOIN gender as g ON g.gender_id = u.gender_id
							INNER JOIN user_school as us ON us.user_id = u.user_id
							INNER JOIN school as sc ON us.school_id = sc.school_id
							WHERE u.is_featured =  "1"
							AND u.approved_date IS NOT NULL
							AND u.current_city_id =  "' . current_city_id() . '"
							AND uj.is_featured =  "1"';
					if ($company_ids) {
						$sql .= ' AND uj.company_id IN (' . implode(',', $company_ids) . ') ';
						unset($company_ids);
					}
					$sql .= 'AND g.display_language_id =  "' . $language_id . '"
							GROUP BY u.user_id ORDER BY u.applied_date DESC LIMIT 3';
					$proffesional_data[$cnt]['employees'] = $this -> general -> sql_query($sql);

				}
			}//end foreach industry_details
		}
		/* End Member Details */
		$data['proffessional_data'] = $proffesional_data;
		$this -> load -> view('template/default', $data);

	}

	/**
	 * Load Landing Page based on landing_page table
	 * @access public
	 * @return
	 * @author by Rajnish
	 */
	public function load_landing_page($short_url = "") {

		//$this -> model_home -> geoip_redirect();
		$this -> general -> set_table('landing_page');
		$landing_page_condition['shortcut_url'] = $short_url;
		$language_id = $this -> session -> userdata('sess_language_id');
		
		//if($short_url == "" && $language_id!="")
		if ($language_id != "") {
			$landing_page_condition['display_language_id'] = $language_id;
		}

		$domain_name = preg_replace('#^www\.(.+\.)#i', '$1', $_SERVER['SERVER_NAME']);
		//$domain_name = $_SERVER['SERVER_NAME'];
		$website_data = array();
		$website_id = 0;
		if ($query = $this -> db -> get_where("website", array('domain' => $domain_name))) {
			$website_data = $query -> result_array();
			if ($website_data) {
				$website_id = $website_data['0']['website_id'];
				$landing_page_condition['website_id'] = $website_id;
			}
			//$landing_page_condition =  "website_id REGEXP '[[:<:]]".$website_id."[[:>:]]'";			// [Website landing page - off]
		}
		//echo $website_id.'ffff';exit;
		if ($landing_page_data = $this -> general -> get("", $landing_page_condition)) {
			//echo $this->db->last_query();

			$landing_page_data = $landing_page_data['0'];
			$landing_page = $this -> landingpage -> geLandingPage($landing_page_data['landing_page_id'], $language_id);

			$landing_page_data['page_title'] = 'Homepage';
			if ($this -> uri -> segment(2) == 'consultation-confirm.html') {
				$page = 'consultation-confirm.html';
			} else {
				$page = 'index.html';
			}
			$this -> load -> view($landing_page_data['html_url'] . '/' . $page);
		} else {
			//echo $this->db->last_query();
		}
	}

	//
	/**
	 * VIP :: Apply coupon code to user account.
	 * @access public
	 * @return coupon.
	 * @author by Rajnish
	 */
	public function vip() {
		if ($postData = $this -> input -> post()) {
			$url = $this -> uri -> segment(1);
			$this -> general -> set_table('coupon_promo');
			$copon_condition = "coupon_promo_code = '" . $postData['coupon_promo_code'] . "' AND DATE(expiry_date) > CURDATE() ";
			if ($coupon_data = $this -> general -> get("", $copon_condition)) {
				//Write a coupon logs...
				$copon_log['coupon_promo_id'] = $coupon_data['0']['coupon_promo_id'];
				$copon_log['entry_time'] = SQL_DATETIME;
				$copon_log['user_ip'] = $this -> input -> ip_address();
				$this -> general -> set_table('coupon_entry_log');
				$this -> general -> save($copon_log);

				$this -> session -> set_userdata('coupon_details', $coupon_data['0']);
				if ($user_id = $this -> session -> userdata('user_id')) {
					$this -> model_user -> apply_coupon($user_id);
					$displayMessage = translate_phrase("Coupon applied successfully.");
					$this -> session -> set_flashdata('dispMessage', '<label class="success">' . $displayMessage . '</label>');
				} else {
					if ($postData['is_datetix_member']) {
						$url = url_city_name() . '/signin.html';
					} else {
						$url = url_city_name() . '/apply.html?event_ticket_id=' . $this -> utility -> encode(488);
					}
				}
			} else {
				//echo $this->db->last_query(); echo "<pre>"; print_r($coupon_data);exit;
				$displayMessage = translate_phrase("Invalid promo code. Please try again.");
				$this -> session -> set_flashdata('dispMessage', '<label class="error">' . $displayMessage . '</label>');
				$this -> session -> set_flashdata('returnErrorData', $postData);
			}
			redirect($url);
		}
		if ($this -> uri -> segment(1) == 'promo') {
			$data['page_title'] = translate_phrase('Enter Your Promo Code to get FREE ENTRY (before 10pm) to the Halloween Dress Up in The Dark Party on Friday, October 31, 2014!');
		} else {
			$data['page_title'] = translate_phrase('Enter Your Promo Code');
		}
		$data['page_name'] = 'home/coupon';
		if ($user_id = $this -> session -> userdata('user_id')) {
			$this -> general -> set_table('user');
			$user_data = $this -> general -> get("", array('user_id' => $user_id));
			$data['user_data'] = $user_data['0'];
			$this -> load -> view('template/editProfileTemplate', $data);
		} else {

			$this -> load -> view('template/default', $data);
		}
	}

	/**
	 * Shortcut url : Redirect all numeric value after domain name to considering event_id
	 * @access public
	 * @return event.
	 * @author by Rajnish
	 */
	public function shortcut_url($id = 1) {
			
		$redirect_link = base_url();
		$language_id = $this -> session -> userdata('sess_language_id');
		$url_segment = strtolower($this -> uri -> segment(1));
		
		$this -> general -> set_table('signup_url');
		$url_condition['url'] = $url_segment;
		if($url_datas = $this -> general -> get("*",$url_condition,array('signup_url_id'=>'desc'),1))
		{
			redirect('/date?partner_id='.$url_datas['0']['partner_id']);
		}
		
		
		//$cur_event_id = 14;
		if ($url_segment == "join") {			
			$redirect_link .= url_city_name() . '/apply.html?src=12';
		} else {
			/*if (!is_numeric($event_id)) {
				if ($event_id == 'bj2015') {
					$event_id = 10;
					$ad_id = 28;
					$this -> session -> set_userdata('sess_city_id', 105);
					$this -> session -> set_userdata('sess_language_id', 3);
				} elseif ($event_id == 'tinavip') {
					$event_id = $cur_event_id;
					$ad_id = 7;
				} elseif ($event_id == 'meetup') {
					$event_id = $cur_event_id;
					$ad_id = 4;
				} elseif ($event_id == 'gangpiaoquan') {
					$event_id = $cur_event_id;
					$ad_id = 21;
				} elseif ($event_id == 'aigangpiao') {
					$event_id = $cur_event_id;
					$ad_id = 16;
				} elseif ($event_id == 'appledating') {
					$event_id = $cur_event_id;
					$ad_id = 22;
					//$language_id = 2;
					//$this -> session -> set_userdata('sess_language_id', '2');
				} elseif ($event_id == 'network') {
					$event_id = $cur_event_id;
					$ad_id = 9;
				} elseif ($event_id == 'tgif') {
					$event_id = $cur_event_id;
					$ad_id = 10;
				} elseif ($event_id == 'mba') {
					$event_id = $cur_event_id;
					$ad_id = 13;
				} elseif ($event_id == 'mnc') {
					$event_id = $cur_event_id;
					$ad_id = 12;
				} elseif ($event_id == 'social') {
					$event_id = $cur_event_id;
					$ad_id = 19;
				} elseif ($event_id == 'networking') {
					$event_id = $cur_event_id;
					$ad_id = 20;
				} elseif ($event_id == 'group') {
					$event_id = $cur_event_id;
					$ad_id = 20;
				} elseif ($event_id == 'angel') {
					$event_id = $cur_event_id;
					$ad_id = 24;
				} elseif ($event_id == 'bachelor') {
					$event_id = $cur_event_id;
					$ad_id = 1;
				} elseif ($event_id == 'spring') {
					$event_id = $cur_event_id;
					$ad_id = 1;
				} elseif ($event_id == 'summer') {
					$event_id = $cur_event_id;
					$ad_id = 1;
				} elseif ($event_id == 'xia') {
					$event_id = $cur_event_id;
					$ad_id = 21;
				} elseif ($event_id == 'sue') {
					$event_id = $cur_event_id;
					$ad_id = 33;
				} elseif ($event_id == 'your-mr-miss-right') {
					$event_id = $cur_event_id;
					$ad_id = 30;
				} elseif ($event_id == 'party') {
					$event_id = $cur_event_id;
					$ad_id = 31;
				} elseif ($event_id == 'jointu') {
					$event_id = 15;
					$ad_id = 1;
				} elseif ($event_id == 'chun') {
					$event_id = $cur_event_id;
					$ad_id = 32;
					$this -> session -> set_userdata('sess_language_id', 3);
				} else {
					$event_id = str_replace('event', '', $event_id);
					$ad_id = 1;
				}
			}*/
			$partner_id = 0;
			$this -> general -> set_table('event');
			$event_data = $this -> general-> get("",array('shortcut_url'=>$url_segment),array('event_start_time'=>'asc'),1);
			if($event_data)
			{
				/// event.shortcut_url found...
				$ad_id = 0;
				
				$event_data = $event_data['0'];
				$event_id = $event_data['event_id'];
				
				$this->general->set_table('event_language');
				$event_lang_condition['event_id'] = $event_id;			
				if($event_languages = $this->general->get("event_id,display_language_id,event_name",$event_lang_condition,array('display_language_id'=>'asc'),1))
				{
					$language_id = $event_languages['0']['display_language_id'];
				}
			}
			else {
				
				$this -> general -> set_table('event_url');
				$event_url_condition['url'] = $url_segment;
				$event_url_datas = $this -> general -> get("*",$event_url_condition,array('event_id'=>'desc'),1);
				
				if (!empty($event_url_datas )) {
					$event_id = $event_url_datas['0']['event_id'];
					$language_id = $event_url_datas['0']['display_language_id'];
					$partner_id = $event_url_datas['0']['partner_id'];
				}
			}
			
			if(isset($event_id) && $event_id)
			{
			
				$this -> session -> set_userdata('sess_language_id',$language_id);
				
				$this -> general -> set_table('event');
				$fields = array('ct.description as city_name', 'ct.city_id');
				$from = 'event as e';
				$joins = array(
						'venue as v' => array('e.venue_id = v.venue_id', 'left'), 
						'neighborhood as n' => array('v.neighborhood_id = n.neighborhood_id', 'left'), 
						'city as ct' => array('ct.city_id = n.city_id', 'left')
					);
	
				$where['e.event_id'] = $event_id;
				$where['ct.display_language_id'] = $language_id;
				$where['n.display_language_id'] = $language_id;
				$event_data = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'e.event_start_time asc');				
				if (!empty($event_data)) {
					$redirect_link .= url_city_name($event_data['0']['city_name']) . '/event.html?id=' . $event_id . '&partner_id='.$partner_id.'&src=' . $ad_id.'&url=' . $url_segment;
				}
				
			}
		}

		redirect($redirect_link);
	}

	/**
	 * Eligible Schools :: Load schools with default selected country
	 * @access public
	 * @return view
	 * @author by Rajnish
	 */
	function eligible_schools() {
		$language_id = $this -> session -> userdata('sess_language_id');
		$fields = array('con.*');
		$from = $this -> tblcity . ' as c';

		$joins = array($this -> tblprovince . ' as pro' => array('pro.province_id = c.province_id', 'INNER'), $this -> tblcountry . ' as con' => array('con.country_id = pro.country_id', 'INNER'), $this -> tblschool => array('school.city_id = c.city_id', 'INNER'));
		$contry_condition['c.display_language_id'] = $language_id;
		$contry_condition['con.display_language_id'] = $language_id;
		$contry_condition['pro.display_language_id'] = $language_id;
		$contry_condition['school.is_active'] = 1;
		$ordersby = 'c.city_id asc';

		$data['countries'] = $this -> general -> multijoins($fields, $from, $joins, $contry_condition, $ordersby, 'array', NULL, NULL, NULL, 'where', 'con.country_id');

		$contry_condition['c.city_id'] = current_city_id();
		$country_datas = $this -> general -> multijoins($fields, $from, $joins, $contry_condition, $ordersby, 'array');

		if ($country_datas) {
			$country_id = $country_datas[0]['country_id'];
			$data['selected_country_data'] = $country_datas[0];

			$this -> general -> set_table($this -> tblcity);
			$fields = array('ct.description as cityName', 'sc.school_id', 'sc.school_name', 'sc.logo_url');
			//$fields = array('sc.school_id','sc.school_name','sc.logo_url');
			$from = $this -> tblschool . ' as sc';
			$joins = array($this -> tblcity . ' as ct' => array('ct.city_id = sc.city_id', 'INNER'), $this -> tblprovince . ' as pro' => array('pro.province_id = ct.province_id', 'INNER'), $this -> tblcountry . ' as con' => array('con.country_id = pro.country_id', 'INNER'));

			$condition['sc.display_language_id'] = $language_id;
			$condition['sc.is_active !='] = '0';
			$condition['ct.display_language_id'] = $language_id;
			$condition['pro.display_language_id'] = $language_id;
			$condition['con.display_language_id'] = $language_id;
			$condition['con.country_id'] = $country_id;

			$ordersby = 'ct.description asc, sc.school_name asc';
			$data['schools'] = $this -> general -> multijoins($fields, $from, $joins, $condition, $ordersby, 'array', $likes = NULL, $num = NULL, $offset = NULL, $wheretype = 'where', $groupby = 'sc.school_id');
		}

		$data['page_name'] = 'home/eligible_schools';
		$data['page_title'] = translate_phrase('Top 150 Schools');
		;
		$this -> load -> view('template/default', $data);
	}

	/**
	 * [POP UP]Google Map :: Show Venue
	 * @access public
	 * @return View data
	 * @author Rajnish Savaliya
	 */
	public function view_map() {

		$language_id = $this -> session -> userdata('sess_language_id');
		$user_venue_id = $this -> utility -> decode($this -> input -> get('venue'));
		if ($return_to = $this -> input -> get('return_to')) {
			if ($tab = $this -> input -> get('tab')) {
				$return_to .= '#' . $tab;
			}

			$return_url = $return_to;
		} else {
			$return_url = url_city_name() . '/index.html';
		}

		$user_intro_id_encoded = $this -> session -> userdata('selected_intro_id');
		$user_intro_id = $this -> utility -> decode($user_intro_id_encoded);

		$this -> general -> set_table('venue');
		if ($venue_data = $this -> general -> get("", array('venue_id' => $user_venue_id))) {
			$tmp = $venue_data['0'];

			//Current Lived in
			$fields = array('ct.description as city_description', 'prvnce.description as province_description', 'cntry.description as country_description', 'cntry.country_code', 'cntry.flag_url');

			$from = 'neighborhood as neigh';
			$joins = array('city as ct' => array('ct.city_id = neigh.city_id ', 'Inner'), 'province as prvnce' => array('ct.province_id = prvnce.province_id', 'LEFT'), 'country as cntry' => array('prvnce.country_id = cntry.country_id', 'LEFT'));

			$where['neigh.neighborhood_id'] = $tmp['neighborhood_id'];
			$where['neigh.display_language_id'] = $language_id;
			$where['ct.display_language_id'] = $language_id;
			$where['prvnce.display_language_id'] = $language_id;
			$where['cntry.display_language_id'] = $language_id;
			if ($temp_venue_data = $this -> model_user -> multijoins($fields, $from, $joins, $where, '', 'city_description asc')) {
				$temp_venue_data = $temp_venue_data['0'];
				$tmp['address'] .= ', ' . $temp_venue_data['city_description'] . ', ' . $temp_venue_data['country_description'];
			}
			$geo_data = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($tmp['address']) . '&sensor=false');
			$geo_data = json_decode($geo_data);
			$tmp['geo_data'] = isset($geo_data -> results['0'] -> geometry -> location) ? $geo_data -> results['0'] -> geometry -> location : array();
			$data['venue_data'] = $tmp;
		}

		$data['heading_txt'] = translate_phrase('View Venue Map');
		$data['page_title'] = translate_phrase('Date Suggestion Sent');
		$data['page_name'] = 'user/dates/map_view';
		$data['return_url'] = $return_url;
		if ($user_id = $this -> session -> userdata('user_id')) {
			$this -> general -> set_table('user');
			$user_data = $this -> general -> get("", array('user_id' => $user_id));
			$data['user_data'] = $user_data['0'];
			$this -> load -> view('template/editProfileTemplate', $data);
		} else {
			$this -> load -> view('template/default', $data);
		}
	}

	/**
	 * [AJAX] :: Eligible Schools :: Load schools by country_id
	 * @access public
	 * @return view
	 * @author by Rajnish
	 */
	function load_eligible_schools() {
		if ($this -> input -> is_ajax_request()) {
			$country_id = $this -> input -> post('country_id');
			$language_id = $this -> session -> userdata('sess_language_id');
			$this -> general -> set_table($this -> tblcity);
			$fields = array('ct.description as cityName', 'sc.school_id', 'sc.school_name', 'sc.logo_url');
			$from = $this -> tblschool . ' as sc';
			$joins = array($this -> tblcity . ' as ct' => array('ct.city_id = sc.city_id', 'INNER'), $this -> tblprovince . ' as pro' => array('pro.province_id = ct.province_id', 'INNER'), $this -> tblcountry . ' as con' => array('con.country_id = pro.country_id', 'INNER'));

			$condition['sc.display_language_id'] = $language_id;
			$condition['ct.display_language_id'] = $language_id;
			$condition['pro.display_language_id'] = $language_id;
			$condition['con.display_language_id'] = $language_id;
			$condition['sc.is_active !='] = '0';
			$condition['con.country_id'] = $country_id;

			$ordersby = 'ct.description asc, sc.school_name asc';

			$schools = $this -> general -> multijoins($fields, $from, $joins, $condition, $ordersby, 'array', $likes = NULL, $num = NULL, $offset = NULL, $wheretype = 'where'/*,$groupby='ct.description'*/);
			if ($schools) {
				$previousCity = '';
				$i = 0;
				foreach ($schools as $key => $school) {

					if ($previousCity != $school['cityName']) {
						echo '<div class="schoolHed">' . $school['cityName'] . '</div>';
						$i = 0;
					}

					echo '<div class="school-container-column ';

					if ($i % 2 != 0)
						echo 'Inational-Mar-L';

					echo '"><div class="school-box">';
					echo '<div class="schoolLogoHed">' . $school['school_name'] . '</div>';
					echo '<div class="schoolLogo"><img height="75" width="80" alt="logo" src="' . base_url() . 'school_logos/' . $school['logo_url'] . '"></div>';
					echo '</div>';
					echo '</div>';

					$previousCity = $school['cityName'];
					$i++;
				}
			} else {
				echo translate_phrase('No Schools Found.');
			}

		}
	}

	/* gets flag of a particular country and if falg is not found then return default notFound image.
	 */
	public function getCountryFlagUrl() {
		$countryId = $this -> input -> post('country_id');
		$languageId = $this -> session -> userdata('sess_language_id');

		$condition = array('country_id' => $countryId, 'display_language_id' => $languageId);

		$result = $this -> general -> getSingleValue('country', 'flag_url', $condition);
		$default_flag = base_url() . 'assets/images/flag404.png';

		if ($result === NULL)
			die(json_encode(array('actionStatus' => 'ok', 'flag_url' => $default_flag)));

		$flag_url = base_url() . 'country_flags/' . $result;
		die(json_encode(array('actionStatus' => 'ok', 'flag_url' => $flag_url)));
	}

	function array_multi_unique($multiArray) {
		$uniqueArray = array();
		foreach ($multiArray as $subArray) {
			if (!in_array($subArray, $uniqueArray)) {
				$uniqueArray[] = $subArray;
			}
		}
		return $uniqueArray;
	}

	public function start_register() {

		$this -> form_validation -> set_rules('email', 'email', 'trim|required|strip_tags|purify|valid_email|max_length[250]');
		$this -> form_validation -> set_rules('first_name', 'first_name', 'trim|required|strip_tags|purify|max_length[250]');
		$this -> form_validation -> set_rules('last_name', 'last_name', 'trim|required|strip_tags|purify|max_length[250]');

		$input_datas = $this -> input -> post();

		$redirect_url = isset($input_datas['redirect_url']) ? $input_datas['redirect_url'] : '';

		if ($this -> form_validation -> run() == false) {
			$this -> register();
		} else {
			$email = form_prep($this -> input -> post('email'));
			$exist = $this -> model_home -> check_email_exist($email);
			if ($exist) {
				$user_name = $this -> model_user -> get_user_by_email($this -> input -> post('email'));
				$insert_id = $user_name ? $user_name -> user_id : '';
				$this -> session -> set_userdata('is_return_apply', 1);

				$displayMessage = 'Some error occured please try again later';
				if (!empty($user_name) && $user_name -> facebook_id != "") {
					$attributes = 'onclick = "fb_login();"';
					$displayMessage = $email . ' already exists, please <a ' . $attributes . ' style="color:#2097d4 !important;">Sign In Using Facebook</a> to access your account';
				} else if (!empty($user_name) && $user_name -> facebook_id == "") {
					$attributes = 'href=' . base_url() . url_city_name() . '/signin.html?highlight=1';
					$displayMessage = $email . ' already exists, please <a ' . $attributes . ' style="color:#2097d4 !important;">Sign In Using Email</a> to access your account';
				}
				if ($redirect_url) {
					$url = $redirect_url . '?errorMsg=' . urlencode($displayMessage);
					redirect($url);
				} else {
					$this -> session -> set_flashdata('dispMessage', '<div class="errormsg">' . $displayMessage . '</div>');
					$this -> session -> set_flashdata('returnErrorData', $input_datas);
					redirect('home/register');
				}

			} else {
				$completed_application_step = $this -> model_home -> completed_application_step($this -> input -> post('email'));
				$completed_step = $completed_application_step ? $completed_application_step -> completed_application_step : '';
				$user_id = $completed_application_step ? $completed_application_step -> user_id : '';

				if ($completed_step != "") {
					$step = $completed_step + 1;
					$this -> session -> set_userdata('user_id', $user_id);
					$this -> session -> set_userdata('sign_up_id', $user_id);
					$this -> session -> set_userdata('is_return_apply', 1);

					if ($redirect_url) {
						$url = $redirect_url . '?errorMsg=' . urlencode($displayMessage);
						redirect($url);
					} else {
						$this -> session -> set_flashdata('dispMessage', '<div class="errormsg">error in action</div>');
						$this -> session -> set_flashdata('returnErrorData', $input_datas);
						redirect('home/register');
					}
				} else {

					if ($invite_id = $this -> session -> userdata('invite_id')) {
						$this -> session -> unset_userdata('invite_id');
					}
                                        
                                        $ad_id=$this -> session -> userdata('ad_id');
					$insert_id = $this -> model_user -> insert_user(array('first_name' => $this -> input -> post('first_name'), 'last_name' => $this -> input -> post('last_name'), 'ref_user_id' => $invite_id, 'password' => sha1($this -> input -> post('password')),'ad_id'=>$ad_id));
					$this -> model_user -> insert_email($insert_id);

					if ($this -> session -> userdata('event_ticket_id')) {
						redirect(base_url() . url_city_name() . "/signup-step-1.html");
					} else {
						$this -> load -> library('encrypt');
						$insert_id = $this -> encrypt -> encode($insert_id);
						$user_id = str_replace('/', '-', $insert_id);
						$this -> model_user -> send_verification_mail($user_id);

						$this -> session -> set_userdata('userEmail', $email);
						$this -> session -> set_userdata('userDetails', $user_id);
						$this -> session -> set_flashdata('dispMessage', '<div class="successmsg">Your application has been received. We have just sent a verification email to <b>' . $email . '</b>. Please click on the verification link in that email to verify your email address. If you cant find that email, please check your junk mailbox or click on the below button to have another verification email sent to you.</div>');
						redirect('home/email_verification');
					}
				}
			}
		}
	}

	/**
	 * [IMPORTANT] Mail Action: function identify user and will help link to save return url data in session/cookie
	 * If user is not logged in then call autosignup call. after redirect based on parameter
	 * @param user_id[encoded]
	 * @return particular url : return_to para.
	 * @author Rajnish Savaliya
	 */
	public function mail_action($login_user_id, $pass = '') {
		$url = $this -> input -> get('return_to');
		if (!$url) {
			$this -> session -> set_flashdata('msg', translate_phrase('No redirect link..'));
			$url = base_url() . 'user/edit_profile';
		}

		if ($ticket_id = $this -> input -> get('ticket_id')) {
			$pass_text = "";
			if ($pass) {
				$pass_text = '/' . $pass;
			}
			$url = base_url() . 'home/mail_action/' . $login_user_id . $pass_text . '?ticket_id=' . $ticket_id;
		}

		// * NO USED CODE ANYMORE **/
		$tab = '';
		if ($intro_row_id = $this -> input -> get('redirect_intro_id')) {
			$this -> session -> set_userdata('redirect_intro_id', $intro_row_id);

			//FETCH DATE data :::
			$this -> general -> set_table('user_date');
			if ($user_date_data = $this -> general -> get("", array('user_intro_id' => $intro_row_id))) {

				if ($user_date_data['0']['date_accepted_time'] == "0000-00-00 00:00:00") {
					$tab = 'ActiveDateTab';
				} else {
					if (date("Y-m-d", strtotime($user_date_data['0']['date_time'])) < SQL_DATE) {
						$tab = 'PastDateTab';
					} else {
						$tab = 'UpcominEasyTab';
					}
				}
			} else {

				$this -> general -> set_table('user_intro');
				$intro = $this -> general -> get("", array('user_intro_id' => $intro_row_id));
				//if expired or not
				if (date("Y-m-d", strtotime($intro['0']['intro_expiry_time'])) < SQL_DATE) {
					$tab = 'PastDateTab';
				} else {
					if (date("Y-m-d", strtotime($intro['0']['intro_available_time'])) > SQL_DATE) {
						$tab = 'UpcominEasyTab';
					} else {
						$tab = 'ActiveDateTab';
					}
				}
			}
		}

		if ($tab == '') {
			if ($tab_name = $this -> input -> get('tab_name')) {
				$this -> session -> set_userdata('redirect_tab_name', $tab_name);
			}
		} else {
			$this -> session -> set_userdata('redirect_tab_name', $tab);
		}

		if ($type = $this -> input -> get('type')) {
			$this -> session -> set_userdata('type', $type);
		}
		// * ABOVE CODE is not used now **/

		$uid = $this -> session -> userdata('user_id');
		$decode_logged_user_id = $this -> utility -> decode($login_user_id);
		if ($uid) {
			if ($decode_logged_user_id != $uid) {
				$this -> auto_login($login_user_id, $pass, $url);
			}
		} else {
			$this -> auto_login($login_user_id, $pass, $url);
		}

		if ($event_ticket_id = $this -> utility -> decode($this -> input -> get('ticket_id'))) {
			$fields = array('e.*');
			$from = 'event_ticket as rsvp';
			$joins = array('event_order as ordr' => array('rsvp.event_order_id = ordr.event_order_id', 'inner'), 'event as e' => array('e.event_id = ordr.event_id', 'inner'));
			$where['rsvp.event_ticket_id'] = $event_ticket_id;
			if ($event_info = $this -> model_user -> multijoins($fields, $from, $joins, $where)) {
				$this -> session -> set_userdata('event_ticket_id', $event_ticket_id);
			}

			$this -> model_user -> is_current_signup_process($uid);
		} else {

			if ($getRequest = $this -> input -> get()) {
				if (isset($getRequest['return_to'])) {
					unset($getRequest['return_to']);
				}
				if($getRequest)
					$url .= '?' . http_build_query($getRequest);
			}

			redirect($url);
		}
	}

	/**
	 * Multiply Score with importance level [mandatory,important,very important]
	 * @param user_id[encoded]
	 * @param importance
	 * @return score
	 * @author Rajnish Savaliya
	 */
	public function auto_login($user_id, $pass, $redirect_url) {
		
		//http://datetix.hk/home/mail_action/ui5Mtj2Tw6ijw8bxfyPlqFMS7a5Jd9TIFkzHBALv9bA/5047aa613df2af07120cf51aeaaadab3d60c6576?return_to=http://datetix.hk/dates/find_dates/730
		
		$decode_user_id = $this -> utility -> decode($user_id);
		if ($pass != '') {
			
			$db_pass = $this -> model_user -> get_user_field($decode_user_id, 'password');
			if (strcasecmp($pass, $db_pass) == 0) {
				
				$this -> session -> set_userdata('user_id', $decode_user_id);
				$this -> session -> set_userdata('sign_up_id', $decode_user_id);
				
				if (!$this -> session -> userdata('user_id')) {
					//above two lines are here..	
				}
			} else {
				$url = base_url() . url_city_name() . '/signin.html?return_url=' . $redirect_url;
				redirect($url);
			}
		} else {
			
			if(is_numeric($decode_user_id))
			{
				$this -> session -> set_userdata('sign_up_id', $decode_user_id);
	            $this -> session -> set_userdata('user_id', $decode_user_id);
				//$this -> model_user -> is_current_signup_process($decode_user_id);
			}
			
			/****** RESTRICT FB API CALL FOR AUTH USER ***************
			parse_str($_SERVER['QUERY_STRING'], $_REQUEST);
			$this -> load -> library('Facebook');
			$userId = $this -> facebook -> getUser();
			$data['url'] = '';
			if ($userId == 0) {
				$cookie = array('name' => 'redirect_url', 'value' => $redirect_url, 'expire' => '86500', 'path' => '/', 'prefix' => 'datetix_', 'secure' => false);

				$this -> input -> set_cookie($cookie);
				$access['scope'] = 'email,user_photos,user_activities,user_about_me,user_birthday,user_education_history,user_groups,user_hometown,user_interests,user_likes,user_location,user_relationships,user_relationship_details,user_website,user_work_history,friends_about_me,friends_activities,friends_birthday,friends_education_history,friends_groups,friends_hometown,friends_interests,friends_likes,friends_relationships,friends_relationship_details,friends_religion_politics,friends_website,friends_work_history,friends_location';
				$access['redirect_uri'] = base_url() . 'fb_login/fb_success';
				$fb_login_url = $this -> facebook -> getLoginUrl($access);
				redirect($fb_login_url);
			} else {
				$user = $this -> model_user -> getByFacebookId($userId);
				$this -> session -> set_userdata('user_id', $user -> user_id);
				$this -> session -> set_userdata('sign_up_id', $user -> user_id);
				$this -> session -> set_userdata('ad_id', $user -> ad_id);
			}*/
		}
	}

	/**
	 * Unsubscribe :: Update user_email record with is_contact=0;
	 * @param email_address [encoded]
	 * @return view [Message]
	 * @author Rajnish Savaliya
	 */
	public function unsubscribe($encoded_email) {
		$email = $this -> utility -> decode($encoded_email);
		$condition['email_address'] = $email;
		$this -> general -> set_table("user_email");

		if ($email_data = $this -> general -> get("", $condition)) {
			$email_data = $email_data['0'];
			if ($this -> general -> update(array('is_contact' => '0'), $condition)) {
				$msg = translate_phrase("Your request to unsubscribe has been approved for ") . '<b>' . $email . '</b>';
			} else {
				$msg = translate_phrase("Your request to unsubscribe has already approved for ") . '<b>' . $email . '</b>';
			}

		} else {
			$msg = "Sorry, No such email exist.";
		}

		$data['page_name'] = 'general_popups/simple_box';
		$data['page_title'] = translate_phrase("Unsubscribe Email");
		$data['heading_txt'] = translate_phrase("Unsubscribe Email");
		$data['return_url'] = "";
		$data['content'] = $msg;

		$this -> load -> view('template/default', $data);
	}

	public function resend_email_verification($userDetails) {
		if (!empty($userDetails)) {
			$user_id = $userDetails;
			$this -> model_user -> send_verification_mail($user_id);
			die(json_encode(array('actionStatus' => 'ok', 'responseText' => 'Email sent successfully...!!!')));
		}
		die(json_encode(array('actionStatus' => 'error', 'responseText' => 'Some error occured please try again later.')));
	}

	public function email_verification() {

		$this -> session -> unset_userdata('is_return_apply');
		$this -> load -> library('encrypt');
		$error = "";
		$id = $this -> uri -> segment(2);

		$user_id = str_replace('-', '/', $id);
		$user_id = $this -> encrypt -> decode($user_id);
		$user_email = $this -> model_user -> get_user_email($user_id);
		$code = $this -> uri -> segment(3);
		$verif_code = "";
		$email = "";
		if (!empty($user_email)) {
			$verif_code = $user_email['verification_code'];
			$email = $user_email['email_address'];
			$user_name = $this -> model_user -> get_user_by_email($email);
			$first_name = $user_name ? $user_name -> first_name : '';

			/*--------Change by Hannan Munshi--------*/
			$isVerified = $user_email['is_verified'];
			//if user is already a verified user then redirect him to login page.
			if ($isVerified == 1) {
				if ($this -> session -> userdata('succ_email_verify') != "") {
					$this -> session -> unset_userdata('succ_email_verify');
				}

				$this -> session -> set_flashdata('isAlreadyVerifiedMsg', "Hi,$first_name. You have already verified your email. Please login with your credentials");
				redirect(base_url() . url_city_name() . '/signin.html?highlight=1');
			}
		}

		if ($code != "") {
			$verification_code = $this -> encrypt -> decode(str_replace('-', '/', $code));

			if ($verif_code == $verification_code) {
				$this -> model_user -> verify_email($user_id, $verification_code);
				$verified = $this -> model_user -> check_email_verification($user_id);
				if ($verified > 0) {
					$this -> session -> set_userdata('user_id', $user_id);
					$this -> session -> set_userdata('sign_up_id', $user_id);
					$this -> session -> set_userdata('succ_email_verify', "Thanks for verifying your email address, " . $first_name . '.');
					redirect(url_city_name() . '/signup-step-1.html');
				}
			} else {
				$error = "Failed to verify your email address.Please try again";
			}
		}
		$data['email'] = $email;
		$data['error'] = $error;
		$data['user_id'] = $id;
		$data['page_title'] = 'Email verification';
		$data['page_name'] = 'home/email_verification';
		$this -> load -> view('template/default', $data);
	}

	public function register() {
           
        $this -> session -> unset_userdata('event_ticket_id');
		if ($event_ticket_id = $this -> utility -> decode($this -> input -> get('event_ticket_id'))) {
			$fields = array('rsvp.*', 'e.*');
			$from = 'event_ticket as rsvp';
			$joins = array('event_order as ordr' => array('rsvp.event_order_id = ordr.event_order_id', 'inner'), 'event as e' => array('e.event_id = ordr.event_id', 'inner'), );
			$where['rsvp.event_ticket_id'] = $event_ticket_id;
			$where['e.display_language_id'] = $this -> session -> userdata('sess_language_id');
			if ($data['event_info'] = $this -> model_user -> multijoins($fields, $from, $joins, $where)) {
				$data['event_info'] = $data['event_info']['0'];
				$this -> session -> set_userdata('event_ticket_id', $event_ticket_id);
			}

			$data['page_title'] = translate_phrase('RSVP');
		} else {
			$data['page_title'] = translate_phrase('Apply Without Facebook');
		}
		//echo $this->session->userdata('partner_id');;
		
        if(($this->input->get('src'))){
            $this -> session -> set_userdata('ad_id', $this -> input -> get('src'));
        }
		
		if(($this->input->get('partner_id'))){
            $this -> session -> set_userdata('partner_id', $this -> input -> get('partner_id'));
        }
		
		//$this -> session -> set_userdata('ad_id', $this -> input -> get('src'));
		$data['page_name'] = 'home/register';
		$this -> load -> view('template/default', $data);
	}

	public function check_sessions() {
		echo "<pre>";
		print_r($this -> session -> all_userdata());
		exit ;
	}

	public function change_city() {
		$lang_id = $this -> session -> userdata('sess_language_id');
		$data['return_to'] = $this -> input -> get('return_to');

		$user_id = $this -> session -> userdata('user_id');

		if (($city_id = $this -> input -> get('city_id')) && !empty($city_id)) {
			if ($data['return_to'] == 'edit-profile.html' && $user_id) {
				$this -> model_user -> update_user($user_id, array('current_city_id' => $city_id));
			}
			$signup_id = $user_id;
			if (!$signup_id) {
				$signup_id = $this -> session -> userdata('sign_up_id');
			}
			//For signlup page
			if ($data['return_to'] == 'signup-step-1.html' && $signup_id) {
				$this -> model_user -> update_user($signup_id, array('current_city_id' => $city_id));
			}

			$this -> model_home -> redirect_to_city($city_id, $data['return_to']);
		}

		$data['city_data'] = $this -> model_home -> getChangeCityData($lang_id);
		$data['page_name'] = 'home/change_city';
		$data['page_title'] = translate_phrase('Change City');
		if ($data['return_to'] == 'edit-profile.html' && $user_id != "") {
			$this -> general -> set_table('user');
			$user_data = $this -> general -> get("", array('user_id' => $user_id));
			$data['user_data'] = $user_data['0'];

			$this -> load -> view('template/editProfileTemplate', $data);
		} else {
			$this -> load -> view('template/default', $data);
		}
	}

	/**
	 * [Static Page] : Fb Benefits
	 * @author Rajnish Savaliya
	 */
	public function benifits_of_using_fb() {
		$data['page_name'] = 'home/fb-benifits';
		$data['page_title'] = 'Top 250 Schools';
		$this -> load -> view('template/default', $data);
	}

	/**
	 * [Static Page] : How it works
	 * @author Rajnish Savaliya
	 */
	public function how_works() {
		$data['page_name'] = 'home/how_it_works';
		$data['page_title'] = translate_phrase('How It Works');
		$this -> load -> view('template/default', $data);
	}

	/**
	 * [Static Page] : How it works
	 * @author Rajnish Savaliya
	 */
	public function privacy() {
		$this -> load -> view('template/default', array('page_title' => translate_phrase('Privacy Policy'), 'page_name' => 'home/privacy'));
	}

	/**
	 * [Static Page] : About Us
	 * @author Rajnish Savaliya
	 */
	public function about() {
		$data['page_title'] = translate_phrase('About Us');
		$data['page_name'] = 'home/about_us';
		$this -> load -> view('template/default', $data);
	}

	/**
	 * [Static Page] : About Us
	 * @author Rajnish Savaliya
	 */
	public function career() {
		$data['page_title'] = translate_phrase('Careers at DATETIX');
		$data['page_name'] = 'home/career';
		$this -> load -> view('template/default', $data);
	}

	/**
	 * [Static Page] : About Us
	 * @author Rajnish Savaliya
	 */
	public function help() {
		$data['page_title'] = translate_phrase('Help');
		$data['page_name'] = 'home/help';
		$this -> load -> view('template/default', $data);
	}

	/**
	 * [Static Page] : Press
	 * @author Rajnish Savaliya
	 */
	public function press() {
		$data['page_title'] = translate_phrase('Press');
		$data['page_name'] = 'home/press';
		$this -> load -> view('template/default', $data);
	}

	/**
	 * [Static Page] : Terms
	 * @author Rajnish Savaliya
	 */
	public function terms() {
		$this -> load -> view('template/default', array('page_title' => translate_phrase('Terms of Use'), 'page_name' => 'home/terms'));
	}
	
	/**
	 * [Static Page] : Press
	 * @author Rajnish Savaliya
	 */
	public function company_overview() {
		$data['page_title'] = translate_phrase('Company Overview');
		$data['page_name'] = 'home/company';
		$this -> load -> view('template/default', $data);
	}
	
	public function board() {
		$data['page_title'] = translate_phrase('Board');
		$data['page_name'] = 'home/board';
		$this -> load -> view('template/default', $data);
	}
	public function management() {
		$data['page_title'] = translate_phrase('Management');
		$data['page_name'] = 'home/management';
		$this -> load -> view('template/default', $data);
	}
	public function corporate_goverance() {
		$data['page_title'] = translate_phrase('Management');
		$data['page_name'] = 'home/management';
		$this -> load -> view('template/default', $data);
	}
	public function compliance() {
		$data['page_title'] = translate_phrase('Compliance');
		$data['page_name'] = 'home/compliance';
		$this -> load -> view('template/default', $data);
	}
	public function documents() {
		$data['page_title'] = translate_phrase('Documents');
		$data['page_name'] = 'home/documents';
		$this -> load -> view('template/default', $data);
	}
	public function news() {
		$data['page_title'] = translate_phrase('ASX & NEWS Announcements');
		$data['page_name'] = 'home/announcements';
		$this -> load -> view('template/default', $data);
	}
	public function announcements() {
		$data['page_title'] = translate_phrase('ASX Announcements');
		$data['page_name'] = 'home/announcements';
		$this -> load -> view('template/default', $data);
	}
	public function factsheet() {
		$data['page_title'] = translate_phrase('Factsheet');
		$data['page_name'] = 'home/factsheet';
		$this -> load -> view('template/default', $data);
	}
	public function investor_faq() {
		$data['page_title'] = translate_phrase('Investor FAQs');
		$data['page_name'] = 'home/investor_FAQs';
		$this -> load -> view('template/default', $data);
	}
	public function media() {
		$data['page_title'] = translate_phrase('Media');
		$data['page_name'] = 'home/media';
		$this -> load -> view('template/default', $data);
	}
	public function share_price() {
		$data['page_title'] = translate_phrase('Share Price');
		$data['page_name'] = 'home/share_price';
		$this -> load -> view('template/default', $data);
	}

	public function broker_reports() {
		$data['page_title'] = translate_phrase('Broker Reports');
		$data['page_name'] = 'home/broker_reports';
		$this -> load -> view('template/default', $data);
	}
	
	public function financials() {
		$data['page_title'] = translate_phrase('Financials');
		$data['page_name'] = 'home/broker_reports';
		$this -> load -> view('template/default', $data);
	}

	public function annual_reports() {
		$data['page_title'] = translate_phrase('Annual Reports');
		$data['page_name'] = 'home/annual_reports';
		$this -> load -> view('template/default', $data);
	}
	public function half_yearly_reports() {
		$data['page_title'] = translate_phrase('Half Yearly Reports');
		$data['page_name'] = 'home/half_yearly_reports';
		$this -> load -> view('template/default', $data);
	}
	public function quartrely_reports() {
		$data['page_title'] = translate_phrase('Quartrely Reports');
		$data['page_name'] = 'home/quartrely_reports';
		$this -> load -> view('template/default', $data);
	}
	public function calendar() {
		$data['page_title'] = translate_phrase('Calendar');
		$data['page_name'] = 'home/calendar';
		$this -> load -> view('template/default', $data);
	}
	public function contact() {
		$data['page_title'] = translate_phrase('Contact');
		$data['page_name'] = 'home/contact';
		$this -> load -> view('template/default', $data);
	}
	public function offices() {
		$data['page_title'] = translate_phrase('Offices');
		$data['page_name'] = 'home/offices';
		$this -> load -> view('template/default', $data);
	}
	
	/**
	 * [Ajax call] :
	 * @author Rajnish Savaliya
	 */
	public function premium_consultation() {
		if ($this -> input -> is_ajax_request()) {
			$response['type'] = 'error';
			$response['msg'] = 'Please fill the required field.';
			$postData = $this -> input -> post();
			if (!empty($postData)) {
				if (!$postData['name']) {
					$response['type'] = 'error';
					$response['msg'] = 'The Name field is required.';
				} elseif (!$postData['phone']) {
					$response['type'] = 'error';
					$response['msg'] = 'The Phone field is required.';
			//	} elseif (!$postData['time']) {
			//		$response['type'] = 'error';
			//		$response['msg'] = 'The Best time to call field is required.';
				} elseif (!$postData['email']) {
					$response['type'] = 'error';
					$response['msg'] = 'The Email field is required.';
				} else {
					$condition['email_address'] = form_prep($postData['email']);

					$duplicate = $this -> general -> checkDuplicate($condition, 'consultation');
					if ($duplicate) {
						$response['type'] = 'error';
						$response['msg'] = $postData['email'] . ' is already exists.';
					} else {
						$data['name'] = form_prep($postData['name']);
						$data['mobile_phone_number'] = form_prep($postData['phone']);
						//$data['best_time_to_call'] = $postData['time'];
						$data['email_address'] = form_prep($postData['email']);

						$this -> general -> set_table('consultation');
						$result = $this -> general -> save($data);
						if ($result) {
							$response['type'] = 'success';
							$response['redirect_url'] = base_url().'premium/consultation-confirm.html';
							//$response['msg'] = 'Thank you for requesting a free consultation! One of our friendly consultants will get in touch with you as soon as possible. In the meantime, <a href="base_url()/apply">apply for a free membership to our online matchmaking platform</a>!';
							//$this -> session -> set_userdata('confirmMSG', $this -> input -> get('src'));
						} else {
							$response['type'] = 'error';
							$response['msg'] = 'Some error occured,please try again.';
						}
					}
				}
			}
			die(json_encode($response));
		} else {
			redirect();
		}
	}

}

/* End of file home.php */
/* Location: ./application/controllers/home.php */
?>
