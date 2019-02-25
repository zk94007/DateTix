<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Date_Review_api_model extends CI_Model {

	public function insert_date_review($insert_array) {

		$this -> db -> insert('date_review', $insert_array);
		return $this -> db -> insert_id();
	}

	public function get_date_review($date_review_id) {

		$this -> db -> where('date_review_id', $date_review_id);
		$result = $this -> db -> get('date_review');

		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}

	public function get_date_reviews_by_date_id($date_id) {

		$this -> db -> where('date_id', $date_id);
		$result = $this -> db -> get('date_review');

		return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
	}

	public function get_date_reviews_by_user_id($user_id) {

		$this -> db -> select('date_review.*');

		$this -> db -> join('date', 'date.date_id = date_review.date_id');
		$this -> db -> where('date.requested_user_id', $user_id);

		$result = $this -> db -> get('date_review');

		return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
	}

}
