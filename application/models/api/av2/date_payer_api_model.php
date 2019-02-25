<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Date_Payer_api_model extends CI_Model {

	public function get_date_payer($date_payer_id, $language_id = '1') {

		$this -> db -> where('date_payer_id', $date_payer_id);
		//$this -> db -> where('display_language_id', $language_id);
		$result = $this -> db -> get('date_payer');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}

	public function get_date_payers($language_id = '1') {
		$this -> db -> select('date_payer_id,description,view_order');
		$this -> db -> where('display_language_id', $language_id);
		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('date_payer');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

}
