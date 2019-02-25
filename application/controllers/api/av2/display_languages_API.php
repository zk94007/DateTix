<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Display_Languages_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/av2/display_language_api_model');
    }

    public function index_get() {

        $display_languages = $this -> display_language_api_model -> get_display_languages();

        $this -> response(array(
            'data' => $display_languages
        ), 200);
    }
}