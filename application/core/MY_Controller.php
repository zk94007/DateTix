<?php 
/**
 * General controller
 * @author Rajnish
 *
 */
class MY_Controller extends CI_Controller {


	/**
	 * Construieste obiectul $user
	 * @return unknown_type
	 */
	function __construct() {
		parent::__construct();
		
		if ($user_id = $this -> session -> userdata('user_id')) {
			
			$this -> model_user -> update_user($user_id, array('last_active_time' => SQL_DATETIME));
			$this -> user_id = $this -> session -> userdata('user_id');
			
			//restric log on ajax call.	
			if (!$this -> input -> is_ajax_request()) {
					
				$this->general->set_table('user_log');
				$user_log_data['user_id'] = $user_id;
				$user_log_data['log_time'] = SQL_DATETIME;
				 
				$user_log_data['url'] = current_url().query_string();
				$user_log_data['ip'] = $this->input->ip_address();
				$this->general->save($user_log_data);
				unset($user_log_data);
			}
			
		}
	}
}
?>
