<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Language extends CI_Controller {

	public function __construct() {
		parent::__construct();
		//        $this->Model_common->system_admin(); //to check the login
		$this -> load -> model('model_language');
		// $this->model_common->is_super_admin();
	}

	public function index($action = '') {
		$data["message"] = "";
		if ($action == "created")
			$data['message'] = "New language added successfully";
		$data['data'] = $this -> model_language -> languages();
		$data['page_name'] = 'language/index';
		$data['submenu'] = 'submenu/language';
		$this -> load -> view('theme/main', $data);
	}

	public function create($action = '', $id = '') {
		$this -> load -> library('form_validation');
		$this -> form_validation -> set_error_delimiters('<div class="error">', '</div>');
		$language_id = $id;
		$this -> load -> helpers("common_functions");
		$data["action"] = $action;
		$data["all_languages"] = get_all_languages();
		if ($this -> form_validation -> run("language") == false) {// validation hasn't been passed
			$data["action"] = $action;
			if ($action == 'edit') {
				$data1 = $this -> model_language -> get_language_details($id);
				$data['language_details'] = $data1;
				$data["language_id"] = $id;
			}
			$data["language_code_array"] = $this -> model_language -> get_existing_languages(1);
		} else {// passed validation
			if ($action == 'edit') {
				$this -> model_language -> update($id);
			} else {
				$language_id = $this -> model_language -> insert();
			}
			$root_path = get_root_path();
			$config["upload_path"] = $root_path . "uploads/flag/";
			$config["allowed_types"] = "gif|jpg|png";
			$config["max_size"] = "250";
			$config["max_width"] = "50";
			$config["max_height"] = "40";
			$this -> load -> library('upload', $config);
			if (!$this -> upload -> do_upload("flag")) {
				$error = array('error' => $this -> upload -> display_errors());
				$data["upload_message"] = $error["error"];
			} else {
				$image_data = array('upload_data' => $this -> upload -> data());
				$this -> model_language -> update_flag($image_data, $language_id);
				$data["upload_message"] = "Image uploaded successfully";
				redirect(base_url() . 'language/index/created');
			}
		}
		$data['page_name'] = 'language/create';
		$data['submenu'] = 'submenu/language';
		$this -> load -> view('theme/main', $data);
	}

	public function make_default($language_id = '') {
		if ($language_id) {
			$this -> model_language -> make_default($language_id);
		}
		redirect(base_url() . 'language/index');
	}
}
?>