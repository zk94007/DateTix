<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Event extends CI_Controller {
		
	var $language_id = '1';
	var $template  = 'template/admin_event';
	var $folder = 'admin_event';
	var $admin_url = 'manage/event';
		
	public function __construct() {
		parent::__construct();
		ini_set('memory_limit', '-1');
		
		$this -> load -> model('general_model', 'general');
		if (!$this -> session -> userdata('sess_language_id')) {
			$this -> session -> set_userdata('sess_language_id', '1');
		}
		$logged_in = $this -> session -> userdata('event_admin_logged_in');
		if ($logged_in === FALSE) {
			
			if (!isset($_SERVER['PHP_AUTH_USER'])) {
				header('WWW-Authenticate: Basic realm="Datetix Event Admin Panel"');
				header('HTTP/1.0 401 Unauthorized');
				echo("Please enter a valid username and password");
				exit();
			}

			$this -> general -> set_table('admin_event');
			$CheckAdminCondition['login'] = $_SERVER['PHP_AUTH_USER'];
			$CheckAdminCondition['password'] = $_SERVER['PHP_AUTH_PW'];
			$CheckAdmin = $this -> general -> get('', $CheckAdminCondition);
			
			if (!empty($CheckAdmin)) {				
				$this -> session -> set_userdata('event_admin_logged_in', $CheckAdmin[0]);				
			} else {
				header('WWW-Authenticate: Basic realm="Datetix Event Admin Panel"');
				header('HTTP/1.0 401 Unauthorized');
				echo("Please enter a valid username and password");
				exit();
			}			
		}
	}

	public function index()
	{
		$city_ids = $this -> session -> userdata['event_admin_logged_in']['city_ids'];		
		$city_select = "select city.city_id, city.description from city WHERE display_language_id = $this->language_id AND city.city_id IN ($city_ids) ORDER BY city.description";		
		$data['cities'] = $this -> general -> sql_query($city_select);
		$data['seleccted_city_id'] = "";
		if ($city_id = $this -> input -> post('city_id')) {
			$data['seleccted_city_id'] = $city_id;
		}
		
		//Get Event datas
		$fields = array('e.event_id, e.event_name, e.event_start_time, e.price_door, e.ticket_sold_at_door, e.cash_collected_at_door, e.price_online, e.price_online_discounted',
						'ct.city_id, ct.description as city_name',
						'(SELECT SUM(event_order.num_tickets) from event_order WHERE event_id = e.event_id) as online_prepaid_tkt',
						'(SELECT SUM(event_order.order_amount) from event_order WHERE event_id = e.event_id) as online_prepaid_amt',
						'crncy.currency_id', 'crncy.description as currency_description', 
						);
		$from = 'event as e';
		$joins = array(
				'venue as v' => array('e.venue_id = v.venue_id', 'inner'), 
				'neighborhood as n' => array('v.neighborhood_id = n.neighborhood_id', 'inner'),
				'city as ct' => array('ct.city_id = n.city_id', 'inner'),
				'province as p' => array('p.province_id = ct.province_id', 'inner'),
				'country as cntry' => array('p.country_id = cntry.country_id', 'LEFT'),
				'currency as crncy' => array('cntry.currency_id = crncy.currency_id ', 'LEFT')
				);				
		if($city_id = $this -> input-> post('city_id'))
		{
			$city_ids = $city_id;	
		}
		
		if($city_ids)
		{
			$this->db->where_in('ct.city_id',explode(',', $city_ids));
		}
		$where['ct.display_language_id'] = $this -> language_id;		
		$where['p.display_language_id'] = $this -> language_id;
		$where['n.display_language_id'] = $this -> language_id;
		$where['cntry.display_language_id'] = $this -> language_id;
		$where['crncy.display_language_id'] = $this -> language_id;
		
		$data['events']  = $this -> general -> multijoins_groupby($fields, $from, $joins, $where,'e.event_start_time desc','array','e.event_id');
		//echo $this->db->last_query();
		//echo "<pre>";print_r($data['events']);exit;
		
		$data['page_title'] = translate_phrase('Event Admin');
		$data['page_name'] = 'dashboard';
		$this -> load -> view($this->template, $data);
	}
	
	public function logout() {
		$this -> session -> sess_destroy();
		unset($_SERVER['PHP_AUTH_USER']);
		unset($_SERVER['PHP_AUTH_PW']);

		redirect('/');
	}

	/* Functino create event with details, Partners info, ticket list, and event photos
	 * @Params : $CurrentTab
	 * @Author : Rajnish
	 */		
	public function create($event_id="")
	{
		
		$city_ids = $this -> session -> userdata['event_admin_logged_in']['city_ids'];		
		//$city_ids = '260,261,263';
		$this->general->set_table('event');
		$data['page_title'] = translate_phrase('Add Event');
		if($event_id)
		{
			//Get Event datas
			$fields = array('e.*');
			$from = 'event as e';
			$joins = array(
					'venue as v' => array('e.venue_id = v.venue_id', 'inner'), 
					'neighborhood as n' => array('v.neighborhood_id = n.neighborhood_id', 'inner'),
					'city as ct' => array('ct.city_id = n.city_id', 'inner'),
					'province as p' => array('p.province_id = ct.province_id', 'inner'),
					);				
			if($city_ids)
			{
				$this->db->where_in('ct.city_id',explode(',', $city_ids));
			}
			
			$where['ct.display_language_id'] = $this -> language_id;
			$where['p.display_language_id'] = $this -> language_id;
			$where['n.display_language_id'] = $this -> language_id;
			$where['e.event_id'] = $event_id;
			$where['e.event_end_time > '] = SQL_DATETIME;
			
			if($event_data  = $this -> general -> multijoins_groupby($fields, $from, $joins, $where,'e.event_start_time desc','array','e.event_id'))
			{
				$data = $event_data['0'];	

				$data['page_title'] = translate_phrase('Edit Event');
			}
			else
			{
				$this->session->set_flashdata('error_msg',translate_phrase('You can not modify event details.'));
				redirect($this->admin_url);
			}
			unset($where);
		}
		
		if($postData = $this->input->post())
		{
			
			//Set Event Date and time in SQL Formate
			$postData['event_start_time'] = date('Y-m-d H:i:s',strtotime($postData['event_start_date'].' '.$postData['event_start_time']));
			$postData['event_end_time'] = date('Y-m-d H:i:s',strtotime($postData['event_end_date'].' '.$postData['event_end_time']));
			unset($postData['event_start_date']);
			unset($postData['event_end_date']);
			
			//
			$postData['is_active'] = 1;
			$postData['display_language_id'] = $this->language_id;
			
			//
			
			$is_updated = 0;
			if($postData['event_id'] && $postData['event_id']>0)
			{
				$event_id = $postData['event_id'];
				$this->general->update($postData,array('event_id'=>$event_id));
				$is_updated = 1;	
			}
			else {
				$event_id = $this->general->save($postData);
			}
			if($event_id)
			{
				$posterPath = 'event_flyers';
			
				if (!file_exists($posterPath.'/'.$event_id)) {
				    mkdir($posterPath.'/'.$event_id, 0777, true);
				}
				$source = $posterPath.'/tmp/'.$this->session->userdata('flayer_img');					
				$destination = "";
				
				if($this->session->userdata('flayer_img') && file_exists($source))
				{
					$destination = $posterPath.'/'.$event_id.'/'.$this->session->userdata('flayer_img');
					
					copy($source,$destination);
					unlink($source);
				}

				if($destination)
				{
					$updateEventData['poster_url'] = base_url($destination);				
					$this->session->unset_userdata('flayer_img');								
					$this->general->update($updateEventData,array('event_id'=>$event_id));
						
				}
				if($is_updated)
				{
					$this->session->set_flashdata('success_msg',translate_phrase('Event updated successfully.'));
				}
				else
				{
					$this->session->set_flashdata('success_msg',translate_phrase('Event created successfully.'));
				}				
				redirect($this->admin_url.'/create/'.$event_id);						
					
			}
			
		}
		
		$city_select = "select city.city_id, city.description from city WHERE display_language_id = $this->language_id AND city.city_id IN ($city_ids) ORDER BY city.description";		
		$data['cities'] = $this -> general -> sql_query($city_select);
		
		//Current Lived in
		$fields = array('v.venue_id', 'v.name', 'crncy.currency_id', 'crncy.description as currency_description', );
		$from = 'venue as v';
		$joins = array(
					'neighborhood as n' => array('v.neighborhood_id = n.neighborhood_id', 'INNER'), 
					'city as c' => array('c.city_id = n.city_id', 'LEFT'),
					'province as prvnce' => array('c.province_id = prvnce.province_id', 'LEFT'), 
					'country as cntry' => array('prvnce.country_id = cntry.country_id', 'LEFT'), 
					'currency as crncy' => array('cntry.currency_id = crncy.currency_id ', 'LEFT')
				);

		
		$this->db->where_in('c.city_id',explode(',', $city_ids));
		$where['c.display_language_id'] = $this->language_id;
		$where['prvnce.display_language_id'] = $this->language_id;
		$where['cntry.display_language_id'] = $this->language_id;
		$where['crncy.display_language_id'] = $this->language_id;

		$data['venues'] = $this -> general -> multijoins_groupby($fields, $from, $joins, $where, 'v.name asc','array','v.venue_id');
		
		
		$data['page_name'] = 'manage_event_details';
		//echo "<pre>";print_r($data);exit;
		
		$this -> load -> view($this->template, $data);
	}
	
	public function add_new_partner($event_id="")
	{
		$city_ids = $this -> session -> userdata['event_admin_logged_in']['city_ids'];		
		$city_select = "select city.city_id, city.description from city WHERE display_language_id = $this->language_id AND city.city_id IN ($city_ids) ORDER BY city.description";
		$data['cities'] = $this -> general -> sql_query($city_select);
		$cities = array();
		
		if($postData = $this->input->post())
		{
			$event_id = $postData['event_id'];
			$this->general->set_table('partner');
			unset($postData['event_id']);
			$this->general->save($postData);
			$this->session->set_flashdata('success_msg',translate_phrase('Event Partner added successfully.'));
			redirect($this->admin_url.'/create/'.$event_id.'#partner');	
		}
		foreach($data['cities']  as $city)
		{
			$cities[$city['city_id']] = $city['description'];
		}
		$data['event_id'] = $event_id;
		
		$data['cities'] = $cities;		
		$data['languages'] = $this->_getLanguages();
		$data['page_title'] = translate_phrase('Add New Partner');
		$data['page_name'] = 'add_new_partner';
		$this -> load -> view($this->template, $data);
	}
	
	private function _getLanguages()
	{
		$this->general->set_table('display_language');
		$languages = $this->general->get("",array(),array('view_order'=>'asc'));
		
		$languages_datas = array();
		if($languages)
		{
			foreach($languages as $lang)
			{
				$languages_datas[$lang['display_language_id']] = $lang['description'];
			}
		}
		return $languages_datas;
	}
	public function uploadFlayer()
	{
		$this -> load -> library('upload');
		//First store photos in temp
		$config['upload_path'] = './event_flyers/tmp/';
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		$this -> upload -> initialize($config);
		
		if ($this -> upload -> do_upload('fileToUpload')) {
			$image_data = $this -> upload -> data();
			$data['image'] = $image_data['file_name'];
			$this->session->set_userdata('flayer_img',$data['image']);
			$data['url'] = base_url('event_flyers/tmp/'.$data['image']);			
			$data['success'] = 1;
		} else {
			$data['msg'] = $this->upload->display_errors('','');
			$data['success'] = 0;
		}
		echo json_encode($data);
	}
}
?>
