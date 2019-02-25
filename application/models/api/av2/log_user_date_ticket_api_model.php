<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('DAILY_SIGN_IN_REWARD', 'Daily Sign In Reward');

class Log_User_Date_Ticket_api_model extends CI_Model {

	public function insert_log_user_date_ticket($insert_array) {

		$this -> db -> insert('log_user_date_ticket', $insert_array);
		return $this -> db -> insert_id();
	}

	public function insert_daily_sign_in_reward_log($user_id, $num_date_tickets, $date_time) {

		$insert_array['user_id'] = $user_id;
		$insert_array['num_date_tickets'] = $num_date_tickets;
		$insert_array['transaction_time'] = $date_time;
		$insert_array['description'] = DAILY_SIGN_IN_REWARD;

		$this -> db -> insert('log_user_date_ticket', $insert_array);
		return $this -> db -> insert_id();
	}

	public function check_daily_sign_in_reward($user_id, $date_time) {

		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('description', DAILY_SIGN_IN_REWARD);
		$this -> db -> where("DATE(transaction_time) = DATE('{$date_time}')", NULL, FALSE);
		$result = $this -> db -> get('log_user_date_ticket');

		return $result -> num_rows() > 0 ? TRUE : FALSE;
	}
}
