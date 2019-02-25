<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Annual_Income_Range_api_model extends CI_Model {

	public function get_annual_income_ranges_by_country_id($country_id){
		$language_id = $this->session->userdata('sess_language_id');

		$this->db->select('annual_income_range.annual_income_range_id,annual_income_range.description,currency.description as currency_sign');
		$this->db->join('country','country.country_id = annual_income_range.country_id');
		$this->db->join('currency','currency.currency_id=country.currency_id');
		$this->db->where('country.country_id',$country_id);
		$this->db->where('currency.display_language_id',$language_id);
		$this->db->where('country.display_language_id',$language_id);

		$this->db->order_by('annual_income_range.view_order','ASC');

		$result = $this->db->get('annual_income_range');
		$results        = array();
		if($result->num_rows()>0){
			$results    = $result->result_array();
			/*
			$results = array();
			foreach($result->result_array() as $row){
				if($row['description']['0'] == '<')
				{
					$results[$row['annual_income_range_id']]  = $row['description']['0'].' '.$row['currency_sign'].' '.str_replace("<"," ",$row['description']);
				}
				else if( $row['description']['0'] == '>')
				{
					$results[$row['annual_income_range_id']]  = $row['description']['0'].' '.$row['currency_sign'].' '.str_replace(">"," ",$row['description']);
				}
				else
				{
					$results[$row['annual_income_range_id']]  = $row['currency_sign'].' '.$row['description'];
				}
			}*/
		}
		return $results;
	}

	public function get_annual_income_range($annual_income_range_id) {

		$this -> db -> where('annual_income_range_id', $annual_income_range_id);
		$result = $this -> db -> get('annual_income_range');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}
}
