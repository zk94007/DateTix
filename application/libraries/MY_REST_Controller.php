<?php

require 'REST_Controller.php';

class MY_REST_Controller extends REST_Controller {

	public function __construct() {
		parent::__construct();

		// Call _detect_api_key to validate api_key
		$allowed = parent::_detect_api_key();

		if ($allowed == FALSE) {

			$this -> response(array(
				'errors' => array(
					array(
						'id' => 'Currently not supported',
						'code' => 1000,
						'title' => 'Failed to call API',
						'detail' => 'Your API Key is invalid.'
					)
				)
			), 200);
		}

		// Update last active time
		$update_user_array['last_active_time'] = SQL_DATETIME;
		$this -> rest -> db -> where('user_id', $this -> rest -> user_id);
		$this -> rest -> db -> update('user', $update_user_array);

		// Get language id
		$language_id_variable = config_item('rest_language_id');
		$key_name = 'HTTP_'.strtoupper(str_replace('-', '_', $language_id_variable));

		$language_id = isset($this->_args[$language_id_variable]) ? $this->_args[$language_id_variable] : $this->input->server($key_name);

		if (!$language_id) {

			// Get last display language id of the user
			if ($row = $this->rest->db->where('user_id', $this->rest->user_id)->get('user')->row()) {

				$language_id = $row -> {'last_display_language_id'};
			}
		}

		if (!$language_id) {

			$language_id = 1;

		} else {

			if ( ! ($row = $this->rest->db->where('display_language_id', $language_id)->get('display_language')->row())) {
				$language_id = 1;
			}
		}

		$this -> rest -> language_id = $language_id;
	}

}