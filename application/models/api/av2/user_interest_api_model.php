<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Interest_api_model extends CI_Model {

	public function get_user_interests_by_user_id($user_id, $language_id = 1) {
		$this -> db -> select('i.interest_id, i.interest_category_id, i.description');
		$this -> db -> join('user_interest as ui', 'ui.interest_id = i.interest_id');
		$this -> db -> where('ui.user_id', $user_id);
		$this -> db -> where('i.display_language_id', $language_id);

		$result = $this -> db -> get('interest as i');
		$results = array();
		if ($result -> num_rows() > 0) {
			$results = $result -> result_array();
		}
		return $results;
	}

	public function insert_user_interest_with_params($user_id, $interest_id) {
		$insert_array   = array('user_id'=>$user_id,
			'interest_id'=>$interest_id);
		$this->db->insert('user_interest',$insert_array);
	}

	public function insert_user_interest($insert_array) {

		$this -> db -> insert('user_interest', $insert_array);
		return $this -> db -> insert_id();
	}

	public function delete_user_interests_by_user_id($user_id) {
		$this -> db -> where('user_id', $user_id);
		$this -> db -> delete('user_interest');
	}

	public function update_user_records_with_interest_ids($user_id, $interest_ids) {

		// Delete records whose 'interest_id' is not in 'interest_ids'.
		$this -> db -> where('user_id', $user_id);
		$this -> db -> where_not_in('interest_id', $interest_ids);
		$this -> db -> delete('user_interest');

		// Insert new records
		foreach ($interest_ids as $interest_id) {

			if ($this -> db -> get_where('user_interest',
					array(
						'user_id' => $user_id,
						'interest_id' => $interest_id)) -> num_rows() == 0) {

				$insert_array['user_id'] = $user_id;
				$insert_array['interest_id'] = $interest_id;

				$this -> insert_user_interest($insert_array);
			}
		}
	}

	public function get_common_interests($user_id, $friend_id) {

		$user_interests = $this -> get_user_interests_by_user_id($user_id);
		$friend_interests = $this -> get_user_interests_by_user_id($friend_id);

		$common_interests = array();

		if (!empty($user_interests) && !empty($friend_interests)) {

			$CI = & get_instance();

			$CI -> load -> model('api/av2/interest_api_model');

			foreach ($user_interests as $user_interest) {
				foreach ($friend_interests as $friend_interest) {

					if ($user_interest['interest_id'] == $friend_interest['interest_id']) {

						$common_interests[] = $CI -> interest_api_model -> get_interest($user_interest['interest_id']);
					}
				}
			}
		}

		return $common_interests;
	}
}
