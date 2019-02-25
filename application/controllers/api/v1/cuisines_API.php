<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Cuisines_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/v1/cuisine_api_model');
    }

    public function index_get() {

        $cuisines = $this -> cuisine_api_model -> get_cuisines($this -> rest ->language_id);

        $this -> response(array(
            'data' => $cuisines
        ), 200);
    }
}