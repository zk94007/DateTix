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
		$this->general->set_table('website');		
		$website_data = $this->general->get();
		
		//echo "<pre>";print_r($website_data);exit;
		
		
		//echo 'URL: ==> '.get_assets('css_url',base_url().'assets/css/stylesheet.css');
		
		$website_data = $this->model_user->get_website_by_user_email('dummy@test.com');
		
		//echo "<pre>";print_r($website_data);exit;
		
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
	/**
	 * Save user answer in database and store in session so we can calculate user test result 
	 * @access public
	 * @author Rajnish Savaliya
	 */
	public function submit_score()
	{
		
		if($user_answers = $this->input->post('answers'))
		{
			$this->general->set_table('user_test_answer');
			$save_user_answers = array();
			$ids = array();
			foreach($user_answers as $answer)
			{
				$temp = array();
				$user_data = explode('-', $answer);
				 	
				
				$temp['test_question_id'] = $user_data['0'];
				$temp['test_answer_id'] = $user_data['1'];
				$temp['user_ip'] = $this->input->ip_address();
				
				
				$temp['answer_time'] = SQL_DATETIME;
				$temp['user_id'] = $this -> user_id?$this -> user_id:null;
				//$save_user_answers[] = $temp;
				
				$this->general->simple_save($temp);
				$ids[] = $temp['test_answer_id'];
			}
			if($ids)
			{
				$this->session->set_userdata('user_test_answer_ids',$ids);	
			}
			
			
			//$this->general->saveBatch($save_user_answers);
		}
		
		$data['page_name'] = 'test/result';		
		$this -> load -> view('template/simple', $data);
	}
	
	/**
	 * Save user answer in database and store in session so we can calculate user test result 
	 * @access public
	 * @author Rajnish Savaliya
	 */
	public function send_result()
	{
		
		if($email = $this->input->post('email'))
		{
			if($ids = $this->session->userdata('user_test_answer_ids'))
			{
				$sql = "SELECT SUM(answer_value) as total_score FROM (test_answer) WHERE test_answer_id IN (".implode(',', $ids).")";
				$result_data = $this -> general -> sql_query($sql);
				$test_sum = $result_data['0']['total_score'];
				
				
				if ($test_sum < 10) {
				  $test_result = translate_phrase("Your personality type is INTJ"); 
				}
				else if ($test_sum >=10 && $test_sum < 20) {
				  $test_result  = translate_phrase("Your personality type is ENTJ"); 
				}
				else if ($test_sum >20) {
				  $test_result  = translate_phrase("Your personality type is INFJ");
				}

				
				
				$subject = translate_phrase("Test Result");								
				$email_data['email_title'] = '';
				$email_data['btn_link'] = base_url() . 'apply';
				$email_data['btn_text'] = translate_phrase("Sign Up for Free");
				$email_data['email_title'] = $test_result;
				
				$email_data['email_content'] = translate_phrase('DateTix is an online matchmaking platform that introduces single professionals to each other for the purpose of developing long-term romantic relationships. We offer a highly personalized and curated dating experience. Using our proprietary matchmaking algorithm, we introduce our members to their most relevant matches based on criteria they deem most important, including personality, education, career and physical traits. To ensure the authenticity and quality of our member base, we use a rigorous member screening process that includes both automated data verification and manual profile review.');
				$email_template = $this -> load -> view('email/common', $email_data, true);
				if($this -> datetix -> mail_to_user($email, $subject, $email_template))
				{
					$msg = translate_phrase("Your test results have been emailed to ").$email.translate_phrase(". Please check your email now!");				
					$this -> session -> set_flashdata('success_msg', $msg);	
				}
				else {
					$this -> session -> set_flashdata('error_msg', translate_phrase("Sorry, Error occured while sending email."));	
				}
			}
			else {
				$this -> session -> set_flashdata('error_msg', translate_phrase("Sorry, No answer found."));	
			}
			
		}
		else {
			$this -> session -> set_flashdata('error_msg', translate_phrase("Please enter your email address."));
		}

		redirect('/test/submit_score');
	}
}
?>
