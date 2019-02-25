<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class People extends MY_Controller {

	var $language_id = '1';
	public function __construct() {
		parent::__construct();
		
		$this -> load -> model('model_date');
		$this -> load -> model('model_people');
				
		if ($user_id = $this -> session -> userdata('user_id')) {

			$this -> language_id = $this -> session -> userdata('sess_language_id');
			$this -> user_id = $this -> session -> userdata('user_id');

			if (!$this -> session -> userdata('sess_city_id')) {
				$this -> session -> set_userdata('sess_city_id', $this -> config -> item('default_city'));
			}

			$this -> city_id = $this -> session -> userdata('sess_city_id');
		}
		if (!$this -> session -> userdata('user_id')) {
			$requested_url = str_replace(base_url(), '', current_url());
			redirect(url_city_name() . '/signin.html?return_url=/' . $requested_url);
		}
	}

	public function index() {

		//temporery
		$this -> find();
	}

	private function _get_user_preference() {
		return $this->model_people->get_user_preference($this->user_id);
	}

	public function find() {

		if ($preferences = $this -> input -> post()) {
			$update_user_data['want_age_range_lower'] = $preferences['want_age_range_lower'];
			$update_user_data['want_age_range_upper'] = $preferences['want_age_range_upper'];
			//print_r($update_user_data);exit;
			$this -> model_user -> update_user($this -> user_id, $update_user_data);

			$this -> model_user -> genericInsert($this -> user_id, 'gender', $preferences['user_want_gender']);
			$this -> model_user -> genericInsert($this -> user_id, 'ethnicity', $preferences['ethnicityPreference']);
		}

		$user_info = $this -> _get_user_preference();
		$data['condition_str'] = $this->model_people->formate_prefered_match_cluase($user_info);
		
		$offset = 0;
		$data['people'] = array();//$this -> getPeople($condtion_str, $offset);
		$data['user_data'] = $user_info;

		$data['page_title'] = translate_phrase('Find Date');
		$data['page_name'] = 'user/people/people_list';
		$this -> load -> view('template/editProfileTemplate', $data);
	}

	public function filter_people() {
		$data['user_info'] = $this -> _get_user_preference();

		$this -> load -> view('user/people/include/filter_popup', $data);
	}

	public function get_next_people() {
		$post = $this -> input -> post();
		if (isset($post['condition_str']) && $post['condition_str']) {
			$condtion_str = $post['condition_str'];
		} else {
			$condtion_str = " 1 = 1";
		}
		$offset = $post['offset'];

		$data['people'] = $this -> getPeople($condtion_str, $offset);
		$this->general->set_table('user');
		$user_select = "user_id,first_name,last_name, num_date_tix,want_age_range_lower,want_age_range_upper,facebook_id";
		$user_data = $this -> general -> get($user_select, array('user_id' => $this -> user_id));
		$user_info = $user_data['0'];

		$data['user_data'] = $user_info;
		echo $this -> load -> view('user/people/include/user_info', $data);
	}

	private function getPeople($condtion_str, $offset) {
		
		$people = $this->model_people->get_prefered_matche_profiles($condtion_str, $offset);
		if ($people) {
			foreach ($people as $key => $user) {
				$people[$key]['user_photos'] = $this -> model_user -> get_photos($user['user_id'], 'profile');
			}
		}
		
		return $people;
	}

	public function undo_user_preference() {
		if ($post = $this -> input -> post()) {
			$condition['target_user_id'] = $post['target_user_id'];
			$condition['user_id'] = $this -> user_id;

			$insertArrayDecision['decision'] = NULL;
			$insertArrayDecision['undo_time'] = SQL_DATETIME;

			$this -> general -> set_table('user_decision');
			$this -> general -> update($insertArrayDecision, $condition);

			$this -> session -> set_userdata('undo_cancel_user_id', 0);
			$condtion_str = 'user_id = ' . $post['target_user_id'];
			$offset = 0;
			$data['people'] = $this -> getPeople($condtion_str, $offset);

			echo $this -> load -> view('user/people/include/user_info', $data);
		}
	}

	public function save_preference() {
		$response['type'] = 'error';
		$response['msg'] = 'Error occured. please try again';

		if ($post = $this -> input -> post()) {
			$decision = $post['decision'];
			$insertArrayDecision['target_user_id'] = $post['target_user_id'];

			if ($decision == 0) {
				$this -> session -> set_userdata('undo_cancel_user_id', $insertArrayDecision['target_user_id']);
			}

			$insertArrayDecision['user_id'] = $this -> user_id;
			$insertArrayDecision['decision'] = $decision;
			$insertArrayDecision['decision_time'] = SQL_DATETIME;

			$response['type'] = 'success';
			$response['msg'] = translate_phrase('User Preference has been saved.');
			$this -> general -> set_table('user_decision');
			$this -> general -> save($insertArrayDecision);

		}
		echo json_encode($response);
	}

	public function get_upcoming_date() {
		$post = $this -> input -> post();
		$userid = $this -> user_id;
		$invite_user_id = $post['target_user_id'];
		$decision = $post['decision'];

		// get invited user id
		$inviteUserDetail = $this -> db -> query("select first_name,last_name from user where user_id='" . $invite_user_id . "'") -> row_array();
		
		$fields = array('d.date_id as date_id, d.requested_user_id as requested_user_id, d.date_time as date_time,
		                       dt.description as date_type,
		                       rt.description as intention_type,
		                       m.merchant_id as mid, m.name, m.address, m.phone_number, m.website_url,m.review_url,
		                       rt.num_date_tix as relationship_num_date_tix');					
		                       
		$from = 'date as d';
		$joins = array(
				'date_type as dt' => array('dt.date_type_id = d.date_type_id', 'left'), 
				'relationship_type as rt' => array('rt.relationship_type_id = d.date_intention_id', 'left'), 
				'merchant as m' => array('m.merchant_id = d.merchant_id', 'left')
		);
		
		$where['dt.display_language_id'] = $this -> language_id;
		$where['rt.display_language_id'] = $this -> language_id;		
		$where['d.completed_step >='] = REQUIRED_DATE_COMPLETED_STEP;		
		$where['d.date_time >='] = SQL_DATETIME;
		$where['d.status !='] = '-1';			
		$where['d.requested_user_id'] = $this->user_id;			
		$order_by = 'd.date_time asc';
		
		$this -> db -> group_by('d.date_id');
		$user_dates  = $this -> general -> multijoins_arr($fields, $from, $joins, $where,"",$order_by);
		
		//echo $this->db->last_query();
		$date_results = array();
		if($user_dates)
		{
			foreach ($user_dates as $key => $date_info) {
				$photo_user_id = 0;
				
					
				$date_info['hosted_by_name'] = 'You';
				
				$this -> general -> set_table('date_decision');
				if ($info = $this -> general -> get("count(date_decision_id) as total_views", array('date_id' => $date_info['date_id']))) {
					$date_info['total_views'] = $info['0']['total_views'];
				}
				$this -> general -> set_table('date_applicant');
				if ($info = $this -> general -> get("count(date_applicant_id) as total_applications", array('date_id' => $date_info['date_id']))) {
					$date_info['total_applications'] = $info['0']['total_applications'];
				}
				$date_results[$key] = $date_info;
			}
		}
		
		// user upcoming dates list
		/*$this -> db -> select('d.date_id as date_id, d.requested_user_id as requested_user_id, d.date_time as date_time, 
                                da.applicant_user_id as applicant_user_id,
                                dt.description as date_type,
                                rt.description as intention_type,
                                dp.description as date_payer,
                                g.description as gender,
                                e.description as ethnicity,
                                m.merchant_id as mid, m.name, m.address, m.phone_number, m.website_url,m.review_url,
                                rt.num_date_tix as relationship_num_date_tix,
                                mb.num_date_tix as budget_num_date_tix,mb.description as venue');
		$this -> db -> join('date_applicant as da', 'da.date_id = d.date_id', 'left');
		$this -> db -> join('date_type as dt', 'dt.date_type_id = d.date_type_id', 'left');
		$this -> db -> join('relationship_type as rt', 'rt.relationship_type_id = d.date_intention_id', 'left');
		$this -> db -> join('date_payer as dp', 'dp.date_payer_id = d.date_payer_id', 'left');
		$this -> db -> join('gender as g', 'FIND_IN_SET(g.gender_id,d.date_gender_ids)', 'left');
		$this -> db -> join('ethnicity as e', 'FIND_IN_SET(e.ethnicity_id,d.date_ethnicity_ids)', 'left');
		$this -> db -> join('merchant as m', 'm.merchant_id = d.merchant_id', 'left');
		$this -> db -> join('budget as mb', 'mb.budget_id = m.budget_id', 'left');
		$this -> db -> where('d.completed_step >=', 5);
		$this -> db -> where('d.date_time >=', date('Y-m-d H:i:s'));
		$this -> db -> where('d.status !=', '-1');
		$this -> db -> where('d.requested_user_id = "' . $userid . '"');
		$this -> db -> order_by('d.date_time', 'asc');

		$this -> db -> group_by('d.date_id');
		$date_results = array();
		$result = $this -> db -> get('date as d');
		
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
			
			
			foreach ($results as $key => $date_info) {
				$photo_user_id = 0;
				if ($date_info['requested_user_id'] != $userid) {
					$photo_user_id = $date_info['requested_user_id'];
					$this -> general -> set_table('user');
					if ($user_info = $this -> general -> get("first_name,last_name", array('user_id' => $date_info['requested_user_id']))) {
						$date_info['hosted_by_name'] = $user_info['0']['first_name'];
					} else {
						$date_info['hosted_by_name'] = 'Unknown';
					}
				} else {
					$photo_user_id = $date_info['applicant_user_id'];
					$date_info['hosted_by_name'] = 'You';
				}

				$date_info['user_photos'] = $this -> get_current_primary_photo($date_info['requested_user_id']);

				$this -> general -> set_table('date_decision');
				if ($info = $this -> general -> get("count(date_decision_id) as total_views", array('date_id' => $date_info['date_id']))) {
					$date_info['total_views'] = $info['0']['total_views'];
				}
				$this -> general -> set_table('date_applicant');
				if ($info = $this -> general -> get("count(date_applicant_id) as total_applications", array('date_id' => $date_info['date_id']))) {
					$date_info['total_applications'] = $info['0']['total_applications'];
				}
				$date_results[$key] = $date_info;
			}
		}
*/	
		$data['invite_user_data'] = $inviteUserDetail;
		$data['upcomingDates'] = $date_results;
		$data['invite_user_id'] = $invite_user_id;
		$data['decision'] = 1;

		$inviteUserAppliedDates = $this -> db -> query("select group_concat(date_id) as date_id from date_applicant where applicant_user_id='" . $invite_user_id . "'") -> row_array();
		$data['applied_dates'] = $inviteUserAppliedDates;

		echo $this -> load -> view('user/people/include/upcoming_date', $data);

	}

	public function get_current_primary_photo($user_id) {
		$this -> db -> select('*');
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('set_primary', '1');
		$r = "";
		$result = $this -> db -> get('user_photo');
		if ($result -> num_rows() > 0) {
			$row = $result -> row_array();
			$r = $row['user_photo_id'];
		} else {
			$row = array();
		}
		return $row;
	}

	public function invite_user() {
		$response['type'] = 'error';
		$response['msg'] = 'Error occured. please try again';

		if ($post = $this -> input -> post()) {
			$response['type'] = 'success';			
			if($post['target_user_id'] != $this->user_id)
			{
				$response['msg'] = $this->model_date->send_date_invitation($post['target_user_id'],$post['date_id'],$post['decision']);	
			}
			else {
				$response['msg'] = '<b class="red">'.translate_phrase("You Can't invite yourself.").'</b>';
			}
		}
		echo json_encode($response);
	}

}
