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
		$data['seleccted_city_id'] = current_city_id();
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
		elseif($data['seleccted_city_id'])
		{
			$city_ids = $data['seleccted_city_id'];	
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
		$data['page_title'] = translate_phrase('Create Event');
		$event_venue_id = 0;
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
			
			if($event_data  = $this -> general -> multijoins_groupby($fields, $from, $joins, $where,'e.event_start_time desc','array','e.event_id'))
			{
				$data = $event_data['0'];
				$event_venue_id = $data['venue_id'];
				$data['page_title'] = translate_phrase('Edit Event');				
				if($data['event_end_time'] < SQL_DATETIME)
				{
					$data['disabled_tab_msg'] = translate_phrase('You can not modify event details.');
					$data['is_past_event'] = "1" ;
				}
				else {
					$data['page_title'] .= ' - '.$data['event_name'];
				}
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
			
			//
			
			$is_updated = 0;
			if($postData['event_id'] && $postData['event_id']>0)
			{
				$event_id = $postData['event_id'];
				unset($postData['event_id']);
				
				$condition['event_id'] = $event_id;
				$condition['event_id'] = $postData['display_language_id'];				
				unset($postData['display_language_id']);
				
				$this->general->update($postData,$condition);
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
		
		//Load Event Form Data : Venues & Cities
		$eventTabData = $this->_getEventTabData($event_venue_id);
		$data = array_merge($data,$eventTabData);		
		if(isset($data['cities']) && $data['cities'])
		{
			foreach($data['cities']  as $city)
			{
				$cities[$city['city_id']] = $city['description'];
			}
			$data['cities'] = $cities;	
		}
		//Load Partner Tab Data : Venues & Cities
		$eventPartnerData = $this->_getPartnerTabData($event_id);
		$data = array_merge($data,$eventPartnerData);
		
		//Load Prepaid List Data
		$eventPrepaidListData = $this->_getPrepaidListTabData($event_id);
		$data = array_merge($data,$eventPrepaidListData);
		
		//Load Event Photos
		$eventPhotoData = $this->_getPhotoTabData($event_id);
		$data = array_merge($data,$eventPhotoData);
		
		$data['page_name'] = 'manage_event_details';
		
		$this -> load -> view($this->template, $data);
	}
	public function upload_event_photos($event_id)
	{
		$this -> load -> library('upload');
		
		$event_photo_path = 'event_photos/'.$event_id;
		if (!file_exists($event_photo_path)) {
		    mkdir($event_photo_path, 0777, true);
		}
		
		$event_photo_thumb_path = $event_photo_path .'/thumbs';		
		if (!file_exists($event_photo_thumb_path )) {
		    mkdir($event_photo_thumb_path , 0777, true);
		}
		
		$config['upload_path'] = './'.$event_photo_path.'/';
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		
		$this -> upload -> initialize($config);
		
		if ($this -> upload -> do_upload('fileToUpload')) {
			$image_data = $this -> upload -> data();
			
			$data['image'] = $image_data['file_name'];
			
			$thumb_config = array(
		      'source_image' => $image_data['full_path'], //get original image
		      'new_image' => $event_photo_thumb_path, //save as new image //need to create thumbs first
		      'maintain_ratio' => true,
		      'width' => 200,
		      'height' => 200
		       
		    );
		    $this->load->library('image_lib', $thumb_config); //load library
		    $this->image_lib->resize(); //generating thumb
		    
			$saveData['event_id'] = $event_id;
			$saveData['photo'] = $data['image'];
			$saveData['event_admin_id'] = $city_ids = $this -> session -> userdata['event_admin_logged_in']['admin_event_id']; 
			
			$this->general->set_table('event_photos');
			$data['event_photo_id'] = $this->general->save($saveData);
			
			$data['url'] = base_url('event_photos/'.$event_id.'/'.$data['image']);			
			
			$data['success'] = 1;
		} else {
			$data['msg'] = $this->upload->display_errors('','');
			$data['success'] = 0;
		}
		echo json_encode($data);
	}
	private function _getPhotoTabData($event_id)
	{
		
		$this->general->set_table('event_photos');
		
		$condition['event_id'] = $event_id;
		$condition['event_admin_id'] = $city_ids = $this -> session -> userdata['event_admin_logged_in']['admin_event_id']; 
		$order_by['view_order'] = 'asc';
		$data['event_photos'] = $this->general->get("",$condition,$order_by);
		return $data;
	}
	
	private function _getEventTabData($event_venue_id)
	{
		$city_ids = $this -> session -> userdata['event_admin_logged_in']['city_ids'];
		$city_select = "select city.city_id, city.description from city WHERE display_language_id = $this->language_id AND city.city_id IN ($city_ids) ORDER BY city.description";		
		$data['cities'] = $this -> general -> sql_query($city_select);		
		
		if($event_venue_id)
		{
			//Current Lived in
			$fields = array('c.city_id');
			$from = 'venue as v';
			$joins = array(
						'neighborhood as n' => array('v.neighborhood_id = n.neighborhood_id', 'INNER'), 
						'city as c' => array('c.city_id = n.city_id', 'LEFT')
					);
			$where['c.display_language_id'] = $this->language_id;
			$where['n.display_language_id'] = $this->language_id;
			$where['v.venue_id'] = $event_venue_id;
			
			if($event_venue = $this -> general -> multijoins_groupby($fields, $from, $joins, $where, 'v.name asc','array','v.venue_id'))
			{
				$city_ids = $event_venue['0']['city_id'];
				$data['city_id'] = $city_ids;				
			}
			unset($where);	
		}
		
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
		
		return $data;
	}
	
	public function getVenuesByCityId($city_id="")
	{
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
		$where['c.city_id'] = $city_id;
		$where['c.display_language_id'] = $this->language_id;
		$where['prvnce.display_language_id'] = $this->language_id;
		$where['cntry.display_language_id'] = $this->language_id;
		$where['crncy.display_language_id'] = $this->language_id;
		$data['venues'] = $this -> general -> multijoins_groupby($fields, $from, $joins, $where, 'v.name asc','array','v.venue_id');
		
		echo json_encode($data);
	}
	private function _getPrepaidListTabData($event_id="")
	{
		$data = array();
		if($event_id)
		{
			
			//Current Lived in
			$fields = array('event_order.*', 'crncy.description as currency_description', );
			$from = 'event_order';
			$joins = array(
				'currency as crncy' => array('event_order.currency_id = crncy.currency_id ', 'LEFT')
			);
	
			$where['crncy.display_language_id'] = $this->language_id;
			$where['event_order.event_id'] = $event_id;
			$data['event_orders'] = $this -> general -> multijoins_groupby($fields, $from, $joins, $where, 'event_order.order_time desc','array');
		
		
			if($data['event_orders'])
			{
				foreach($data['event_orders']  as $key=>$user_order)
				{
					$data['event_orders'][$key]['gender'] = '';
					$data['event_orders'][$key]['birth_date'] = '';
					
					
					if($user_order['paid_by_user_id'])
					{
						$this->general->set_table('user');
						if($user_info = $this->general->get("first_name,last_name,mobile_phone_number,gender_id,birth_date",array('user_id'=>$user_order['paid_by_user_id'])))
						{
							$data['event_orders'][$key]['paid_by_name']	= $user_info['0']['first_name'].' '.$user_info['0']['last_name'];							
							$data['event_orders'][$key]['paid_by_mobile_phone_number']	= $user_info['0']['mobile_phone_number'];					
							$data['event_orders'][$key]['birth_date']	= $user_info['0']['birth_date'];		
							
							//@TODO : Write a static condition for male female - ingore db call for optimization..										
							$data['event_orders'][$key]['gender']	= $user_info['0']['gender_id']==1?"M":"F";
						}
						
						//If not email address then fetch email from user_email table.
						if($data['event_orders'][$key]['paid_by_email'])
						{
							$this->general->set_table('user_email');
							if($user_info = $this->general->get("email_address",array('user_id'=>$user_order['paid_by_user_id'])))
							{
								$data['event_orders'][$key]['paid_by_email'] = $user_info['0']['email_address'];
							}	
						}
					}					
				}
			}
			//echo "<pre>"; print_r($data['event_orders']);exit;
			
		}
		return $data;
	}
	
	private function _getPartnerTabData($event_id)
	{
		$this->general->set_table('event_url');
		$data['event_urls'] = $this->general->get("",array('event_id'=>$event_id),array('event_url_id'=>'asc'));
		
		$data['languages'] = $this->_getLanguages();		
		$city_ids = $this -> session -> userdata['event_admin_logged_in']['city_ids'];
		$sql = "SELECT * FROM partner 
					WHERE default_language_id = $this->language_id AND 
					city_id IN ($city_ids) 
					ORDER BY name";
		$data['partners'] = $this -> general -> sql_query($sql);		
		return $data;
	}
	public function save_event_partner()
	{
		if($postData = $this->input->post())
		{
			$event_id = $postData['event_id'];
			$event_urls = $postData['event_url'];
			
			//@TODO: Ads id is not inserted in event_url table :( [Need to discuss]			
			$is_record_changed = false;			
			if($event_urls && $event_id)
			{
				
				$this->general->set_table('event_url');
				foreach($event_urls as $event_data)
				{
					if(isset($event_data['event_url_id']) && $event_data['event_url_id'])
					{
						$event_url_id = $event_data['event_url_id'];
						unset($event_data['event_url_id']);
						$flag = $this->general->update($event_data,array('event_url_id'=>$event_url_id));						
						if($flag)
							$is_record_changed = true;
					}
					else {
						$event_data['event_id'] = $event_id;
						$id = $this->general->save($event_data);
						if($id)
							$is_record_changed = true;
					}					
				}
			}
			
			if($is_record_changed)
			{
				$this->session->set_flashdata('success_msg',translate_phrase('Event URL is saved successfully.'));
			}
			else
			{
				$this->session->set_flashdata('error_msg',translate_phrase('Error occured while saving db, Please try again.'));
			}
			redirect($this->admin_url.'/create/'.$event_id.'#partner');
		}
	}
	/* Delete Event Partner */
	public function delete_event_partner()
	{
		$response['type'] = 'error';
		$response['msg'] = translate_phrase('Error occured while deleting record Please try again');
		if($event_url_id = $this->input->post('event_url_id'))
		{
			$this->general->set_table('event_url');
			if($this->general->delete(array('event_url_id'=>$event_url_id)))
			{
				$response['type'] = 'success';
				$response['msg'] = translate_phrase('Record is deleted.');	
			}
		}
		
		echo json_encode($response);
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
