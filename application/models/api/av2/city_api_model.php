<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class City_api_model extends CI_Model {

	public function get_cities($select = array(), $language_id) {

		$this -> db -> select($select);
		$this -> db -> where('display_language_id', $language_id);

		return $this -> db -> get('city') -> result_array();
	}

	/**
	 * Get city details by id
	 * @param  int $city_id
	 * @param  int $language_id
	 * @return mixed          returns null on invalid id, result object
	 */
	public function get($city_id, $language_id)
	{
		/*
		 | Query with fallback, selects city with any language if city name with current
		 | lang desc doesn't exist
		 */
		$sql = "
				SELECT t1.*, country.country_id FROM
				(SELECT * FROM `city` WHERE `city_id` = ? AND `display_language_id` = ?
				UNION
				SELECT * FROM `city` WHERE `city_id` = ? ORDER BY city_id,display_language_id
				LIMIT 1) as t1
				JOIN province ON province.province_id = t1.province_id
				JOIN country ON country.country_id = province.country_id
				LIMIT 1
        ";
		$q = $this->db->query($sql, array($city_id, $language_id, $city_id));
		return ($q->num_rows() > 0) ? $q->row() : NULL;
	}

	public function getByName($city_name)
	{
		$this->db->where('description', $city_name);
		$this->db->where('display_language_id', 1);
		$this->db->where('is_active', 1);
		$q = $this->db->get('city');
		return ($q->num_rows()) ? $q->row() : NULL ;
	}

	public function getCountryByCity($city_id)
	{
		$this->db->where('city.city_id', $city_id);
		$this->db->join('province', 'province.province_id = city.province_id', 'inner');
		$this->db->join('country', 'country.country_id = province.country_id', 'inner');
		$this->db->select('country.*');
		$q = $this->db->get('city', 1);

		return ($q->num_rows() > 0) ? $q->row_array() : NULL ;
	}
}

/* End of file model_city.php */
/* Location: ./application/model/model_city.php */
