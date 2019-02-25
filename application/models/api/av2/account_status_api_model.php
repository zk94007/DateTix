<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_Status_api_model extends CI_Model {

	public function get_account_statuses($language_id = '1') {
		$this -> db -> select('account_status_id,description,view_order');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('account_status');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

}
