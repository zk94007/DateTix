<?php
if (!defined('BASEPATH')) 	exit('No direct script access allowed'); 

class Test extends CI_Controller {
	var $language_id = '1';
	var $user_id = null;
	public function __construct() {
		parent::__construct();
		
		//Load Model
		$this -> load -> model('general_model', 'general');
		if ($this -> user_id = $this -> session -> userdata('user_id')) {
			$this -> model_user -> update_user($this -> user_id, array('last_active_time' => SQL_DATETIME));
			$this -> user_id = $this -> session -> userdata('user_id');			
		}
		$this -> language_id = $this -> session -> userdata('sess_language_id');
	}

	public function index()
	{
		$this->test_details('personality_type');
	}
	/**
	 * Fetch test details
	 * @access public
	 * @author Rajnish Savaliya
	 */
	public function test_details($test_name="") {
		
		
		$condtion['is_active'] = "1";
		$condition['url'] = $test_name;
		$condition['display_language_id'] = $this -> language_id;
		
		$this->general->set_table('test');
		if($test_details = $this->general->get('',$condtion,array(),1))
		{
			$test_details = $test_details['0'];
			
			$this->general->set_table('test_question');
			$test_condition['test_id'] = $test_details['test_id'];
			$test_condition['display_language_id'] = $this -> language_id;
		
			if($test_details['questions'] = $this->general->get('',$test_condition,array('view_order'=>'asc')))
			{
				$this->general->set_table('test_answer');
				$test_question_condition['display_language_id'] = $this -> language_id;
				foreach($test_details['questions'] as $key=>$question)
				{
					$test_question_condition['test_question_id'] = $question['test_question_id'];
					$test_details['questions'][$key]['answers'] = $this->general->get('',$test_question_condition,array('view_order'=>'asc'));
				}
			}			
		}
		
		//echo "<pre>";print_r($test_details);exit;
		
		$data['test_details'] = $test_details;		
		$data['page_name'] = 'test/details';		
		$this -> load -> view('template/simple', $data);
		
	}
	
	public function submit_score()
	{
		if($user_answers = $this->input->post('answers'))
		{
			$this->general->set_table('user_test_answer');
			$save_user_answers = array();
			foreach($user_answers as $answer)
			{
				$temp = array();
				$user_data = explode('-', $answer);
				 	
				$temp['user_id'] = $this -> user_id?$this -> user_id:null;
				$temp['user_ip'] = $this->input->ip_address();
				$temp['test_question_id'] = $user_data['0'];
				$temp['test_answer_id'] = $user_data['1'];
				$temp['answer_time'] = SQL_DATETIME;
				
				//$save_user_answers[] = $temp;
				
				$this->general->simple_save($temp);
				
			}
			
			
			//$this->general->saveBatch($save_user_answers);
		}
		
		$data['page_name'] = 'test/result';		
		$this -> load -> view('template/simple', $data);
	}
}
?>
