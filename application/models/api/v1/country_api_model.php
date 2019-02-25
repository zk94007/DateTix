<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Country_api_model extends CI_Model {

	public function get_country_by_city_id($city_id) {
		$this->db->select('country.*');
		$this->db->where('city_id', $city_id);
		$this->db->join('province', 'province.country_id = country.country_id', 'inner');
		$this->db->join('city', 'city.province_id = province.province_id', 'inner');
		$q = $this->db->get('country', 1);
		return ($q->num_rows() > 0) ? $q->row() : NULL ;
	}

	public function get_country_id_by_country_code($country_code) {

		$this -> db -> where('country_code', $country_code);
		$result = $this -> db -> get('country');

		return $result -> num_rows() > 0 ? $result -> row() -> country_id : NULL;
	}
}
