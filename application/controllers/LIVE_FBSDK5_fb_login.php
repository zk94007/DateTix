<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Fb_login extends MY_Controller{

	public function __construct(){

		parent::__construct();
		//for Facebook Login
		//parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );
		$this->load->library('facebook_four');
		
		$this->load->model("general_model",'general');
		$this->load->model('model_user');
	}

	/**
	 * login Associative Function
	 * @param User type
	 * @access public
	 * @return true or false (redirect to view)
	 * @author  by Rajnish
	 */
	public function facebook()
	{
		
		if($this->facebook_four->is_login())
		{
			$userId = $this->facebook_four->get_user_fb_id();
			
			$user = $this->model_user->getByFacebookId($userId);
			$fb_user = $this->facebook_four->getUser();
			if ($user  == false) {

				$albumprofile = '';
				$albumsfb = $this->facebook_four->api('/me/albums?fields=id,type');
				foreach ($albumsfb['data'] as $albumfb) {
					if ($albumfb['type']=='profile'){
						$albumprofile = $albumfb['id'];
						break;
					};
				}
				if(!empty($albumprofile))
				{
					$photoprofilefb = $this->facebook_four->api('/'.$albumprofile.'/photos?limit='.FB_PHOTO_LIMIT);

					if(isset($photoprofilefb['data']))
					$fb_user['profile_album'] = $photoprofilefb['data'];
				}
					
				$data['user_exsist'] = 0;
			}
			else
			{
				$this->session->set_userdata('is_return_apply', 1);
				$data['step']        = (int)$user->completed_application_step;
				$data['user_exsist'] = 1;
				$data['approved_date'] = $user->approved_date;

				if($user->last_display_language_id != '0')
				{
					$this->load->model('model_translation');
					$this->model_translation->setLang($user->last_display_language_id);
				}

			}
			if(isset($fb_user['email']) && $fb_user['email'])
			{
				$this->model_user->setFacebookData($fb_user, $this->input->get('invite_id'));	
				
				if($this->session->userdata('is_email_exist'))
				{
					$data['user_exsist'] = '-1';
					$data['msg'] = $fb_user['email'].translate_phrase(' already exists, please Sign In Using Email to access your account.');
	
					$this->session->set_userdata('is_email_exist','');
					$this->session->set_userdata('fb_login_error',$data['msg']);
				}
			}
			else{
				$data['user_exsist'] = '-1';
				$data['msg'] = translate_phrase('You must have at least one verified email address associated with your Facebook account in order to apply to DateTix using Facebook.');
				$this->session->set_userdata('fb_login_error',$data['msg']);
			}			
		}
		else {
			$data['url'] = $this->facebook_four->login_url();								
		}
		echo json_encode($data);
	}

	public function login_with_email()
	{
		//destroy all sessions before signup
		$this -> datetix -> destroy_current_session();
		
		$data['page_name']  = 'user/sign_in_using_email';
		$data['page_title'] = 'Login';
		$this->load->view('template/default',$data);
	}

	public function bkp_profile_photos()
	{
		$albumsfb = $this->facebook_four->api('/me/albums?fields=id,type');
		foreach ($albumsfb['data'] as $albumfb) {
			if ($albumfb['type']=='profile'){
				$albumprofile = $albumfb['id'];
				break;
			};
		}

		$photoprofilefb = $this->facebook_four->api('/'.$albumprofile.'/photos');
		echo "<pre>";print_r($photoprofilefb);exit;
	}

	public function fb_success()
	{
		if($fb_user = $this->facebook_four->getUser())
		{
			
			$userId = $fb_user['id'];
			
			if ($userId && ($user = $this->model_user->getByFacebookId($userId)) == false) {
				$step = 1;
	
				$albumsfb = $this->facebook_four->api('/me/albums');
				$albumprofile = '';
	
				foreach ($albumsfb['data'] as $albumfb) {
					if ($albumfb['type']=='profile'){
						$albumprofile = $albumfb['id'];
						break;
					};
				}
	
				if(!empty($albumprofile))
					$photoprofilefb = $this->facebook_four->api('/'.$albumprofile.'/photos?limit='.FB_PHOTO_LIMIT);
	
				if(isset($photoprofilefb['data']))
					$fb_user['profile_album'] = $photoprofilefb['data'];
			}
			else
			{
				$this->session->set_userdata('is_return_apply', 1);
				$step        = $user->completed_application_step?$user->completed_application_step:1;
			}
			
			$this->model_user->setFacebookData($fb_user, $this->input->get('invite_id'));
			if($url = $this->input->cookie('datetix_redirect_url'))
			{
				delete_cookie('datetix_redirect_url');
				redirect($url);
			}
			else
			{
				$this -> model_user -> is_current_signup_process($user->user_id);
			}
		}
		else {
			redirect('apply');
		}
				
	}

	public function import_data($redirect_page = 'edit-profile')
	{
		$userId = $this->facebook_four->get_user_fb_id();
		
		if(!$userId)
		{
			$redirect_url = base_url().'fb_login/import_data/'.$redirect_page;
			$login_url = $this->facebook_four->login_url($redirect_url);
			redirect($login_url);
		}
		else {
			
			$userFbDataDb = $this->model_user->getByFacebookId($userId);
			if ($userFbDataDb == false) {
				$fb_user = $this->facebook_four->getUser();			
				$user_data['first_name']  = $fb_user['first_name'];
				$user_data['last_name']  = $fb_user['last_name'];
				$user_data['facebook_id']  = $fb_user['id'];
				
				$this -> session -> set_flashdata('edit_profile_msg', translate_phrase('Your account has been successfully connected to Facebook. Please simply click on the Facebook Sign In button in the future to sign in to your account.'));
				
				$albumsfb = $this->facebook_four->api('me/albums?fields=id,type');
				
				echo "<pre>";print_r($albumsfb );exit;
				
				if($albumsfb['data'])
				{
					$albumprofile = '';
					foreach ($albumsfb['data'] as $albumfb) {
						if ($albumfb['type']=='profile'){
							$albumprofile = $albumfb['id'];
							break;
						};
					}
	
					if(!empty($albumprofile))
					$photoprofilefb = $this->facebook_four->api('/'.$albumprofile.'/photos?limit='.FB_PHOTO_LIMIT);
	
					if(isset($photoprofilefb['data']))
						$fb_user['profile_album'] = $photoprofilefb['data'];
						
				}
				
				$language_id = $this->session->userdata('sess_language_id');
				$user_id = $this->session->userdata('user_id');
					
				$friends = $this -> facebook_four -> api('/me/friends');
				if (!empty($friends["data"])) {
					foreach ($friends["data"] as $friend) {
						$fb_friend_data["user_id"] = $user_id;
						$fb_friend_data["facebook_id"] = $friend["id"];
						$this -> general -> set_table('user_fb_friend');
						if (!$this -> general -> checkDuplicate($fb_friend_data)) {
							$this -> general -> save($fb_friend_data);
						}
					}
				}

				$fb_details['relationship_status'] = "";
				if($gender_id = $this->model_user->get_gender_id(trim($fb_user['gender'])))
				{
					$user_data['gender_id'] = $gender_id;
				}

				if($relationship_status_id = $this->model_user->get_relationship_status_id(trim($fb_user['relationship_status'])))
				{
					$user_data['relationship_status_id'] = $relationship_status_id;
				}

				// myself
				$fb_details['myself'] = '';
				if (isset($fb_user['bio'])) {
					$user_data['self_summary']  = trim($fb_user['bio']);
				}
				// dob

				if (isset($fb_user['birthday'])) {
					$dob = explode('/', $fb_user['birthday']);
					$user_data['birth_date'] = trim($dob[2]).'-'.trim($dob[0]).'-'.trim($dob[1]);
				}
				if (isset($fb_user['location'])) {
					$location = explode(',', $fb_user['location']['name'], 2);

					if($city_id = $this->model_user->get_city_id(trim($location[0]),$language_id))
					{
						$user_data['current_city_id'] = $city_id;
					}
				}

				if (isset($fb_user['hometown'])) {
					$location      = explode(',', $fb_user['hometown']['name'], 2);
					if(isset($location[1]))
					{
						if($city_id = $this->model_user->get_city_id(trim($location[0]),$language_id))
						{
							$user_data['birth_city_id'] = $city_id;
							$user_data['birth_city_name'] = '';
						}
						else {
							$user_data['birth_city_id'] = '';
							$user_data['birth_city_name'] = trim($location[0]);
								
						}
						if($born_country  = $this->model_user->get_country_id(trim($location[1]))){
							$user_data['birth_country_id'] = $born_country;
						}
					}
				}

				// want to date
				$fb_details['want_date'] = array();

				if (!empty($fb_user['interested_in'])) {
					for($i=0;$i<count($fb_user['interested_in']);$i++){
						$want_to_date   = $this->model_user->get_gender_id(trim($fb_user['interested_in'][$i]));
						$fb_details['want_date'][$i]= $want_to_date;
					}
					$this->model_user->clear_data($user_id,'user_want_gender');
					$this->model_user->insert_user_want_gender($user_id,$this->input->post('want_to_date'));

				}

				// education
				if (isset($fb_user['education'])) {
					foreach ($fb_user['education'] as $education) {
						if(isset($education['school']['name'])){
							$fb_edu = array(
													'school' => trim($education['school']['name']),
													'year'   => 'N.A',
													'degree' => 'N.A',
													'education_type'=>'N.A',
													'majors' => array(),
							);
							if (isset($education['type'])) {
								$fb_edu['education_type'] = trim($education['type']);
							}
					
							if (isset($education['year'])) {
								$fb_edu['year'] = trim($education['year']['name']);
							}

							if (isset($education['degree'])) {
								$fb_edu['degree'] = trim($education['degree']['name']);
							}

							if (isset($education['concentration'])) {
								foreach ($education['concentration'] as $major) {
									$fb_edu['majors'][] = trim($major['name']);
								}
							}

							$fb_details['education'][] = $fb_edu;
						}
					}
					$this->model_user->get_school_by_facebok($language_id,$user_id,$fb_details['education']);
				}

				// work
				$fb_details['work'] = array();
				if (isset($fb_user['work'])) {
					foreach ($fb_user['work'] as $work) {
						$fb_work = array(
							'company' => trim($work['employer']['name']),
							'job_title' => 'N.A',
							'year_from' => 'N.A',
							'year_to' => 'N.A');

						if (isset($work['position'])) {
							$fb_work['job_title'] = trim($work['position']['name']);
						}

						if (isset($work['location'])) {
							$fb_work['job_city_name'] = trim($work['location']['name']);
						}

						if (isset($work['start_date'])) {
							list($fb_work['year_from']) = explode('-', $work['start_date']);
						}

						if (isset($work['end_date'])) {
							list($fb_work['year_to']) = explode('-', $work['end_date']);
						}
						else
						{
							$fb_work['year_to'] = '9999';
						}
						$fb_details['work'][] = $fb_work;
					}
					$this->model_user->get_company_by_facebok($language_id,$user_id,$fb_details['work']);
				}
					
				
				$this->model_user->update_user($user_id,$user_data);
				//echo $this->db->last_query();exit;
					
					
				//Store Facebook Photos
				if(isset($fb_user['profile_album']))
				{
					if($user_id)
					{
						$pathToUpload = './user_photos/user_'.$user_id.'/';

						if ( ! file_exists($pathToUpload) )
						{
							$create = mkdir($pathToUpload, 0777);
						}

						foreach ($fb_user['profile_album']  as $key=>$img)
						{
							$file_name = $img['id'].'_profile_pic.jpg';
							if(!file_exists($pathToUpload.$file_name))
							{
								file_put_contents($pathToUpload.$file_name,file_get_contents($img['source']));
								$data   = array('user_id'=>$user_id,'photo'=>$file_name);
								$this->db->insert('user_photo',$data);
							}
							
							if($key == 0)
							{
								$last_id = $this->db->insert_id();
								if($last_id != 0)
								{
									//Remove Primary Photos
									$this->db->where(array('user_id'=>$user_id));
									$this->db->update('user_photo',array('set_primary'=>'0'));

									//set Primary Photos
									$this->db->where(array('user_photo_id'=>$last_id));
									$this->db->update('user_photo',array('set_primary'=>'1'));
								}
							}
						}
					}
				}
			}
			else{
				$this -> session -> set_flashdata('edit_profile_msg_error', translate_phrase('You have already signed up for a DateTix account using this Facebook account. Please click the Sign In button at the top of the page to sign in using Facebook.'));
			}
			
			$url = base_url().url_city_name().'/'.$redirect_page.'.html';
			redirect($url);
		}
	}
	
	public function logout()
	{
		$this->session->sess_destroy();
		redirect('apply');
	}
}
/* End of file register.php */
/* Location: ./application/controllers/register.php */
