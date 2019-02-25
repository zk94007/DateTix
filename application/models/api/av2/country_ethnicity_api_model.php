<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Country_Ethnicity_api_model extends CI_Model {

	public function get_ethnicities_by_country_id($country_id = NULL, $display_language_id = 1) {

		$this -> db -> select('DISTINCT(ethnicity.ethnicity_id), ethnicity.description, ethnicity.view_order');

		$this -> db -> join('ethnicity', 'country_ethnicity.ethnicity_id = ethnicity.ethnicity_id');

		if (!empty($country_id)) {
			$this -> db -> where('country_ethnicity.country_id', $country_id);
		}
		$this -> db -> where('ethnicity.display_language_id', $display_language_id);

		$this -> db -> order_by('ethnicity.view_order', 'ASC');

		$result = $this -> db -> get('country_ethnicity');

		return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
	}
}
