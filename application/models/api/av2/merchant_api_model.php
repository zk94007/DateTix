<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Merchant_api_model extends CI_Model {

	public function get_merchant($merchant_id) {

		$this -> db -> where('merchant_id', $merchant_id);
		$result = $this -> db -> get('merchant');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}

	public function get_merchant_with_photo($merchant_id) {

		$this -> db -> join('merchant_photo', 'merchant_photo.merchant_id = merchant.merchant_id');
		$this -> db -> order_by('set_primary', 'desc');
		$this -> db -> where('merchant.merchant_id', $merchant_id);
		$result = $this -> db -> get('merchant');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}

	public function get_merchants() {

		$this -> db -> order_by('view_order', 'ASC');
		$result = $this -> db -> get('merchant');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function filter_merchants($budget_id = NULL, $sort_by = NULL, $cuisine_ids = NULL, $neighborhood_ids = NULL, $date_type_id = NULL, $user_lat = NULL, $user_long = NULL) {

		$this -> db -> join('merchant_photo', 'merchant_photo.merchant_id = merchant.merchant_id');

		if (!empty($budget_id)) {
			$this -> db -> where('budget_id', $budget_id);
		}

		if (!empty($neighborhood_ids)) {
			$this -> db -> where_in('neighborhood_id', $neighborhood_ids);
		}

		if (!empty($cuisine_ids)) {
			$this -> db -> join('merchant_cuisine', 'merchant_cuisine.merchant_id = merchant.merchant_id');
			$this -> db -> where_in('merchant_cuisine.cuisine_id', $cuisine_ids);
		}

		if (!empty($date_type_id)) {
			$this -> db -> join('merchant_date_type', 'merchant_date_type.merchant_id = merchant.merchant_id');
			$this -> db -> where('merchant_date_type.date_type_id', $date_type_id);
		}

		if (!empty($sort_by)) {

			if ($sort_by == 'Featured') {
				$this -> db -> order_by('is_featured', 'DESC');
				$this -> db -> order_by('name', 'ASC');

			} else if ($sort_by == 'Name') {
				$this -> db -> order_by('name', 'ASC');

			} else if ($sort_by == 'Price') {
				$this -> db -> order_by('price_range', 'ASC');

			} else {
				$this -> db -> order_by('merchant.view_order', 'ASC');
			}
		}

		$result = $this -> db -> get('merchant');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}

		// Add 'away_in_km' attribute if available
		$CI = & get_instance();
		$CI -> load -> model('api/av2/user_api_model');

		$results_with_distance = array();

		foreach ($results as $merchant) {

			if (!empty($user_lat) &&
				!empty($user_long) &&
				!empty($merchant['gps_lat']) &&
				!empty($merchant['gps_long'])) {

				$distance = $CI -> user_api_model -> distance_between_two_points($user_lat, $user_long, $merchant['gps_lat'], $merchant['gps_long'], 'K');

				$merchant['away_in_km'] = $distance;
			}
			$results_with_distance[] = $merchant;
		}
		$results = $results_with_distance;

		// Sort by distance
		if (!empty($sort_by) && $sort_by == 'Distance') {

			$results_with_distance = array();

			// Get only merchants that has gps location
			foreach ($results as $merchant) {
				if (!empty($merchant['away_in_km']))
					$results_with_distance[] = $merchant;
			}

			// Sort
			usort($results_with_distance, function ($a, $b) {
				if ($a['away_in_km'] > $b['away_in_km']) {
					return 1;
				} else if ($a['away_in_km'] < $b['away_in_km']) {
					return -1;
				}
				return 0;
			});

			$results = $results_with_distance;
		}

		return $results;
	}

}
