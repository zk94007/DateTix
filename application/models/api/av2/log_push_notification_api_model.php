<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('NOTIFICATION_TYPE_CHOSEN_DATE_IN_12_HOURS', 'chosen_date_in_12_hours');
define('NOTIFICATION_TYPE_HOSTED_DATE_IN_12_HOURS', 'hosted_date_in_12_hours');
define('NOTIFICATION_TYPE_FOLLOWING_DATE_IN_12_HOURS', 'following_date_in_12_hours');

class Log_Push_Notification_api_model extends CI_Model {

	public function insert_notification_chosen_date_in_12_hours($date_id) {

		$insert_array['notification_type'] = NOTIFICATION_TYPE_CHOSEN_DATE_IN_12_HOURS;
		$insert_array['date_id'] = $date_id;
		$insert_array['created_at'] = SQL_DATETIME;
		$this -> db -> insert('log_push_notification', $insert_array);

		return $this -> db -> insert_id();
	}

	public function insert_notification_hosted_date_in_12_hours($date_id) {

		$insert_array['notification_type'] = NOTIFICATION_TYPE_HOSTED_DATE_IN_12_HOURS;
		$insert_array['date_id'] = $date_id;
		$insert_array['created_at'] = SQL_DATETIME;
		$this -> db -> insert('log_push_notification', $insert_array);

		return $this -> db -> insert_id();
	}

	public function insert_notification_following_date_in_12_hours($date_id) {

		$insert_array['notification_type'] = NOTIFICATION_TYPE_FOLLOWING_DATE_IN_12_HOURS;
		$insert_array['date_id'] = $date_id;
		$insert_array['created_at'] = SQL_DATETIME;
		$this -> db -> insert('log_push_notification', $insert_array);

		return $this -> db -> insert_id();
	}

	public function check_notification_chosen_date_in_12_hours($date_id) {

		$this -> db -> where('notification_type', NOTIFICATION_TYPE_CHOSEN_DATE_IN_12_HOURS);
		$this -> db -> where('date_id', $date_id);
		$result = $this -> db -> get('log_push_notification');

		return $result -> num_rows() > 0 ? TRUE : FALSE;
	}

	public function check_notification_hosted_date_in_12_hours($date_id) {

		$this -> db -> where('notification_type', NOTIFICATION_TYPE_HOSTED_DATE_IN_12_HOURS);
		$this -> db -> where('date_id', $date_id);
		$result = $this -> db -> get('log_push_notification');

		return $result -> num_rows() > 0 ? TRUE : FALSE;
	}

	public function check_notification_following_date_in_12_hours($date_id) {

		$this -> db -> where('notification_type', NOTIFICATION_TYPE_FOLLOWING_DATE_IN_12_HOURS);
		$this -> db -> where('date_id', $date_id);
		$result = $this -> db -> get('log_push_notification');

		return $result -> num_rows() > 0 ? TRUE : FALSE;
	}
}
