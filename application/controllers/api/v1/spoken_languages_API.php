<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Spoken_Languages_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/v1/spoken_language_api_model');
    }

    public function index_get() {

        $spoken_languages = $this -> spoken_language_api_model -> get_spoken_languages($this -> rest ->language_id);

        $this -> response(array(
            'data' => $spoken_languages
        ), 200);
    }
}