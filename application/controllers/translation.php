<?php

if (!defined('BASEPATH'))
exit('No direct script access allowed');

class Translation extends CI_Controller {

	public function __construct() {
		parent::__construct();
		//        $this->Model_common->system_admin(); //to check the login
		$this->load->model('model_translation');
		ini_set('max_execution_time', 24000);
	}

	public function phrases($language_id = '') {
		if (!$language_id)
		// $language_id = 2;
		//        $post_language_id = $this->input->post("language_id");
		//        if ($post_language_id)
		//            $language_id = $post_language_id;
		//        $search_submit = $this->input->post("search");
		//        $search_text = "";
		//        if ($search_submit) {
		//            $search_text = $this->input->post("search_text");
		//        }
		$search_text = "";
		$not_approved = urldecode($this->input->get('na', true));
		$search_text = urldecode($this->input->get('search_text', true));
		$query_string = query_string();

		/* pagination */
		$this->load->library("pagination");
		$config = array();
		$link = base_url() . "translation/phrases/" . $language_id . "/";
		$config["base_url"] = $link;
		$config["total_rows"] = $this->model_translation->get_phrases_with_limit($language_id, $search_text, 1, '', '', $not_approved);
		$config["per_page"] = 50;
		$config["uri_segment"] = 4;
		$config['suffix'] = $query_string;
		$config['first_url'] = $link . $query_string;

		$this->pagination->initialize($config);
		$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$data["phrases"] = $this->model_translation->get_phrases_with_limit($language_id, $search_text, '', $config["per_page"], $page, $not_approved);
		$data["links"] = $this->pagination->create_links();
		/* end pagination */

		$data["language_id"] = $language_id;
		$data["search_text"] = $search_text;
		$this->load->model("model_language");
		$data["language"] = $this->model_language->get_language($language_id);
		$data["language_array"] = $this->model_language->get_all_languages();

		$data['page_name'] = 'language/phrase';
		$data['submenu'] = 'submenu/translation';
		$this->load->view('theme/main', $data);
	}

	public function translate_phrase() {
		$language_id = $this->input->post("language_id");
		$phrase_id = $this->input->post("phrase_id");
		$translation = $this->input->post("translation");
		$save = $this->input->post("save");
		if ($save == "approve") {
			$this->model_translation->approve_translation($phrase_id, $language_id);
		} else if ($save == "save_approve") {
			$this->model_translation->update_translation($phrase_id, $language_id, $translation);
			$this->model_translation->approve_translation($phrase_id, $language_id);
		} else {
			$this->model_translation->update_translation($phrase_id, $language_id, $translation);
		}
		exit;
	}

	public function switch_language() {
			
			
		$language_id = $this->input->get('lang_id');
		$this->load->model('model_user');
		if($user_id = $this->session->userdata('user_id'))
		{
			$data['last_display_language_id'] = $language_id ;
			$this->model_user->update_user($user_id,$data);
		}
			
			
		$this->model_translation->setLang($language_id);
		if ( !($return_to = $this->input->get('return_to')) ) {
			$return_to = 'index.html';
		}
		else
		{
			if($extra_url_para = $this->session->userdata('extra_para'))
			{
				$return_to.= '?'.$extra_url_para;
			}
		}

		redirect(url_city_name() . '/' . $return_to);
	}

	public function translate_new($language_id = '') {
		if ($language_id) {
			$this->load->model("model_language");
			$language_code = $this->model_language->get_language_code($language_id);
			$this->model_translation->google_translate_new($language_id, $language_code);
		}
		redirect(base_url() . "language/");
	}

	public function translate_all($language_id = '') {
		if ($language_id) {
			$this->load->model("model_language");
			$language_code = $this->model_language->get_language_code($language_id);
			$this->model_translation->google_translate_all($language_id, $language_code);
		}
		redirect(base_url() . "language/");
	}

	public function notapproved($language_id = '') {
		if (!$language_id)
		redirect(base_url() . "translation/phrases");
		$query_string = query_string();
		$link = "translation/phrases/$language_id" . $query_string;
		redirect($link);
	}

	public function search() {
		$language_id = $this->input->get('language_id', true);
		$query_string = query_string();
		$link = "translation/phrases/$language_id" . $query_string;
		redirect($link);
	}

	public function remove($language_id) {
		if (!$language_id)
		redirect(base_url() . "translation/phrases");
		$this->model_translation->remove_all_phrases($language_id);
		redirect(base_url() . "translation/phrases/$language_id");
	}

	public function save_phrases(){
		$this->model_translation->save_all_phrases();
		$language_id = $this->input->post("language_id");
		redirect(base_url() . "translation/phrases/$language_id");
	}
	public function oldest_phrase($offset=0) {
		$d = $this->input->get("d");
		if ($d)
		$data["feedback"] = "Phrases deleted successfully";
		$ns = $this->input->get("ns");
		if ($ns)
		$data["feedback"] = "No phrases selected";
		/* pagination */
		$month = $this->input->post("month");
		if (!$month)
		$month = 1;
		$data["month"] = $month;
		$this->load->library("pagination");
		$config = array();
		$link = base_url() . "translation/oldest_phrase/";
		$config["base_url"] = $link;
		$config["per_page"] = 5;
		$phrases = $this->model_translation->get_oldest_phrases($month, $offset, $config["per_page"]);
		$config["total_rows"] = $phrases["count"];
		$config["uri_segment"] = 3;
		$config['first_url'] = $link;
		$data["phrases"] = $phrases["data"];
		$this->pagination->initialize($config);
		$data["links"] = $this->pagination->create_links();
		/* end pagination */

		$data['page_name'] = 'language/oldest_phrase';
		$data['submenu'] = 'submenu/translation';
		$this->load->view('theme/main', $data);
	}

	public function delete_phrase() {
		$delete_phrase = $this->input->post("phrase_id");
		if (!empty($delete_phrase)) {
			$this->model_translation->delete_oldest_phrase();
			redirect(base_url() . "translation/oldest_phrase/?d=1");
		} else {
			redirect(base_url() . "translation/oldest_phrase/?ns=1");
		}
	}
	/*     * ******* end functions *********** */
}