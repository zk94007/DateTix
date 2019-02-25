<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Spoken_Language_Levels_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/av2/spoken_language_level_api_model');
    }

    public function index_get() {

        $spoken_language_levels = $this -> spoken_language_level_api_model -> get_spoken_language_levels($this -> rest -> language_id);

        $this -> response(array(
            'data' => $spoken_language_levels
        ), 200);
    }
}