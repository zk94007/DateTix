<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Body_Types_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/av2/body_type_api_model');
    }

    public function index_get() {

        $body_types = $this -> body_type_api_model -> get_body_types($this -> rest ->language_id);

        $this -> response(array(
            'data' => $body_types
        ), 200);
    }
}