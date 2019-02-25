<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Education_Levels_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/av2/education_level_api_model');
    }

    public function index_get() {

        $education_levels = $this -> education_level_api_model -> get_education_levels($this -> rest ->language_id);

        $this -> response(array(
            'data' => $education_levels
        ), 200);
    }
}